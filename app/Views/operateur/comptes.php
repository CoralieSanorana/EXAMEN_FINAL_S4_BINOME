<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Comptes clients</h1>
    <p>Consultation globale de la situation des comptes enregistrés.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Nombre de comptes</div>
            <div class="mm-stat-value"><?= $stats['nombre_comptes'] ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Solde total cumulé</div>
            <div class="mm-stat-value accent"><?= $stats['solde_total'] ?> Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Solde moyen / compte</div>
            <div class="mm-stat-value"><?= $stats['solde_moyen'] ?> Ar</div>
        </div>
    </div>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-people"></i> Liste des comptes</div>
        <form action="<?= base_url('operateur/comptes') ?>" method="get" class="input-group" style="max-width:280px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Rechercher un numéro..." value="<?= esc($search ?? '') ?>">
        </form>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Opérateur</th>
                <th>Date de création</th>
                <th class="text-end">Solde</th>
                <th class="text-end">Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($comptes)): ?>
                <?php foreach ($comptes as $compte): ?>
                    <tr>
                        <td><strong><?= $compte['numero_telephone'] ?></strong></td>
                        <td><?= $compte['nom'] ?> <?= $compte['prenom'] ?></td>
                        <td><?= date('d/m/Y', strtotime($compte['cree_le'] ?? 'now')) ?></td>
                        <td class="text-end"><?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</td>
                        <td class="text-end">
                            <span class="badge badge-mm badge-depot">Actif</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Aucun compte trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav class="d-flex justify-content-end mt-3">
        <?= $pager ?>
    </nav>
</div>

<?= $this->endSection() ?>
