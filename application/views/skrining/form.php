<!-- Form Skrining Baru dengan Real-time Rule-Based Reasoning Engine -->

<div class="grid-form">
  <!-- Left Column: Form Input (15 Faktor Terstruktur) -->
  <form id="screeningForm" action="<?= base_url('skrining/simpan'); ?>" method="POST">
    
    <!-- Bagian 0: Identitas Pasien -->
    <div class="card" style="border-top: 4px solid var(--blue-accent);">
      <div class="card-header">
        <div>
          <div class="card-header-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Identifikasi Pasien
          </div>
          <div class="card-header-subtitle">Pilih data pasien yang terdaftar atau daftarkan pasien baru</div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleNewPatientFields()">
          + Pasien Baru On-the-fly
        </button>
      </div>

      <div class="card-body">
        <!-- Existing Patient Select -->
        <div id="existing_patient_box" class="form-group" style="margin-bottom: 12px;">
          <label class="form-label" for="id_pasien_select">Pilih Pasien Terdaftar <span class="required">*</span></label>
          <select name="id_pasien" id="id_pasien_select" class="form-control">
            <option value="">-- Pilih Rekam Medis / Nama Pasien --</option>
            <?php foreach ($pasien_list as $p): ?>
              <?php
                $sel = ($selected_pasien && $selected_pasien['id_pasien'] == $p['id_pasien']) ? 'selected' : '';
              ?>
              <option value="<?= $p['id_pasien']; ?>" data-dob="<?= $p['tanggal_lahir']; ?>" <?= $sel; ?>>
                <?= htmlspecialchars($p['no_rm']); ?> - <?= htmlspecialchars($p['nama']); ?> (Lahir: <?= $p['tanggal_lahir'] ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-'; ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Quick New Patient Box (Hidden by default) -->
        <div id="new_patient_box" style="display:none; background:var(--navy-50); padding:16px; border-radius:var(--radius-md); border:1.5px dashed var(--navy-300); margin-bottom:14px;">
          <div style="font-weight:700; color:var(--navy-900); font-size:13.5px; margin-bottom:10px;">
            Input Cepat Pasien Baru:
          </div>
          <div class="grid-2" style="gap:12px; margin-bottom:10px;">
            <div>
              <label class="form-label">Nama Lengkap Pasien <span class="required">*</span></label>
              <input type="text" name="new_patient_nama" id="new_patient_nama" class="form-control" placeholder="Contoh: Ny. Siti Aminah">
            </div>
            <div>
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="new_patient_dob" id="new_patient_dob" class="form-control" onchange="autoFillAgeFromNewPatient(this.value)">
            </div>
          </div>
          <div class="grid-2" style="gap:12px;">
            <div>
              <label class="form-label">No. Rekam Medis (Opsional)</label>
              <input type="text" name="new_patient_no_rm" class="form-control" placeholder="Otomatis jika kosong">
            </div>
            <div>
              <label class="form-label">Alamat / Kota Domisili</label>
              <input type="text" name="new_patient_alamat" class="form-control" placeholder="Contoh: Jakarta Barat">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelompok 1: Demografi & Antropometri -->
    <div class="factor-group-card">
      <div class="factor-group-header">
        <div class="factor-group-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          1. Demografi & Indeks Massa Tubuh (IMT)
        </div>
        <span class="factor-group-badge">F01, F10</span>
      </div>

      <!-- F01: Usia -->
      <div class="factor-row" id="row_F01">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F01 &bull; BOBOT: 2</div>
          <div class="factor-name">Usia Pasien Saat Skrining</div>
          <div class="factor-desc">Risiko meningkat seiring bertambahnya usia. Terpenuhi bila Usia &ge; 50 tahun (Aturan R01).</div>
        </div>
        <div class="factor-input-col">
          <?php
            $default_age = '';
            if ($selected_pasien && !empty($selected_pasien['tanggal_lahir'])) {
              $dob = new DateTime($selected_pasien['tanggal_lahir']);
              $now = new DateTime();
              $default_age = $now->diff($dob)->y;
            }
          ?>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="usia_input" id="usia_input" class="form-control" min="15" max="100" placeholder="Th" value="<?= $default_age; ?>" style="text-align:right;">
            <span style="font-size:13px; font-weight:600; color:var(--text-muted);">Tahun</span>
          </div>
        </div>
      </div>

      <!-- F10: Pengukuran BB, TB & Kalkulasi IMT -->
      <div class="factor-row" id="row_F10">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F10 &bull; BOBOT: 1</div>
          <div class="factor-name">Indeks Massa Tubuh (IMT / Obesitas)</div>
          <div class="factor-desc">
            Dihitung otomatis: BB (kg) / (TB (m))&sup2;. Terpenuhi bila IMT &ge; 30.0 kg/m&sup2; (Aturan R10).
          </div>
          <div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
            <span style="font-size:12px; color:var(--text-muted);">Hasil Kalkulasi IMT:</span>
            <strong id="imt_display" style="font-size:15px; color:var(--navy-900);">-</strong>
            <span id="imt_category_badge" class="badge badge-navy">Belum lengkap</span>
          </div>
        </div>
        <div class="factor-input-col">
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; align-items:center; gap:6px;">
              <input type="number" step="0.1" name="berat_badan" id="berat_badan" class="form-control" placeholder="BB (kg)" style="text-align:right;">
              <span style="font-size:12px; color:var(--text-muted); width:24px;">kg</span>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" class="form-control" placeholder="TB (cm)" style="text-align:right;">
              <span style="font-size:12px; color:var(--text-muted); width:24px;">cm</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kelompok 2: Riwayat Reproduksi & Hormonal -->
    <div class="factor-group-card">
      <div class="factor-group-header">
        <div class="factor-group-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 6v6l4 2"/></svg>
          2. Riwayat Reproduksi & Hormonal
        </div>
        <span class="factor-group-badge">F05, F06, F07, F08, F09</span>
      </div>

      <!-- F05: Menarche dini -->
      <div class="factor-row" id="row_F05">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F05 &bull; BOBOT: 1</div>
          <div class="factor-name">Usia Menarche Dini</div>
          <div class="factor-desc">Paparan hormon estrogen lebih lama. Terpenuhi jika usia haid pertama &lt; 12 tahun (Aturan R05).</div>
        </div>
        <div class="factor-input-col">
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="f_F05" id="f_F05" class="form-control" min="8" max="20" placeholder="12" style="text-align:right;">
            <span style="font-size:13px; font-weight:600; color:var(--text-muted);">Tahun</span>
          </div>
        </div>
      </div>

      <!-- F06: Menopause terlambat -->
      <div class="factor-row" id="row_F06">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F06 &bull; BOBOT: 1</div>
          <div class="factor-name">Status & Usia Menopause</div>
          <div class="factor-desc">Terpenuhi jika status "Sudah menopause" DAN usia menopause &gt; 55 tahun (Aturan R06).</div>
        </div>
        <div class="factor-input-col">
          <div style="display:flex; flex-direction:column; gap:8px;">
            <select name="status_menopause" id="status_menopause" class="form-control">
              <option value="Belum menopause">Belum menopause</option>
              <option value="Sudah menopause">Sudah menopause</option>
            </select>
            <div id="menopause_age_wrapper" style="display:none;">
              <div style="display:flex; align-items:center; gap:6px;">
                <input type="number" name="usia_menopause" id="usia_menopause" class="form-control" min="30" max="75" placeholder="Usia menopause" style="text-align:right;">
                <span style="font-size:12px; color:var(--text-muted);">Th</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- F07: Nulipara / anak pertama > 30 th -->
      <div class="factor-row" id="row_F07">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F07 &bull; BOBOT: 1</div>
          <div class="factor-name">Nulipara / Melahirkan Anak Pertama Usia &gt; 30 Tahun</div>
          <div class="factor-desc">Belum pernah melahirkan atau baru melahirkan anak pertama setelah usia 30 tahun (Aturan R07).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F07" id="f_F07" value="Ya">
            <span style="font-size:13.5px; font-weight:600; color:var(--navy-900);">Ya, terpenuhi</span>
          </label>
        </div>
      </div>

      <!-- F08: Tidak pernah menyusui -->
      <div class="factor-row" id="row_F08">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F08 &bull; BOBOT: 1</div>
          <div class="factor-name">Tidak Pernah Menyusui ASI</div>
          <div class="factor-desc">Tidak memiliki riwayat memberikan ASI eksklusif/menyusui (Aturan R08).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F08" id="f_F08" value="Ya">
            <span style="font-size:13.5px; font-weight:600; color:var(--navy-900);">Ya, terpenuhi</span>
          </label>
        </div>
      </div>

      <!-- F09: Kontrasepsi hormonal / TSH jangka panjang -->
      <div class="factor-row" id="row_F09">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F09 &bull; BOBOT: 1</div>
          <div class="factor-name">Kontrasepsi Hormonal / Terapi Sulih Hormon (TSH) Jangka Panjang</div>
          <div class="factor-desc">Penggunaan pil KB/suntik atau terapi sulih hormon kombinasi jangka panjang (Aturan R09).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F09" id="f_F09" value="Ya">
            <span style="font-size:13.5px; font-weight:600; color:var(--navy-900);">Ya, terpenuhi</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Kelompok 3: Riwayat Genetik & Medis Mayor (Override Rule Support) -->
    <div class="factor-group-card" style="border: 2px solid #E2E8F0;">
      <div class="factor-group-header" style="background: linear-gradient(90deg, #F8FAFC 0%, #FFFFFF 100%);">
        <div class="factor-group-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          3. Riwayat Genetik & Medis Mayor (Indikator Override)
        </div>
        <span class="factor-group-badge" style="background:#FEE2E2; color:#B91C1C;">F02, F03, F04, F14</span>
      </div>

      <!-- F03: Riwayat kanker payudara pribadi (OVERRIDE) -->
      <div class="factor-row" id="row_F03">
        <div class="factor-info">
          <div class="factor-id" style="color:#DC2626;">FAKTOR F03 &bull; BOBOT: 4 &bull; OVERRIDE TINGGI</div>
          <div class="factor-name">Riwayat Kanker Payudara Pribadi Sebelumnya</div>
          <div class="factor-desc">Pernah terdiagnosis Ca Mammae pada payudara yang sama atau kontralateral (Aturan R03 & R16).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F03" id="f_F03" value="Ya">
            <span style="font-size:13.5px; font-weight:700; color:#DC2626;">Ya (Pernah)</span>
          </label>
        </div>
      </div>

      <!-- F04: Mutasi BRCA1/BRCA2 (OVERRIDE) -->
      <div class="factor-row" id="row_F04">
        <div class="factor-info">
          <div class="factor-id" style="color:#DC2626;">FAKTOR F04 &bull; BOBOT: 5 &bull; OVERRIDE TINGGI</div>
          <div class="factor-name">Mutasi Gen BRCA1 / BRCA2 Positif</div>
          <div class="factor-desc">Berdasarkan hasil uji profil genetik laboratorium molekuler terverifikasi (Aturan R04 & R16).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F04" id="f_F04" value="Ya">
            <span style="font-size:13.5px; font-weight:700; color:#DC2626;">Ya (Positif)</span>
          </label>
        </div>
      </div>

      <!-- F14: Radioterapi area dada (OVERRIDE) -->
      <div class="factor-row" id="row_F14">
        <div class="factor-info">
          <div class="factor-id" style="color:#DC2626;">FAKTOR F14 &bull; BOBOT: 3 &bull; OVERRIDE TINGGI</div>
          <div class="factor-name">Riwayat Radioterapi di Area Dada</div>
          <div class="factor-desc">Pernah menjalani terapi radiasi dinding dada pada usia muda (mis. Limfoma Hodgkin) (Aturan R14 & R16).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F14" id="f_F14" value="Ya">
            <span style="font-size:13.5px; font-weight:700; color:#DC2626;">Ya (Pernah)</span>
          </label>
        </div>
      </div>

      <!-- F02: Riwayat keluarga derajat pertama -->
      <div class="factor-row" id="row_F02">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F02 &bull; BOBOT: 3</div>
          <div class="factor-name">Riwayat Kanker Payudara Keluarga Derajat Pertama</div>
          <div class="factor-desc">Terpenuhi jika kerabat kandung (Ibu, Saudari kandung, atau Anak perempuan) menderita Ca Mammae (Aturan R02).</div>
        </div>
        <div class="factor-input-col">
          <select name="f_F02" id="f_F02" class="form-control">
            <option value="Tidak ada">Tidak ada riwayat</option>
            <option value="Ibu">Ibu Kandung (+3)</option>
            <option value="Saudari kandung">Saudari Kandung (+3)</option>
            <option value="Anak">Anak Perempuan (+3)</option>
            <option value="Kerabat jauh">Kerabat Jauh / Derajat 2 (0)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Kelompok 4: Gaya Hidup & Faktor Pendukung -->
    <div class="factor-group-card">
      <div class="factor-group-header">
        <div class="factor-group-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><activity x1="22" y1="12" x2="2" y2="12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
          4. Gaya Hidup & Faktor Pendukung Lainnya
        </div>
        <span class="factor-group-badge">F11, F12, F13, F15</span>
      </div>

      <!-- F11: Konsumsi alkohol -->
      <div class="factor-row" id="row_F11">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F11 &bull; BOBOT: 1</div>
          <div class="factor-name">Konsumsi Minuman Beralkohol</div>
          <div class="factor-desc">Terpenuhi jika pola konsumsi tergolong Rutin (Aturan R11).</div>
        </div>
        <div class="factor-input-col">
          <select name="f_F11" id="f_F11" class="form-control">
            <option value="Tidak pernah">Tidak pernah</option>
            <option value="Kadang-kadang">Kadang-kadang</option>
            <option value="Rutin">Rutin (+1)</option>
          </select>
        </div>
      </div>

      <!-- F12: Aktivitas fisik kurang -->
      <div class="factor-row" id="row_F12">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F12 &bull; BOBOT: 1</div>
          <div class="factor-name">Tingkat Aktivitas Fisik / Olahraga</div>
          <div class="factor-desc">Terpenuhi jika aktivitas fisik tergolong Kurang (&lt; 150 menit aerobik/minggu) (Aturan R12).</div>
        </div>
        <div class="factor-input-col">
          <select name="f_F12" id="f_F12" class="form-control">
            <option value="Cukup">Cukup / Teratur</option>
            <option value="Kurang">Kurang (+1)</option>
          </select>
        </div>
      </div>

      <!-- F13: Biopsi atipikal -->
      <div class="factor-row" id="row_F13">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F13 &bull; BOBOT: 3</div>
          <div class="factor-name">Riwayat Biopsi Payudara dengan Hiperplasia Atipikal</div>
          <div class="factor-desc">Hasil pemeriksaan patologi anatomi menunjukkan lesi payudara jinak atipikal (Aturan R13).</div>
        </div>
        <div class="factor-input-col">
          <label class="custom-checkbox">
            <input type="checkbox" name="f_F13" id="f_F13" value="Ya">
            <span style="font-size:13.5px; font-weight:600; color:var(--navy-900);">Ya, ada riwayat</span>
          </label>
        </div>
      </div>

      <!-- F15: Densitas Payudara (BI-RADS) -->
      <div class="factor-row" id="row_F15">
        <div class="factor-info">
          <div class="factor-id">FAKTOR F15 &bull; BOBOT: 2</div>
          <div class="factor-name">Densitas Jaringan Payudara (BI-RADS)</div>
          <div class="factor-desc">Pencitraan mamografi menunjukkan jaringan heterogen padat (Kategori C) atau sangat padat (Kategori D) (Aturan R15).</div>
        </div>
        <div class="factor-input-col">
          <select name="f_F15" id="f_F15" class="form-control">
            <option value="Belum diperiksa">Belum diperiksa</option>
            <option value="A">Kategori A (Dominan lemak)</option>
            <option value="B">Kategori B (Fibroglandular tersebar)</option>
            <option value="C">Kategori C (Heterogen padat) (+2)</option>
            <option value="D">Kategori D (Sangat padat / Extremely dense) (+2)</option>
          </select>
        </div>
      </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
      <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary btn-lg">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Proses & Simpan Hasil Skrining</span>
      </button>
    </div>
  </form>

  <!-- Right Column: Sticky Live Inference Preview Card -->
  <aside>
    <div class="live-calc-card">
      <div class="live-calc-header">
        <div class="live-calc-title">Estimasi Keputusan Klinis</div>
        <span class="live-badge-live">
          <span class="pulse-dot"></span>
          Live CDSS
        </span>
      </div>

      <!-- Live Score Counter -->
      <div class="live-score-display">
        <div class="live-score-number" id="liveScore">0</div>
        <div class="live-score-label">Total Skor Terakumulasi</div>
      </div>

      <!-- Live Category Badge -->
      <div id="liveCategory" class="live-risk-badge badge-low">
        RISIKO RENDAH
      </div>

      <!-- Override Alert Banner -->
      <div id="liveOverrideBanner" style="display:none; background:#FEF2F2; border:1px solid #FECACA; border-radius:var(--radius-md); padding:10px 12px; margin-bottom:14px;">
        <div style="font-size:11.5px; font-weight:800; color:#DC2626; text-transform:uppercase; display:flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/></svg>
          Aturan Override Terpicu (R16)
        </div>
        <div style="font-size:11px; color:#991B1B; margin-top:3px; line-height:1.4;">
          Faktor risiko tinggi mayor aktif (BRCA / Radioterapi / Ca Pribadi). Kategori ditetapkan <strong>TINGGI</strong> secara otomatis.
        </div>
      </div>

      <!-- Active Rules Triggered -->
      <div style="margin-bottom:16px;">
        <div style="font-size:12px; font-weight:700; color:var(--navy-900); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:8px;">
          Aturan Terpenuhi (Forward Chaining):
        </div>
        <div id="liveTriggeredRules" style="max-height:160px; overflow-y:auto; background:var(--bg-subtle); border-radius:var(--radius-md); padding:8px 12px; border:1px solid var(--border-light);">
          <div style="font-size:12px; color:#94A3B8; text-align:center; padding:10px;">Belum ada aturan yang terpenuhi</div>
        </div>
      </div>

      <!-- Live Clinical Recommendation -->
      <div style="background:var(--navy-50); border:1px solid var(--navy-200); border-radius:var(--radius-md); padding:12px 14px; margin-bottom:20px;">
        <div style="font-size:11px; font-weight:700; color:var(--navy-700); text-transform:uppercase; margin-bottom:4px;">
          Rekomendasi Tindak Lanjut:
        </div>
        <p id="liveRecommendation" style="font-size:12px; color:var(--navy-900); line-height:1.5;">
          Edukasi dan SADARI rutin setiap bulan; pemeriksaan klinis payudara (SADANIS) berkala; ulangi skrining sesuai jadwal.
        </p>
      </div>

      <button type="button" class="btn btn-primary" style="width:100%;" onclick="document.getElementById('screeningForm').requestSubmit();">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Simpan Skrining
      </button>
    </div>
  </aside>
</div>

<script>
function toggleNewPatientFields() {
  const box = document.getElementById('new_patient_box');
  const existingSelect = document.getElementById('existing_patient_box');
  if (box.style.display === 'none') {
    box.style.display = 'block';
    document.getElementById('id_pasien_select').value = '';
    document.getElementById('new_patient_nama').focus();
  } else {
    box.style.display = 'none';
  }
}

function autoFillAgeFromNewPatient(dobStr) {
  if (!dobStr) return;
  const birthDate = new Date(dobStr);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  const uInput = document.getElementById('usia_input');
  if (uInput) {
    uInput.value = age;
    uInput.dispatchEvent(new Event('input'));
  }
}
</script>
