<?php
include 'header.php';

// Ambil daftar pasien dan dokter untuk dropdown
$patients = $conn->query("SELECT id, name FROM patients");
$doctors = $conn->query("SELECT id, name FROM doctors");

// Proses input data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examination_date = trim($_POST['examination_date']);
    $patient_id = $_POST['patient_id'];
    $examination_type = trim($_POST['examination_type']);
    $doctor_id = $_POST['doctor_id'];
    $room = trim($_POST['room']);
    $status = $_POST['status'];

    // Array untuk menyimpan error
    $errors = [];

    // Validasi input
    if (empty($examination_date)) {
        $errors[] = "Tanggal pemeriksaan wajib diisi.";
    }
    if (empty($patient_id)) {
        $errors[] = "Pasien wajib dipilih.";
    }
    if (empty($examination_type)) {
        $errors[] = "Jenis pemeriksaan wajib diisi.";
    }
    if (empty($doctor_id)) {
        $errors[] = "Dokter wajib dipilih.";
    }
    if (empty($room)) {
        $errors[] = "Ruangan wajib diisi.";
    }
    if (empty($status)) {
        $errors[] = "Status pemeriksaan wajib dipilih.";
    }

    // Cek duplikasi data (pemeriksaan dengan tanggal, pasien, dan dokter yang sama)
    $dupQuery = "SELECT COUNT(*) FROM examinations WHERE examination_date = ? AND patient_id = ? AND doctor_id = ?";
    $dupStmt = $conn->prepare($dupQuery);
    $dupStmt->bind_param("sii", $examination_date, $patient_id, $doctor_id);
    $dupStmt->execute();
    $dupStmt->bind_result($dupCount);
    $dupStmt->fetch();
    $dupStmt->close();

    if ($dupCount > 0) {
        $errors[] = "Pemeriksaan dengan tanggal, pasien, dan dokter yang sama sudah ada.";
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($errors)) {
        $query = "INSERT INTO examinations (examination_date, patient_id, examination_type, doctor_id, room, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sissss", $examination_date, $patient_id, $examination_type, $doctor_id, $room, $status);

        if ($stmt->execute()) {
            echo "Data berhasil ditambahkan.";
            header("Location: pemeriksaan.php");
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat menambahkan data.";
        }
    }
}
?>

<div style="width: 80%; padding: 10px;">
<h2>Tambah Data Pemeriksaan</h2>

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
            <td><label for="name">Tanggal Pemeriksaan:</label></td>
            <td><input type="date" id="examination_date" name="examination_date" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="patient_name">Nama Pasien:</label></td>
            <td>
                <select name="patient_id" required>
                    <option disabled selected value>Pilih Pasien</option>
                    <?php while ($patient = $patients->fetch_assoc()): ?>
                        <option value="<?= $patient['id']?>"><?= htmlspecialchars($patient['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="examination_type">Jenis Pemeriksaan:</label></td>
            <td><input type="text" id="examination_type" name="examination_type" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label for="nama_dokter">Nama Dokter:</label></td>
            <td>
                <select name="doctor_id" required>
                    <option disabled selected value>Pilih Dokter</option>
                    <?php while ($doctor = $doctors->fetch_assoc()): ?>
                        <option value="<?= $doctor['id']?>"><?= htmlspecialchars($doctor['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="ruangan">Ruangan:</label></td>
            <td><input type="text" id="room" name="room" style="width: 100%;" required></td>
        </tr>
        <tr>
            <td><label>Status Pemeriksaan:</label></td>
            <td>
                <select name="status" required>
                    <option disabled selected value>--Pilih--</option>
                    <option value="Dijadwalkan">Dijadwalkan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
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
<a href="pemeriksaan.php">Kembali</a>
</div>
</body>
</html>
