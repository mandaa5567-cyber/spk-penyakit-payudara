<!-- Modul Simulasi Skrining CDSS (Mengacu pada Sheet Simulasi Excel) -->

<div style="margin-bottom: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
  <div>
    <h2 style="font-size: 20px; font-weight: 800; color: var(--navy-900); margin: 0;">
      Simulasi Skrining Risiko Kanker Payudara
    </h2>
    <p style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0;">
      Simulasi inferensi klinis <em>Rule-Based Reasoning (Forward Chaining)</em> tanpa harus menyimpan ke database terlebih dahulu.
    </p>
  </div>

  <!-- Quick Presets from Excel -->
  <div style="display: flex; align-items: center; gap: 8px;">
    <span style="font-size: 12.5px; font-weight: 700; color: var(--navy-900);">Muat Skenario:</span>
    <select id="scenarioPreset" class="form-control" style="font-size: 12.5px; padding: 6px 12px; width: auto; background: #FFFFFF;" onchange="loadScenario(this.value)">
      <option value="">-- Pilih Skenario Simulasi Excel --</option>
      <option value="contoh_1">Contoh 1: P-001 (Risiko Sedang, Skor 7)</option>
      <option value="contoh_2">Contoh 2: P-002 (Risiko Tinggi, Skor 15 via BRCA Override)</option>
      <option value="contoh_3">Contoh 3: Pasien Sehat (Risiko Rendah, Skor 0)</option>
      <option value="contoh_4">Contoh 4: Pasien Radioterapi (Risiko Tinggi via Override R14)</option>
    </select>
    <button type="button" class="btn btn-secondary btn-sm" onclick="resetSimulasiForm()">Reset</button>
  </div>
</div>

