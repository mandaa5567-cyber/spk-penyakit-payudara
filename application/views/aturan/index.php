<!-- Manajemen Basis Aturan & Bobot (FR-06) -->

<div class="card" style="margin-bottom: 24px;">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        Manajemen Parameter Faktor Risiko & Basis Aturan (Rule Base)
      </div>
      <div class="card-header-subtitle">Administrator dapat mengubah bobot, nilai ambang, dan status override secara dinamis tanpa merubah kode</div>
    </div>
    <span class="badge badge-navy">15 Parameter Aktif</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Faktor Risiko</th>
          <th>Tipe Input</th>
          <th>Kriteria (Operator & Ambang / Pemicu)</th>
          <th style="text-align: center;">Bobot</th>
          <th style="text-align: center;">Override Tinggi</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($faktor_list as $f): ?>
          <tr>
            <td>
              <strong style="color:var(--navy-700); font-family:monospace; font-size:13.5px;">
                <?= htmlspecialchars($f['id_faktor']); ?>
              </strong>
            </td>
            <td>
              <div style="font-weight:700; color:var(--navy-900);">
                <?= htmlspecialchars($f['nama_faktor']); ?>
              </div>
            </td>
            <td>
              <span class="badge badge-navy" style="font-size:11px;">
                <?= htmlspecialchars($f['tipe_input']); ?>
              </span>
            </td>
            <td>
              <?php if ($f['tipe_input'] === 'angka'): ?>
                <span style="font-weight:600; color:var(--navy-800);">
                  <?= htmlspecialchars($f['operator']); ?> <?= (float)$f['ambang']; ?>
                </span>
              <?php else: ?>
                <span style="font-size:12.5px; color:var(--text-secondary);">
                  <?= htmlspecialchars($f['nilai_pemicu']); ?>
                </span>
              <?php endif; ?>
            </td>
            <td style="text-align: center;">
              <span style="font-size:16px; font-weight:900; color:var(--blue-primary);">
                <?= (int)$f['bobot']; ?>
              </span>
            </td>
            <td style="text-align: center;">
              <?php if ($f['override_tinggi']): ?>
                <span class="badge badge-override" style="font-size:11px;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  Ya (Override)
                </span>
              <?php else: ?>
                <span style="color:var(--text-light); font-size:12px;">Tidak</span>
              <?php endif; ?>
            </td>
            <td style="text-align: right;">
              <button type="button" class="btn btn-secondary btn-sm" onclick='editFaktor(<?= json_encode($f); ?>)'>
                Ubah Parameter
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Kategori Risiko & Rekomendasi Section -->
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Ambang Batas Kategori Risiko & Teks Rekomendasi
      </div>
      <div class="card-header-subtitle">Pengaturan batas skor minimum dan narasi rekomendasi klinis</div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Kategori Risiko</th>
          <th style="width: 140px; text-align: center;">Skor Minimum</th>
          <th>Teks Rekomendasi Tindak Lanjut Klinis</th>
          <th style="text-align: right; width: 120px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kategori_list as $k): ?>
          <?php
            $cat = $k['nama_kategori'];
            $bClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
          ?>
          <tr>
            <td>
              <span class="badge <?= $bClass; ?>" style="font-size:13px; font-weight:700;">
                <?= htmlspecialchars($cat); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <span style="font-size:16px; font-weight:800; color:var(--navy-900);">
                &ge; <?= (int)$k['skor_min']; ?> Poin
              </span>
            </td>
            <td>
              <div style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                <?= htmlspecialchars($k['rekomendasi']); ?>
              </div>
            </td>
            <td style="text-align: right;">
              <button type="button" class="btn btn-secondary btn-sm" onclick='editKategori(<?= json_encode($k); ?>)'>
                Ubah
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Basis Aturan IF-THEN (Forward Chaining - Sheet Aturan Excel) -->
<div class="card" style="margin-top: 24px; border-top: 4px solid var(--blue-accent);">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Daftar 20 Basis Aturan (Rule Base) – Forward Chaining
      </div>
      <div class="card-header-subtitle">Mesin inferensi CDSS: Fakta Pasien &rarr; Evaluasi Aturan IF-THEN &rarr; Agregasi Skor / Override &rarr; Kategori Risiko &rarr; Rekomendasi</div>
    </div>
    <span class="badge badge-navy">20 Aturan Terverifikasi</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th style="width: 80px;">ID Aturan</th>
          <th style="width: 170px;">Tahap Inferensi</th>
          <th>Kondisi (IF)</th>
          <th>Kesimpulan (THEN)</th>
          <th style="text-align: center; width: 80px;">Bobot</th>
          <th style="text-align: center; width: 110px;">Override</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rules_list as $r): ?>
          <?php
            $isOverride = ($r['override'] === 'Ya');
            $rowBg = $isOverride ? 'background:#FEF2F2;' : '';
          ?>
          <tr style="<?= $rowBg; ?>">
            <td>
              <strong style="color:var(--navy-900); font-family:monospace; font-size:13.5px;">
                <?= htmlspecialchars($r['id']); ?>
              </strong>
            </td>
            <td>
              <?php if ($r['tahap'] === 'Override' || strpos($r['tahap'], 'Override') !== false): ?>
                <span class="badge badge-override" style="font-size:11px;">
                  <?= htmlspecialchars($r['tahap']); ?>
                </span>
              <?php elseif ($r['tahap'] === 'Agregasi'): ?>
                <span class="badge badge-navy" style="font-size:11px;">Agregasi</span>
              <?php elseif ($r['tahap'] === 'Klasifikasi'): ?>
                <span class="badge badge-low" style="font-size:11px;">Klasifikasi</span>
              <?php else: ?>
                <span class="badge badge-secondary" style="font-size:11px;">Penilaian Faktor</span>
              <?php endif; ?>
            </td>
            <td>
              <code style="font-size:12.5px; font-weight:600; color:var(--navy-900); background:#F1F5F9; padding:3px 8px; border-radius:4px;">
                <?= htmlspecialchars($r['kondisi']); ?>
              </code>
            </td>
            <td>
              <span style="font-size:13px; font-weight:600; color:<?= $isOverride ? '#B91C1C' : 'var(--text-secondary)'; ?>;">
                &rarr; <?= htmlspecialchars($r['kesimpulan']); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <?php if ($r['bobot'] !== '-'): ?>
                <strong style="color:var(--blue-primary); font-size:14px;">+<?= $r['bobot']; ?></strong>
              <?php else: ?>
                <span style="color:var(--text-light);">-</span>
              <?php endif; ?>
            </td>
            <td style="text-align: center;">
              <?php if ($isOverride): ?>
                <span class="badge badge-override" style="font-size:11px;">Ya</span>
              <?php else: ?>
                <span style="color:var(--text-light); font-size:12px;">Tidak</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Kebutuhan Sistem (Sheet Kebutuhan & Ringkasan Excel) -->
