<!-- Dashboard Executive Overview -->

<!-- KPI Metric Cards -->
<div class="grid-4" style="margin-bottom: 24px;">
  <!-- Total Pasien -->
  <div class="stat-card" style="--stat-color: #2563EB; --stat-bg: #EFF6FF;">
    <div class="stat-icon">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format($stats['total_pasien'] ?? 0); ?></div>
      <div class="stat-label">Total Pasien Terdaftar</div>
      <div class="stat-meta">Basis data pasien skrining</div>
    </div>
  </div>

  <!-- Total Skrining -->
  <div class="stat-card" style="--stat-color: #0EA5E9; --stat-bg: #F0F9FF;">
    <div class="stat-icon">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format($stats['total_skrining'] ?? 0); ?></div>
      <div class="stat-label">Total Skrining Dilakukan</div>
      <div class="stat-meta">Inferensi forward chaining</div>
    </div>
  </div>

  <!-- Pasien Risiko Tinggi -->
  <div class="stat-card" style="--stat-color: #DC2626; --stat-bg: #FEF2F2;">
    <div class="stat-icon" style="color: #DC2626;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value" style="color: #DC2626;"><?= number_format($stats['total_tinggi'] ?? 0); ?></div>
      <div class="stat-label">Kasus Risiko Tinggi</div>
      <div class="stat-meta">Membutuhkan rujukan spesialis</div>
    </div>
  </div>

  <!-- Rata-rata Skor -->
  <div class="stat-card" style="--stat-color: #059669; --stat-bg: #ECFDF5;">
    <div class="stat-icon" style="color: #059669;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
    </div>
    <div class="stat-content">
      <div class="stat-value"><?= number_format($stats['avg_skor'] ?? 0, 1); ?></div>
      <div class="stat-label">Rata-rata Skor Risiko</div>
      <div class="stat-meta">Skor 0 - 30 skala agregat</div>
    </div>
  </div>
</div>

<!-- Charts Section -->
<div class="grid-2" style="margin-bottom: 24px;">
  <!-- Donut Chart: Distribusi Kategori Risiko -->
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-header-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10h-10z"/></svg>
          Distribusi Kategori Risiko
        </div>
        <div class="card-header-subtitle">Proporsi hasil klasifikasi (Rendah, Sedang, Tinggi)</div>
      </div>
      <span class="badge badge-navy"><?= $stats['total_skrining']; ?> Skrining</span>
    </div>
    <div class="card-body" style="height: 300px; display: flex; align-items: center; justify-content: center;">
      <canvas id="categoryChart"></canvas>
    </div>
    <div class="card-footer" style="justify-content: space-around; background: #FFFFFF; border-top: 1px solid var(--border-light); padding: 12px 20px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#10B981;"></span>
        <span style="font-size:12.5px; font-weight:600; color:var(--text-secondary);">Rendah: <strong><?= $stats['total_rendah']; ?></strong></span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#F59E0B;"></span>
        <span style="font-size:12.5px; font-weight:600; color:var(--text-secondary);">Sedang: <strong><?= $stats['total_sedang']; ?></strong></span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#EF4444;"></span>
        <span style="font-size:12.5px; font-weight:600; color:var(--text-secondary);">Tinggi: <strong><?= $stats['total_tinggi']; ?></strong></span>
      </div>
    </div>
  </div>

  <!-- Bar Chart: Frekuensi Faktor Risiko Terbanyak -->
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-header-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Frekuensi Faktor Risiko
        </div>
        <div class="card-header-subtitle">Faktor yang paling sering terpenuhi pada populasi pasien</div>
      </div>
    </div>
    <div class="card-body" style="height: 340px;">
      <canvas id="factorChart"></canvas>
    </div>
  </div>
</div>

<!-- Recent Screenings Table -->
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-header-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Aktivitas Skrining Terbaru
      </div>
      <div class="card-header-subtitle">Daftar evaluasi klinis yang baru saja selesai diproses</div>
    </div>
    <a href="<?= base_url('skrining/riwayat'); ?>" class="btn btn-navy-outline btn-sm">
      Lihat Semua Riwayat &rarr;
    </a>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Tanggal & Jam</th>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>Total Skor</th>
          <th>Override</th>
          <th>Kategori Risiko</th>
          <th>Petugas</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recent_screenings)): ?>
          <?php foreach ($recent_screenings as $row): ?>
            <tr>
              <td>
                <div style="font-weight:600; color:var(--navy-900);"><?= date('d M Y', strtotime($row['tanggal'])); ?></div>
                <div style="font-size:11.5px; color:var(--text-muted);"><?= date('H:i', strtotime($row['tanggal'])); ?> WIB</div>
              </td>
              <td>
                <span class="badge badge-navy" style="font-family:monospace; font-size:12px;">
                  <?= htmlspecialchars($row['no_rm']); ?>
                </span>
              </td>
              <td>
                <div style="font-weight:600; color:var(--navy-900);">
                  <?= htmlspecialchars($row['nama_pasien']); ?>
                </div>
              </td>
              <td>
                <span style="font-size:15px; font-weight:800; color:var(--navy-900);">
                  <?= (int)$row['total_skor']; ?>
                </span>
                <span style="font-size:11px; color:var(--text-muted);">/ 30</span>
              </td>
              <td>
                <?php if ($row['override_aktif']): ?>
                  <span class="badge badge-override" title="Kategori otomatis Tinggi via aturan klinis">
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
                  <a href="<?= base_url('skrining/cetak/' . $row['id_skrining']); ?>" target="_blank" class="btn btn-primary btn-sm" title="Cetak Lembar Rekam Medis">
                    Cetak
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align:center; padding:32px; color:var(--text-muted);">
              Belum ada riwayat skrining. Silakan klik tombol "Skrining Baru".
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Chart.js Scripts Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Category Donut Chart
  const ctxCat = document.getElementById('categoryChart');
  if (ctxCat) {
    new Chart(ctxCat, {
      type: 'doughnut',
      data: {
        labels: ['Rendah', 'Sedang', 'Tinggi'],
        datasets: [{
          data: [
            <?= (int)$stats['total_rendah']; ?>,
            <?= (int)$stats['total_sedang']; ?>,
            <?= (int)$stats['total_tinggi']; ?>
          ],
          backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
          borderWidth: 3,
          borderColor: '#FFFFFF',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              font: { family: 'Plus Jakarta Sans', weight: '600', size: 12 }
            }
          }
        },
        cutout: '70%'
      }
    });
  }

  // Factor Bar Chart
  const ctxFactor = document.getElementById('factorChart');
  if (ctxFactor) {
    <?php
      $fLabels = [];
      $fCounts = [];
      foreach ($frekuensi_faktor as $f) {
        $fLabels[] = $f['id_faktor'] . ' ' . (strlen($f['nama_faktor']) > 22 ? substr($f['nama_faktor'], 0, 22) . '...' : $f['nama_faktor']);
        $fCounts[] = (int)$f['jumlah_pasien'];
      }
    ?>
    new Chart(ctxFactor, {
      type: 'bar',
      data: {
        labels: <?= json_encode($fLabels); ?>,
        datasets: [{
          label: 'Jumlah Pasien Terpenuhi',
          data: <?= json_encode($fCounts); ?>,
          backgroundColor: '#1E3E62',
          borderRadius: 6,
          borderSkipped: false
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: { precision: 0, font: { family: 'Plus Jakarta Sans' } },
            grid: { color: '#F1F5F9' }
          },
          y: {
            ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } },
            grid: { display: false }
          }
        }
      }
    });
  }
});
</script>
