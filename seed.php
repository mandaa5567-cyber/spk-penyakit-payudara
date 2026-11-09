<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=cdss_kanker_payudara', 'root', 'MysqlManda', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Check users
    $uCount = $pdo->query('SELECT count(*) FROM users')->fetchColumn();
    if ($uCount == 0) {
        $stmt = $pdo->prepare('INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Dr. dr. Rian Pratama, Sp.B(K)Onk', 'admin', password_hash('admin123', PASSWORD_BCRYPT), 'admin']);
        $stmt->execute(['Ns. Maya Indah, S.Kep', 'nakes', password_hash('nakes123', PASSWORD_BCRYPT), 'nakes']);
        echo "Users seeded.\n";
    } else {
        echo "Users already exist ($uCount).\n";
    }

    // Check patients
    $pCount = $pdo->query('SELECT count(*) FROM pasien')->fetchColumn();
    if ($pCount == 0) {
        $stmtP = $pdo->prepare('INSERT INTO pasien (no_rm, nama, tanggal_lahir, alamat) VALUES (?, ?, ?, ?)');
        $stmtP->execute(['RM-2026-001', 'Ny. Ratna Sari', '1974-05-12', 'Jl. Melati No. 45, Jakarta Selatan']);
        $p1 = $pdo->lastInsertId();

        $stmtP->execute(['RM-2026-002', 'Ny. Dewi Lestari', '1968-08-20', 'Jl. Dahlia No. 12, Bandung']);
        $p2 = $pdo->lastInsertId();

        $stmtP->execute(['RM-2026-003', 'Nn. Anisa Putri', '1998-03-15', 'Jl. Anggrek No. 8, Surabaya']);
        $p3 = $pdo->lastInsertId();

        $stmtP->execute(['RM-2026-004', 'Ny. Sri Wahyuni', '1981-11-04', 'Jl. Cempaka No. 22, Semarang']);
        $p4 = $pdo->lastInsertId();

        echo "Patients seeded.\n";

        // Seed Screenings
        $adminId = $pdo->query("SELECT id_user FROM users WHERE username='admin'")->fetchColumn();
        $nakesId = $pdo->query("SELECT id_user FROM users WHERE username='nakes'")->fetchColumn();

        // Patient 1
        $stmtS = $pdo->prepare('INSERT INTO skrining (id_pasien, id_user, tanggal, status_menopause, berat_badan, tinggi_badan) VALUES (?, ?, ?, ?, ?, ?)');
        $stmtS->execute([$p1, $nakesId, '2026-09-28 10:15:00', 'Belum menopause', 68.0, 155.0]);
        $s1 = $pdo->lastInsertId();

        $factors1 = [
            'F01' => '52', 'F02' => 'Ibu', 'F03' => 'Tidak', 'F04' => 'Tidak',
            'F05' => '11', 'F06' => '', 'F07' => 'Tidak', 'F08' => 'Tidak',
            'F09' => 'Tidak', 'F10' => '28.3', 'F11' => 'Tidak pernah', 'F12' => 'Kurang',
            'F13' => 'Tidak', 'F14' => 'Tidak', 'F15' => 'B'
        ];
        $stmtD = $pdo->prepare('INSERT INTO detail_skrining (id_skrining, id_faktor, nilai_input) VALUES (?, ?, ?)');
        foreach ($factors1 as $f => $val) {
            $stmtD->execute([$s1, $f, $val]);
        }
        $pdo->query("CALL sp_hitung_skrining($s1)");

        // Patient 2
        $stmtS->execute([$p2, $nakesId, '2026-09-29 14:30:00', 'Sudah menopause', 78.0, 152.0]);
        $s2 = $pdo->lastInsertId();
        $factors2 = [
            'F01' => '58', 'F02' => 'Saudari kandung', 'F03' => 'Tidak', 'F04' => 'Ya',
            'F05' => '12', 'F06' => '56', 'F07' => 'Tidak', 'F08' => 'Ya',
            'F09' => 'Tidak', 'F10' => '33.8', 'F11' => 'Kadang-kadang', 'F12' => 'Cukup',
            'F13' => 'Tidak', 'F14' => 'Tidak', 'F15' => 'D'
        ];
        foreach ($factors2 as $f => $val) {
            $stmtD->execute([$s2, $f, $val]);
        }
        $pdo->query("CALL sp_hitung_skrining($s2)");

        // Patient 3
        $stmtS->execute([$p3, $nakesId, '2026-09-30 09:00:00', 'Belum menopause', 52.0, 160.0]);
        $s3 = $pdo->lastInsertId();
        $factors3 = [
            'F01' => '28', 'F02' => 'Tidak ada', 'F03' => 'Tidak', 'F04' => 'Tidak',
            'F05' => '13', 'F06' => '', 'F07' => 'Tidak', 'F08' => 'Tidak',
            'F09' => 'Tidak', 'F10' => '20.3', 'F11' => 'Tidak pernah', 'F12' => 'Rutin',
            'F13' => 'Tidak', 'F14' => 'Tidak', 'F15' => 'A'
        ];
        foreach ($factors3 as $f => $val) {
            $stmtD->execute([$s3, $f, $val]);
        }
        $pdo->query("CALL sp_hitung_skrining($s3)");

        // Patient 4
        $stmtS->execute([$p4, $adminId, '2026-10-01 11:20:00', 'Belum menopause', 64.0, 158.0]);
        $s4 = $pdo->lastInsertId();
        $factors4 = [
            'F01' => '45', 'F02' => 'Tidak ada', 'F03' => 'Tidak', 'F04' => 'Tidak',
            'F05' => '13', 'F06' => '', 'F07' => 'Tidak', 'F08' => 'Tidak',
            'F09' => 'Tidak', 'F10' => '25.6', 'F11' => 'Tidak pernah', 'F12' => 'Cukup',
            'F13' => 'Tidak', 'F14' => 'Ya', 'F15' => 'B'
        ];
        foreach ($factors4 as $f => $val) {
            $stmtD->execute([$s4, $f, $val]);
        }
        $pdo->query("CALL sp_hitung_skrining($s4)");

        echo "Screenings seeded and calculated via sp_hitung_skrining successfully!\n";
    }

    echo "--- SEED REPORT ---\n";
    echo "Users: " . $pdo->query('SELECT count(*) FROM users')->fetchColumn() . "\n";
    echo "Pasien: " . $pdo->query('SELECT count(*) FROM pasien')->fetchColumn() . "\n";
    echo "Skrining: " . $pdo->query('SELECT count(*) FROM skrining')->fetchColumn() . "\n";
    foreach($pdo->query('SELECT * FROM v_hasil_skrining') as $row) {
        echo "ID: {$row['id_skrining']} | Pasien: {$row['nama_pasien']} ({$row['no_rm']}) | Skor: {$row['total_skor']} | Override: {$row['override_aktif']} | Kategori: {$row['kategori_risiko']} | Petugas: {$row['petugas']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
