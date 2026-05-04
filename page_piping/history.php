<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['role'])) {
    header('location: ../index.php');
    exit;
}

if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('location: ../index.php');
    exit;
}

$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Daftar kolom yang akan ditampilkan
$columns = [
    'CAP_5K2_IN',
    'CAP_5K2_OUT',
    'DIS_5K2_IN',
    'DIS_5K2_OUT',
    'SUC_5K2_IN',
    'SUC_5K2_OUT',
    'CAP_7K1_IN',
    'CAP_7K1_OUT',
    'DIS_7K1_IN',
    'DIS_7K1_OUT',
    'CAP_9K2_IN',
    'CAP_9K2_OUT',
    'DIS_9K2_IN',
    'DIS_9K2_OUT',
    'SUC_9K2_IN',
    'SUC_9K2_OUT',
    'CAP_9CY_IN',
    'CAP_9CY_OUT',
    'DIS_9CY_IN',
    'DIS_9CY_OUT',
    'SUC_9CY_IN',
    'SUC_9CY_OUT',
    'CAP_68K_IN',
    'CAP_68K_OUT',
    'DIS_68K_IN',
    'DIS_68K_OUT',
    'SUC_68K_IN',
    'SUC_68K_OUT',
    'CAP_10K_IN',
    'CAP_10K_OUT',
    'DIS_10K_IN',
    'DIS_10K_OUT',
    'CAP_13K_IN',
    'CAP_13K_OUT',
    'DIS_13K_IN',
    'DIS_13K_OUT',
    'SUC_13K_IN',
    'SUC_13K_OUT',
];

// Ambil data dari DB
$data = [];
$totals = array_fill_keys($columns, 0);

$sql = "SELECT tgl_lot, shift, " . implode(", ", $columns) . " 
        FROM piping_prod_report 
        WHERE MONTH(tgl_lot) = ? AND YEAR(tgl_lot) = ?
        ORDER BY tgl_lot, shift";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tgl = $row['tgl_lot'];
    $shift = (int)$row['shift'];
    foreach ($columns as $col) {
        $val = (int)$row[$col];
        $data[$tgl][$shift][$col] = $val;
        $totals[$col] += $val;
    }
}

$dates = [];
$dataCAP = [];
$dataDIS = [];
$dataSUC = [];

foreach ($data as $tgl => $shifts) {
    $dates[] = $tgl;
    $capTotal = $disTotal = $sucTotal = 0;

    foreach ($shifts as $shift) {
        foreach ($shift as $col => $val) {
            if (str_starts_with($col, 'CAP') && str_ends_with($col, '_IN')) $capTotal += $val;
            if (str_starts_with($col, 'DIS') && str_ends_with($col, '_IN')) $disTotal += $val;
            if (str_starts_with($col, 'SUC') && str_ends_with($col, '_IN')) $sucTotal += $val;
        }
    }

    $dataCAP[] = $capTotal;
    $dataDIS[] = $disTotal;
    $dataSUC[] = $sucTotal;
}

if (empty($data)) {
    $data = [];
    foreach ($columns as $col) {
        $totals[$col] = 0;
    }
}
?>

<!DOCTYPE html>
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
            height: 290px;
        }
    </style>
</head>

<body>
    <!-- Themes Mode -->
    <?php include '../library/themes.php'; ?>

    <div class="container-fluid text-center mt-3">
        <!-- ROW 1 -->
        <div class="row mb-3">
            <!-- Button Main Menu -->
            <div class="col text-start">
                <a href="../dashboard.php" class="btn btn-sm btn-outline-success mx-1" style="width: 150px;">Main Menu</a>
            </div>
            <!-- Button Sub Menu -->
            <div class="col text-center">
                <a href="index.php" class="btn btn-sm btn-outline-primary mx-1" style="width: 150px;">Dashboard</a>
                <a href="history.php" class="btn btn-sm btn-outline-info mx-1" style="width: 150px;">Production Report</a>
            </div>
            <!-- Button Logout -->
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
                            <div class="modal-body">
                                Logout?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary" name="btn_logout">Yes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bulan dan Tahun -->
        <form method="POST" class="mt-3 d-flex gap-2 align-items-center justify-content-center">
            <select name="month" class="form-select form-select-sm w-auto">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == (int)$selectedMonth ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select form-select-sm w-auto">
                <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $y == (int)$selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
        </form>

        <!-- Grafik -->
        <div class="mt-2 charts-container">
            <div class="chart-container">
                <canvas id="chartCAP"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="chartDIS"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="chartSUC"></canvas>
            </div>
        </div>

        <!-- Tabel Laporan Produksi -->
        <div class="mt-2">
            <?php if (!empty($data)): ?>
                <?php
                $categories = [
                    'Capilarry Report' => array_filter($columns, fn($c) => str_starts_with($c, 'CAP')),
                    'Discharge Report' => array_filter($columns, fn($c) => str_starts_with($c, 'DIS')),
                    'Suction Report' => array_filter($columns, fn($c) => str_starts_with($c, 'SUC')),
                ];
                ?>

                <?php foreach ($categories as $cat => $catCols): ?>
                    <h5 class="mt-2"><?= $cat ?></h5>
                    <table class="table table-bordered table-sm mt-2" style="font-size:10px;">
                        <thead class="table">
                            <tr>
                                <th rowspan="2" class="text-center align-middle bg-light-subtle" style="width: 70px;">Tanggal</th>
                                <?php foreach ($catCols as $col): ?>
                                    <?php $colClass = str_contains($col, 'IN') ? 'text-primary' : 'text-danger'; ?>
                                    <th colspan="3" class="text-center bg-light-subtle <?= $colClass ?>"><?= $col ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php foreach ($catCols as $col): ?>
                                    <?php for ($s = 1; $s <= 3; $s++): ?>
                                        <th class="text-center bg-light-subtle <?= str_contains($col, 'IN') ? 'text-primary' : 'text-danger' ?>" style="width: 50px;">
                                            <?= $s ?>
                                        </th>
                                    <?php endfor; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $tgl => $shifts): ?>
                                <tr>
                                    <td class="text-center align-middle"><?= date('d-m-Y', strtotime($tgl)) ?></td>
                                    <?php foreach ($catCols as $col): ?>
                                        <?php for ($s = 1; $s <= 3; $s++): ?>
                                            <td class="text-center <?= str_contains($col, 'IN') ? 'text-primary' : 'text-danger' ?>">
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
                                <?php foreach ($catCols as $col): ?>
                                    <th colspan="3" class="text-center bg-light-subtle <?= str_contains($col, 'IN') ? 'text-primary' : 'text-danger' ?>">
                                        <?= number_format($totals[$col]) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center">Tidak ada data untuk bulan dan tahun ini.</div>
            <?php endif; ?>
        </div>

        <!-- Chart.js -->
        <script>
            const dates = <?= json_encode($dates) ?>;
            const dataCAP = <?= json_encode($dataCAP) ?>;
            const dataDIS = <?= json_encode($dataDIS) ?>;
            const dataSUC = <?= json_encode($dataSUC) ?>;

            function createChart(canvasId, label, data, color) {
                new Chart(document.getElementById(canvasId).getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: color,
                            backgroundColor: color + '33', // transparan
                            fill: true,
                            tension: 0.2,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Produksi'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            }
                        }
                    }
                });
            }

            createChart('chartCAP', 'Production CAP', dataCAP, '#007bff');
            createChart('chartDIS', 'Production DIS', dataDIS, '#28a745');
            createChart('chartSUC', 'Production SUC', dataSUC, '#dc3545');
        </script>

        <!-- Javascript -->
        <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>