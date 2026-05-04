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

function processLot($conn, $coupon, $part_code, $tgl_lot, $shift, $qty, $status, $isUpdate)
{
    if ($status === 'IN') {
        if ($isUpdate) {
            $lotQuery = "UPDATE piping_lot SET tgl_lot = '$tgl_lot', shift = $shift, qty = $qty, status = 'Used' WHERE coupon = '$coupon'";
        } else {
            $lotQuery = "INSERT INTO piping_lot (coupon, tgl_lot, shift, qty, status) VALUES ('$coupon', '$tgl_lot', $shift, $qty, 'Used')";
        }
        $stokQuery = "UPDATE piping_stok SET qty = qty + $qty WHERE part_code = '$part_code'";
    } else {
        $lotQuery = "UPDATE piping_lot SET tgl_lot = '0000-00-00 00:00:00', qty = 0, shift = 0, status = 'Available' WHERE coupon = '$coupon'";
        $stokQuery = "UPDATE piping_stok SET qty = qty - $qty WHERE part_code = '$part_code'";
    }

    // Ambil nama_part
    $sqlNamaPart = "SELECT nama_part FROM piping_stok WHERE part_code = '$part_code'";
    $resultNamaPart = mysqli_query($conn, $sqlNamaPart);

    if ($resultNamaPart && mysqli_num_rows($resultNamaPart) > 0) {
        $rowNamaPart = mysqli_fetch_assoc($resultNamaPart);
        $nama_part = $rowNamaPart['nama_part'];
    } else {
        $nama_part = "Nama Part tidak ditemukan"; // atau kasih default/null tergantung kebutuhan
    }

    $historyQuery = "INSERT INTO piping_history_lot (coupon, tgl_lot, shift, qty, status) VALUES ('$coupon', '$tgl_lot', $shift, $qty, '$status')";

    writeLog("Executed lotQuery: $lotQuery");
    writeLog("Executed stokQuery: $stokQuery");
    writeLog("Executed historyQuery: $historyQuery");

    $res1 = mysqli_query($conn, $lotQuery);
    $res2 = mysqli_query($conn, $stokQuery);
    $res3 = mysqli_query($conn, $historyQuery);

    if ($res1 && $res2 && $res3) {
        header('location: process_lot.php?coupon=' . $coupon);
    } else {
        writeLog("ERROR: Query execution failed for coupon $coupon");
        echo "<script>alert('Something went wrong!')</script>";
    }
}

list($currentShift, $tgl_lot) = getShiftAndTglLot();
$shiftLabel = "Shift $currentShift — Tanggal Lot: $tgl_lot";

// ===== MAIN CODE =====

$coupon = $_GET['coupon'] ?? '';
$coupon = strtoupper(trim($coupon));

$partModelMap = [
    'SUC-IVT' => ['AU-X6B', 'AU-X8B', 'AU-X10'],
    'SUC-5K2' => ['AU-A5B', 'AU-A7B'],
    'SUC-9K2' => ['AU-A9B'],
    'SUC-9CY' => ['AU-A9C'],
    'SUC-13K' => ['AU-A13'],
    'DIS-5K2' => ['AU-A5B'],
    'CAP-5K2' => ['AU-A5B'],
    'DIS-7K1' => ['AU-A7B'],
    'CAP-7K1' => ['AU-A7B'],
    'DIS-9K2' => ['AU-A9B'],
    'CAP-9K2' => ['AU-A9B'],
    'DIS-9CY' => ['AU-A9C'],
    'CAP-9CY' => ['AU-A9C'],
    'DIS-68K' => ['AU-X6B', 'AU-X8B'],
    'CAP-68K' => ['AU-X6B', 'AU-X8B'],
    'DIS-10K' => ['AU-X10'],
    'CAP-10K' => ['AU-X10'],
    'DIS-13K' => ['AU-X13'],
    'CAP-13K' => ['AU-X13'],
    'ALL-IDU' => ['All'],
];

$validPartCodes = array_keys($partModelMap);

// 1️⃣ Validasi format umum (3 bagian dipisah - dan 2 digit terakhir)
if (!preg_match('/^[A-Z]+-[A-Z0-9]+-(0[1-9]|[1-9][0-9])$/', $coupon)) {
    echo "<script>alert('Format coupon tidak valid!'); window.location='index.php';</script>";
    exit();
}

$parts = explode('-', $coupon);

// Pastikan ada minimal 3 bagian
if (count($parts) < 3) {
    echo "<script>alert('Format coupon salah!'); window.location='index.php';</script>";
    exit();
}

// Ambil part code (gabungan 2 bagian pertama)
$part_code = $parts[0] . '-' . $parts[1];

// 2️⃣ Validasi part_code harus ada di mapping
if (!in_array($part_code, $validPartCodes)) {
    echo "<script>alert('Part code tidak terdaftar!'); window.location='index.php';</script>";
    exit();
}

// ✅ Lolos validasi
echo "Coupon valid & part code terdaftar!";

// === Tentukan model code berdasarkan part_code ===
$modelCodes = $partModelMap[$part_code] ?? ['Check'];

// Cek History LOT
$checkHistory = mysqli_query($conn, "SELECT SUM(qty) AS total FROM piping_history_lot WHERE status='OUT' AND DATE(tgl_lot) = CURDATE() AND shift = $currentShift AND coupon LIKE '$part_code%'");
$historyData = mysqli_fetch_assoc($checkHistory);
$totalUsed = $historyData['total'] ?? 0;

if (!$part_code) {
    echo "<script>alert('Coupon not registered!')</script>";
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

$role = $_SESSION['role'];
list($shift, $tgl_lot_formatted) = getShiftAndTglLot();

$specialQtyMap = [
    'DIS-68K'  => 100,
    'DIS-10K'  => 100,
    'DIS-13K'  => 100,
    'SUC-IVT'  => 75,
    'SUC-13K'  => 75,
    'ALL-IDU'  => 60
];
$qty = $specialQtyMap[$part_code] ?? 150;

if ($_SESSION['role'] === 'Leader-HE') {
    $checkCoupon = mysqli_query($conn, "SELECT status FROM piping_lot WHERE coupon = '$coupon'");
    $data = mysqli_fetch_assoc($checkCoupon);

    if ($data && isset($data['status'])) {
        if ($data['status'] === 'Used') {
            echo "<script>alert('Coupon belum di release Main Line!')</script>";
            echo "<script>window.location.href='index.php'</script>";
            exit();
        }
        processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, 'IN', true);
    } else {
        processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, 'IN', false);
    }
} else if ($_SESSION['role'] === 'Leader-ML') {
    $checkCoupon = mysqli_query($conn, "SELECT status FROM piping_lot WHERE coupon = '$coupon'");
    $data = mysqli_fetch_assoc($checkCoupon);

    if ($data && isset($data['status'])) {
        if ($data['status'] === 'Available') {
            echo "<script>alert('Coupon belum di Scan dari pihak HE!')</script>";
            echo "<script>window.location.href='index.php'</script>";
        } else {
            $status = 'OUT';
            processLot($conn, $coupon, $part_code, $tgl_lot_formatted, $shift, $qty, $status, true);
        }
    } else {
        echo "<script>alert('Coupon belum di Scan dari pihak HE!')</script>";
        echo "<script>window.location.href='index.php'</script>";
        exit();
    }
} else {
    echo "<script>alert('Access denied!')</script>";
    echo "<script>window.location.href='index.php'</script>";
    exit();
}
?>
<!-- @raffizh24 -->