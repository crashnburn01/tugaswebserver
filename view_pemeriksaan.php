<?php
include 'header.php';

// Ambil ID pemeriksaan dari URL
$id = $_GET['id'];

// Query untuk mendapatkan data pemeriksaan berdasarkan ID
$query = "
    SELECT examinations.*, 
           patients.name AS patient_name, 
           doctors.name AS doctor_name
    FROM examinations
    JOIN patients ON examinations.patient_id = patients.id
    JOIN doctors ON examinations.doctor_id = doctors.id
    WHERE examinations.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$examination = $result->fetch_assoc();

if (!$examination) {
    echo "Data pemeriksaan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pemeriksaan</title>
</head>
<body>
    <div style="width: 80%; padding: 10px;">
        <h2>Detail Pemeriksaan</h2>
        <table cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; width: 50%;">
            <tr>
                <th>ID Pemeriksaan</th>
                <td><?= $examination['id'] ?></td>
            </tr>
            <tr>
                <th>Tanggal Pemeriksaan</th>
                <td><?= $examination['examination_date'] ?></td>
            </tr>
            <tr>
                <th>Nama Pasien</th>
                <td><?= $examination['patient_name'] ?></td>
            </tr>
            <tr>
                <th>Jenis Pemeriksaan</th>
                <td><?= $examination['examination_type'] ?></td>
            </tr>
            <tr>
                <th>Nama Dokter</th>
                <td><?= $examination['doctor_name'] ?></td>
            </tr>
            <tr>
                <th>Ruangan</th>
                <td><?= $examination['room'] ?></td>
            </tr>
            <tr>
                <th>Status Pemeriksaan</th>
                <td><?= $examination['status'] ?></td>
            </tr>
        </table>
        <br>
        <a href="pemeriksaan.php" style="color: blue;">Kembali</a>
    </div>
</body>
</html>
