<?php
include 'header.php';

// Ambil ID dokter dari URL
$id = $_GET['id'];

// Validasi jika ID tidak ditemukan
if (!$id) {
    echo "ID dokter tidak ditemukan.";
    exit;
}

// Ambil data dokter dari database
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

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);

    // Array untuk menampung error
    $errors = [];

    // Validasi input
    if (empty($name)) {
        $errors[] = "Nama wajib diisi.";
    }
    if (empty($specialization)) {
        $errors[] = "Spesialisasi wajib diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (empty($address)) {
        $errors[] = "Alamat wajib diisi.";
    }
    if (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
        $errors[] = "Nomor telepon harus berupa angka dengan panjang 10-15 karakter.";
    }

    // Cek duplikasi data kecuali pada data yang sedang diedit
    $dupQuery = "SELECT COUNT(*) FROM doctors WHERE (name = ? OR email = ?) AND id != ?";
    $dupStmt = $conn->prepare($dupQuery);
    $dupStmt->bind_param("ssi", $name, $email, $id);
    $dupStmt->execute();
    $dupStmt->bind_result($dupCount);
    $dupStmt->fetch();
    $dupStmt->close();

    if ($dupCount > 0) {
        $errors[] = "Dokter dengan nama atau email yang sama sudah ada.";
    }

    // Jika tidak ada error, update data di database
    if (empty($errors)) {
        $updateQuery = "UPDATE doctors SET name = ?, specialization = ?, email = ?, address = ?, phone_number = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssssi", $name, $specialization, $email, $address, $phone_number, $id);

        if ($updateStmt->execute()) {
            echo "Data berhasil diperbarui.";
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat memperbarui data.";
        }
    }
}
?>

<div style="width: 80%; padding: 10px;">
<h2>Edit Data Dokter</h2>

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
            <td><input type="text" id="name" name="name" value="<?= htmlspecialchars($doctor['name']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="specialization">Spesialisasi:</label></td>
            <td><input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($doctor['specialization']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="email">Email:</label></td>
            <td><input type="email" id="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="address">Alamat:</label></td>
            <td><input type="text" id="address" name="address" value="<?= htmlspecialchars($doctor['address']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="phone_number">Telepon:</label></td>
            <td><input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($doctor['phone_number']) ?>" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <button type="submit">Update</button>
            </td>
        </tr>
    </table>
</form>
<a href="dashboard.php">Batal</a>
</div>
</div>
</body>
</html>
