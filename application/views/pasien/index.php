<!-- Manajemen Data Pasien (FR-02) -->

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Master Data Pasien Skrining
      </div>
      <div class="card-header-subtitle">Daftar pasien terdaftar, nomor rekam medis, dan histori skrining</div>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal('modalTambahPasien')">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>Tambah Pasien Baru</span>
    </button>
  </div>

  <!-- Search & Filter Bar -->
  <div class="card-body" style="border-bottom: 1px solid var(--border-light); padding: 18px 24px; background: var(--bg-subtle);">
    <form action="<?= base_url('pasien'); ?>" method="GET" style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
      <div style="font-size:13px; font-weight:700; color:var(--navy-900);">
        Total: <?= count($pasien_list); ?> Pasien Terdata
      </div>
      <div style="display:flex; gap:8px; min-width:320px;">
        <input type="text" name="q" class="form-control" placeholder="Cari nama, No. RM, atau alamat..." value="<?= htmlspecialchars($search ?? ''); ?>" style="background:#FFFFFF;">
        <button type="submit" class="btn btn-navy-outline btn-sm">Cari</button>
        <?php if (!empty($search)): ?>
          <a href="<?= base_url('pasien'); ?>" class="btn btn-secondary btn-sm">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>Tanggal Lahir / Usia</th>
          <th>Alamat Domisili</th>
          <th style="text-align: center;">Total Skrining</th>
          <th>Status Terakhir</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pasien_list)): ?>
          <?php foreach ($pasien_list as $p): ?>
            <?php
              $usia = null;
              if (!empty($p['tanggal_lahir'])) {
                $dob = new DateTime($p['tanggal_lahir']);
                $now = new DateTime();
                $usia = $now->diff($dob)->y;
              }
            ?>
            <tr>
              <td>
                <span class="badge badge-navy" style="font-family:monospace; font-size:13px; font-weight:700;">
                  <?= htmlspecialchars($p['no_rm']); ?>
                </span>
              </td>
              <td>
                <a href="<?= base_url('pasien/detail/' . $p['id_pasien']); ?>" style="font-weight:700; color:var(--navy-900);">
                  <?= htmlspecialchars($p['nama']); ?>
                </a>
              </td>
              <td>
                <?php if ($p['tanggal_lahir']): ?>
                  <div style="font-weight:600; color:var(--navy-800);"><?= date('d M Y', strtotime($p['tanggal_lahir'])); ?></div>
                  <div style="font-size:11.5px; color:var(--text-muted);"><?= $usia; ?> Tahun</div>
                <?php else: ?>
                  <span style="color:var(--text-light);">-</span>
                <?php endif; ?>
              </td>
              <td>
                <span style="font-size:13px; color:var(--text-secondary);">
                  <?= htmlspecialchars($p['alamat'] ?? '-'); ?>
                </span>
              </td>
              <td style="text-align: center;">
                <span class="badge badge-navy" style="font-size:12px;">
                  <?= (int)$p['total_skrining']; ?>x
                </span>
              </td>
              <td>
                <?php if (!empty($p['kategori_terakhir'])): ?>
                  <?php
                    $cat = $p['kategori_terakhir'];
                    $bClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
                  ?>
                  <span class="badge <?= $bClass; ?>">
                    <?= htmlspecialchars($cat); ?>
                  </span>
                <?php else: ?>
                  <span style="font-size:12px; color:var(--text-light);">Belum Diskrining</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display:inline-flex; gap:6px;">
                  <a href="<?= base_url('skrining/baru/' . $p['id_pasien']); ?>" class="btn btn-primary btn-sm" title="Mulai Skrining untuk Pasien Ini">
                    Skrining
                  </a>
                  <a href="<?= base_url('pasien/detail/' . $p['id_pasien']); ?>" class="btn btn-secondary btn-sm" title="Lihat Rekam Medis">
                    Profil
                  </a>
                  <button type="button" class="btn btn-secondary btn-sm" onclick='editPasien(<?= json_encode($p); ?>)' title="Ubah Data">
                    Edit
                  </button>
                  <a href="<?= base_url('pasien/hapus/' . $p['id_pasien']); ?>" class="btn btn-secondary btn-sm" style="color:#DC2626;" onclick="return confirm('Hapus data pasien <?= htmlspecialchars($p['nama']); ?>? Seluruh riwayat skrining pasien ini juga akan terhapus.')" title="Hapus">
                    Hapus
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
              Tidak ada data pasien yang ditemukan.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Pasien Baru -->
<div id="modalTambahPasien" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('pasien/simpan'); ?>" method="POST">
      <div class="modal-header">
        <div class="modal-title">Tambah Data Pasien Baru</div>
        <button type="button" class="modal-close" onclick="closeModal('modalTambahPasien')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="no_rm">Nomor Rekam Medis (No. RM)</label>
          <input type="text" name="no_rm" id="no_rm" class="form-control" value="<?= htmlspecialchars($next_no_rm); ?>" required>
          <div class="form-hint">Dibuat otomatis oleh sistem, dapat diubah sesuai format rumah sakit.</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="nama">Nama Lengkap Pasien <span class="required">*</span></label>
          <input type="text" name="nama" id="nama" class="form-control" placeholder="Contoh: Ny. Kartika Dewi" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control">
          <div class="form-hint">Digunakan untuk menghitung usia pasien saat skrining.</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="alamat">Alamat Lengkap & Kota Domisili</label>
          <textarea name="alamat" id="alamat" class="form-control" placeholder="Masukkan alamat lengkap pasien..."></textarea>
        </div>

        <div style="margin-top:10px;">
          <label class="custom-checkbox">
            <input type="checkbox" name="start_screening" value="1" checked>
            <span style="font-size:13px; font-weight:600; color:var(--navy-900);">Langsung lanjutkan ke formulir skrining setelah simpan</span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahPasien')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Pasien</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Pasien -->
<div id="modalEditPasien" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('pasien/simpan'); ?>" method="POST">
      <input type="hidden" name="id_pasien" id="edit_id_pasien">
      <div class="modal-header">
        <div class="modal-title">Ubah Data Pasien</div>
        <button type="button" class="modal-close" onclick="closeModal('modalEditPasien')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="edit_no_rm">Nomor Rekam Medis (No. RM)</label>
          <input type="text" name="no_rm" id="edit_no_rm" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_nama">Nama Lengkap Pasien <span class="required">*</span></label>
          <input type="text" name="nama" id="edit_nama" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_tanggal_lahir">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_alamat">Alamat Lengkap</label>
          <textarea name="alamat" id="edit_alamat" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditPasien')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editPasien(p) {
  document.getElementById('edit_id_pasien').value = p.id_pasien;
  document.getElementById('edit_no_rm').value = p.no_rm;
  document.getElementById('edit_nama').value = p.nama;
  document.getElementById('edit_tanggal_lahir').value = p.tanggal_lahir || '';
  document.getElementById('edit_alamat').value = p.alamat || '';
  openModal('modalEditPasien');
}
</script>
