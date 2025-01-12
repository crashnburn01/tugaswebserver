<?php
include 'header.php';

// Ambil ID dokter dari URL
$id = $_GET['id'];

// Hapus data dokter dari database
$query = "DELETE FROM doctors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Data berhasil dihapus.";
    header("Location: dashboard.php");
    exit;
} else {
    echo "Terjadi kesalahan saat menghapus data.";
}
?>
