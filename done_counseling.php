<?php
session_start();
include "db.php";

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

$conn->query("
    UPDATE counseling_records 
    SET status = 'finished' 
    WHERE id = $id
");

header("Location: list_discipline_counseling.php");
