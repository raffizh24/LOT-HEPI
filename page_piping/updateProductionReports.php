<?php
// Insert/Update prod_report otomatis untuk shift hari ini dan sebelumnya
$sqlPiping = "
INSERT INTO piping_prod_report (
    tgl_lot, shift,

    -- TUBE ASSY
    ALL_IDU_IN, ALL_IDU_OUT,

    -- CAP
    CAP_5K2_IN, CAP_5K2_OUT,
    CAP_7K1_IN, CAP_7K1_OUT,
    CAP_9K2_IN, CAP_9K2_OUT,
    CAP_68K_IN, CAP_68K_OUT,
    CAP_10K_IN, CAP_10K_OUT,
    CAP_13K_IN, CAP_13K_OUT,
    CAP_9CY_IN, CAP_9CY_OUT,

    -- SUC
    SUC_5K2_IN, SUC_5K2_OUT,
    SUC_7K1_IN, SUC_7K1_OUT,
    SUC_9K2_IN, SUC_9K2_OUT,
    SUC_68K_IN, SUC_68K_OUT,
    SUC_10K_IN, SUC_10K_OUT,
    SUC_13K_IN, SUC_13K_OUT,
    SUC_9CY_IN, SUC_9CY_OUT,

    -- DIS
    DIS_5K2_IN, DIS_5K2_OUT,
    DIS_7K1_IN, DIS_7K1_OUT,
    DIS_9K2_IN, DIS_9K2_OUT,
    DIS_68K_IN, DIS_68K_OUT,
    DIS_10K_IN, DIS_10K_OUT,
    DIS_13K_IN, DIS_13K_OUT,
    DIS_9CY_IN, DIS_9CY_OUT

)
SELECT
    DATE(tgl_lot), shift,

    -- TUBE ASSY
    SUM(IF(LEFT(coupon, 7) = 'ALL-IDU' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'ALL-IDU' AND status = 'OUT', qty, 0)),

    -- CAP
    SUM(IF(LEFT(coupon, 7) = 'CAP-5K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'CAP-5K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'CAP-7K1' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'CAP-7K1' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'CAP-9K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'CAP-9K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'CAP-68K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'CAP-68K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'CAP-10K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'CAP-10K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'CAP-13K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'CAP-13K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'CAP-9CY' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'CAP-9CY' AND status = 'OUT', qty, 0)),

    -- SUC
    SUM(IF(LEFT(coupon, 7) = 'SUC-5K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'SUC-5K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'SUC-7K1' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'SUC-7K1' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'SUC-9K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'SUC-9K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'SUC-68K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'SUC-68K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'SUC-10K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'SUC-10K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'SUC-13K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'SUC-13K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'SUC-9CY' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'SUC-9CY' AND status = 'OUT', qty, 0)),

    -- DIS
    SUM(IF(LEFT(coupon, 7) = 'DIS-5K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'DIS-5K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'DIS-7K1' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'DIS-7K1' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'DIS-9K2' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'DIS-9K2' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 7) = 'DIS-68K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 7) = 'DIS-68K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'DIS-10K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'DIS-10K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'DIS-13K' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'DIS-13K' AND status = 'OUT', qty, 0)),

    SUM(IF(LEFT(coupon, 8) = 'DIS-9CY' AND status = 'IN', qty, 0)),
    SUM(IF(LEFT(coupon, 8) = 'DIS-9CY' AND status = 'OUT', qty, 0))

