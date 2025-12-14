<?php
session_start();
include "db.php";

// Allow only counselor/admin
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM counseling_records WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list_discipline_counseling.php?msg=deleted");
    exit();
} else {
    echo "Failed to delete record.";
}
?>
