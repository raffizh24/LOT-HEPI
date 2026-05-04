<?php
require '../conn.php';
session_start();
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

function getCurrentShift()
{
    date_default_timezone_set('Asia/Jakarta');
    $now = time();
    $hour = date('H', $now);
    $minute = date('i', $now);
    $total_minutes = $hour * 60 + $minute;

    if ($total_minutes >= 475 && $total_minutes < 1020) {
        $shift = 1;
        $shiftLabel = 'Shift 1 (07:55 - 17:00)';
        $tgl_lot = $now;
    } elseif ($total_minutes >= 1020 && $total_minutes < 1440) {
        $shift = 2;
        $shiftLabel = 'Shift 2 (17:00 - 00:30)';
        $tgl_lot = $now;
    } elseif ($total_minutes >= 0 && $total_minutes < 30) {
        $shift = 2;
        $shiftLabel = 'Shift 2 (17:00 - 00:30)';
        $tgl_lot = strtotime('-1 day', $now);
    } else {
        $shift = 3;
        $shiftLabel = 'Shift 3 (00:30 - 07:55)';
        $tgl_lot = strtotime('-1 day', $now);
    }

    return [$shift, $shiftLabel, date('Y-m-d H:i:s', $tgl_lot)];
}

list($currentShift, $shiftLabel) = getCurrentShift();

// Daftar komponen
$komponenList = [
    'CAP-5K2' => ['name' => 'Capillary 5K2', 'part_code' => 'CAP-5K2'],
    'CAP-7K1' => ['name' => 'Capillary 7K', 'part_code' => 'CAP-7K1'],
    'CAP-9K2' => ['name' => 'Capillary 9K2', 'part_code' => 'CAP-9K2'],
    'CAP-9CY' => ['name' => 'Capillary 9CAY', 'part_code' => 'CAP-9CY'],
    'CAP-68K' => ['name' => 'Capillary 6K & 8K', 'part_code' => 'CAP-68K'],
    'CAP-10K' => ['name' => 'Capillary 10K', 'part_code' => 'CAP-10K'],
    'CAP-13K' => ['name' => 'Capillary 13K', 'part_code' => 'CAP-13K'],

    'DIS-5K2' => ['name' => 'Discharge 5K2', 'part_code' => 'DIS-5K2'],
    'DIS-7K1' => ['name' => 'Discharge 7K', 'part_code' => 'DIS-7K1'],
    'DIS-9K2' => ['name' => 'Discharge 9K2', 'part_code' => 'DIS-9K2'],
    'DIS-9CY' => ['name' => 'Discharge 9CAY', 'part_code' => 'DIS-9CY'],
    'DIS-68K' => ['name' => 'Discharge 6K & 8K', 'part_code' => 'DIS-68K'],
    'DIS-10K' => ['name' => 'Discharge 10K', 'part_code' => 'DIS-10K'],
    'DIS-13K' => ['name' => 'Discharge 13K', 'part_code' => 'DIS-13K'],

    'SUC-5K2' => ['name' => 'Suction 5K2 & 7K', 'part_code' => 'SUC-5K2'],
    'SUC-9K2' => ['name' => 'Suction 9K2', 'part_code' => 'SUC-9K2'],
    'SUC-9CY' => ['name' => 'Suction 9CAY', 'part_code' => 'SUC-9CY'],
    'SUC-IVT' => ['name' => 'Suction 6K, 8K & 10K', 'part_code' => 'SUC-IVT'],
    'SUC-13K' => ['name' => 'Suction 13K', 'part_code' => 'SUC-13K'],

    'ALL-IDU' => ['name' => 'Tube Assy - Indoor', 'part_code' => 'ALL-IDU'],
];

$komponen = [];

// Ambil data IN/OUT dan stok untuk setiap komponen
$tgl_shift = date('Y-m-d', strtotime(getCurrentShift()[2]));;
foreach ($komponenList as $kode => $info) {
    // LOT
    $lot = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM piping_lot WHERE coupon LIKE '{$kode}%' AND status='Used'"), MYSQLI_ASSOC);

    // Total IN/OUT bulan ini
    $in = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM piping_history_lot WHERE status='IN' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    $out = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM piping_history_lot WHERE status='OUT' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    // IN & OUT untuk shift aktif (per tanggal hari ini)
    $inShift = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM piping_history_lot 
         WHERE status='IN' AND DATE(tgl_lot)='{$tgl_shift}'
         AND shift=$currentShift AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    $outShift = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM piping_history_lot 
         WHERE status='OUT' AND DATE(tgl_lot)='{$tgl_shift}'
         AND shift=$currentShift AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    // Stok
    $stok = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT qty FROM piping_stok WHERE part_code='{$info['part_code']}'"
    ))['qty'] ?? 0;

    $komponen[$kode] = [
        'name' => $info['name'],
        'lot' => $lot,
        'total_in' => $in,
        'total_out' => $out,
        'stok' => $stok,
        'shift_in' => $inShift,
        'shift_out' => $outShift,
    ];
}

include 'updateProductionReports.php';
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>HEPI Apps</title>
    <script src="../js/color-modes.js"></script>
    <script src="../js/jquery-3.7.1.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../css/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" rel="stylesheet">
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
        <!-- ROW 2 -->
        <div class="row mt-3">
            <?php foreach ($komponen as $kode => $data): ?>
                <div class="col">
                    <div class="card text-center mb-3" style="width: 245px; height: 400px;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['name'] ?></h5>
                            <!-- Monthly -->
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-primary w-100" disabled><?= $data['total_in'] ?></button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-danger w-100" disabled><?= $data['total_out'] ?></button>
                                </div>
                            </div>
                            <!-- Daily -->
                            <div class="row mt-1">
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100" disabled>
                                        S<?= $currentShift ?> : <?= $data['shift_in'] ?>
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100" disabled>
                                        S<?= $currentShift ?> : <?= $data['shift_out'] ?>
                                    </button>
                                </div>
                            </div>
                            <!-- Live Stock -->
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" class="btn btn-sm btn-success w-100" disabled>Live Stock: <?= $data['stok'] ?></button>
                            </div>
                            <!-- Coupon -->
                            <div class="list-group my-3">
                                <a class="list-group-item list-group-item-action" aria-current="true">
                                    <div class="d-flex w-100 justify-content-between">
                                        <!-- Scrollable Table Wrapper -->
                                        <div style="max-height: 195px; overflow-y: auto; width: 100%;">
                                            <table class="table table-sm m-0 text-center" style="font-size: 7px;">
                                                <thead>
                                                    <tr>
                                                        <th>Coupon</th>
                                                        <th>Date</th>
                                                        <th>Qty</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['lot'] as $lot): ?>
                                                        <tr>
                                                            <td><?= $lot['coupon'] ?></td>
                                                            <td>
                                                                <?php
                                                                $tgl_lot = strtotime($lot['tgl_lot']);
                                                                echo ($lot['tgl_lot'] !== '0000-00-00 00:00:00')
                                                                    ? date('Y-m-d H:i', $tgl_lot)
                                                                    : '0000-00-00 00:00';
                                                                ?>
                                                            </td>
                                                            <td><?= $lot['qty'] ?></td>
                                                            <td><?= $lot['status'] ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col">
                <div class="card text-center mb-3" style="width: 245px; height: 400px;">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascript -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>