<?= $this->extend('layouts/layout_client') ?>

<?php
$oldDestinataires = old('numero_destinataires');
if (is_string($oldDestinataires)) {
    $oldDestinataires = preg_split('/[\r\n,;]+/', $oldDestinataires) ?: [];
}
if (!is_array($oldDestinataires) || empty(array_filter($oldDestinataires, static fn ($item) => trim((string) $item) !== ''))) {
    $oldDestinataires = [''];
}
?>

<?= $this->section('content') ?>

<style>
    /* Permet de donner au conteneur client le même rendu et la même hauteur parfaite qu'un input Bootstrap */
    .input-like-badge {
        height: 38px;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
</style>

<div class="mm-page-header mb-4">
    <h1>Effectuer un transfert</h1>
    <p class="text-muted">Envoyez de l'argent vers un ou plusieurs numéros mobile money.</p>
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

<div class="row g-4">
    <!-- Colonne de Gauche : Formulaire -->
    <div class="col-lg-7">
        <div class="mm-card p-4 border rounded shadow-sm bg-white">
            <div class="mm-card-title h5 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-right text-primary"></i> Formulaire de transfert
            </div>

            <form action="/client/processTransfert" method="post" id="transfertForm">
                <?= csrf_field() ?>
                
                <!-- Émetteur -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Numéro émetteur</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person-check"></i></span>
                        <input type="text" class="form-control" id="numeroEmetteur" value="<?= esc(session()->get('client_telephone') ?? '033 12 345 67') ?>" disabled>
                    </div>
                </div>

                <!-- Destinataires -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted mb-0">Numéros destinataires</label>
                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" id="addDestinataireBtn">
                            <i class="bi bi-plus-lg"></i> Ajouter un numéro
                        </button>
                    </div>

                    <!-- Liste symétrique des lignes de numéros -->
                    <div id="destinatairesContainer" class="d-flex flex-column gap-3">
                        <?php foreach ($oldDestinataires as $index => $numero): ?>
                            <div class="destinataire-row p-3 border rounded bg-light bg-opacity-50" data-index="<?= $index ?>">
                                <div class="row g-3 align-items-center">
                                    
                                    <!-- Index & Numéro -->
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text fw-bold text-muted bg-white">#<?= $index + 1 ?></span>
                                            <span class="input-group-text bg-white border-start-0"><i class="bi bi-phone text-muted"></i></span>
                                            <input
                                                type="text"
                                                name="numero_destinataires[]"
                                                class="form-control destinataire-input"
                                                placeholder="Ex : 0341234567"
                                                value="<?= esc($numero) ?>"
                                            >
                                        </div>
                                    </div>
                                    
                                    <!-- Identité Client (Alignée symétriquement au champ de saisie) -->
                                    <div class="col-md-4">
                                        <div class="destinataire-client input-like-badge border rounded px-3 bg-white text-muted shadow-sm">
                                            <i class="bi bi-search me-2 text-black-50 small"></i> En attente...
                                        </div>
                                    </div>
                                    
                                    <!-- Actions (Alignées verticalement) -->
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm add-row-btn" title="Ajouter après"><i class="bi bi-plus-lg"></i></button>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn" title="Supprimer"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-text mt-2 text-muted">
                        <i class="bi bi-info-circle"></i> Tous les numéros saisis doivent obligatoirement appartenir au même opérateur pour valider l'envoi multiple.
                    </div>
                    
                    <div id="destinatairesInfo" class="mt-3" style="display:none;">
                        <div class="alert alert-success py-2 px-3 mb-0">
                            <div class="fw-semibold"><i class="bi bi-people-fill"></i> <span id="destinatairesResume"></span></div>
                            <div class="small text-muted mt-1" id="destinatairesPreview"></div>
                        </div>
                    </div>
                    <div id="destinatairesError" class="text-danger small mt-2" style="display:none;"></div>
                </div>

                <!-- Montant réparti -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Montant total à répartir</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="montant" class="form-control fw-bold" id="montantInput" placeholder="Ex : 50000" min="100" step="0.01" value="<?= esc(old('montant')) ?>" required>
                        <span class="input-group-text bg-primary text-white fw-semibold">Ar</span>
                    </div>
                    <div class="form-text d-flex justify-content-between mt-2">
                        <span>Solde disponible :</span>
                        <span class="fw-semibold text-dark"><span id="soldeDisponible"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?></span> Ar</span>
                    </div>
                    <div id="montantError" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                <!-- Switch Optionnel -->
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="form-check form-switch card-switch">
                        <input class="form-check-input style-switch" type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetrait" value="1" <?= old('inclure_frais_retrait') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold ms-2" for="inclureFraisRetrait">
                            Inclure les frais de retrait dans le montant envoyé
                        </label>
                    </div>
                    <div class="form-text text-muted mt-2 ps-4 small">
                        Si activé, le destinataire recevra sa part intacte. Les frais théoriques de retrait requis lui seront directement attribués et ajoutés à votre débit.
                    </div>
                </div>

                <!-- Validation -->
                <button type="submit" class="btn btn-primary btn-lg w-100 py-2" id="submitBtn" disabled>
                    <i class="bi bi-check-circle-fill me-2"></i> Confirmer le transfert
                </button>
            </form>
        </div>
    </div>

    <!-- Colonne de Droite : Résumé des Frais -->
    <div class="col-lg-5">
        <div class="mm-card p-4 border rounded shadow-sm bg-white h-100">
            <div class="mm-card-title h5 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-secondary"></i> Résumé des frais
            </div>
            
            <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 0.95rem;">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Solde actuel</span>
                    <span class="fw-semibold text-dark" id="soldeActuel"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Nombre de destinataires</span>
                    <span class="fw-semibold text-dark" id="nombreDestinataires">0</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant de base total</span>
                    <span class="fw-semibold text-dark" id="montantBase">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Part brute par destinataire</span>
                    <span class="text-secondary" id="partParDestinataire">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de retrait cumulés</span>
                    <span class="text-danger" id="fraisRetrait">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Montant réellement envoyé cumulé</span>
                    <span class="text-success fw-semibold" id="montantTransfert">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Frais de transfert cumulés</span>
                    <span class="text-danger" id="fraisApplicables">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-3 border-bottom bg-light px-2 rounded">
                    <span class="text-dark fw-bold">Débit total</span>
                    <span class="text-primary fw-bold" id="totalDebit">0 Ar</span>
                </li>
                <li class="d-flex justify-content-between py-3">
                    <span class="text-dark fw-semibold">Nouveau solde estimé</span>
                    <span class="h5 mb-0 fw-bold text-success" id="nouveauSolde"><?= number_format(session()->get('client_solde') ?? 0, 0, '.', ' ') ?> Ar</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    window.prefixesOperateurs = <?= json_encode($prefixesOperateurs ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="/assets/js/Cllient/transfert.js"></script>

<?= $this->endSection() ?>