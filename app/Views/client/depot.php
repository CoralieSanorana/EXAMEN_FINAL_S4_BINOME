<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un dépôt</h1>
    <p>Alimentez votre compte mobile money (opération supposée automatique).</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-download"></i> Formulaire de dépôt</div>

            <form>
                <div class="mb-3">
                    <label class="form-label">Numéro de compte</label>
                    <input type="text" class="form-control" value="033 12 345 67" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant à déposer (Ar)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="Ex : 100000">
                        <span class="input-group-text">Ar</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Source du dépôt</label>
                    <select class="form-select">
                        <option>Agent partenaire</option>
                        <option>Virement bancaire</option>
                        <option>Dépôt cash guichet</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Confirmer le dépôt
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-info-circle"></i> Résumé</div>
            <ul class="list-unstyled mb-0" style="font-size:.9rem;">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Solde actuel</span>
                    <span>452 300 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de dépôt</span>
                    <span>0 Ar (gratuit)</span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span class="text-muted">Nouveau solde estimé</span>
                    <span class="fw-semibold">452 300 Ar</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
