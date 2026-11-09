<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lembar Hasil Skrining - <?= htmlspecialchars($skrining['no_rm']); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', Arial, sans-serif;
      color: #0F172A;
      background: #FFFFFF;
      padding: 30px;
      max-width: 850px;
      margin: 0 auto;
      line-height: 1.5;
    }
    .print-actions {
      margin-bottom: 25px;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
    .btn {
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      font-size: 13px;
    }
    .btn-print {
      background: #0B192C;
      color: #FFFFFF;
      border: 1px solid #0B192C;
    }
    .btn-close {
      background: #F1F5F9;
      color: #334155;
      border: 1px solid #CBD5E1;
    }
    .kop-surat {
      border-bottom: 3px double #0B192C;
      padding-bottom: 14px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .kop-title h1 {
      font-size: 20px;
      font-weight: 800;
      color: #0B192C;
      margin: 0;
      text-transform: uppercase;
      letter-spacing: -0.01em;
    }
    .kop-title p {
      font-size: 12px;
      color: #475569;
      margin: 2px 0 0;
    }
    .report-title {
      text-align: center;
      margin: 20px 0;
    }
    .report-title h2 {
      font-size: 16px;
      font-weight: 800;
      color: #0B192C;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin: 0;
    }
    .report-title p {
      font-size: 12px;
      color: #64748B;
      margin-top: 2px;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      background: #F8FAFC;
      border: 1px solid #E2E8F0;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 20px;
      font-size: 13px;
    }
    .info-row {
      display: flex;
      margin-bottom: 4px;
    }
    .info-label {
      width: 140px;
      font-weight: 600;
      color: #475569;
    }
    .info-val {
      font-weight: 700;
      color: #0F172A;
    }
    .result-box {
      border: 2px solid #0B192C;
      border-radius: 8px;
      padding: 18px 24px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .result-category {
      font-size: 22px;
      font-weight: 800;
    }
    .cat-tinggi { color: #DC2626; }
    .cat-sedang { color: #D97706; }
    .cat-rendah { color: #059669; }

    .score-pill {
      background: #0B192C;
      color: #FFFFFF;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 15px;
      font-weight: 700;
    }
    .recommendation-box {
      background: #F1F5F9;
      border-left: 4px solid #0B192C;
      border-radius: 4px;
      padding: 14px 18px;
      margin-bottom: 22px;
      font-size: 13px;
      line-height: 1.6;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
      margin-bottom: 30px;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #CBD5E1;
      padding: 7px 10px;
      text-align: left;
    }
    table.data-table th {
      background: #0B192C;
      color: #FFFFFF;
      font-weight: 700;
    }
    .signatures {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-top: 40px;
      font-size: 13px;
    }
    .sign-col {
      text-align: center;
    }
    .sign-space {
      height: 70px;
    }
    @media print {
      .print-actions { display: none !important; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

<div class="print-actions">
  <button class="btn btn-close" onclick="window.close()">Tutup</button>
  <button class="btn btn-print" onclick="window.print()">
    &equiv; Cetak Lembar Medis
  </button>
</div>

<!-- Kop Surat Medis -->
<div class="kop-surat">
  <div style="display:flex; align-items:center; gap:14px;">
    <div style="width:48px; height:48px; border-radius:8px; background:#0B192C; color:#fff; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800;">
      +
    </div>
    <div class="kop-title">
      <h1>PUSAT LAYANAN DETEKSI DINI ONKOLOGI</h1>
      <p>Clinical Decision Support System (CDSS) Skrining Risiko Kanker Payudara</p>
      <p style="font-size:11px; color:#64748B;">Alamat: Jl. Pelayanan Kesehatan No. 10 | Telp: (021) 555-0199 | Website: cdss-onkologi.id</p>
    </div>
  </div>
  <div style="text-align:right;">
    <div style="font-size:11px; font-weight:700; color:#475569;">NOMOR REKAM MEDIS</div>
    <div style="font-size:18px; font-weight:800; font-family:monospace; color:#0B192C;">
      <?= htmlspecialchars($skrining['no_rm']); ?>
    </div>
  </div>
</div>

<div class="report-title">
  <h2>LEMBAR HASIL SKRINING RISIKO KANKER PAYUDARA</h2>
  <p>Metode Inferensi: Rule-Based Reasoning (Forward Chaining)</p>
</div>

<!-- Info Pasien -->
<div class="info-grid">
  <div>
    <div class="info-row"><span class="info-label">Nama Pasien</span>: <span class="info-val"><?= htmlspecialchars($skrining['nama_pasien']); ?></span></div>
    <div class="info-row"><span class="info-label">Tanggal Lahir / Usia</span>: <span class="info-val"><?= $skrining['tanggal_lahir'] ? date('d/m/Y', strtotime($skrining['tanggal_lahir'])) : '-'; ?> (<?= $usia ? $usia . ' Th' : '-'; ?>)</span></div>
    <div class="info-row"><span class="info-label">Alamat Pasien</span>: <span class="info-val"><?= htmlspecialchars($skrining['alamat'] ?? '-'); ?></span></div>
  </div>
  <div>
    <div class="info-row"><span class="info-label">Tanggal Skrining</span>: <span class="info-val"><?= date('d F Y, H:i', strtotime($skrining['tanggal'])); ?> WIB</span></div>
    <div class="info-row"><span class="info-label">Status Menopause</span>: <span class="info-val"><?= htmlspecialchars($skrining['status_menopause'] ?? '-'); ?></span></div>
    <div class="info-row"><span class="info-label">BB / TB / IMT</span>: <span class="info-val"><?= $skrining['berat_badan']; ?> kg / <?= $skrining['tinggi_badan']; ?> cm (IMT: <?= $skrining['imt'] ?? '-'; ?>)</span></div>
  </div>
</div>

<!-- Hasil Klasifikasi -->
<?php
  $cat = $skrining['nama_kategori'] ?? 'Rendah';
  $catClass = ($cat === 'Tinggi') ? 'cat-tinggi' : (($cat === 'Sedang') ? 'cat-sedang' : 'cat-rendah');
?>
<div class="result-box">
  <div>
    <div style="font-size:12px; font-weight:700; color:#64748B; text-transform:uppercase;">Hasil Penilaian Tingkat Risiko:</div>
    <div class="result-category <?= $catClass; ?>">
      RISIKO <?= strtoupper($cat); ?>
      <?php if ($skrining['override_aktif']): ?>
        <span style="font-size:12px; background:#FEE2E2; color:#DC2626; padding:3px 8px; border-radius:12px; margin-left:8px;">(Override Aktif)</span>
      <?php endif; ?>
    </div>
  </div>
  <div>
    <span class="score-pill">Total Skor: <?= (int)$skrining['total_skor']; ?> / 30 Poin</span>
  </div>
</div>

<!-- Rekomendasi Klinis -->
<div style="font-size:12px; font-weight:700; color:#0B192C; text-transform:uppercase; margin-bottom:6px;">
  Rekomendasi Tindak Lanjut Klinis:
</div>
<div class="recommendation-box">
  <strong>Rekomendasi Resmi:</strong><br>
  <?= htmlspecialchars($skrining['rekomendasi'] ?? '-'); ?>
</div>

<!-- Tabel Rincian Faktor -->
<div style="font-size:12px; font-weight:700; color:#0B192C; text-transform:uppercase; margin-bottom:6px;">
  Evaluasi 15 Parameter Faktor Risiko:
</div>
<table class="data-table">
  <thead>
    <tr>
      <th style="width: 40px;">No</th>
      <th>Faktor Risiko</th>
      <th>Kriteria / Ambang</th>
      <th>Nilai Input</th>
      <th style="text-align: center; width: 80px;">Status</th>
      <th style="text-align: right; width: 60px;">Skor</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; foreach ($skrining['details'] as $d): ?>
      <tr style="<?= $d['terpenuhi'] ? 'background:#F8FAFC;' : ''; ?>">
        <td><?= $no++; ?></td>
        <td>
          <strong><?= htmlspecialchars($d['id_faktor']); ?></strong> - <?= htmlspecialchars($d['nama_faktor']); ?>
          <?= $d['override_tinggi'] ? '<span style="color:#DC2626; font-size:10px;">[Override]</span>' : ''; ?>
        </td>
        <td>
          <?= ($d['tipe_input'] === 'angka') ? $d['operator'] . ' ' . (float)$d['ambang'] : $d['nilai_pemicu']; ?>
        </td>
        <td><?= htmlspecialchars($d['nilai_input'] ?? '-'); ?></td>
        <td style="text-align: center; font-weight:600; color:<?= $d['terpenuhi'] ? '#DC2626' : '#64748B'; ?>;">
          <?= $d['terpenuhi'] ? 'Ya (+'.(int)$d['bobot'].')' : 'Tidak (0)'; ?>
        </td>
        <td style="text-align: right; font-weight:700;"><?= (int)$d['skor']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Kolom Tanda Tangan -->
<div class="signatures">
  <div class="sign-col">
    <div>Mengetahui,</div>
    <div style="font-weight:600;">Tenaga Kesehatan Penilai</div>
    <div class="sign-space"></div>
    <div style="font-weight:700; text-decoration:underline;">( <?= htmlspecialchars($skrining['nama_petugas'] ?? '...........................'); ?> )</div>
    <div style="font-size:11px; color:#64748B;">NIP/SIP: Tenaga Medis Pemeriksa</div>
  </div>

  <div class="sign-col">
    <div>Dokter Penanggung Jawab Pelayanan (DPJP)</div>
    <div style="font-weight:600;">Spesialis Bedah / Onkologi</div>
    <div class="sign-space"></div>
    <div style="font-weight:700; text-decoration:underline;">( Dr. dr. Rian Pratama, Sp.B(K)Onk )</div>
    <div style="font-size:11px; color:#64748B;">SIP: 446/1029/DS-SP.ONK/2026</div>
  </div>
</div>

</body>
</html>
