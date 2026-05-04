<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['role'])) {
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

$fullCoupon = $_GET['coupon'] ?? '';

if (empty($fullCoupon)) {
    header('location: ../index.php');
    exit;
}

$parts = explode('-', $fullCoupon);
$part_code = $parts[0];
$coupon = $fullCoupon;

$lot = mysqli_fetch_all(
    mysqli_query($conn, "SELECT * FROM lot WHERE coupon LIKE '{$part_code}%' AND status='Used'"),
    MYSQLI_ASSOC
);

usort($lot, function ($a, $b) {
    return strtotime($b['tgl_lot']) <=> strtotime($a['tgl_lot']);
});

$in = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(qty) AS total FROM history_lot 
     WHERE status='IN' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) 
     AND coupon LIKE '{$part_code}%'"
))['total'] ?? 0;

$out = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(qty) AS total FROM history_lot 
     WHERE status='OUT' AND MONTH(tgl_lot)=MONTH(CURRENT_DATE()) 
     AND coupon LIKE '{$part_code}%'"
))['total'] ?? 0;

$inShift = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(qty) AS total FROM history_lot 
     WHERE status='IN' AND DATE(tgl_lot)=CURRENT_DATE() 
     AND shift=$currentShift AND coupon LIKE '{$part_code}%'"
))['total'] ?? 0;

$outShift = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT SUM(qty) AS total FROM history_lot 
     WHERE status='OUT' AND DATE(tgl_lot)=CURRENT_DATE() 
     AND shift=$currentShift AND coupon LIKE '{$part_code}%'"
))['total'] ?? 0;

$stok = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT qty FROM stok WHERE part_code='{$part_code}'"
))['qty'] ?? 0;

$data = [
    'name'       => $part_code,
    'total_in'   => $in,
    'total_out'  => $out,
    'shift_in'   => $inShift,
    'shift_out'  => $outShift,
    'stok'       => $stok,
    'lot'        => $lot,
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="refresh" content="5;url=index.php">
    <script src="../js/color-modes.js"></script>
    <title>HEPI Apps</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/sign-in.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <?php include '../library/themes.php'; ?>

    <main class="form-signin w-100 m-auto">
        <h1 class="h3 mb-3 text-center">Successfully</h1>
        <div class="card text-center mb-3" style="height: 500px;">
            <div class="card-body">
                <h5 class="card-title"><?= $data['name'] ?></h5>

                <!-- Monthly -->
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary w-100" disabled>
                            <?= $data['total_in'] ?>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-danger w-100" disabled>
                            <?= $data['total_out'] ?>
                        </button>
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
                    <button type="button" class="btn btn-sm btn-success w-100" disabled>
                        Live Stock: <?= $data['stok'] ?>
                    </button>
                </div>

                <!-- Coupon Table -->
                <div class="list-group my-3">
                    <a class="list-group-item list-group-item-action" aria-current="true">
                        <div class="d-flex w-100 justify-content-between">
                            <div style="max-height: 295px; overflow-y: auto; width: 100%;">
                                <table class="table table-sm m-0 text-center" style="font-size: 10px;">
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
                                            <?php $highlight = ($lot['coupon'] === $coupon) ? 'table-danger fw-bold' : ''; ?>
                                            <tr class="<?= $highlight ?>">
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
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>