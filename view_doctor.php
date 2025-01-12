<?php
include 'header.php';

// Ambil ID dokter dari URL
$id = $_GET['id'];

// Query untuk mendapatkan data dokter berdasarkan ID
$query = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo "Dokter tidak ditemukan.";
    exit;
}
?>

    <!-- Main Content -->
    <div style="width: 80%; padding: 10px;">
        <h2>Detail Dokter</h2>
        <table cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; width: 50%;">
            <tr>
                <th>Nama</th>
                <td><?= $doctor['name'] ?></td>
            </tr>
            <tr>
                <th>Spesialisasi</th>
                <td><?= $doctor['specialization'] ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= $doctor['email'] ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= $doctor['address'] ?></td>
            </tr>
            <tr>
                <th>No. Telp</th>
                <td><?= $doctor['phone_number'] ?></td>
            </tr>
        </table>
        <br>
        <a href="dashboard.php">Kembali</a>

<?php
$stmt->close();
?>
        </div>
    </div>
</body>
</html>

