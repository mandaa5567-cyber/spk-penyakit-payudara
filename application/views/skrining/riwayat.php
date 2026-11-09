<!-- Riwayat Skrining (FR-07) -->

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Riwayat Skrining Risiko Kanker Payudara
      </div>
      <div class="card-header-subtitle">Daftar rekam jejak evaluasi klinis dan inferensi rule-based reasoning</div>
    </div>
    <div style="display:flex; gap:8px;">
      <a href="<?= base_url('rekap/export_matrix_csv'); ?>" class="btn btn-navy-outline btn-sm" title="Unduh Register Data Pasien format Sheet Data_Pasien Excel">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Ekspor Matriks CSV
      </a>
      <a href="<?= base_url('skrining/baru'); ?>" class="btn btn-primary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Skrining Baru</span>
      </a>
    </div>
  </div>

  <div class="card-body" style="border-bottom: 1px solid var(--border-light); padding: 18px 24px; background: var(--bg-subtle);">
    <form action="<?= base_url('skrining/riwayat'); ?>" method="GET" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
      
      <!-- Filter Categories -->
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="font-size:13px; font-weight:700; color:var(--navy-900);">Kategori:</span>
        <a href="<?= base_url('skrining/riwayat' . ($search ? '?q='.$search : '')); ?>" class="btn <?= empty($filter_kategori) ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">
          Semua (<?= $stats['total_skrining']; ?>)
        </a>
        <a href="<?= base_url('skrining/riwayat?kategori=Tinggi' . ($search ? '&q='.$search : '')); ?>" class="btn <?= ($filter_kategori === 'Tinggi') ? 'btn-danger' : 'btn-secondary'; ?> btn-sm">
          Tinggi (<?= $stats['total_tinggi']; ?>)
        </a>
        <a href="<?= base_url('skrining/riwayat?kategori=Sedang' . ($search ? '&q='.$search : '')); ?>" class="btn <?= ($filter_kategori === 'Sedang') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">
          Sedang (<?= $stats['total_sedang']; ?>)
        </a>
        <a href="<?= base_url('skrining/riwayat?kategori=Rendah' . ($search ? '&q='.$search : '')); ?>" class="btn <?= ($filter_kategori === 'Rendah') ? 'btn-success' : 'btn-secondary'; ?> btn-sm">
          Rendah (<?= $stats['total_rendah']; ?>)
        </a>
      </div>

      <!-- Search Input -->
      <div style="display:flex; gap:8px; min-width:280px;">
        <input type="text" name="q" class="form-control" placeholder="Cari nama pasien atau No. RM..." value="<?= htmlspecialchars($search ?? ''); ?>" style="background:#FFFFFF;">
        <button type="submit" class="btn btn-navy-outline btn-sm">
          Cari
        </button>
        <?php if (!empty($search) || !empty($filter_kategori)): ?>
          <a href="<?= base_url('skrining/riwayat'); ?>" class="btn btn-secondary btn-sm" title="Reset filter">
            Reset
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Tanggal & Jam</th>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>IMT</th>
          <th>Total Skor</th>
          <th>Override</th>
          <th>Kategori Risiko</th>
          <th>Petugas</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($riwayat_list)): ?>
          <?php foreach ($riwayat_list as $row): ?>
            <tr>
              <td>
                <div style="font-weight:700; color:var(--navy-900);"><?= date('d M Y', strtotime($row['tanggal'])); ?></div>
                <div style="font-size:11.5px; color:var(--text-muted);"><?= date('H:i', strtotime($row['tanggal'])); ?> WIB</div>
              </td>
              <td>
                <span class="badge badge-navy" style="font-family:monospace; font-size:12.5px;">
                  <?= htmlspecialchars($row['no_rm']); ?>
                </span>
              </td>
              <td>
                <a href="<?= base_url('pasien/detail/' . $row['id_pasien']); ?>" style="font-weight:700; color:var(--navy-900);">
                  <?= htmlspecialchars($row['nama_pasien']); ?>
                </a>
                <div style="font-size:11.5px; color:var(--text-muted);">
                  <?= htmlspecialchars($row['alamat'] ?? '-'); ?>
                </div>
              </td>
              <td>
                <span style="font-weight:600; color:var(--navy-800);">
                  <?= $row['imt'] ?? '-'; ?>
                </span>
              </td>
              <td>
                <span style="font-size:16px; font-weight:900; color:var(--navy-900);">
                  <?= (int)$row['total_skor']; ?>
                </span>
                <span style="font-size:11.5px; color:var(--text-muted);">/ 30</span>
              </td>
              <td>
                <?php if ($row['override_aktif']): ?>
                  <span class="badge badge-override" title="Kategori otomatis Tinggi via aturan override">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Aktif
                  </span>
                <?php else: ?>
                  <span style="color:var(--text-light); font-size:12px;">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $cat = $row['nama_kategori'] ?? 'Rendah';
                  $badgeClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
                ?>
                <span class="badge <?= $badgeClass; ?>">
                  <?= htmlspecialchars($cat); ?>
                </span>
              </td>
              <td>
                <div style="font-size:13px; color:var(--navy-800); font-weight:500;">
                  <?= htmlspecialchars($row['nama_petugas'] ?? '-'); ?>
                </div>
              </td>
              <td style="text-align: right;">
                <div style="display:inline-flex; gap:6px;">
                  <a href="<?= base_url('skrining/hasil/' . $row['id_skrining']); ?>" class="btn btn-secondary btn-sm" title="Lihat Rekam Skrining">
                    Detail
                  </a>
                  <a href="<?= base_url('skrining/cetak/' . $row['id_skrining']); ?>" target="_blank" class="btn btn-primary btn-sm" title="Cetak Hasil">
                    Cetak
                  </a>
                  <?php if ($this->session->userdata('role') === 'admin'): ?>
                    <a href="<?= base_url('skrining/hapus/' . $row['id_skrining']); ?>" class="btn btn-secondary btn-sm" style="color:#DC2626;" onclick="return confirm('Apakah Anda yakin ingin menghapus data skrining ini?')" title="Hapus Data">
                      Hapus
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">
              Tidak ada data riwayat skrining yang cocok dengan kriteria pencarian.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
