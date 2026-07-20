<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Types d'opérations &amp; barèmes de frais</h1>
    <p>Gérez les opérations disponibles (dépôt, retrait, transfert) et leurs tranches de frais.</p>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-diagram-3"></i> Types d'opérations</div>
        <button class="btn btn-mm-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeModal">
            <i class="bi bi-plus-lg"></i> Nouveau type
        </button>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Opération</th>
                <th>Description</th>
                <th>Statut</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($types_operations)): ?>
                <?php foreach ($types_operations as $type): ?>
                    <tr>
                        <td>
                            <span class="badge badge-mm 
                                <?= $type['code'] === 'DEPOT' ? 'badge-depot' : ($type['code'] === 'RETRAIT' ? 'badge-retrait' : 'badge-transfert') ?>">
                                <?= $type['code'] ?>
                            </span>
                        </td>
                        <td><?= $type['nom'] ?></td>
                        <td><span class="badge badge-mm badge-depot">Actif</span></td>
                        <td class="text-end">
                            <button class="btn btn-mm-outline btn-sm" onclick="editTypeOperation(<?= $type['id'] ?>, '<?= $type['code'] ?>', '<?= $type['nom'] ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?= base_url('operateur/bareme/' . $type['id']) ?>" class="btn btn-mm-primary btn-sm">
                                <i class="bi bi-eye"></i> Voir barème
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Aucun type d'opération trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour ajouter un type d'opération -->
<div class="modal fade" id="addTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un type d'opération</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('operateur/addTypeOperation') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" placeholder="DEPOT, RETRAIT, TRANSFERT" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" placeholder="Dépôt, Retrait, Transfert" required>
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

<!-- Modal pour modifier un type d'opération -->
<div class="modal fade" id="editTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le type d'opération</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" id="editTypeForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editTypeId">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" id="editTypeCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" id="editTypeNom" class="form-control" required>
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
function editTypeOperation(id, code, nom) {
    document.getElementById('editTypeId').value = id;
    document.getElementById('editTypeCode').value = code;
    document.getElementById('editTypeNom').value = nom;
    document.getElementById('editTypeForm').action = '<?= base_url('operateur/editTypeOperation/') ?>' + id;
    var modal = new bootstrap.Modal(document.getElementById('editTypeModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
