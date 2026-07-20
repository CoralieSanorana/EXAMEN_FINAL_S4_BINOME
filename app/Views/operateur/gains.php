<?= $this->extend('layouts/layout_operateur') ?>

<?= $this->section('content') ?>

<div class="mm-page-header">
    <h1>Situation des gains</h1>
    <p>Vue d'ensemble des revenus générés par les frais de retrait et de transfert.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Gains totaux (retrait)</div>
            <div class="mm-stat-value positive"><?= $stats['gains_retrait'] ?> Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Gains totaux (transfert)</div>
            <div class="mm-stat-value accent"><?= $stats['gains_transfert'] ?> Ar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mm-stat-card">
            <div class="mm-stat-label">Total cumulé</div>
            <div class="mm-stat-value"><?= $stats['total_cumule'] ?> Ar</div>
        </div>
    </div>
</div>

<div class="mm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mm-card-title mb-0"><i class="bi bi-cash-stack"></i> Détail des frais perçus</div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width:auto;" onchange="window.location.href='<?= base_url('operateur/gains') ?>?filter=' + this.value">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="RETRAIT" <?= $filter === 'RETRAIT' ? 'selected' : '' ?>>Retrait</option>
                <option value="TRANSFERT" <?= $filter === 'TRANSFERT' ? 'selected' : '' ?>>Transfert</option>
            </select>
        </div>
    </div>

    <table class="table mm-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Numéro client</th>
                <th>Opération</th>
                <th>Montant</th>
                <th class="text-end">Frais perçus</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($transaction['effectue_le'])) ?></td>
                        <td><?= $transaction['compte_source_numero'] ?? 'N/A' ?></td>
                        <td>
                            <span class="badge badge-mm <?= $transaction['type_code'] === 'RETRAIT' ? 'badge-retrait' : 'badge-transfert' ?>">
                                <?= $transaction['type_nom'] ?>
                            </span>
                        </td>
                        <td><?= number_format($transaction['montant'], 0, ',', ' ') ?> Ar</td>
                        <td class="text-end"><?= number_format($transaction['frais_appliques'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Aucune transaction trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav class="d-flex justify-content-end mt-3">
        <?= $pager ?>
    </nav>
</div>

<?= $this->endSection() ?>
