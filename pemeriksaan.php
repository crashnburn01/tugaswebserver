<?php
include 'header.php';

// Ambil data pemeriksaan dari database
$query = "
    SELECT examinations.*, patients.name AS patient_name, doctors.name AS doctor_name
    FROM examinations
    JOIN patients ON examinations.patient_id = patients.id
    JOIN doctors ON examinations.doctor_id = doctors.id
    WHERE examinations.status IN ('Dijadwalkan', 'Dibatalkan')
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pemeriksaan</title>
</head>
<body>
        <!-- Main Content -->
        <div style="width: 80%; padding: 10px;">
            <h2>Data Pemeriksaan</h2>
            <div style="text-align: right; margin-bottom: 10px;">
                <a href="add_pemeriksaan.php" style="color: blue;">Tambah Pemeriksaan</a>
            </div>
            <table border="1" width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Pasien</th>
                    <th>J. Pemeriksaan</th>
                    <th>Nama Dokter</th>
                    <th>Ruangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
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
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="view_pemeriksaan.php?id=<?= $row['id'] ?>">View</a> | 
                        <a href="edit_pemeriksaan.php?id=<?= $row['id'] ?>">Edit</a> | 
                        <a href="delete_pemeriksaan.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                    </td>
                </tr>
                <?php $no++; endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