<div class="grid-form">
  <!-- Left Column: Bagian A (Input Pasien) & Bagian D (Simpan/Transfer) -->
  <div>
    <!-- Bagian A: Data Input Pasien -->
    <div class="card" style="border-top: 4px solid var(--blue-accent); margin-bottom: 20px;">
      <div class="card-header">
        <div>
          <div class="card-header-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            A. Data Input Pasien Simulasi
          </div>
          <div class="card-header-subtitle">Isi parameter demografi, antropometri, dan faktor risiko klinis</div>
        </div>
        <span class="badge badge-navy">15 Faktor Input</span>
      </div>

      <div class="card-body">
        <form id="simForm">
          <!-- Identitas / Inisial -->
          <div class="grid-2" style="gap:12px; margin-bottom:14px;">
            <div class="form-group">
              <label class="form-label" for="sim_inisial">ID / Inisial Pasien</label>
              <input type="text" id="sim_inisial" class="form-control" placeholder="Contoh: P-001 (Fiktif)" value="P-001">
            </div>
            <div class="form-group">
              <label class="form-label" for="sim_usia">Usia Pasien (Tahun) <span class="required">*</span></label>
              <div style="display:flex; align-items:center; gap:6px;">
                <input type="number" id="sim_usia" class="form-control" min="15" max="100" value="52" style="text-align:right;">
                <span style="font-size:12.5px; font-weight:600; color:var(--text-muted);">Th</span>
              </div>
            </div>
          </div>

          <!-- Antropometri BB & TB -->
          <div class="grid-3" style="gap:12px; margin-bottom:14px; background:var(--bg-subtle); padding:12px 14px; border-radius:var(--radius-md);">
            <div class="form-group" style="margin:0;">
              <label class="form-label" for="sim_bb">Berat Badan (kg)</label>
              <input type="number" step="0.1" id="sim_bb" class="form-control" value="68" placeholder="68.0" style="text-align:right;">
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label" for="sim_tb">Tinggi Badan (cm)</label>
              <input type="number" step="0.1" id="sim_tb" class="form-control" value="155" placeholder="155.0" style="text-align:right;">
            </div>
            <div class="form-group" style="margin:0;">
              <label class="form-label">IMT (Otomatis)</label>
              <div style="display:flex; align-items:center; gap:6px; height:38px;">
                <strong id="sim_imt_display" style="font-size:16px; color:var(--navy-900);">28.3</strong>
                <span id="sim_imt_badge" class="badge badge-med" style="font-size:10.5px;">Kelebihan BB</span>
              </div>
            </div>
          </div>

          <!-- Reproduksi & Menopause -->
          <div class="grid-3" style="gap:12px; margin-bottom:14px;">
            <div class="form-group">
              <label class="form-label" for="sim_menarche">Usia Menarche (Th)</label>
              <input type="number" id="sim_menarche" class="form-control" value="11" placeholder="12" style="text-align:right;">
            </div>
            <div class="form-group">
              <label class="form-label" for="sim_status_menopause">Status Menopause</label>
              <select id="sim_status_menopause" class="form-control">
                <option value="Belum menopause" selected>Belum menopause</option>
                <option value="Sudah menopause">Sudah menopause</option>
              </select>
            </div>
            <div class="form-group" id="sim_usia_menopause_box" style="display:none;">
              <label class="form-label" for="sim_usia_menopause">Usia Menopause (Th)</label>
              <input type="number" id="sim_usia_menopause" class="form-control" placeholder="Isi bila sudah" style="text-align:right;">
            </div>
          </div>

          <!-- Riwayat Keluarga, Alkohol, Aktivitas, Densitas -->
          <div class="grid-2" style="gap:12px; margin-bottom:14px;">
            <div class="form-group">
              <label class="form-label" for="sim_kerabat">Kerabat Penderita Ca Payudara (Derajat 1)</label>
              <select id="sim_kerabat" class="form-control">
                <option value="Tidak ada">Tidak ada riwayat</option>
                <option value="Ibu" selected>Ibu Kandung (+3)</option>
                <option value="Saudari kandung">Saudari Kandung (+3)</option>
                <option value="Anak">Anak Perempuan (+3)</option>
                <option value="Kerabat jauh">Kerabat Derajat 2 / Jauh (0)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="sim_densitas">Densitas Payudara (BI-RADS)</label>
              <select id="sim_densitas" class="form-control">
                <option value="Belum diperiksa">Belum diperiksa</option>
                <option value="A">Kategori A (Dominan Lemak)</option>
                <option value="B" selected>Kategori B (Fibroglandular Tersebar)</option>
                <option value="C">Kategori C (Heterogen Padat) (+2)</option>
                <option value="D">Kategori D (Sangat Padat) (+2)</option>
              </select>
            </div>
          </div>

          <div class="grid-2" style="gap:12px; margin-bottom:14px;">
            <div class="form-group">
              <label class="form-label" for="sim_alkohol">Konsumsi Minuman Alkohol</label>
              <select id="sim_alkohol" class="form-control">
                <option value="Tidak pernah" selected>Tidak pernah</option>
                <option value="Kadang-kadang">Kadang-kadang</option>
                <option value="Rutin">Rutin (+1)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="sim_aktivitas">Aktivitas Fisik / Olahraga</label>
              <select id="sim_aktivitas" class="form-control">
                <option value="Cukup">Cukup / Teratur</option>
                <option value="Kurang" selected>Kurang (+1)</option>
              </select>
            </div>
          </div>

          <!-- Faktor Klinis & Override (Kotak Centang) -->
          <div style="background:var(--navy-50); border:1px solid var(--navy-200); border-radius:var(--radius-md); padding:14px; margin-bottom:16px;">
            <div style="font-weight:700; color:var(--navy-900); font-size:13px; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.03em;">
              Faktor Risiko Klinis &amp; Indikator Override:
            </div>
            
            <div style="display:flex; flex-direction:column; gap:8px;">
              <label class="custom-checkbox">
                <input type="checkbox" id="sim_ca_pribadi">
                <div>
                  <span style="font-size:13px; font-weight:700; color:#DC2626;">F03: Riwayat Kanker Payudara Pribadi Sebelumnya</span>
                  <span class="badge badge-override" style="font-size:10px; margin-left:4px;">Override Tinggi (+4)</span>
                </div>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_brca">
                <div>
                  <span style="font-size:13px; font-weight:700; color:#DC2626;">F04: Mutasi Gen BRCA1/BRCA2 Positif</span>
                  <span class="badge badge-override" style="font-size:10px; margin-left:4px;">Override Tinggi (+5)</span>
                </div>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_radioterapi">
                <div>
                  <span style="font-size:13px; font-weight:700; color:#DC2626;">F14: Riwayat Radioterapi di Area Dinding Dada</span>
                  <span class="badge badge-override" style="font-size:10px; margin-left:4px;">Override Tinggi (+3)</span>
                </div>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_nulipara">
                <span style="font-size:13px; font-weight:600; color:var(--navy-900);">F07: Nulipara / Melahirkan Anak Pertama Usia &gt; 30 Tahun (+1)</span>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_tidak_menyusui">
                <span style="font-size:13px; font-weight:600; color:var(--navy-900);">F08: Tidak Pernah Menyusui ASI (+1)</span>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_kb_tsh">
                <span style="font-size:13px; font-weight:600; color:var(--navy-900);">F09: Kontrasepsi Hormonal / Terapi Sulih Hormon (TSH) Jangka Panjang (+1)</span>
              </label>

              <label class="custom-checkbox">
                <input type="checkbox" id="sim_biopsi_atipikal">
                <span style="font-size:13px; font-weight:600; color:var(--navy-900);">F13: Riwayat Biopsi Payudara dengan Hiperplasia Atipikal (+3)</span>
              </label>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Bagian B: Evaluasi 15 Faktor Risiko (Live Table) -->
    <div class="card" style="margin-bottom: 20px;">
      <div class="card-header">
        <div>
          <div class="card-header-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            B. Evaluasi 15 Faktor Risiko (Forward Chaining)
          </div>
          <div class="card-header-subtitle">Hasil evaluasi status (1/0), bobot, dan perolehan skor tiap parameter</div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="custom-table" id="simEvaluationTable">
          <thead>
            <tr>
              <th style="width: 60px;">Faktor</th>
              <th>Nama Parameter</th>
              <th style="text-align: center; width: 110px;">Status Terpenuhi</th>
              <th style="text-align: center; width: 70px;">Bobot</th>
              <th style="text-align: right; width: 70px;">Skor</th>
            </tr>
          </thead>
          <tbody id="simEvaluationBody">
            <!-- Populated dynamically by JS -->
          </tbody>
          <tfoot>
            <tr style="background:var(--bg-subtle); font-weight:800;">
              <td colspan="4" style="text-align:right; font-size:13px; color:var(--navy-900);">TOTAL SKOR TERAKUMULASI:</td>
              <td style="text-align:right; font-size:16px; color:var(--blue-primary);" id="simTotalScoreFooter">0</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Column: Bagian C (Hasil Inferensi) & Bagian D (Opsi Transfer ke Data Pasien) -->
  <aside>
    <!-- Bagian C: Hasil Inferensi -->
    <div class="live-calc-card" style="margin-bottom: 20px;">
      <div class="live-calc-header">
        <div class="live-calc-title">C. Hasil Inferensi CDSS</div>
        <span class="live-badge-live">
          <span class="pulse-dot"></span>
          Simulasi
        </span>
      </div>

      <div class="live-score-display">
        <div class="live-score-number" id="simScoreDisplay">0</div>
        <div class="live-score-label">Total Skor Risiko Pasien</div>
      </div>

      <!-- Category Badge -->
      <div id="simCategoryBadge" class="live-risk-badge badge-low" style="font-size:15px; padding:8px 16px;">
        RISIKO RENDAH
      </div>

      <!-- Override Banner -->
      <div id="simOverrideBanner" style="display:none; background:#FEF2F2; border:1px solid #FECACA; border-radius:var(--radius-md); padding:10px 12px; margin-bottom:14px;">
        <div style="font-size:11.5px; font-weight:800; color:#DC2626; text-transform:uppercase; display:flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/></svg>
          Aturan Override Terpicu (R16)
        </div>
        <div style="font-size:11px; color:#991B1B; margin-top:3px; line-height:1.4;">
          Faktor risiko tinggi mayor aktif (BRCA / Radioterapi / Ca Pribadi). Kategori ditetapkan <strong>TINGGI</strong> secara otomatis tanpa memandang skor.
        </div>
      </div>

      <!-- Clinical Recommendation -->
      <div style="background:var(--navy-50); border:1px solid var(--navy-200); border-radius:var(--radius-md); padding:12px 14px; margin-bottom:16px;">
        <div style="font-size:11px; font-weight:700; color:var(--navy-700); text-transform:uppercase; margin-bottom:4px;">
          Rekomendasi Tindak Lanjut Klinis:
        </div>
        <p id="simRecommendationText" style="font-size:12px; color:var(--navy-900); line-height:1.5; margin:0;">
          Edukasi dan SADARI rutin setiap bulan; pemeriksaan klinis payudara (SADANIS) berkala; ulangi skrining sesuai jadwal.
        </p>
      </div>

      <div style="font-size:11px; color:var(--text-muted); font-style:italic; line-height:1.4;">
        Catatan: Hasil inferensi ini merupakan instrumen penunjang keputusan klinis awal (CDSS) dan bukan pengganti diagnosis definitif dokter.
      </div>
    </div>

    <!-- Bagian D: Baris Siap Salin / Transfer ke Data Pasien (Sheet Simulasi Excel) -->
    <div class="card" style="border-top: 4px solid #10B981;">
      <div class="card-header">
        <div>
          <div class="card-header-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            D. Simpan Hasil ke Data Pasien
          </div>
          <div class="card-header-subtitle">Transfer hasil simulasi ini langsung ke register rekam medis</div>
        </div>
      </div>

      <div class="card-body">
        <form action="<?= base_url('simulasi/transfer_ke_pasien'); ?>" method="POST" id="transferForm">
          <!-- Hidden inputs populated dynamically from simulation -->
          <input type="hidden" name="usia_input" id="trans_usia">
          <input type="hidden" name="berat_badan" id="trans_bb">
          <input type="hidden" name="tinggi_badan" id="trans_tb">
          <input type="hidden" name="status_menopause" id="trans_status_menopause">
          <input type="hidden" name="usia_menopause" id="trans_usia_menopause">
          <input type="hidden" name="f_F01" id="trans_f_F01">
          <input type="hidden" name="f_F02" id="trans_f_F02">
          <input type="hidden" name="f_F03" id="trans_f_F03">
          <input type="hidden" name="f_F04" id="trans_f_F04">
          <input type="hidden" name="f_F05" id="trans_f_F05">
          <input type="hidden" name="f_F06" id="trans_f_F06">
          <input type="hidden" name="f_F07" id="trans_f_F07">
          <input type="hidden" name="f_F08" id="trans_f_F08">
          <input type="hidden" name="f_F09" id="trans_f_F09">
          <input type="hidden" name="f_F10" id="trans_f_F10">
          <input type="hidden" name="f_F11" id="trans_f_F11">
          <input type="hidden" name="f_F12" id="trans_f_F12">
          <input type="hidden" name="f_F13" id="trans_f_F13">
          <input type="hidden" name="f_F14" id="trans_f_F14">
          <input type="hidden" name="f_F15" id="trans_f_F15">

          <div class="form-group" style="margin-bottom:12px;">
            <label class="form-label" for="sim_trans_id_pasien">Pilih Pasien Terdaftar:</label>
            <select name="id_pasien" id="sim_trans_id_pasien" class="form-control">
              <option value="">-- Pilih Pasien Terdaftar --</option>
              <?php foreach ($pasien_list as $p): ?>
                <option value="<?= $p['id_pasien']; ?>">
                  <?= htmlspecialchars($p['no_rm']); ?> - <?= htmlspecialchars($p['nama']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="text-align:center; margin:8px 0; font-size:12px; color:var(--text-muted); font-weight:600;">— ATAU —</div>

          <div class="form-group" style="margin-bottom:12px;">
            <label class="form-label" for="sim_trans_new_nama">Nama Pasien Baru (On-the-fly):</label>
            <input type="text" name="new_patient_nama" id="sim_trans_new_nama" class="form-control" placeholder="Contoh: Ny. Ratna Sari">
          </div>

          <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="sim_trans_new_dob">Tanggal Lahir (Opsional):</label>
            <input type="date" name="new_patient_dob" id="sim_trans_new_dob" class="form-control">
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;" onclick="prepareTransfer()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Simpan Hasil ke Rekam Pasien
          </button>
        </form>
      </div>
    </div>
  </aside>
</div>

<script>
const PRESETS = <?= json_encode($presets); ?>;

const FACTORS = [
  { id: 'F01', name: 'Usia ≥ 50 tahun', bobot: 2, override: false },
  { id: 'F02', name: 'Riwayat keluarga derajat 1 (Ibu/Saudari/Anak)', bobot: 3, override: false },
  { id: 'F03', name: 'Riwayat Ca payudara pribadi', bobot: 4, override: true },
  { id: 'F04', name: 'Mutasi gen BRCA1/BRCA2 diketahui', bobot: 5, override: true },
  { id: 'F05', name: 'Menarche dini (< 12 tahun)', bobot: 1, override: false },
  { id: 'F06', name: 'Menopause terlambat (> 55 tahun)', bobot: 1, override: false },
  { id: 'F07', name: 'Nulipara / anak 1 > 30 tahun', bobot: 1, override: false },
  { id: 'F08', name: 'Tidak pernah menyusui ASI', bobot: 1, override: false },
  { id: 'F09', name: 'Kontrasepsi hormonal / TSH jangka panjang', bobot: 1, override: false },
  { id: 'F10', name: 'Obesitas (IMT ≥ 30.0 kg/m²)', bobot: 1, override: false },
  { id: 'F11', name: 'Konsumsi minuman beralkohol rutin', bobot: 1, override: false },
  { id: 'F12', name: 'Aktivitas fisik / olahraga kurang', bobot: 1, override: false },
  { id: 'F13', name: 'Riwayat biopsi hiperplasia atipikal', bobot: 3, override: false },
  { id: 'F14', name: 'Riwayat radioterapi di area dada', bobot: 3, override: true },
  { id: 'F15', name: 'Densitas payudara BI-RADS C atau D', bobot: 2, override: false }
];

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('simForm');
  form.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', runSimulation);
    el.addEventListener('change', runSimulation);
  });

  const statusMeno = document.getElementById('sim_status_menopause');
  statusMeno.addEventListener('change', function() {
    const box = document.getElementById('sim_usia_menopause_box');
    if (this.value === 'Sudah menopause') {
      box.style.display = 'block';
    } else {
      box.style.display = 'none';
      document.getElementById('sim_usia_menopause').value = '';
    }
    runSimulation();
  });

  runSimulation();
});

