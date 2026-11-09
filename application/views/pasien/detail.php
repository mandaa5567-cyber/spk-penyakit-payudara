<?php
  $usia = null;
  if (!empty($pasien['tanggal_lahir'])) {
    $dob = new DateTime($pasien['tanggal_lahir']);
    $now = new DateTime();
    $usia = $now->diff($dob)->y;
  }
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <div>
    <a href="<?= base_url('pasien'); ?>" class="btn btn-secondary btn-sm">
      &larr; Kembali ke Data Pasien
    </a>
  </div>
  <div>
    <a href="<?= base_url('skrining/baru/' . $pasien['id_pasien']); ?>" class="btn btn-primary">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>Mulai Skrining Baru Pasien Ini</span>
    </a>
  </div>
</div>

<!-- Profile Card -->
<div class="card" style="border-top: 4px solid var(--navy-800);">
  <div class="card-body" style="padding: 28px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:20px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
          <span class="badge badge-navy" style="font-family:monospace; font-size:13px; font-weight:700;">
            <?= htmlspecialchars($pasien['no_rm']); ?>
          </span>
          <span style="font-size:12px; color:var(--text-muted);">
            Terdaftar: <?= date('d M Y', strtotime($pasien['created_at'])); ?>
          </span>
        </div>
        <h2 style="font-size:24px; font-weight:800; color:var(--navy-900); letter-spacing:-0.02em;">
          <?= htmlspecialchars($pasien['nama']); ?>
        </h2>
        <div style="font-size:14px; color:var(--text-muted); margin-top:4px;">
          <?= htmlspecialchars($pasien['alamat'] ?? 'Alamat belum diisi'); ?>
        </div>
      </div>

      <div style="display:flex; gap:16px; flex-wrap:wrap;">
        <div style="background:var(--navy-50); padding:12px 18px; border-radius:var(--radius-md); border:1px solid var(--navy-200); text-align:center;">
          <div style="font-size:11px; font-weight:700; color:var(--navy-700); text-transform:uppercase;">Tanggal Lahir</div>
          <div style="font-size:15px; font-weight:700; color:var(--navy-900); margin-top:2px;">
            <?= $pasien['tanggal_lahir'] ? date('d/m/Y', strtotime($pasien['tanggal_lahir'])) : '-'; ?>
          </div>
          <div style="font-size:11.5px; color:var(--text-muted);"><?= $usia ? $usia . ' Tahun' : ''; ?></div>
        </div>

        <div style="background:var(--navy-50); padding:12px 18px; border-radius:var(--radius-md); border:1px solid var(--navy-200); text-align:center;">
          <div style="font-size:11px; font-weight:700; color:var(--navy-700); text-transform:uppercase;">Total Skrining</div>
          <div style="font-size:20px; font-weight:900; color:var(--blue-primary); margin-top:2px;">
            <?= count($riwayat); ?>x
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- History Table for this patient -->
<div class="card">
  <div class="card-header">
    <div class="card-header-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Rekam Historis Skrining Pasien Ini
    </div>
    <span class="badge badge-navy"><?= count($riwayat); ?> Riwayat</span>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Tanggal Skrining</th>
          <th>Status Menopause</th>
          <th>IMT</th>
          <th>Total Skor</th>
          <th>Override</th>
          <th>Kategori Risiko</th>
          <th>Rekomendasi</th>
          <th>Petugas</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($riwayat)): ?>
          <?php foreach ($riwayat as $r): ?>
            <tr>
              <td>
                <div style="font-weight:700; color:var(--navy-900);"><?= date('d M Y', strtotime($r['tanggal'])); ?></div>
                <div style="font-size:11.5px; color:var(--text-muted);"><?= date('H:i', strtotime($r['tanggal'])); ?> WIB</div>
              </td>
              <td><?= htmlspecialchars($r['status_menopause'] ?? '-'); ?></td>
              <td><?= $r['imt'] ?? '-'; ?></td>
              <td>
                <span style="font-size:15px; font-weight:800; color:var(--navy-900);"><?= (int)$r['total_skor']; ?></span>
              </td>
              <td>
                <?php if ($r['override_aktif']): ?>
                  <span class="badge badge-override" style="font-size:11px;">Aktif</span>
                <?php else: ?>
                  <span style="color:var(--text-light);">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $cat = $r['nama_kategori'] ?? 'Rendah';
                  $badgeClass = ($cat === 'Tinggi') ? 'badge-high' : (($cat === 'Sedang') ? 'badge-med' : 'badge-low');
                ?>
                <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($cat); ?></span>
              </td>
              <td>
                <div style="font-size:12px; color:var(--text-secondary); max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($r['rekomendasi'] ?? ''); ?>">
                  <?= htmlspecialchars($r['rekomendasi'] ?? '-'); ?>
                </div>
              </td>
              <td><?= htmlspecialchars($r['nama_petugas'] ?? '-'); ?></td>
              <td style="text-align: right;">
                <div style="display:inline-flex; gap:6px;">
                  <a href="<?= base_url('skrining/hasil/' . $r['id_skrining']); ?>" class="btn btn-secondary btn-sm">Detail</a>
                  <a href="<?= base_url('skrining/cetak/' . $r['id_skrining']); ?>" target="_blank" class="btn btn-primary btn-sm">Cetak</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align:center; padding:32px; color:var(--text-muted);">
              Belum ada data skrining untuk pasien ini. Klik tombol "Mulai Skrining Baru Pasien Ini" di atas.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
