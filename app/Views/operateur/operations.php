<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Types d'opérations &amp; barèmes de frais</h1>
    <p>Gérez les opérations disponibles (dépôt, retrait, transfert) et leurs tranches de frais.</p>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-diagram-3"></i> Types d'opérations</div>
        <a href="#" class="btn btn-mm-primary btn-sm"><i class="bi bi-plus-lg"></i> Nouveau type</a>
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
            <tr>
                <td><span class="badge badge-mm badge-depot">Dépôt</span></td>
                <td>Alimentation du compte client (gratuit)</td>
                <td><span class="badge badge-mm badge-depot">Actif</span></td>
                <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
            </tr>
            <tr>
                <td><span class="badge badge-mm badge-retrait">Retrait</span></td>
                <td>Sortie d'argent avec frais applicables</td>
                <td><span class="badge badge-mm badge-depot">Actif</span></td>
                <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
            </tr>
            <tr>
                <td><span class="badge badge-mm badge-transfert">Transfert</span></td>
                <td>Envoi vers un autre numéro avec frais applicables</td>
                <td><span class="badge badge-mm badge-depot">Actif</span></td>
                <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-layers"></i> Barème de frais &mdash; Retrait</div>

            <table class="table mm-table">
                <thead>
                    <tr>
                        <th>Tranche (Ar)</th>
                        <th>Frais fixe</th>
                        <th>Frais %</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0 &ndash; 10 000</td>
                        <td>200 Ar</td>
                        <td>&mdash;</td>
                        <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                    <tr>
                        <td>10 001 &ndash; 50 000</td>
                        <td>500 Ar</td>
                        <td>&mdash;</td>
                        <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                    <tr>
                        <td>50 001 &ndash; 200 000</td>
                        <td>&mdash;</td>
                        <td>1,5 %</td>
                        <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                    <tr>
                        <td>&gt; 200 000</td>
                        <td>&mdash;</td>
                        <td>2 %</td>
                        <td class="text-end"><a href="#" class="btn btn-mm-outline btn-sm"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="mm-card">
            <div class="mm-card-title"><i class="bi bi-plus-circle"></i> Ajouter une tranche</div>

            <form>
                <div class="mb-3">
                    <label class="form-label">Type d'opération</label>
                    <select class="form-select">
                        <option>Retrait</option>
                        <option>Transfert</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Montant min (Ar)</label>
                        <input type="number" class="form-control" placeholder="0">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Montant max (Ar)</label>
                        <input type="number" class="form-control" placeholder="10000">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Frais fixe (Ar)</label>
                        <input type="number" class="form-control" placeholder="200">
                    </div>
                    <div class="col-6 mb-4">
                        <label class="form-label">Frais (%)</label>
                        <input type="number" step="0.1" class="form-control" placeholder="0">
                    </div>
                </div>
                <button type="submit" class="btn btn-mm-primary w-100">
                    <i class="bi bi-check-lg"></i> Enregistrer la tranche
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
