<!-- Topbar -->
<div class="main-wrapper">
  <header class="topbar">
    <div class="topbar-left">
      <button id="sidebarToggle" class="btn btn-secondary btn-sm" style="display:none;" aria-label="Toggle Menu">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div>
        <h1 class="topbar-title"><?= isset($title) ? htmlspecialchars($title) : 'Dashboard CDSS'; ?></h1>
        <div class="topbar-subtitle">Sistem Pendukung Keputusan Klinis Skrining Risiko Kanker Payudara</div>
      </div>
    </div>

    <div class="topbar-right">
      <!-- Quick Demo Switcher -->
      <div style="display: flex; align-items: center; gap: 8px; background: var(--navy-50); padding: 5px 10px; border-radius: var(--radius-full); border: 1px solid var(--navy-200);">
        <span style="font-size: 11px; font-weight: 700; color: var(--navy-700); text-transform: uppercase;">Peran:</span>
        <a href="<?= base_url('quick-login/admin'); ?>" class="btn <?= ($this->session->userdata('role') === 'admin') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm" style="padding: 3px 8px; font-size: 11px;" title="Beralih ke akun Admin">
          Admin
        </a>
        <a href="<?= base_url('quick-login/nakes'); ?>" class="btn <?= ($this->session->userdata('role') === 'nakes') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm" style="padding: 3px 8px; font-size: 11px;" title="Beralih ke akun Nakes">
          Nakes
        </a>
      </div>

      <a href="<?= base_url('skrining/baru'); ?>" class="btn btn-primary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Skrining Baru</span>
      </a>

      <a href="<?= base_url('logout'); ?>" class="btn btn-secondary btn-sm" title="Keluar dari sistem">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Keluar</span>
      </a>
    </div>
  </header>

  <main class="content-body">
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <div><?= $this->session->flashdata('success'); ?></div>
      </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <div><?= $this->session->flashdata('error'); ?></div>
      </div>
    <?php endif; ?>