function loadScenario(key) {
  if (!key || !PRESETS[key]) return;
  const p = PRESETS[key];
  document.getElementById('sim_usia').value = p.usia;
  document.getElementById('sim_menarche').value = p.menarche;
  document.getElementById('sim_status_menopause').value = p.status_menopause;
  
  const menoBox = document.getElementById('sim_usia_menopause_box');
  if (p.status_menopause === 'Sudah menopause') {
    menoBox.style.display = 'block';
    document.getElementById('sim_usia_menopause').value = p.usia_menopause;
  } else {
    menoBox.style.display = 'none';
    document.getElementById('sim_usia_menopause').value = '';
  }

  document.getElementById('sim_bb').value = p.bb;
  document.getElementById('sim_tb').value = p.tb;
  document.getElementById('sim_kerabat').value = p.kerabat;
  document.getElementById('sim_alkohol').value = p.alkohol;
  document.getElementById('sim_aktivitas').value = p.aktivitas;
  document.getElementById('sim_densitas').value = p.densitas;

  document.getElementById('sim_ca_pribadi').checked = p.ca_pribadi;
  document.getElementById('sim_brca').checked = p.brca;
  document.getElementById('sim_nulipara').checked = p.nulipara;
  document.getElementById('sim_tidak_menyusui').checked = p.tidak_menyusui;
  document.getElementById('sim_kb_tsh').checked = p.kb_tsh;
  document.getElementById('sim_biopsi_atipikal').checked = p.biopsi_atipikal;
  document.getElementById('sim_radioterapi').checked = p.radioterapi;

  runSimulation();
}

