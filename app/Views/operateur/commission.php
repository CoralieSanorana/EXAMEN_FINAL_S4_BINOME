<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Commissions inter-opérateurs</h1>
    <p>Configurez les commissions appliquées lors des transferts entre différents opérateurs.</p>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-7">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-currency-exchange"></i> Configurations de commissions</div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Opérateur source</th>
                        <th>Opérateur destination</th>
                        <th>Commission (%)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($commissions)): ?>
                        <?php foreach ($commissions as $commission): ?>
                            <tr>
                                <td><?= $commission['operateur_source_nom'] ?? 'Non défini' ?></td>
                                <td><?= $commission['operateur_destination_nom'] ?? 'Non défini' ?></td>
                                <td><strong><?= number_format($commission['pourcentage_commission'], 2, ',', ' ') ?> %</strong></td>
                                <td class="text-end">
                                    <button class="btn btn-mm-outline btn-sm" onclick="editCommission(<?= $commission['id'] ?>, <?= $commission['operateur_source_id'] ?>, <?= $commission['operateur_destination_id'] ?>, <?= $commission['pourcentage_commission'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('operateur/deleteCommission/' . $commission['id']) ?>" class="btn btn-mm-outline btn-sm text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette configuration de commission ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucune configuration de commission trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-plus-circle"></i> Ajouter une configuration</div>

            <form action="<?= base_url('operateur/addCommission') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Opérateur source</label>
                    <select name="operateur_source_id" class="form-select" required>
                        <option value="">Sélectionner l'opérateur source</option>
                        <?php foreach ($operateurs as $operateur): ?>
                            <option value="<?= $operateur['id'] ?>">
                                <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Opérateur destination</label>
                    <select name="operateur_destination_id" class="form-select" required>
                        <option value="">Sélectionner l'opérateur destination</option>
                        <?php foreach ($operateurs as $operateur): ?>
                            <option value="<?= $operateur['id'] ?>">
                                <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Pourcentage de commission (%)</label>
                    <input type="number" name="pourcentage_commission" class="form-control" placeholder="Ex : 2.5" step="0.1" min="0" required>
                </div>
                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Enregistrer la configuration
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modification -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la configuration de commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Opérateur source</label>
                        <select name="operateur_source_id" id="editOperateurSourceId" class="form-select" required>
                            <option value="">Sélectionner l'opérateur source</option>
                            <?php foreach ($operateurs as $operateur): ?>
                                <option value="<?= $operateur['id'] ?>">
                                    <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opérateur destination</label>
                        <select name="operateur_destination_id" id="editOperateurDestinationId" class="form-select" required>
                            <option value="">Sélectionner l'opérateur destination</option>
                            <?php foreach ($operateurs as $operateur): ?>
                                <option value="<?= $operateur['id'] ?>">
                                    <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pourcentage de commission (%)</label>
                        <input type="number" name="pourcentage_commission" id="editPourcentage" class="form-control" step="0.1" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-mm-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCommission(id, operateurSourceId, operateurDestinationId, pourcentage) {
    document.getElementById('editId').value = id;
    document.getElementById('editOperateurSourceId').value = operateurSourceId;
    document.getElementById('editOperateurDestinationId').value = operateurDestinationId;
    document.getElementById('editPourcentage').value = pourcentage;
    document.getElementById('editForm').action = '<?= base_url('operateur/editCommission/') ?>' + id;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>