<div class="card" style="margin-top: 24px;">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        Matriks Kebutuhan Sistem (FR &amp; NFR) Sesuai Spesifikasi CDSS
      </div>
      <div class="card-header-subtitle">Daftar kebutuhan fungsional (FR-01 s/d FR-09) dan non-fungsional (NFR-01 s/d NFR-05) hasil analisis sistem</div>
    </div>
    <span class="badge badge-navy">14 Kebutuhan Terpenuhi</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th style="width: 80px;">ID</th>
          <th style="width: 140px;">Jenis</th>
          <th>Kebutuhan</th>
          <th>Deskripsi</th>
          <th style="width: 130px;">Aktor</th>
          <th style="text-align: center; width: 100px;">Prioritas</th>
          <th style="text-align: center; width: 100px;">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($kebutuhan_list as $req): ?>
          <tr>
            <td>
              <strong style="color:var(--navy-900); font-family:monospace; font-size:13px;">
                <?= htmlspecialchars($req['id']); ?>
              </strong>
            </td>
            <td>
              <span class="badge <?= ($req['jenis'] === 'Fungsional') ? 'badge-navy' : 'badge-secondary'; ?>" style="font-size:11px;">
                <?= htmlspecialchars($req['jenis']); ?>
              </span>
            </td>
            <td>
              <div style="font-weight:700; color:var(--navy-900); font-size:13px;">
                <?= htmlspecialchars($req['kebutuhan']); ?>
              </div>
            </td>
            <td>
              <div style="font-size:12.5px; color:var(--text-secondary); line-height:1.4;">
                <?= htmlspecialchars($req['deskripsi']); ?>
              </div>
            </td>
            <td>
              <span style="font-size:12px; font-weight:600; color:var(--navy-800);">
                <?= htmlspecialchars($req['aktor']); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <?php
                $pClass = ($req['prioritas'] === 'Tinggi') ? 'badge-high' : (($req['prioritas'] === 'Sedang') ? 'badge-med' : 'badge-navy');
              ?>
              <span class="badge <?= $pClass; ?>" style="font-size:11px;">
                <?= htmlspecialchars($req['prioritas']); ?>
              </span>
            </td>
            <td style="text-align: center;">
              <span class="badge badge-low" style="font-size:11px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                Aktif
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Edit Faktor -->
<div id="modalEditFaktor" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('aturan/update_faktor'); ?>" method="POST">
      <input type="hidden" name="id_faktor" id="edit_f_id">
      <div class="modal-header">
        <div class="modal-title" id="modalFaktorTitle">Ubah Parameter Faktor</div>
        <button type="button" class="modal-close" onclick="closeModal('modalEditFaktor')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="edit_f_nama">Nama Faktor Risiko</label>
          <input type="text" name="nama_faktor" id="edit_f_nama" class="form-control" required>
        </div>

        <div class="grid-2" style="gap:12px;">
          <div class="form-group">
            <label class="form-label" for="edit_f_tipe">Tipe Input</label>
            <select name="tipe_input" id="edit_f_tipe" class="form-control" onchange="toggleFaktorInputFields(this.value)">
              <option value="angka">Angka</option>
              <option value="pilihan">Pilihan</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="edit_f_bobot">Bobot Skor (1 - 5)</label>
            <input type="number" name="bobot" id="edit_f_bobot" class="form-control" min="1" max="10" required>
          </div>
        </div>

        <div id="wrapper_angka" class="grid-2" style="gap:12px;">
          <div class="form-group">
            <label class="form-label" for="edit_f_operator">Operator Pembanding</label>
            <select name="operator" id="edit_f_operator" class="form-control">
              <option value=">=">&gt;=</option>
              <option value=">">&gt;</option>
              <option value="<=">&lt;=</option>
              <option value="<">&lt;</option>
              <option value="=">=</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="edit_f_ambang">Nilai Ambang</label>
            <input type="number" step="0.1" name="ambang" id="edit_f_ambang" class="form-control">
          </div>
        </div>

        <div id="wrapper_pilihan" class="form-group">
          <label class="form-label" for="edit_f_pemicu">Nilai Pemicu (Koma untuk lebih dari satu)</label>
          <input type="text" name="nilai_pemicu" id="edit_f_pemicu" class="form-control" placeholder="Contoh: Ibu,Saudari kandung,Anak">
          <div class="form-hint">Faktor akan bernilai 1 (terpenuhi) jika input pasien cocok dengan salah satu nilai ini.</div>
        </div>

        <div class="form-group" style="margin-top:14px;">
          <label class="custom-checkbox">
            <input type="checkbox" name="override_tinggi" id="edit_f_override" value="1">
            <div>
              <span style="font-size:13.5px; font-weight:700; color:#DC2626;">Aktifkan Status Override Tinggi (Aturan R16)</span>
              <div class="form-hint">Jika dicentang, apabila faktor ini terpenuhi maka pasien langsung diklasifikasikan sebagai Risiko Tinggi.</div>
            </div>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditFaktor')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Parameter</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditKategori" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('aturan/update_kategori'); ?>" method="POST">
      <input type="hidden" name="id_kategori" id="edit_k_id">
      <div class="modal-header">
        <div class="modal-title" id="modalKategoriTitle">Ubah Kategori Risiko</div>
        <button type="button" class="modal-close" onclick="closeModal('modalEditKategori')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kategori</label>
          <input type="text" id="edit_k_nama" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_k_skor_min">Skor Minimum</label>
          <input type="number" name="skor_min" id="edit_k_skor_min" class="form-control" min="0" max="30" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_k_rekomendasi">Teks Rekomendasi Klinis</label>
          <textarea name="rekomendasi" id="edit_k_rekomendasi" class="form-control" rows="4" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditKategori')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
      </div>
    </form>
  </div>
