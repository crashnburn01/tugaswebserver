<?php
include 'header.php';

$result = $conn->query("
    SELECT patients.*, doctors.name AS doctor_name
    FROM patients
    JOIN doctors ON patients.doctor_id = doctors.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pasien</title>
</head>
<body>
        <!-- Main Content -->
        <div style="width: 80%; padding: 10px;">
            <h2>Data Pasien</h2>
            <div style="text-align: right; margin-bottom: 10px;">
                <a href="add_patients.php" style="color: blue;">Tambah Pasien</a>
            </div>
            <table border="1" width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Alamat</th>
                    <th>J. Kelamin</th>
                    <th>Dokter</th>
                    <th>Aksi</th>
                </tr>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td><?= $row['j_kelamin'] ?></td>
                    <td><?= $row['doctor_name'] ?></td>
                    <td>
                        <a href="view_patients.php?id=<?= $row['id'] ?>">View</a> | 
                        <a href="edit_patients.php?id=<?= $row['id'] ?>">Edit</a> | 
                        <a href="delete_patients.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                    </td>
                </tr>
                <?php $no++; endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
