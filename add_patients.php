<?php
include 'header.php';

// Ambil daftar dokter dari database
$query = "SELECT id, name FROM doctors";
$result = $conn->query($query);

// Proses tambah data pasien
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

    // Cek duplikasi data (pasien dengan nama dan alamat yang sama)
    $dupQuery = "SELECT COUNT(*) FROM patients WHERE name = ? AND address = ?";
    $dupStmt = $conn->prepare($dupQuery);
    $dupStmt->bind_param("ss", $name, $address);
    $dupStmt->execute();
    $dupStmt->bind_result($dupCount);
    $dupStmt->fetch();
    $dupStmt->close();

    if ($dupCount > 0) {
        $errors[] = "Pasien dengan nama dan alamat yang sama sudah terdaftar.";
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($errors)) {
        $query = "INSERT INTO patients (name, age, address, j_kelamin, doctor_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sissi", $name, $age, $address, $j_kelamin, $doctor_id);

        if ($stmt->execute()) {
            echo "Data pasien berhasil ditambahkan.";
            header("Location: patients.php");
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat menambahkan data pasien.";
        }
    }
}
?>

<div style="width: 80%; padding: 10px;">
<h2>Tambah Data Pasien</h2>

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
            <td><input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="age">Umur:</label></td>
            <td><input type="text" id="age" name="age" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="address">Alamat:</label></td>
            <td><input type="text" id="address" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label>Jenis Kelamin:</label></td>
            <td>
                <select name="j_kelamin" required>
                    <option value="" <?= !isset($_POST['j_kelamin']) ? 'selected' : '' ?>>--Pilih--</option>
                    <option value="Laki-laki" <?= ($_POST['j_kelamin'] ?? '') === "Laki-laki" ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= ($_POST['j_kelamin'] ?? '') === "Perempuan" ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Dokter:</label></td>
            <td>
                <select name="doctor_id" required>
                    <option value="">Pilih Dokter</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= ($_POST['doctor_id'] ?? '') == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <button type="submit">Simpan</button>
            </td>
        </tr>
    </table>
</form>
<a href="patients.php">Kembali</a>
</div>
</div>
</body>
</html>