</div>

<script>
function editFaktor(f) {
  document.getElementById('modalFaktorTitle').innerText = 'Ubah Parameter ' + f.id_faktor + ' - ' + f.nama_faktor;
  document.getElementById('edit_f_id').value = f.id_faktor;
  document.getElementById('edit_f_nama').value = f.nama_faktor;
  document.getElementById('edit_f_tipe').value = f.tipe_input;
  document.getElementById('edit_f_bobot').value = f.bobot;
  document.getElementById('edit_f_operator').value = f.operator || '>=';
  document.getElementById('edit_f_ambang').value = f.ambang || '';
  document.getElementById('edit_f_pemicu').value = f.nilai_pemicu || '';
  document.getElementById('edit_f_override').checked = (parseInt(f.override_tinggi) === 1);
  toggleFaktorInputFields(f.tipe_input);
  openModal('modalEditFaktor');
}

function toggleFaktorInputFields(type) {
  if (type === 'angka') {
    document.getElementById('wrapper_angka').style.display = 'grid';
    document.getElementById('wrapper_pilihan').style.display = 'none';
  } else {
    document.getElementById('wrapper_angka').style.display = 'none';
    document.getElementById('wrapper_pilihan').style.display = 'block';
  }
}

function editKategori(k) {
  document.getElementById('modalKategoriTitle').innerText = 'Ubah Kategori: ' + k.nama_kategori;
  document.getElementById('edit_k_id').value = k.id_kategori;
  document.getElementById('edit_k_nama').value = k.nama_kategori;
  document.getElementById('edit_k_skor_min').value = k.skor_min;
  document.getElementById('edit_k_rekomendasi').value = k.rekomendasi;
  openModal('modalEditKategori');
}
</script>
