<header class="mm-topbar">
    <a href="#" class="mm-brand">
        <span class="mm-logo">MM</span>
        <span>MobileMoney <strong>Simulateur</strong></span>
    </a>

    <div class="mm-topbar-right">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-shield-check"></i> Espace Opérateur
        </span>

        <div class="mm-user-chip">
            <span class="avatar">OP</span>
            <span><?= session('operateur_name'); ?> - <?= session('operateur_email'); ?></span>
        </div>

        <a href="<?= base_url('operateur/logout'); ?>" class="btn btn-mm-outline btn-sm">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</header>
