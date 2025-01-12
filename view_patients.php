<?php
include 'header.php';

// Ambil ID pasien dari URL
$id = $_GET['id'];

// Query untuk mendapatkan data pasien berdasarkan ID
$query = "
    SELECT patients.*, doctors.name AS doctor_name 
    FROM patients 
    JOIN doctors ON patients.doctor_id = doctors.id 
    WHERE patients.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "Pasien tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pasien</title>
</head>
<body>
    <!-- Main Content -->
    <div style="width: 80%; padding: 10px;">
        <h2>Detail Pasien</h2>
        <table cellpadding="10" cellspacing="0" border="1" style="border-collapse: collapse; width: 30%;">
            <tr>
                <th>Nama</th>
                <td><?= $patient['name'] ?></td>
            </tr>
            <tr>
                <th>Umur</th>
                <td><?= $patient['age'] ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= $patient['address'] ?></td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td><?= $patient['j_kelamin'] ?></td>
            </tr>
            <tr>
                <th>Dokter</th>
                <td><?= $patient['doctor_name'] ?></td>
            </tr>
        </table>
        <br>
        <a href="patients.php">Kembali</a>
    </div>

<?php
$stmt->close();
?>
</body>
</html>
