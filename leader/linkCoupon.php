<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['role'])) {
    header('location: ../index.php');
    exit();
}

function writeLog($message)
{
    $logPath = __DIR__ . '/lot_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logPath, $entry, FILE_APPEND);
}

function getPartCode($coupon)
{
    $prefix = substr($coupon, 0, 3);
    $map = [
        'CSR' => 'DCON-B070JBEZ',
        'CDR' => 'DCON-B105JBEZ',
        'SRI' => 'DCON-B074JBEZ',
        'DRI' => 'DCON-B075JBEZ',
        'EVA' => 'PEVA-B243JBEZ'
    ];
    return $map[$prefix] ?? null;
}

function getShiftAndTglLot()
{
    date_default_timezone_set('Asia/Jakarta');
    $now = time();
    $hour = date('H', $now);
    $minute = date('i', $now);
    $total_minutes = $hour * 60 + $minute;

    if ($total_minutes >= 475 && $total_minutes < 1020) {
        $shift = 1;
        $tgl_lot = $now;
    } elseif ($total_minutes >= 1020 && $total_minutes < 1440) {
        $shift = 2;
        $tgl_lot = $now;
    } elseif ($total_minutes >= 0 && $total_minutes < 30) {
        $shift = 2;
        $tgl_lot = strtotime('-1 day', $now);
    } else {
        $shift = 3;
        $tgl_lot = strtotime('-1 day', $now);
    }

    return [$shift, date('Y-m-d H:i:s', $tgl_lot)];
}

list(
    $currentShift,
    $tgl_lot
) = getShiftAndTglLot();
$shiftLabel = "Shift $currentShift — Tanggal Lot: $tgl_lot";

function processLot($conn, $coupon, $part_code, $tgl_lot, $shift, $qty, $status, $isUpdate = true)
{
    if ($status === 'IN') {
        $lotQuery = $isUpdate
            ? "UPDATE lot SET tgl_lot = '$tgl_lot', shift = $shift, qty = $qty, status = 'Used' WHERE coupon = '$coupon'"
            : "INSERT INTO lot (coupon, tgl_lot, shift, qty, status) VALUES ('$coupon', '$tgl_lot', $shift, $qty, 'Used')";
        $stokQuery = "UPDATE stok SET qty = qty + $qty WHERE part_code = '$part_code'";
    } else {
        $lotQuery = "UPDATE lot SET tgl_lot = '0000-00-00 00:00:00', qty = 0, shift = 0, status = 'Available' WHERE coupon = '$coupon'";
        $stokQuery = "UPDATE stok SET qty = qty - $qty WHERE part_code = '$part_code'";
    }

    $historyQuery = "INSERT INTO history_lot (coupon, tgl_lot, shift, qty, status) VALUES ('$coupon', '$tgl_lot', $shift, $qty, '$status')";

    writeLog("Executed lotQuery: $lotQuery");
    writeLog("Executed stokQuery: $stokQuery");
    writeLog("Executed historyQuery: $historyQuery");

    $res1 = mysqli_query($conn, $lotQuery);
    $res2 = mysqli_query($conn, $stokQuery);
    $res3 = mysqli_query($conn, $historyQuery);

    if ($res1 && $res2 && $res3) {
        if ($status === 'IN') {
            header('location: process_lot_in.php?coupon=' . $coupon);
        } else {
            header('location: process_lot_out.php?coupon=' . $coupon);
        }
    } else {
        writeLog("ERROR: Query execution failed for coupon $coupon");
        echo "<script>alert('Something went wrong!')</script>";
    }
}

// MAIN LOGIC
$coupon = $_GET['coupon'] ?? '';

if (!preg_match('/^(CSR|CDR|SRI|DRI|EVA)-(0[1-9]|[1-9][0-9])$/', $coupon)) {
    echo "<script>
        alert('Format coupon tidak valid!')
        window.location.href = 'index.php'
    </script>";
    exit();
}

$part_code = getPartCode($coupon);

if (!$part_code) {
    echo "<script>
    alert('Coupon not registered!')
</script>";
    echo "<script>
    window.location.href = 'index.php'
</script>";
    exit();
}

$role = $_SESSION['role'];
list($shift, $tgl_lot_formatted) = getShiftAndTglLot();

$isSpecialQty = in_array($part_code, ['PEVA-B243JBEZ', 'DCON-B105JBEZ', 'DCON-B075JBEZ']);
$qty = $isSpecialQty ? 100 : 200;

if ($role === 'Leader-HE') {
    $checkCoupon = mysqli_query($conn, "SELECT status FROM lot WHERE coupon = '$coupon'");
    $data = mysqli_fetch_assoc($checkCoupon);

    if ($data) {
        if ($data['status'] === 'Used') {
            echo "<script>
    alert('Coupon already used!')
</script>";
            echo "<script>
    window.location.href = 'index.php'
</script>";
            exit();
        }
        processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, 'IN', true);
    } else {
        processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, 'IN', false);
    }
} elseif ($role === 'Leader-ML') {
    $checkCoupon = mysqli_query($conn, "SELECT status FROM lot WHERE coupon = '$coupon'");
    $data = mysqli_fetch_assoc($checkCoupon);
    if ($data) {
        if ($data['status'] === 'Available') {
            echo "<script>
    alert('Coupon cannot use!')
</script>";
            echo "<script>
    window.location.href = 'index.php'
</script>";
            exit();
        }
        processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, 'OUT', true);
    } else {
        echo "<script>
    alert('Coupon not registered!')
</script>";
        echo "<script>
    window.location.href = 'index.php'
</script>";
    }
} else {
    echo "<script>
    alert('Access denied!')
</script>";
    echo "<script>
    window.location.href = 'index.php'
</script>";
}
?>
<!-- @raffizh24 -->