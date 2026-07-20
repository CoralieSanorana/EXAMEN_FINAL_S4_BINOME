<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>

<div class="mm-auth-wrapper">
    <div class="mm-auth-card">
        <div class="text-center mb-4">
            <span class="mm-logo mm-brand-logo d-inline-flex align-items-center justify-content-center" style="border-radius:14px;background:#5c677d;color:#fff;">MM</span>
            <h1 class="h5 mt-3 mb-1">MobileMoney Simulateur</h1>
            <p class="text-muted small mb-0">Connectez-vous avec votre Email et Mot de passe</p>
        </div>

        <?php if (session('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('operateur/authenticate'); ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control" value="operateur@gmail.com">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" value="operateur123">
                </div>
            </div>

            <button type="submit" class="btn btn-mm-primary w-100 mt-2">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted small mb-0">
            Espace opérateur &bull; MobileMoney Simulateur &copy; 2026
        </p>
    </div>
</div>

<?= $this->endSection() ?>
