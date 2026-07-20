<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Préfixes opérateur</h1>
    <p>Définissez les préfixes téléphoniques valides pour l'ouverture de comptes mobile money.</p>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-list-ul"></i> Préfixes configurés</div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Préfixe</th>
                        <th>Libellé</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>033</strong></td>
                        <td>Orange Madagascar</td>
                        <td><span class="badge badge-mm badge-depot">Actif</span></td>
                        <td class="text-end">
                            <a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a>
                            <a href="#" class="btn btn-mm-outline btn-sm text-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>037</strong></td>
                        <td>Airtel Madagascar</td>
                        <td><span class="badge badge-mm badge-depot">Actif</span></td>
                        <td class="text-end">
                            <a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a>
                            <a href="#" class="btn btn-mm-outline btn-sm text-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>038</strong></td>
                        <td>Telma Madagascar</td>
                        <td><span class="badge badge-mm badge-retrait">Inactif</span></td>
                        <td class="text-end">
                            <a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a>
                            <a href="#" class="btn btn-mm-outline btn-sm text-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-plus-circle"></i> Ajouter un préfixe</div>

            <form>
                <div class="mb-3">
                    <label class="form-label">Préfixe (3 chiffres)</label>
                    <input type="text" class="form-control" placeholder="Ex : 034" maxlength="3">
                </div>
                <div class="mb-3">
                    <label class="form-label">Libellé</label>
                    <input type="text" class="form-control" placeholder="Ex : Orange Madagascar">
                </div>
                <div class="mb-4">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option>Actif</option>
                        <option>Inactif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Enregistrer le préfixe
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