function resetSimulasiForm() {
  document.getElementById('scenarioPreset').value = '';
  document.getElementById('simForm').reset();
  document.getElementById('sim_usia_menopause_box').style.display = 'none';
  runSimulation();
}

function runSimulation() {
  const usia = parseFloat(document.getElementById('sim_usia').value) || 0;
  const bb = parseFloat(document.getElementById('sim_bb').value) || 0;
  const tb = parseFloat(document.getElementById('sim_tb').value) || 0;
  const menarche = parseFloat(document.getElementById('sim_menarche').value) || 0;
  const statusMeno = document.getElementById('sim_status_menopause').value;
  const usiaMeno = parseFloat(document.getElementById('sim_usia_menopause').value) || 0;
  const kerabat = document.getElementById('sim_kerabat').value;
  const alkohol = document.getElementById('sim_alkohol').value;
  const aktivitas = document.getElementById('sim_aktivitas').value;
  const densitas = document.getElementById('sim_densitas').value;

  const caPribadi = document.getElementById('sim_ca_pribadi').checked;
  const brca = document.getElementById('sim_brca').checked;
  const nulipara = document.getElementById('sim_nulipara').checked;
  const tidakMenyusui = document.getElementById('sim_tidak_menyusui').checked;
  const kbTsh = document.getElementById('sim_kb_tsh').checked;
  const biopsiAtipikal = document.getElementById('sim_biopsi_atipikal').checked;
  const radioterapi = document.getElementById('sim_radioterapi').checked;

  // IMT Calculation
  let imt = 0;
  if (tb > 0 && bb > 0) {
    const tbM = tb / 100;
    imt = parseFloat((bb / (tbM * tbM)).toFixed(1));
    document.getElementById('sim_imt_display').innerText = imt;

    const b = document.getElementById('sim_imt_badge');
    if (imt < 18.5) { b.innerText = 'Kurus (< 18.5)'; b.className = 'badge badge-navy'; }
    else if (imt < 25) { b.innerText = 'Normal (18.5-24.9)'; b.className = 'badge badge-low'; }
    else if (imt < 30) { b.innerText = 'Kelebihan BB'; b.className = 'badge badge-med'; }
    else { b.innerText = 'Obesitas (≥ 30.0)'; b.className = 'badge badge-high'; }
  } else {
    document.getElementById('sim_imt_display').innerText = '-';
  }

  // Evaluate Factors
  const fStatus = {
    F01: (usia >= 50) ? 1 : 0,
    F02: (['Ibu', 'Saudari kandung', 'Anak'].includes(kerabat)) ? 1 : 0,
    F03: caPribadi ? 1 : 0,
    F04: brca ? 1 : 0,
    F05: (menarche > 0 && menarche < 12) ? 1 : 0,
    F06: (statusMeno === 'Sudah menopause' && usiaMeno > 55) ? 1 : 0,
    F07: nulipara ? 1 : 0,
    F08: tidakMenyusui ? 1 : 0,
    F09: kbTsh ? 1 : 0,
    F10: (imt >= 30) ? 1 : 0,
    F11: (alkohol === 'Rutin') ? 1 : 0,
    F12: (aktivitas === 'Kurang') ? 1 : 0,
    F13: biopsiAtipikal ? 1 : 0,
    F14: radioterapi ? 1 : 0,
    F15: (['C', 'D'].includes(densitas)) ? 1 : 0
  };

  let totalScore = 0;
  let override = false;
  let tbodyHtml = '';

  FACTORS.forEach(f => {
    const isMet = (fStatus[f.id] === 1);
    const score = isMet ? f.bobot : 0;
    totalScore += score;
    if (isMet && f.override) override = true;

    const statusBadge = isMet 
      ? '<span class="badge badge-high" style="font-size:11px;">1 (Ya)</span>' 
      : '<span class="badge badge-secondary" style="font-size:11px;">0 (Tidak)</span>';

    tbodyHtml += `
      <tr style="${isMet ? 'background:#F8FAFC;' : ''}">
        <td><strong style="color:var(--navy-900); font-family:monospace;">${f.id}</strong></td>
        <td>
          <span style="font-weight:600; color:${isMet ? 'var(--navy-900)' : 'var(--text-secondary)'};">
            ${f.name}
          </span>
          ${f.override ? '<span class="badge badge-override" style="font-size:9.5px; margin-left:4px;">Override</span>' : ''}
        </td>
        <td style="text-align:center;">${statusBadge}</td>
        <td style="text-align:center; font-weight:700; color:var(--text-muted);">${f.bobot}</td>
        <td style="text-align:right; font-weight:800; color:${isMet ? 'var(--blue-primary)' : 'var(--text-light)'};">${score}</td>
      </tr>
    `;
  });

  document.getElementById('simEvaluationBody').innerHTML = tbodyHtml;
  document.getElementById('simTotalScoreFooter').innerText = totalScore;
  document.getElementById('simScoreDisplay').innerText = totalScore;

  // Inference Category & Recommendation
  let category = 'Rendah';
  let catClass = 'badge-low';
  let reco = 'Edukasi dan SADARI rutin setiap bulan; pemeriksaan klinis payudara (SADANIS) berkala; ulangi skrining sesuai jadwal.';

  if (override || totalScore >= 8) {
    category = 'Tinggi';
    catClass = 'badge-high';
    reco = 'Rujuk ke dokter spesialis untuk evaluasi lanjut (mis. mamografi/USG, konseling genetik bila diperlukan).';
  } else if (totalScore >= 4) {
    category = 'Sedang';
    catClass = 'badge-med';
    reco = 'SADARI + SADANIS oleh tenaga kesehatan secara berkala; pertimbangkan USG/mamografi sesuai usia dan saran dokter.';
  }

  const catBadge = document.getElementById('simCategoryBadge');
  catBadge.innerText = 'RISIKO ' + category.toUpperCase();
  catBadge.className = 'live-risk-badge ' + catClass;

  document.getElementById('simRecommendationText').innerText = reco;

  const ovBanner = document.getElementById('simOverrideBanner');
  if (override) {
    ovBanner.style.display = 'block';
  } else {
    ovBanner.style.display = 'none';
  }
}

