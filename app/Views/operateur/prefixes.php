<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Préfixes opérateur</h1>
    <p>Définissez les préfixes téléphoniques valides pour l'ouverture de comptes mobile money.</p>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-7">
        <!-- Tableau 1: Préfixes de mon opérateur (interne) -->
        <div class="mm-card mb-4">
            <div class="mm-card-title"><i class="bi bi-building"></i> Préfixes de mon opérateur</div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Préfixe</th>
                        <th>Opérateur</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prefixes_internes)): ?>
                        <?php foreach ($prefixes_internes as $prefixe): ?>
                            <tr>
                                <td><strong><?= $prefixe['prefixe'] ?></strong></td>
                                <td><?= $prefixe['operateur_nom'] ?? 'Non défini' ?></td>
                                <td>
                                    <span class="badge badge-mm <?= $prefixe['statut'] === 'actif' ? 'badge-depot' : 'badge-retrait' ?>">
                                        <?= ucfirst($prefixe['statut'] ?? 'actif') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-mm-outline btn-sm" onclick="editPrefixe(<?= $prefixe['id'] ?>, '<?= $prefixe['prefixe'] ?>', <?= $prefixe['operateur_id'] ?>, '<?= $prefixe['statut'] ?? 'actif' ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('operateur/deletePrefixe/' . $prefixe['id']) ?>" class="btn btn-mm-outline btn-sm text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucun préfixe configuré pour votre opérateur</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tableau 2: Préfixes des autres opérateurs (externe) -->
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-globe"></i> Préfixes des autres opérateurs</div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Préfixe</th>
                        <th>Opérateur</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prefixes_externes)): ?>
                        <?php foreach ($prefixes_externes as $prefixe): ?>
                            <tr>
                                <td><strong><?= $prefixe['prefixe'] ?></strong></td>
                                <td><?= $prefixe['operateur_nom'] ?? 'Non défini' ?></td>
                                <td>
                                    <span class="badge badge-mm <?= $prefixe['statut'] === 'actif' ? 'badge-depot' : 'badge-retrait' ?>">
                                        <?= ucfirst($prefixe['statut'] ?? 'actif') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-mm-outline btn-sm" onclick="editPrefixe(<?= $prefixe['id'] ?>, '<?= $prefixe['prefixe'] ?>', <?= $prefixe['operateur_id'] ?>, '<?= $prefixe['statut'] ?? 'actif' ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= base_url('operateur/deletePrefixe/' . $prefixe['id']) ?>" class="btn btn-mm-outline btn-sm text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucun préfixe configuré pour les autres opérateurs</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-plus-circle"></i> Ajouter un préfixe</div>

            <form action="<?= base_url('operateur/addPrefixe') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Opérateur</label>
                    <select name="operateur_id" class="form-select" required>
                        <option value="">Sélectionner un opérateur</option>
                        <?php foreach ($operateurs as $operateur): ?>
                            <option value="<?= $operateur['id'] ?>">
                                <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Préfixe (3 chiffres)</label>
                    <input type="text" name="prefixe" class="form-control" placeholder="Ex : 034" maxlength="3" required pattern="[0-9]{3}">
                </div>
                <div class="mb-4">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Enregistrer le préfixe
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
                <h5 class="modal-title">Modifier le préfixe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Opérateur</label>
                        <select name="operateur_id" id="editOperateurId" class="form-select" required>
                            <option value="">Sélectionner un opérateur</option>
                            <?php foreach ($operateurs as $operateur): ?>
                                <option value="<?= $operateur['id'] ?>">
                                    <?= $operateur['nom'] ?> <?= $operateur['est_interne'] == 1 ? '(Mon opérateur)' : '(Autre)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Préfixe (3 chiffres)</label>
                        <input type="text" name="prefixe" id="editPrefixe" class="form-control" maxlength="3" required pattern="[0-9]{3}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="statut" id="editStatut" class="form-select">
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                        </select>
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
function editPrefixe(id, prefixe, operateurId, statut) {
    document.getElementById('editId').value = id;
    document.getElementById('editPrefixe').value = prefixe;
    document.getElementById('editOperateurId').value = operateurId;
    document.getElementById('editStatut').value = statut;
    document.getElementById('editForm').action = '<?= base_url('operateur/editPrefixe/') ?>' + id;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
