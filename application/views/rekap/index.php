<!-- Halaman Rekapitulasi & Statistik Hasil Skrining (Sheet Rekap & Data_Pasien Excel) -->

<div style="margin-bottom: 24px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
  <div>
    <h2 style="font-size: 20px; font-weight: 800; color: var(--navy-900); margin: 0;">
      Rekapitulasi Hasil Skrining &amp; Statistik Klinis
    </h2>
    <p style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0;">
      Rekapitulasi populasi pasien berdasarkan distribusi kategori risiko dan frekuensi 15 faktor risiko klinis (Sesuai Sheet Rekap &amp; Data_Pasien).
    </p>
  </div>

  <div style="display: flex; gap: 8px;">
    <a href="<?= base_url('rekap/export_rekap_csv'); ?>" class="btn btn-navy-outline btn-sm">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Ekspor Rekap (CSV)
    </a>
    <a href="<?= base_url('rekap/export_matrix_csv'); ?>" class="btn btn-primary btn-sm">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Ekspor Register Matriks (Sheet Data_Pasien)
    </a>
    <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
      Cetak Rekap
    </button>
  </div>
</div>

<!-- KPI Cards -->
<div class="grid-4" style="margin-bottom: 24px;">
  <div class="stat-card" style="--stat-color: #2563EB; --stat-bg: #EFF6FF;">
    <div class="stat-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format($stats['total_skrining'] ?? 0); ?></div>
      <div class="stat-label">Jumlah Pasien Terskrining</div>
      <div class="stat-meta">Populasi register aktif</div>
    </div>
  </div>

  <div class="stat-card" style="--stat-color: #059669; --stat-bg: #ECFDF5;">
    <div class="stat-icon" style="color:#059669;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format($stats['avg_skor'] ?? 0, 1); ?></div>
      <div class="stat-label">Rata-rata Total Skor</div>
      <div class="stat-meta">Skala 0 - 30 agregat risiko</div>
    </div>
  </div>

  <div class="stat-card" style="--stat-color: #DC2626; --stat-bg: #FEF2F2;">
    <div class="stat-icon" style="color:#DC2626;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value" style="color:#DC2626;"><?= number_format($stats['total_tinggi'] ?? 0); ?></div>
      <div class="stat-label">Pasien Risiko Tinggi</div>
      <div class="stat-meta">Prioritas rujukan spesialis</div>
    </div>
  </div>

  <div class="stat-card" style="--stat-color: #D97706; --stat-bg: #FFFBEB;">
    <div class="stat-icon" style="color:#D97706;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value" style="color:#D97706;"><?= number_format($stats['total_sedang'] ?? 0); ?></div>
      <div class="stat-label">Pasien Risiko Sedang</div>
      <div class="stat-meta">SADANIS &amp; USG berkala</div>
    </div>
  </div>
</div>

<!-- Bagian 1: Tabel Distribusi Kategori Risiko (Sheet Rekap Excel) -->
<div class="card" style="margin-bottom: 24px; border-top: 4px solid var(--blue-accent);">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10h-10z"/></svg>
        1. Rekapitulasi Kategori Risiko Pasien
      </div>
      <div class="card-header-subtitle">Proporsi dan persentase klasifikasi risiko kanker payudara pada populasi terskrining</div>
    </div>
    <span class="badge badge-navy">Total <?= $stats['total_skrining']; ?> Pasien</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th style="width: 140px;">Kategori Risiko</th>
          <th style="width: 120px; text-align: center;">Skor Minimum</th>
          <th style="width: 100px; text-align: center;">Jumlah Pasien</th>
          <th style="width: 220px;">Persentase</th>
          <th>Rekomendasi Tindak Lanjut Klinis</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rekap_kategori as $k): ?>
          <?php
            $cat = $k['nama_kategori'];
            $bClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
            $pctColor = ($cat === 'Tinggi') ? '#EF4444' : (($cat === 'Sedang') ? '#F59E0B' : '#10B981');
            $pct = (float)($k['persentase'] ?? 0);
          ?>
          <tr>
            <td>
              <span class="badge <?= $bClass; ?>" style="font-size:12.5px; font-weight:700;">
                <?= htmlspecialchars($cat); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <span style="font-weight:700; color:var(--navy-900);">&ge; <?= (int)$k['skor_min']; ?> Poin</span>
            </td>
            <td style="text-align: center;">
              <span style="font-size:16px; font-weight:900; color:var(--navy-900);">
                <?= (int)$k['jumlah']; ?>
              </span>
            </td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div style="flex:1; height:10px; background:#E2E8F0; border-radius:5px; overflow:hidden;">
                  <div style="width:<?= $pct; ?>%; height:100%; background:<?= $pctColor; ?>; border-radius:5px;"></div>
                </div>
                <strong style="width:45px; text-align:right; font-size:13px; color:var(--navy-900);"><?= $pct; ?>%</strong>
              </div>
            </td>
            <td>
              <div style="font-size:12.5px; color:var(--text-secondary); line-height:1.4;">
                <?= htmlspecialchars($k['rekomendasi']); ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="background:var(--bg-subtle); font-weight:800;">
          <td>Total</td>
          <td style="text-align:center;">-</td>
          <td style="text-align:center; font-size:16px; color:var(--navy-900);"><?= $stats['total_skrining']; ?></td>
          <td>
            <div style="display:flex; align-items:center; gap:10px;">
              <div style="flex:1; height:10px; background:#E2E8F0; border-radius:5px; overflow:hidden;">
                <div style="width:100%; height:100%; background:#2563EB; border-radius:5px;"></div>
              </div>
              <strong style="width:45px; text-align:right; font-size:13px; color:var(--navy-900);">100.0%</strong>
            </div>
          </td>
          <td style="font-size:12px; color:var(--text-muted); font-style:italic;">Seluruh rekam skrining yang terverifikasi</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Bagian 2: Tabel Frekuensi Faktor Risiko (Sheet Rekap Excel) -->
