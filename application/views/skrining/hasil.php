<?php
  $cat = $skrining['nama_kategori'] ?? 'Rendah';
  $isHigh = ($cat === 'Tinggi');
  $isMed  = ($cat === 'Sedang');
  $isLow  = ($cat === 'Rendah');

  $badgeClass = $isHigh ? 'badge-high' : ($isMed ? 'badge-med' : 'badge-low');
  $themeBg    = $isHigh ? '#FEF2F2' : ($isMed ? '#FFFBEB' : '#ECFDF5');
  $themeBorder= $isHigh ? '#FECACA' : ($isMed ? '#FDE68A' : '#A7F3D0');
  $themeText  = $isHigh ? '#DC2626' : ($isMed ? '#D97706' : '#059669');
?>

<!-- Action Bar -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <div>
    <a href="<?= base_url('skrining/riwayat'); ?>" class="btn btn-secondary btn-sm">
      &larr; Kembali ke Riwayat
    </a>
  </div>
  <div style="display:flex; gap:10px;">
    <a href="<?= base_url('skrining/baru/' . $skrining['id_pasien']); ?>" class="btn btn-navy-outline">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
      Skrining Ulang Pasien
    </a>
    <a href="<?= base_url('skrining/cetak/' . $skrining['id_skrining']); ?>" target="_blank" class="btn btn-primary">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Cetak Lembar Hasil Medis
    </a>
  </div>
</div>