FROM piping_history_lot
WHERE DATE(tgl_lot) BETWEEN CURDATE() - INTERVAL 3 DAY AND CURDATE() + INTERVAL 1 DAY
GROUP BY DATE(tgl_lot), shift
ON DUPLICATE KEY UPDATE
    ALL_IDU_IN = VALUES(ALL_IDU_IN),
    ALL_IDU_OUT = VALUES(ALL_IDU_OUT),
    
    CAP_5K2_IN = VALUES(CAP_5K2_IN),
    CAP_5K2_OUT = VALUES(CAP_5K2_OUT),
    CAP_7K1_IN = VALUES(CAP_7K1_IN),
    CAP_7K1_OUT = VALUES(CAP_7K1_OUT),
    CAP_9K2_IN = VALUES(CAP_9K2_IN),
    CAP_9K2_OUT = VALUES(CAP_9K2_OUT),
    CAP_68K_IN = VALUES(CAP_68K_IN),
    CAP_68K_OUT = VALUES(CAP_68K_OUT),
    CAP_10K_IN = VALUES(CAP_10K_IN),
    CAP_10K_OUT = VALUES(CAP_10K_OUT),
    CAP_13K_IN = VALUES(CAP_13K_IN),
    CAP_13K_OUT = VALUES(CAP_13K_OUT),
    CAP_9CY_IN = VALUES(CAP_9CY_IN),
    CAP_9CY_OUT = VALUES(CAP_9CY_OUT),

    SUC_5K2_IN = VALUES(SUC_5K2_IN),
    SUC_5K2_OUT = VALUES(SUC_5K2_OUT),
    SUC_7K1_IN = VALUES(SUC_7K1_IN),
    SUC_7K1_OUT = VALUES(SUC_7K1_OUT),
    SUC_9K2_IN = VALUES(SUC_9K2_IN),
    SUC_9K2_OUT = VALUES(SUC_9K2_OUT),
    SUC_68K_IN = VALUES(SUC_68K_IN),
    SUC_68K_OUT = VALUES(SUC_68K_OUT),
    SUC_10K_IN = VALUES(SUC_10K_IN),
    SUC_10K_OUT = VALUES(SUC_10K_OUT),
    SUC_13K_IN = VALUES(SUC_13K_IN),
    SUC_13K_OUT = VALUES(SUC_13K_OUT),
    SUC_9CY_IN = VALUES(SUC_9CY_IN),
    SUC_9CY_OUT = VALUES(SUC_9CY_OUT),

    DIS_5K2_IN = VALUES(DIS_5K2_IN),
    DIS_5K2_OUT = VALUES(DIS_5K2_OUT),
    DIS_7K1_IN = VALUES(DIS_7K1_IN),
    DIS_7K1_OUT = VALUES(DIS_7K1_OUT),
    DIS_9K2_IN = VALUES(DIS_9K2_IN),
    DIS_9K2_OUT = VALUES(DIS_9K2_OUT),
    DIS_68K_IN = VALUES(DIS_68K_IN),
    DIS_68K_OUT = VALUES(DIS_68K_OUT),
    DIS_10K_IN = VALUES(DIS_10K_IN),
    DIS_10K_OUT = VALUES(DIS_10K_OUT),
    DIS_13K_IN = VALUES(DIS_13K_IN),
    DIS_13K_OUT = VALUES(DIS_13K_OUT),
    DIS_9CY_IN = VALUES(DIS_9CY_IN),
    DIS_9CY_OUT = VALUES(DIS_9CY_OUT);
";
mysqli_query($conn, $sqlPiping);

$sql = "
INSERT INTO prod_report (
    tgl_lot, shift,
    EVA_IN, EVA_OUT,
    CSR_IN, CSR_OUT,
    CDR_IN, CDR_OUT,
    SRI_IN, SRI_OUT,
    DRI_IN, DRI_OUT
)
SELECT
    DATE(tgl_lot) AS tgl_lot,  -- ambil tanggal saja tanpa waktu
    shift,
    
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'EVA' AND UPPER(TRIM(status)) = 'IN' THEN qty ELSE 0 END),
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'EVA' AND UPPER(TRIM(status)) = 'OUT' THEN qty ELSE 0 END),
    
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'CSR' AND UPPER(TRIM(status)) = 'IN' THEN qty ELSE 0 END),
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'CSR' AND UPPER(TRIM(status)) = 'OUT' THEN qty ELSE 0 END),

    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'CDR' AND UPPER(TRIM(status)) = 'IN' THEN qty ELSE 0 END),
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'CDR' AND UPPER(TRIM(status)) = 'OUT' THEN qty ELSE 0 END),

    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'SRI' AND UPPER(TRIM(status)) = 'IN' THEN qty ELSE 0 END),
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'SRI' AND UPPER(TRIM(status)) = 'OUT' THEN qty ELSE 0 END),

    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'DRI' AND UPPER(TRIM(status)) = 'IN' THEN qty ELSE 0 END),
    SUM(CASE WHEN LEFT(TRIM(coupon), 3) = 'DRI' AND UPPER(TRIM(status)) = 'OUT' THEN qty ELSE 0 END)
FROM history_lot
WHERE DATE(tgl_lot) BETWEEN CURDATE() - INTERVAL 3 DAY AND CURDATE() + INTERVAL 1 DAY
GROUP BY DATE(tgl_lot), shift
ON DUPLICATE KEY UPDATE
    EVA_IN = VALUES(EVA_IN),
    EVA_OUT = VALUES(EVA_OUT),
    CSR_IN = VALUES(CSR_IN),
    CSR_OUT = VALUES(CSR_OUT),
    CDR_IN = VALUES(CDR_IN),
    CDR_OUT = VALUES(CDR_OUT),
    SRI_IN = VALUES(SRI_IN),
    SRI_OUT = VALUES(SRI_OUT),
    DRI_IN = VALUES(DRI_IN),
    DRI_OUT = VALUES(DRI_OUT);
";
mysqli_query($conn, $sql);
?>
<!-- @raffizh24 -->