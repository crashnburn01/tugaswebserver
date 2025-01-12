<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);

    // Validasi input
    $errors = [];
    if (empty($name)) {
        $errors[] = "Nama wajib diisi.";
    }
    if (empty($specialization)) {
        $errors[] = "Spesialisasi wajib diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if (empty($address)) {
        $errors[] = "Alamat wajib diisi.";
    }
    if (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
        $errors[] = "Nomor telepon harus berupa angka dengan panjang 10-15 karakter.";
    }

    // Periksa apakah ada data duplikat
    $stmt = $conn->prepare("SELECT COUNT(*) FROM doctors WHERE name = ? OR email = ?");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $errors[] = "Dokter dengan nama atau email yang sama sudah ada.";
    }

    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, email, address, phone_number) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $specialization, $email, $address, $phone_number);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data. Silakan coba lagi.";
        }
        $stmt->close();
    }
}
?>

<div style="width: 80%; padding: 10px;">
<h2>Tambah Data Dokter</h2>

<?php
// Tampilkan pesan error jika ada
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
            <td><input type="text" id="name" name="name" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="specialization">Spesialisasi:</label></td>
            <td><input type="text" id="specialization" name="specialization" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="email">Email:</label></td>
            <td><input type="email" id="email" name="email" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="address">Alamat:</label></td>
            <td><input type="text" id="address" name="address" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="phone_number">Telepon:</label></td>
            <td><input type="text" id="phone_number" name="phone_number" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <button type="submit">Simpan</button>
            </td>
        </tr>
    </table>
</form>
<a href="dashboard.php">Kembali</a>
</div>
</div>
</body>
</html>
