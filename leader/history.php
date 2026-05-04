<?php
require '../conn.php';
session_start();

// Validasi session
if (!isset($_SESSION['role'])) {
    header('location: ../index.php');
    exit;
}

// Auto Refresh every 60 seconds
echo '<meta http-equiv="refresh" content="60">';

// Logout
if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('location: ../index.php');
    exit;
}

// Ambil bulan dan tahun yang dipilih
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Query ambil data
$sql = "SELECT 
            DATE(tgl_lot) AS tanggal, 
            shift,
            SUM(EVA_IN) as EVA_IN, SUM(EVA_OUT) as EVA_OUT,
            SUM(CSR_IN) as CSR_IN, SUM(CSR_OUT) as CSR_OUT,
            SUM(CDR_IN) as CDR_IN, SUM(CDR_OUT) as CDR_OUT,
            SUM(SRI_IN) as SRI_IN, SUM(SRI_OUT) as SRI_OUT,
            SUM(DRI_IN) as DRI_IN, SUM(DRI_OUT) as DRI_OUT
        FROM prod_report
        WHERE MONTH(tgl_lot) = ? AND YEAR(tgl_lot) = ?
        GROUP BY DATE(tgl_lot), shift
        ORDER BY DATE(tgl_lot), shift";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$columns = ['EVA-IN', 'EVA-OUT', 'CSR-IN', 'CSR-OUT', 'CDR-IN', 'CDR-OUT', 'SRI-IN', 'SRI-OUT', 'DRI-IN', 'DRI-OUT'];
$totals = array_fill_keys($columns, 0);

// Array grafik individual
$datesEVA = [];
$dataEVA = [];

$datesSR = [];
$dataSR = [];

$datesDR = [];
$dataDR = [];

while ($row = $result->fetch_assoc()) {
    $tgl = $row['tanggal'];
    $shift = $row['shift'];

    foreach ($columns as $col) {
        $row[$col] = $row[str_replace('-', '_', $col)];
        $totals[$col] += $row[$col];
    }

    $data[$tgl][$shift] = $row;
}

// Proses ulang untuk masing-masing grafik
foreach ($data as $tgl => $shifts) {
    $evaIn = array_sum(array_column($shifts, 'EVA-IN'));
    $srCond = array_sum(array_column($shifts, 'CSR-IN')) + array_sum(array_column($shifts, 'SRI-IN'));
    $drCond = array_sum(array_column($shifts, 'CDR-IN')) + array_sum(array_column($shifts, 'DRI-IN'));

    if ($evaIn > 0) {
        $datesEVA[] = $tgl;
        $dataEVA[] = $evaIn;
    }

    if ($srCond > 0) {
        $datesSR[] = $tgl;
        $dataSR[] = $srCond;
    }

    if ($drCond > 0) {
        $datesDR[] = $tgl;
        $dataDR[] = $drCond;
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <title>HEPI Apps</title>
    <script src="../js/color-modes.js"></script>
    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../css/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
        }

        .chart-container {
            width: 32%;
            /* karena sekarang ada 3 chart */
            height: 300px;
        }
    </style>
</head>

<body>
    <!-- Themes Mode -->
    <?php include '../library/themes.php'; ?>

    <div class="container-fluid text-center">
        <!-- ROW 1 -->
        <div class="row mt-3">
            <div class="col text-start">
                <a href="../dashboard.php" class="btn btn-sm btn-outline-success me-2" style="width: 150px;">Main Menu</a>
            </div>
            <div class="col text-center">
                <a href="index.php" class="btn btn-sm btn-outline-primary" style="width: 150px;">Live Monitor</a>
            </div>
            <div class="col text-end">
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
            </div>

            <!-- Modal Logout -->
            <div class="text-start modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="" method="POST">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="logoutModalLabel">Notification</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">Logout?</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary" name="btn_logout">Yes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pilih Bulan dan Tahun -->
        <form method="POST" class="mt-3 d-flex gap-2 align-items-center justify-content-center">
            <select name="month" class="form-select w-auto">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == (int)$selectedMonth ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select w-auto">
                <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $y == (int)$selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>

        <!-- Grafik -->
        <div class="mt-5 charts-container">
            <div class="chart-container">
                <canvas id="chartEVA"></canvas>
            </div>

            <div class="chart-container">
                <canvas id="chartSRCOND"></canvas>
            </div>

            <div class="chart-container">
                <canvas id="chartDRCOND"></canvas>
            </div>
        </div>

        <!-- Tabel Laporan Produksi -->
        <div class="mt-3">
            <?php if (!empty($data)): ?>
                <table class="table table-bordered mt-4">
                    <thead class="table">
                        <tr>
                            <th rowspan="2" class="text-center align-middle bg-light-subtle">Tanggal</th>
                            <?php foreach ($columns as $col): ?>
                                <?php $colClass = str_contains($col, 'IN') ? 'text-primary' : 'text-danger'; ?>
                                <th colspan="3" class="text-center bg-light-subtle <?= $colClass ?>"><?= $col ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <?php for ($s = 1; $s <= 3; $s++): ?>
                                    <?php $subColClass = str_contains($col, 'IN') ? 'text-primary' : 'text-danger'; ?>
                                    <th class="text-center bg-light-subtle <?= $subColClass ?>" style="width: 58px;"><?= $s ?></th>
                                <?php endfor; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $tgl => $shifts): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $tgl ?></td>
                                <?php foreach ($columns as $col): ?>
                                    <?php for ($s = 1; $s <= 3; $s++): ?>
                                        <?php $cellClass = str_contains($col, 'IN') ? 'text-primary' : 'text-danger'; ?>
                                        <td class="text-center <?= $cellClass ?>">
                                            <?= isset($shifts[$s][$col]) ? number_format($shifts[$s][$col]) : '0' ?>
                                        </td>
                                    <?php endfor; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table">
                        <tr>
                            <th class="text-center bg-light-subtle">Total</th>
                            <?php foreach ($columns as $col): ?>
                                <?php $totalClass = str_contains($col, 'IN') ? 'text-primary' : 'text-danger'; ?>
                                <th colspan="3" class="text-center bg-light-subtle <?= $totalClass ?>"><?= number_format($totals[$col]) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <div class="alert alert-warning text-center">Tidak ada data untuk bulan dan tahun ini.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Inject data individual
        var datesEVA = <?php echo json_encode($datesEVA); ?>;
        var dataEVA = <?php echo json_encode($dataEVA); ?>;

        var datesSR = <?php echo json_encode($datesSR); ?>;
        var dataSR = <?php echo json_encode($dataSR); ?>;

        var datesDR = <?php echo json_encode($datesDR); ?>;
        var dataDR = <?php echo json_encode($dataDR); ?>;

        function createChart(canvasId, label, dates, data, color) {
            new Chart(document.getElementById(canvasId).getContext('2d'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        fill: false,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        createChart('chartEVA', 'Production Evaporator', datesEVA, dataEVA, '#C51F1A');
        createChart('chartSRCOND', 'Production SR COND', datesSR, dataSR, '#0000ff');
        createChart('chartDRCOND', 'Production DR COND', datesDR, dataDR, '#008000');
    </script>

</body>

</html>