<!-- Header Card: Result Summary -->
<div class="card" style="border-top: 6px solid <?= $themeText; ?>;">
  <div class="card-body" style="padding: 32px;">
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:24px;">
      
      <!-- Patient Information Details -->
      <div style="flex: 1; min-width: 280px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <span class="badge badge-navy" style="font-family:monospace; font-size:13px; font-weight:700;">
            <?= htmlspecialchars($skrining['no_rm']); ?>
          </span>
          <span style="font-size:12.5px; color:var(--text-muted);">
            ID Skrining: #SKR-<?= str_pad($skrining['id_skrining'], 4, '0', STR_PAD_LEFT); ?>
          </span>
        </div>

        <h2 style="font-size:26px; font-weight:800; color:var(--navy-900); letter-spacing:-0.02em;">
          <?= htmlspecialchars($skrining['nama_pasien']); ?>
        </h2>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-top:16px;">
          <div>
            <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Usia Pasien:</div>
            <div style="font-size:14px; font-weight:700; color:var(--navy-900);">
              <?= $usia ? $usia . ' Tahun' : '-'; ?>
            </div>
          </div>
          <div>
            <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Status Menopause:</div>
            <div style="font-size:14px; font-weight:700; color:var(--navy-900);">
              <?= htmlspecialchars($skrining['status_menopause'] ?? '-'); ?>
            </div>
          </div>
          <div>
            <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">IMT (BB / TB):</div>
            <div style="font-size:14px; font-weight:700; color:var(--navy-900);">
              <?= $skrining['imt'] ? $skrining['imt'] . ' kg/m²' : '-'; ?>
              <span style="font-size:12px; font-weight:normal; color:var(--text-muted);">
                (<?= $skrining['berat_badan']; ?>kg / <?= $skrining['tinggi_badan']; ?>cm)
              </span>
            </div>
          </div>
          <div>
            <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Tanggal Pemeriksaan:</div>
            <div style="font-size:14px; font-weight:700; color:var(--navy-900);">
              <?= date('d F Y, H:i', strtotime($skrining['tanggal'])); ?> WIB
            </div>
          </div>
          <div>
            <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Tenaga Medis Penilai:</div>
            <div style="font-size:14px; font-weight:700; color:var(--navy-900);">
              <?= htmlspecialchars($skrining['nama_petugas'] ?? '-'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Result Risk Indicator Box -->
      <div style="text-align:center; background:<?= $themeBg; ?>; border:2px solid <?= $themeBorder; ?>; border-radius:var(--radius-xl); padding:24px 36px; min-width:240px;">
        <div style="font-size:12px; font-weight:700; color:<?= $themeText; ?>; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px;">
          Tingkat Risiko Terhitung
        </div>
        <div style="font-size:32px; font-weight:900; color:<?= $themeText; ?>; line-height:1.1; margin-bottom:10px;">
          RISIKO <?= strtoupper($cat); ?>
        </div>
        
        <div style="display:inline-flex; align-items:baseline; gap:4px; background:#FFFFFF; padding:6px 16px; border-radius:var(--radius-full); box-shadow:var(--shadow-sm); border:1px solid <?= $themeBorder; ?>;">
          <span style="font-size:24px; font-weight:900; color:var(--navy-900);"><?= (int)$skrining['total_skor']; ?></span>
          <span style="font-size:12px; font-weight:600; color:var(--text-muted);">/ 30 poin agregat</span>
        </div>

        <?php if ($skrining['override_aktif']): ?>
          <div style="margin-top:10px;">
            <span class="badge badge-override" style="font-size:11px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              Override Aktif (R16)
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Override Notice if Active -->
    <?php if ($skrining['override_aktif']): ?>
      <div style="margin-top:24px; background:#FEF2F2; border:1.5px solid #FECACA; border-radius:var(--radius-md); padding:16px; display:flex; align-items:flex-start; gap:12px;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
          <div style="font-size:13.5px; font-weight:800; color:#DC2626; text-transform:uppercase;">
            Pemberitahuan Klinis Khusus: Aturan Override Terpicu
          </div>
          <p style="font-size:13px; color:#991B1B; margin-top:3px; line-height:1.5;">
            Pasien memiliki satu atau lebih faktor risiko tinggi absolut (Mutasi BRCA1/2, Riwayat Radioterapi Dada, atau Riwayat Kanker Payudara Pribadi). Mesin inferensi secara otomatis mengklasifikasikan pasien ke dalam kategori <strong>Risiko Tinggi</strong> terlepas dari batas skor minimum numerik.
          </p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Recommendation Card (FR-05) -->
<div class="card">
  <div class="card-header" style="background:var(--navy-900); color:#FFFFFF;">
    <div class="card-header-title" style="color:#FFFFFF;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Rekomendasi Tindak Lanjut Klinis Resmi
    </div>
    <span class="badge" style="background:rgba(255,255,255,0.15); color:#FFFFFF;">Standar Konsensus</span>
  </div>
  <div class="card-body">
    <div style="font-size:15px; font-weight:600; color:var(--navy-900); line-height:1.7; background:var(--bg-subtle); padding:20px; border-radius:var(--radius-md); border-left:4px solid var(--navy-800);">
      <?= htmlspecialchars($skrining['rekomendasi'] ?? '-'); ?>
    </div>

    <!-- Clinical Action Steps Guide -->
    <div class="grid-3" style="gap:16px; margin-top:20px;">
      <div style="background:#FFFFFF; border:1px solid var(--border-light); border-radius:var(--radius-md); padding:16px;">
        <div style="font-weight:700; color:var(--navy-900); font-size:13.5px; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#2563EB;"></span>
          Pemeriksaan SADARI
        </div>
        <div style="font-size:12.5px; color:var(--text-muted); line-height:1.5;">
          Edukasi pasien untuk melakukan Pemeriksaan Payudara Sendiri secara mandiri pada hari ke-7 hingga ke-10 setelah hari pertama haid setiap bulan.
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--border-light); border-radius:var(--radius-md); padding:16px;">
        <div style="font-weight:700; color:var(--navy-900); font-size:13.5px; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#0EA5E9;"></span>
          Pemeriksaan SADANIS
        </div>
        <div style="font-size:12.5px; color:var(--text-muted); line-height:1.5;">
          Pemeriksaan payudara klinis oleh dokter atau bidan terlatih secara berkala untuk palpasi sistematis area payudara dan kelenjar getah bening aksila.
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--border-light); border-radius:var(--radius-md); padding:16px;">
        <div style="font-weight:700; color:var(--navy-900); font-size:13.5px; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#DC2626;"></span>
          Pencitraan & Rujukan
        </div>
        <div style="font-size:12.5px; color:var(--text-muted); line-height:1.5;">
          Pada risiko sedang atau tinggi, pertimbangkan modalitas pencitraan (USG Mammae, Mamografi digital, atau MRI Payudara) serta rujukan spesialis onkologi.
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Factors Breakdown Matrix -->
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Rincian Evaluasi 15 Parameter Faktor Risiko
      </div>
      <div class="card-header-subtitle">Hasil inferensi nilai input terhadap basis aturan dan bobot klinis</div>
    </div>
    <span class="badge badge-navy">15 Faktor Teruji</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th style="width: 80px;">ID</th>
          <th>Nama Faktor Risiko</th>
          <th>Kriteria Ambang / Pemicu</th>
          <th>Nilai Input Pasien</th>
          <th style="text-align: center;">Status Terpenuhi</th>
          <th style="text-align: center;">Bobot</th>
          <th style="text-align: right;">Skor Pasien</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($skrining['details'] as $d): ?>
          <tr class="<?= $d['terpenuhi'] ? ($d['override_tinggi'] ? 'active-override' : 'active-risk') : ''; ?>">
            <td>
              <strong style="color:var(--navy-600);"><?= htmlspecialchars($d['id_faktor']); ?></strong>
            </td>
            <td>
              <div style="font-weight:600; color:var(--navy-900);">
                <?= htmlspecialchars($d['nama_faktor']); ?>
              </div>
              <?php if ($d['override_tinggi']): ?>
                <span class="badge badge-override" style="font-size:10.5px; padding:1px 6px; margin-top:2px;">
                  Override Tinggi
                </span>
              <?php endif; ?>
            </td>
            <td>
              <span style="font-size:12.5px; color:var(--text-muted);">
                <?php if ($d['tipe_input'] === 'angka'): ?>
                  <?= htmlspecialchars($d['operator']); ?> <?= (float)$d['ambang']; ?>
                <?php else: ?>
                  <?= htmlspecialchars($d['nilai_pemicu']); ?>
                <?php endif; ?>
              </span>
            </td>
            <td>
              <span style="font-weight:600; color:var(--navy-900);">
                <?= !empty($d['nilai_input']) ? htmlspecialchars($d['nilai_input']) : '<span style="color:#94A3B8;">-</span>'; ?>
              </span>
            </td>
            <td style="text-align: center;">
              <?php if ($d['terpenuhi']): ?>
                <span class="badge badge-high" style="font-weight:700;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  Ya
                </span>
              <?php else: ?>
                <span class="badge badge-navy" style="color:var(--text-muted); background:var(--bg-subtle);">
                  Tidak
                </span>
              <?php endif; ?>
            </td>
            <td style="text-align: center; font-weight:600; color:var(--navy-800);">
              <?= (int)$d['bobot']; ?>
            </td>
            <td style="text-align: right;">
              <span style="font-size:15px; font-weight:800; color:<?= $d['terpenuhi'] ? 'var(--blue-accent)' : 'var(--text-light)'; ?>;">
                +<?= (int)$d['skor']; ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="background:var(--navy-50);">
          <td colspan="5" style="font-weight:700; color:var(--navy-900); padding:16px 18px; text-transform:uppercase; font-size:13px;">
            Total Akumulasi Skor Risiko
          </td>
          <td style="text-align:center; font-weight:800; color:var(--navy-900);">
            30 Max
          </td>
          <td style="text-align:right; font-size:18px; font-weight:900; color:var(--blue-primary); padding:16px 18px;">
            <?= (int)$skrining['total_skor']; ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
