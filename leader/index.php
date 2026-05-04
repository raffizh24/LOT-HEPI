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
    'EVA' => ['name' => 'Evaporator', 'part_code' => 'PEVA-B243JBEZ'],
    'CSR' => ['name' => 'Condensor B070', 'part_code' => 'DCON-B070JBEZ'],
    'CDR' => ['name' => 'Condensor B105', 'part_code' => 'DCON-B105JBEZ'],
    'SRI' => ['name' => 'Condensor B074', 'part_code' => 'DCON-B074JBEZ'],
    'DRI' => ['name' => 'Condensor B075', 'part_code' => 'DCON-B075JBEZ'],
];

$komponen = [];

// Ambil data IN/OUT dan stok untuk setiap komponen
$tgl_shift = date('Y-m-d', strtotime(getCurrentShift()[2]));;
foreach ($komponenList as $kode => $info) {

    // LOT
    $lot = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM lot WHERE coupon LIKE '{$kode}%' AND status='Used'"), MYSQLI_ASSOC);

    // Total IN/OUT bulan ini
    $in = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM history_lot WHERE status='IN' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    $out = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM history_lot WHERE status='OUT' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    // IN & OUT untuk shift aktif (per tanggal hari ini)
    $inShift = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM history_lot 
         WHERE status='IN' AND DATE(tgl_lot)= '{$tgl_shift}'
         AND shift=$currentShift AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    $outShift = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total FROM history_lot 
         WHERE status='OUT' AND DATE(tgl_lot)='{$tgl_shift}' 
         AND shift=$currentShift AND coupon LIKE '{$kode}%'"
    ))['total'] ?? 0;

    // Stok
    $stok = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT qty FROM stok WHERE part_code='{$info['part_code']}'"
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
    <title>LOT HEPI</title>
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

    <div class="container-fluid text-center">
        <!-- ROW 1 -->
        <div class="row mt-3">
            <div class="col text-start">
                <a href="../dashboard.php" class="btn btn-sm btn-outline-success me-2" style="width: 150px;">Main Menu</a>
            </div>
            <div class="col text-center">
                <a href="history.php" class="btn btn-sm btn-outline-primary" style="width: 150px;">Production Report</a>
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
                    <div class="card text-center mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['name'] ?></h5>
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-primary w-100" disabled><?= $data['total_in'] ?></button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-danger w-100" disabled><?= $data['total_out'] ?></button>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100" disabled>
                                        Shift <?= $currentShift ?> IN: <?= $data['shift_in'] ?>
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100" disabled>
                                        Shift <?= $currentShift ?> OUT: <?= $data['shift_out'] ?>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" class="btn btn-sm btn-success w-100" disabled>Live Stock: <?= $data['stok'] ?></button>
                            </div>
                            <div class="list-group my-3">
                                <a class="list-group-item list-group-item-action" aria-current="true">
                                    <div class="d-flex w-100 justify-content-between">
                                        <table class="table table-sm m-0 text-center" style="font-size: 12px;">
                                            <tr>
                                                <th>Coupon</th>
                                                <th>Date</th>
                                                <th>Shift</th>
                                                <th>Qty</th>
                                                <th>Status</th>
                                            </tr>
                                            <?php foreach ($data['lot'] as $lot): ?>
                                                <tr>
                                                    <td><?= $lot['coupon'] ?></td>
                                                    <td>
                                                        <?php
                                                        $tgl_lot = strtotime($lot['tgl_lot']);
                                                        if ($lot['tgl_lot'] !== '0000-00-00 00:00:00') {
                                                            echo date('Y-m-d H:i', $tgl_lot);
                                                        } else {
                                                            echo '0000-00-00 00:00';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $lot['shift'] ?></td>
                                                    <td><?= $lot['qty'] ?></td>
                                                    <td><?= $lot['status'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Javascript -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>