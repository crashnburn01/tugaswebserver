<?php
include 'header.php';

$id = $_GET['id'];

$query = "DELETE FROM examinations WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Data berhasil dihapus.";
    header("Location: pemeriksaan.php");
    exit;
} else {
    echo "Terjadi kesalahan saat menghapus data.";
}
?>
