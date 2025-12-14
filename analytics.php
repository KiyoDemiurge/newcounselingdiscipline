<?php
session_start();
include "db.php";

header("Content-Type: application/json");

// Only admin & counselor can view analytics
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// ======================
// TOTAL DISCIPLINE CASES
// ======================
$totalDiscipline = $conn->query("SELECT COUNT(*) AS total FROM discipline")->fetch_assoc()['total'];

// ======================
// TOTAL COUNSELING SESSIONS
// ======================
$totalCounseling = $conn->query("SELECT COUNT(*) AS total FROM counseling")->fetch_assoc()['total'];

// ======================
// MONTHLY DISCIPLINE TREND
// ======================
$monthlyTrend = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, COUNT(*) AS total
    FROM discipline
    GROUP BY month
    ORDER BY month ASC
")->fetch_all(MYSQLI_ASSOC);

// ======================
// MOST COMMON VIOLATION
// ======================
$topViolation = $conn->query("
    SELECT name, COUNT(*) AS total
    FROM discipline
    GROUP BY name
    ORDER BY total DESC
    LIMIT 1
")->fetch_assoc();

// ======================
// TOP 5 OFFENDERS
// ======================
$topOffenders = $conn->query("
    SELECT student_id, COUNT(*) AS total
    FROM discipline
    GROUP BY student_id
    ORDER BY total DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// ======================
// COUNSELOR CASE LOAD
// ======================
$counselorLoad = $conn->query("
    SELECT counselor, COUNT(*) AS total
    FROM counseling
    GROUP BY counselor
")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "summary" => [
        "totalDiscipline" => $totalDiscipline,
        "totalCounseling" => $totalCounseling,
        "topViolation" => $topViolation,
    ],
    "trends" => [
        "monthlyTrend" => $monthlyTrend,
        "topOffenders" => $topOffenders,
        "counselorLoad" => $counselorLoad
    ]
]);
