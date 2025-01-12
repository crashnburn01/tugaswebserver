<?php
include 'header.php';

// Ambil ID pasien dari URL
$id = $_GET['id'];

// Ambil data pasien dari database
$query = "SELECT * FROM patients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "Pasien tidak ditemukan.";
    exit;
}

// Ambil daftar dokter untuk dropdown
$doctorsQuery = "SELECT id, name FROM doctors";
$doctorsResult = $conn->query($doctorsQuery);

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $address = trim($_POST['address']);
    $j_kelamin = $_POST['j_kelamin'];
    $doctor_id = $_POST['doctor_id'];

    // Array untuk menampung error
    $errors = [];

    // Validasi input
    if (empty($name)) {
        $errors[] = "Nama wajib diisi.";
    }
    if (!ctype_digit($age) || (int)$age <= 0) {
        $errors[] = "Umur harus berupa angka positif.";
    }
    if (empty($address)) {
        $errors[] = "Alamat wajib diisi.";
    }
    if (empty($j_kelamin)) {
        $errors[] = "Jenis kelamin wajib dipilih.";
    }
    if (empty($doctor_id)) {
        $errors[] = "Dokter wajib dipilih.";
    }

    // Cek duplikasi data (pasien dengan nama dan alamat yang sama, kecuali pasien yang sedang diedit)
    $dupQuery = "SELECT COUNT(*) FROM patients WHERE name = ? AND address = ? AND id != ?";
    $dupStmt = $conn->prepare($dupQuery);
    $dupStmt->bind_param("ssi", $name, $address, $id);
    $dupStmt->execute();
    $dupStmt->bind_result($dupCount);
    $dupStmt->fetch();
    $dupStmt->close();

    if ($dupCount > 0) {
        $errors[] = "Pasien dengan nama dan alamat yang sama sudah terdaftar.";
    }

    // Jika tidak ada error, perbarui data di database
    if (empty($errors)) {
        $updateQuery = "UPDATE patients SET name = ?, age = ?, address = ?, j_kelamin = ?, doctor_id = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sissii", $name, $age, $address, $j_kelamin, $doctor_id, $id);

        if ($updateStmt->execute()) {
            echo "Data berhasil diperbarui.";
            header("Location: patients.php");
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat memperbarui data.";
        }
    }
}
?>

<div style="width: 80%; padding: 10px;">
<h2>Edit Data Pasien</h2>

<?php
// Tampilkan error jika ada
if (!empty($errors)) {
    echo "<div style='color: red;'><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul></div>";
}
?>

<form action="" method="POST">
    <table cellpadding="5" cellspacing="0" style="width: 30%; border-collapse: collapse;">
        <tr>
            <td><label for="name">Nama:</label></td>
            <td><input type="text" id="name" name="name" value="<?= htmlspecialchars($patient['name']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="age">Umur:</label></td>
            <td><input type="number" id="age" name="age" value="<?= htmlspecialchars($patient['age']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="address">Alamat:</label></td>
            <td><input type="text" id="address" name="address" value="<?= htmlspecialchars($patient['address']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="j_kelamin">Jenis Kelamin:</label></td>
            <td>
                <select id="j_kelamin" name="j_kelamin" style="width: 100%;" required>
                    <option value="Laki-laki" <?= $patient['j_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $patient['j_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="doctor_id">Dokter:</label></td>
            <td>
                <select id="doctor_id" name="doctor_id" style="width: 100%;" required>
                    <?php while ($doctor = $doctorsResult->fetch_assoc()): ?>
                        <option value="<?= $doctor['id'] ?>" <?= $patient['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($doctor['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <button type="submit">Update</button>
            </td>
        </tr>
    </table>
</form>
<a href="patients.php">Batal</a>
</div>
</body>
</html>
