<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>

<div class="mm-auth-wrapper">
    <div class="mm-auth-card">
        <div class="text-center mb-4">
            <span class="mm-logo mm-brand-logo d-inline-flex align-items-center justify-content-center" style="border-radius:14px;background:#5c677d;color:#fff;">MM</span>
            <h1 class="h5 mt-3 mb-1">MobileMoney Simulateur</h1>
            <p class="text-muted small mb-0">Connectez-vous avec votre numéro de téléphone</p>
        </div>

        <form>
            <div class="mb-3">
                <label class="form-label">Numéro de téléphone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="text" class="form-control" placeholder="033 12 345 67">
                </div>
                <div class="form-text">Aucune inscription requise, la connexion est automatique.</div>
            </div>

            <button type="submit" class="btn btn-mm-primary w-100 mt-2">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </form>

        <hr class="my-4">
        <p class="text-center text-muted small mb-0">
            Espace client &bull; MobileMoney Simulateur &copy; 2026
        </p>
    </div>
</div>

<?= $this->endSection() ?>
