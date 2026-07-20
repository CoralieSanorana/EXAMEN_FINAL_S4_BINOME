<header class="mm-topbar">
    <a href="#" class="mm-brand">
        <span class="mm-logo">MM</span>
        <span>MobileMoney <strong>Simulateur</strong></span>
    </a>

    <div class="mm-topbar-right">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-phone"></i> Espace Client
        </span>

        <div class="mm-user-chip">
            <span class="avatar"><?= substr(session()->get('client_prenom') ?? 'JD', 0, 2) ?></span>
            <span><?= esc((session()->get('client_prenom') ?? '') . ' ' . (session()->get('client_nom') ?? '')) ?> (<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>)</span>
        </div>

        <a href="/client/logout" class="btn btn-mm-outline btn-sm">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</header>
