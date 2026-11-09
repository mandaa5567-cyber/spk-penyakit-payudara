<!-- Manajemen Pengguna & Hak Akses (FR-01) -->

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        Manajemen Akun Petugas & Hak Akses
      </div>
      <div class="card-header-subtitle">Daftar dokter spesialis dan tenaga kesehatan yang memiliki otorisasi sistem</div>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal('modalTambahUser')">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>Tambah Pengguna Baru</span>
    </button>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Nama Lengkap & Gelar</th>
          <th>Username</th>
          <th>Peran (Role)</th>
          <th>Hak Akses</th>
          <th>Terdaftar</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($user_list as $u): ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="user-avatar" style="width:34px; height:34px; font-size:12px;">
                  <?= strtoupper(substr($u['nama'], 0, 1)); ?>
                </div>
                <div>
                  <div style="font-weight:700; color:var(--navy-900);">
                    <?= htmlspecialchars($u['nama']); ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge badge-navy" style="font-family:monospace; font-size:12px;">
                <?= htmlspecialchars($u['username']); ?>
              </span>
            </td>
            <td>
              <?php if ($u['role'] === 'admin'): ?>
                <span class="badge" style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD;">
                  Administrator / Spesialis
                </span>
              <?php else: ?>
                <span class="badge" style="background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0;">
                  Tenaga Kesehatan (Nakes)
                </span>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-size:12.5px; color:var(--text-secondary);">
                <?php if ($u['role'] === 'admin'): ?>
                  Akses penuh: Skrining, Pasien, Basis Aturan & Manajemen Pengguna
                <?php else: ?>
                  Akses klinis: Skrining Baru, Data Pasien & Cetak Laporan
                <?php endif; ?>
              </div>
            </td>
            <td>
              <span style="font-size:12.5px; color:var(--text-muted);">
                <?= date('d M Y', strtotime($u['created_at'])); ?>
              </span>
            </td>
            <td style="text-align: right;">
              <div style="display:inline-flex; gap:6px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick='editUser(<?= json_encode($u); ?>)'>
                  Edit
                </button>
                <?php if ($u['id_user'] != $this->session->userdata('id_user')): ?>
                  <a href="<?= base_url('users/hapus/' . $u['id_user']); ?>" class="btn btn-secondary btn-sm" style="color:#DC2626;" onclick="return confirm('Hapus akun pengguna <?= htmlspecialchars($u['nama']); ?>?')">
                    Hapus
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah User -->
<div id="modalTambahUser" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('users/simpan'); ?>" method="POST">
      <div class="modal-header">
        <div class="modal-title">Tambah Pengguna Baru</div>
        <button type="button" class="modal-close" onclick="closeModal('modalTambahUser')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="add_nama">Nama Lengkap & Gelar <span class="required">*</span></label>
          <input type="text" name="nama" id="add_nama" class="form-control" placeholder="Contoh: dr. Annisa, Sp.B" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="add_username">Username Login <span class="required">*</span></label>
          <input type="text" name="username" id="add_username" class="form-control" placeholder="Contoh: dr_annisa" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="add_password">Kata Sandi <span class="required">*</span></label>
          <input type="password" name="password" id="add_password" class="form-control" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="add_role">Peran (Hak Akses) <span class="required">*</span></label>
          <select name="role" id="add_role" class="form-control">
            <option value="nakes">Tenaga Kesehatan (Nakes / Bidan / Perawat)</option>
            <option value="admin">Administrator / Dokter Spesialis</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahUser')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit User -->
<div id="modalEditUser" class="modal-backdrop">
  <div class="modal-dialog">
    <form action="<?= base_url('users/simpan'); ?>" method="POST">
      <input type="hidden" name="id_user" id="edit_u_id">
      <div class="modal-header">
        <div class="modal-title">Ubah Data Pengguna</div>
        <button type="button" class="modal-close" onclick="closeModal('modalEditUser')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="edit_u_nama">Nama Lengkap & Gelar <span class="required">*</span></label>
          <input type="text" name="nama" id="edit_u_nama" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_u_username">Username Login <span class="required">*</span></label>
          <input type="text" name="username" id="edit_u_username" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_u_password">Kata Sandi Baru</label>
          <input type="password" name="password" id="edit_u_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah sandi">
          <div class="form-hint">Hanya diisi jika ingin mereset kata sandi pengguna ini.</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_u_role">Peran (Hak Akses)</label>
          <select name="role" id="edit_u_role" class="form-control">
            <option value="nakes">Tenaga Kesehatan (Nakes / Bidan / Perawat)</option>
            <option value="admin">Administrator / Dokter Spesialis</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditUser')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editUser(u) {
  document.getElementById('edit_u_id').value = u.id_user;
  document.getElementById('edit_u_nama').value = u.nama;
  document.getElementById('edit_u_username').value = u.username;
  document.getElementById('edit_u_password').value = '';
  document.getElementById('edit_u_role').value = u.role;
  openModal('modalEditUser');
}
</script>
