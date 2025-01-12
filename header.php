<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Include koneksi database
include 'db.php';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <!-- Header -->
    <div style="width: 100%; height: 50px; border-bottom: 1px solid #000; text-align: right; padding: 10px;">
        <span>Hallo, <?= $_SESSION['user'] ?> |</span>
        <a href="logout.php" style="color: red; margin-left: 5px;">Logout</a>
    </div>

    <!-- Container -->
    <div style="display: flex; height: calc(100vh - 50px);">
        <!-- Sidebar -->
        <div style="width: 20%; border-right: 1px solid #000; padding: 10px;">
            <h3>Dashboard</h3>
            <ul style="list-style: none; padding: 0;">
                <li><a href="patients.php">Data Pasien</a></li>
                <li><a href="dashboard.php">Data Dokter</a></li>
                <li><a href="pemeriksaan.php">Data Pemeriksaan</a></li>
                <li><a href="laporan.php">Report</a></li>
            </ul>
        </div>
