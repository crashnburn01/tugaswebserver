<?php
include 'header.php';

$query = "SELECT * FROM doctors";
$result = $conn->query($query);

?>
        <!-- Main Content -->
        <div style="width: 80%; padding: 10px;">
            <h2>Data Dokter</h2>
            <div style="text-align: right; margin-bottom: 10px;">
                <a href="add_doctors.php" style="color: blue;">Tambah Dokter</a>
            </div>
            <table border="1" width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Spesialisasi</th>
                    <th>E-mail</th>
                    <th>Alamat</th>
                    <th>No. Telp</th>
                    <th>Aksi</th>
                </tr>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['specialization'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td><?= $row['phone_number'] ?></td>
                    <td>
                        <a href="view_doctor.php?id=<?= $row['id'] ?>">View</a> | 
                        <a href="edit_doctor.php?id=<?= $row['id'] ?>">Edit</a> | 
                        <a href="delete_doctor.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                    </td>
                </tr>
                <?php $no++; endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
