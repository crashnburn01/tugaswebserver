<?php
include 'header.php';

// Ambil data laporan dari database
$query = "SELECT * FROM laporan";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Laporan</title>
</head>
<body>
    <div style="width: 80%; padding: 10px;">
        <h2>Data Laporan</h2>
        <table border="1" width="100%" cellpadding="5" cellspacing="0">
            <tr>
                <th>No</th>
                <th>Tanggal Pemeriksaan</th>
                <th>Nama Pasien</th>
                <th>Jenis Pemeriksaan</th>
                <th>Nama Dokter</th>
                <th>Ruangan</th>
                <th>Dibuat Pada</th>
            </tr>
            <?php 
            $no = 1;
            while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $row['examination_date'] ?></td>
                <td><?= $row['patient_name'] ?></td>
                <td><?= $row['examination_type'] ?></td>
                <td><?= $row['doctor_name'] ?></td>
                <td><?= $row['room'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php $no++; endwhile; ?>
        </table>
    </div>
</body>
</html>