<div class="card" style="margin-bottom: 24px;">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        2. Frekuensi 15 Parameter Faktor Risiko Terbanyak
      </div>
      <div class="card-header-subtitle">Distribusi prevalensi faktor risiko klinis yang ditemukan pada pasien (Diurutkan dari F01 s/d F15)</div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th style="width: 70px;">ID</th>
          <th>Nama Faktor Risiko</th>
          <th style="text-align: center; width: 70px;">Bobot</th>
          <th style="text-align: center; width: 110px;">Override Tinggi</th>
          <th style="text-align: center; width: 110px;">Jumlah Pasien</th>
          <th style="width: 260px;">Persentase (%)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($frekuensi_faktor as $f): ?>
          <?php
            $pct = (float)($f['persentase'] ?? 0);
            $hasOverride = !empty($f['override_tinggi']);
          ?>
          <tr>
            <td>
              <strong style="color:var(--navy-900); font-family:monospace; font-size:13.5px;">
                <?= htmlspecialchars($f['id_faktor']); ?>
              </strong>
            </td>
            <td>
              <span style="font-weight:700; color:var(--navy-900); font-size:13px;">
                <?= htmlspecialchars($f['nama_faktor']); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <span style="font-weight:800; color:var(--blue-primary); font-size:13.5px;">
                <?= (int)$f['bobot']; ?>
              </span>
            </td>
            <td style="text-align: center;">
              <?php if ($hasOverride): ?>
                <span class="badge badge-override" style="font-size:11px;">Ya (Override)</span>
              <?php else: ?>
                <span style="color:var(--text-light); font-size:12px;">Tidak</span>
              <?php endif; ?>
            </td>
            <td style="text-align: center;">
              <strong style="font-size:15px; color:var(--navy-900);">
                <?= (int)$f['jumlah_pasien']; ?>
              </strong>
            </td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div style="flex:1; height:8px; background:#E2E8F0; border-radius:4px; overflow:hidden;">
                  <div style="width:<?= $pct; ?>%; height:100%; background:<?= $hasOverride ? '#DC2626' : '#2563EB'; ?>; border-radius:4px;"></div>
                </div>
                <strong style="width:45px; text-align:right; font-size:12.5px; color:var(--navy-900);"><?= $pct; ?>%</strong>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Bagian 3: Matriks Register Pasien Lengkap (Sheet Data_Pasien Excel) -->
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        3. Register Matriks Pasien Lengkap (Sheet Data_Pasien Excel)
      </div>
      <div class="card-header-subtitle">Tampilan matriks kolom 15 faktor risiko klinis (1 = terpenuhi, 0 = tidak) sesuai format Excel register</div>
    </div>
    <a href="<?= base_url('rekap/export_matrix_csv'); ?>" class="btn btn-navy-outline btn-sm">
      Unduh Matriks CSV
    </a>
  </div>

  <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
    <table class="custom-table" style="font-size: 11.5px;">
      <thead>
        <tr>
          <th>No</th>
          <th>Tgl Skrining</th>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>Usia</th>
          <th>IMT</th>
          <th style="text-align: center;">Total Skor</th>
          <th style="text-align: center;">Override</th>
          <th>Kategori</th>
          <th title="F01: Usia">F01</th>
          <th title="F02: Kerabat">F02</th>
          <th title="F03: Ca Pribadi">F03</th>
          <th title="F04: BRCA">F04</th>
          <th title="F05: Menarche">F05</th>
          <th title="F06: Menopause">F06</th>
          <th title="F07: Nulipara">F07</th>
          <th title="F08: Tidak Menyusui">F08</th>
          <th title="F09: KB/TSH">F09</th>
          <th title="F10: IMT">F10</th>
          <th title="F11: Alkohol">F11</th>
          <th title="F12: Aktivitas">F12</th>
          <th title="F13: Hiperplasia">F13</th>
          <th title="F14: Radioterapi">F14</th>
          <th title="F15: BI-RADS">F15</th>
          <th>Petugas</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($matrix_data as $row): ?>
          <?php
            $cat = $row['kategori_risiko'];
            $bClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
          ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal_skrining'])); ?></td>
            <td><strong style="font-family:monospace; color:var(--navy-900);"><?= htmlspecialchars($row['no_rm']); ?></strong></td>
            <td><strong><?= htmlspecialchars($row['nama_pasien']); ?></strong></td>
            <td><?= $row['usia_saat_skrining'] ?? '-'; ?> th</td>
            <td><?= $row['imt'] ?? '-'; ?></td>
            <td style="text-align: center;"><strong style="font-size:13px; color:var(--blue-primary);"><?= $row['total_skor']; ?></strong></td>
            <td style="text-align: center;">
              <?= ($row['override_aktif'] === 'Ya') ? '<span class="badge badge-override" style="font-size:9.5px;">Ya</span>' : '-'; ?>
            </td>
            <td><span class="badge <?= $bClass; ?>" style="font-size:10px;"><?= $cat; ?></span></td>
            <!-- F01..F15 Indicators (1/0) -->
            <?php for ($i = 1; $i <= 15; $i++): ?>
              <?php
                $fid = sprintf('f%02d_terpenuhi', $i);
                $isMet = !empty($row[$fid]);
              ?>
              <td style="text-align:center; font-weight:700; background:<?= $isMet ? '#FEF3C7' : '#FFFFFF'; ?>; color:<?= $isMet ? '#B45309' : '#CBD5E1'; ?>;">
                <?= $isMet ? '1' : '0'; ?>
              </td>
            <?php endfor; ?>
            <td><span style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($row['petugas']); ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
