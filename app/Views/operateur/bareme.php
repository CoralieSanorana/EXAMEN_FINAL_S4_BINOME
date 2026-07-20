<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Barème de frais &mdash; <?= $type_operation['nom'] ?></h1>
    <p>Gestion des tranches de frais pour l'opération <?= $type_operation['nom'] ?>.</p>
    <a href="<?= base_url('operateur/operations'); ?>" class="btn btn-mm-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Retour aux opérations
    </a>
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
            <div class="mm-card-title">
                <i class="bi bi-layers"></i> Tranches configurées
            </div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Tranche (Ar)</th>
                        <th>Frais</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($baremes)): ?>
                        <?php foreach ($baremes as $frais): ?>
                            <tr>
                                <td><?= number_format($frais['montant_min'], 0, ',', ' ') ?> &ndash; <?= number_format($frais['montant_max'], 0, ',', ' ') ?></td>
                                <td><?= number_format($frais['frais'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end">
                                    <button class="btn btn-mm-outline btn-sm" onclick="editBareme(<?= $frais['id'] ?>, <?= $frais['montant_min'] ?>, <?= $frais['montant_max'] ?>, <?= $frais['frais'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('operateur/deleteBareme/' . $frais['id'] . '/' . $type_operation['id']) ?>" class="btn btn-mm-outline btn-sm text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tranche ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Aucune tranche configurée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-plus-circle"></i> Ajouter une tranche</div>

            <form action="<?= base_url('operateur/addBareme') ?>" method="post">
                <input type="hidden" name="type_operation_id" value="<?= $type_operation['id'] ?>">
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Montant min (Ar)</label>
                        <input type="number" name="montant_min" class="form-control" placeholder="0" required min="0">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Montant max (Ar)</label>
                        <input type="number" name="montant_max" class="form-control" placeholder="10000" required min="0">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Frais (Ar)</label>
                    <input type="number" name="frais" class="form-control" placeholder="200" required min="0">
                </div>
                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Enregistrer la tranche
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
                <h5 class="modal-title">Modifier la tranche de frais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="type_operation_id" value="<?= $type_operation['id'] ?>">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Montant min (Ar)</label>
                            <input type="number" name="montant_min" id="editMontantMin" class="form-control" required min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Montant max (Ar)</label>
                            <input type="number" name="montant_max" id="editMontantMax" class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frais (Ar)</label>
                        <input type="number" name="frais" id="editFrais" class="form-control" required min="0">
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
function editBareme(id, montantMin, montantMax, frais) {
    document.getElementById('editId').value = id;
    document.getElementById('editMontantMin').value = montantMin;
    document.getElementById('editMontantMax').value = montantMax;
    document.getElementById('editFrais').value = frais;
    document.getElementById('editForm').action = '<?= base_url('operateur/editBareme/') ?>' + id + '/' + <?= $type_operation['id'] ?>;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
