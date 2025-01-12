<?php
include 'header.php';

// Ambil ID pemeriksaan dari URL
$id = $_GET['id'];

// Ambil data pemeriksaan dari database
$query = "
    SELECT examinations.*, 
           patients.name AS patient_name, 
           doctors.name AS doctor_name 
    FROM examinations 
    JOIN patients ON examinations.patient_id = patients.id 
    JOIN doctors ON examinations.doctor_id = doctors.id 
    WHERE examinations.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$examination = $result->fetch_assoc();

if (!$examination) {
    echo "Pemeriksaan tidak ditemukan.";
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examination_date = trim($_POST['examination_date']);
    $examination_type = trim($_POST['examination_type']);
    $room = trim($_POST['room']);
    $status = trim($_POST['status']);
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id = (int)$_POST['doctor_id'];

    // Validasi input
    if (empty($examination_date) || empty($examination_type) || empty($room) || empty($status) || !$patient_id || !$doctor_id) {
        echo "Semua kolom wajib diisi.";
        exit;
    }

    // Cek redundansi data
    $checkQuery = "
        SELECT id 
        FROM examinations 
        WHERE examination_date = ? 
          AND examination_type = ? 
          AND room = ? 
          AND patient_id = ? 
          AND doctor_id = ? 
          AND id != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssssii", $examination_date, $examination_type, $room, $patient_id, $doctor_id, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "Data pemeriksaan dengan informasi serupa sudah ada.";
        exit;
    }

    // Lakukan pembaruan
    $updateQuery = "
        UPDATE examinations 
        SET examination_date = ?, 
            examination_type = ?, 
            room = ?, 
            status = ?, 
            patient_id = ?, 
            doctor_id = ? 
        WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssiii", $examination_date, $examination_type, $room, $status, $patient_id, $doctor_id, $id);

    if ($updateStmt->execute()) {
        // Jika status diperbarui menjadi "Selesai", tambahkan data ke tabel laporan
        if ($status === "Selesai") {
            $selectQuery = "
                SELECT examinations.id AS examination_id, 
                       examinations.examination_date, 
                       patients.name AS patient_name, 
                       examinations.examination_type, 
                       doctors.name AS doctor_name, 
                       examinations.room 
                FROM examinations
                JOIN patients ON examinations.patient_id = patients.id
                JOIN doctors ON examinations.doctor_id = doctors.id
                WHERE examinations.id = ?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param("i", $id);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            $examinationData = $result->fetch_assoc();

            $insertQuery = "
                INSERT INTO laporan (examination_id, examination_date, patient_name, examination_type, doctor_name, room) 
                VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param(
                "isssss", 
                $examinationData['examination_id'], 
                $examinationData['examination_date'], 
                $examinationData['patient_name'], 
                $examinationData['examination_type'], 
                $examinationData['doctor_name'], 
                $examinationData['room']
            );
            $insertStmt->execute();
        }

        echo "Data pemeriksaan berhasil diperbarui.";
        header("Location: pemeriksaan.php");
        exit;
    } else {
        echo "Terjadi kesalahan saat memperbarui data.";
    }
}

// Ambil daftar pasien dan dokter untuk dropdown
$patientsQuery = "SELECT id, name FROM patients";
$patientsResult = $conn->query($patientsQuery);

$doctorsQuery = "SELECT id, name FROM doctors";
$doctorsResult = $conn->query($doctorsQuery);
?>


<div style="width: 80%; padding: 10px;">
    <h2>Edit Data Pemeriksaan</h2>
    <form action="" method="POST">
        <table cellpadding="5" cellspacing="0" style="width: 50%; border-collapse: collapse;">
            <tr>
                <td><label for="examination_date">Tanggal Pemeriksaan:</label></td>
                <td><input type="date" id="examination_date" name="examination_date" value="<?= $examination['examination_date'] ?>" style="width: 100%;" required></td>
            </tr>
            <tr>
                <td><label for="examination_type">Jenis Pemeriksaan:</label></td>
                <td><input type="text" id="examination_type" name="examination_type" value="<?= $examination['examination_type'] ?>" style="width: 100%;" required></td>
            </tr>
            <tr>
                <td><label for="room">Ruangan:</label></td>
                <td><input type="text" id="room" name="room" value="<?= $examination['room'] ?>" style="width: 100%;" required></td>
            </tr>
            <tr>
                <td><label for="status">Status:</label></td>
                <td>
                    <select id="status" name="status" style="width: 100%;" required>
                        <option value="Dijadwalkan" <?= $examination['status'] === 'Dijadwalkan' ? 'selected' : '' ?>>Dijadwalkan</option>
                        <option value="Selesai" <?= $examination['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="DIbatalkan" <?= $examination['status'] === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="patient_id">Pasien:</label></td>
                <td>
                    <select id="patient_id" name="patient_id" style="width: 100%;" required>
                        <?php while ($patient = $patientsResult->fetch_assoc()): ?>
                            <option value="<?= $patient['id'] ?>" <?= $examination['patient_id'] == $patient['id'] ? 'selected' : '' ?>>
                                <?= $patient['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="doctor_id">Dokter:</label></td>
                <td>
                    <select id="doctor_id" name="doctor_id" style="width: 100%;" required>
                        <?php while ($doctor = $doctorsResult->fetch_assoc()): ?>
                            <option value="<?= $doctor['id'] ?>" <?= $examination['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                <?= $doctor['name'] ?>
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
    <a href="pemeriksaan.php">Batal</a>
</div>
</body>
</html>
