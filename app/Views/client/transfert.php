<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Effectuer un transfert</h1>
    <p>Envoyez de l'argent vers un autre numéro mobile money.</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-arrow-left-right"></i> Formulaire de transfert</div>

            <form>
                <div class="mb-3">
                    <label class="form-label">Numéro émetteur</label>
                    <input type="text" class="form-control" value="<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Numéro destinataire</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="text" class="form-control" placeholder="Ex : 037 44 556 78">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Montant à transférer (Ar)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="Ex : 50000">
                        <span class="input-group-text">Ar</span>
                    </div>
                    <div class="form-text">Solde disponible : <?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</div>
                </div>

                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Confirmer le transfert
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-info-circle"></i> Résumé des frais</div>
            <ul class="list-unstyled mb-0" style="font-size:.9rem;">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Solde actuel</span>
                    <span><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant à transférer</span>
                    <span>50 000 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais applicables</span>
                    <span>500 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span class="text-muted">Nouveau solde estimé</span>
                    <span class="fw-semibold">401 800 Ar</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
