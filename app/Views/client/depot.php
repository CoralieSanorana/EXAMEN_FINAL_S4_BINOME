<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un dépôt</h1>
    <p>Alimentez votre compte mobile money (opération supposée automatique).</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-download"></i> Formulaire de dépôt</div>

            <form action="/client/processDepot" method="post" id="depotForm">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Numéro de compte</label>
                    <input type="text" class="form-control" value="<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant à déposer (Ar)</label>
                    <div class="input-group">
                        <input type="number" name="montant" class="form-control" id="montantInput" placeholder="Ex : 100000" min="100" required>
                        <span class="input-group-text">Ar</span>
                    </div>
                    <div class="form-text">Solde actuel : <span id="soldeActuel"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?></span> Ar</div>
                    <div id="montantError" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Source du dépôt</label>
                    <select class="form-select" name="source_depot">
                        <option value="agent">Agent partenaire</option>
                        <option value="virement">Virement bancaire</option>
                        <option value="guichet">Dépôt cash guichet</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-mm-primary w-100" id="submitBtn">
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
                    <span id="soldeActuelDisplay"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant à déposer</span>
                    <span id="montantDepot">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de dépôt</span>
                    <span>0 Ar (gratuit)</span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span class="text-muted">Nouveau solde estimé</span>
                    <span class="fw-semibold" id="nouveauSolde"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script src="/assets/js/Cllient/depot.js"></script>

<?= $this->endSection() ?>
