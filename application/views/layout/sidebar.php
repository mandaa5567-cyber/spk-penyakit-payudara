<?php
$role = $this->session->userdata('role');
$active = $active_menu ?? '';
?>
<!-- Sidebar (Deep Navy Theme) -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        <path d="M12 7v6m-3-3h6" stroke="#FFFFFF" stroke-width="2.5"/>
      </svg>
    </div>
    <div class="brand-text">
      <h2>SPKK Payudara</h2>
      <p>Rule-Based CDSS</p>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-title">Menu Utama</div>
    
    <a href="<?= base_url('dashboard'); ?>" class="nav-item <?= ($active === 'dashboard') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="9"/>
        <rect x="14" y="3" width="7" height="5"/>
        <rect x="14" y="12" width="7" height="9"/>
        <rect x="3" y="16" width="7" height="5"/>
      </svg>
      <span>Dashboard</span>
    </a>

    <a href="<?= base_url('skrining/baru'); ?>" class="nav-item <?= ($active === 'skrining_baru') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="16"/>
        <line x1="8" y1="12" x2="16" y2="12"/>
      </svg>
      <span>Skrining Baru</span>
    </a>

    <a href="<?= base_url('simulasi'); ?>" class="nav-item <?= ($active === 'simulasi') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
      <span>Simulasi Skrining</span>
    </a>

    <a href="<?= base_url('skrining/riwayat'); ?>" class="nav-item <?= ($active === 'skrining_riwayat') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
      <span>Riwayat Skrining</span>
    </a>

    <a href="<?= base_url('rekap'); ?>" class="nav-item <?= ($active === 'rekap') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="20" x2="18" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="6" y1="20" x2="6" y2="14"/>
      </svg>
      <span>Rekap &amp; Statistik</span>
    </a>

    <div class="nav-section-title">Data Medis</div>

    <a href="<?= base_url('pasien'); ?>" class="nav-item <?= ($active === 'pasien') ? 'active' : ''; ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span>Data Pasien</span>
    </a>

    <?php if ($role === 'admin'): ?>
      <div class="nav-section-title">Pengaturan Sistem</div>

      <a href="<?= base_url('aturan'); ?>" class="nav-item <?= ($active === 'aturan') ? 'active' : ''; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
        <span>Basis Aturan & Bobot</span>
      </a>

      <a href="<?= base_url('users'); ?>" class="nav-item <?= ($active === 'users') ? 'active' : ''; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <line x1="19" y1="8" x2="19" y2="14"/>
          <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
        <span>Manajemen Pengguna</span>
      </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-badge">
      <div class="user-avatar">
        <?= strtoupper(substr($this->session->userdata('nama') ?? 'U', 0, 1)); ?>
      </div>
      <div class="user-info">
        <div class="user-name" title="<?= htmlspecialchars($this->session->userdata('nama')); ?>">
          <?= htmlspecialchars($this->session->userdata('nama')); ?>
        </div>
        <span class="user-role-badge <?= ($role === 'admin') ? 'role-admin' : 'role-nakes'; ?>">
          <?= ($role === 'admin') ? 'Admin Spesialis' : 'Tenaga Kesehatan'; ?>
        </span>
      </div>
    </div>
  </div>
</aside>