function prepareTransfer() {
  document.getElementById('trans_usia').value = document.getElementById('sim_usia').value;
  document.getElementById('trans_bb').value = document.getElementById('sim_bb').value;
  document.getElementById('trans_tb').value = document.getElementById('sim_tb').value;
  document.getElementById('trans_status_menopause').value = document.getElementById('sim_status_menopause').value;
  document.getElementById('trans_usia_menopause').value = document.getElementById('sim_usia_menopause').value;

  document.getElementById('trans_f_F01').value = document.getElementById('sim_usia').value;
  document.getElementById('trans_f_F02').value = document.getElementById('sim_kerabat').value;
  document.getElementById('trans_f_F03').value = document.getElementById('sim_ca_pribadi').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F04').value = document.getElementById('sim_brca').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F05').value = document.getElementById('sim_menarche').value;
  document.getElementById('trans_f_F06').value = document.getElementById('sim_usia_menopause').value;
  document.getElementById('trans_f_F07').value = document.getElementById('sim_nulipara').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F08').value = document.getElementById('sim_tidak_menyusui').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F09').value = document.getElementById('sim_kb_tsh').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F10').value = document.getElementById('sim_imt_display').innerText;
  document.getElementById('trans_f_F11').value = document.getElementById('sim_alkohol').value;
  document.getElementById('trans_f_F12').value = document.getElementById('sim_aktivitas').value;
  document.getElementById('trans_f_F13').value = document.getElementById('sim_biopsi_atipikal').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F14').value = document.getElementById('sim_radioterapi').checked ? 'Ya' : 'Tidak';
  document.getElementById('trans_f_F15').value = document.getElementById('sim_densitas').value;
}
</script>
