<?= $this->extend('layouts/layout_client') ?>

<?= $this->section('content') ?>

<div class="mm-page-header mb-4">
    <h1 class="fw-bold">Mon profil</h1>
    <p class="text-muted">Gérez vos informations personnelles et consultez vos statistiques en temps réel.</p>
</div>

<div class="row g-4 match-height">
    <!-- Colonne Gauche : Informations personnelles -->
    <div class="col-lg-4">
        <div class="mm-card h-100 d-flex flex-column justify-content-between p-4">
            <div>
                <div class="mm-card-title mb-4 fs-5 fw-semibold border-bottom pb-2">
                    <i class="bi bi-person text-secondary me-2"></i>Informations personnelles
                </div>
                
                <form action="/client/updateProfil" method="POST" id="profileForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Numéro de téléphone</label>
                        <input type="text" class="form-control bg-light border-0 py-2" value="<?= esc($compte['numero_telephone']) ?>" disabled>
                        <small class="text-muted text-xs"><i class="bi bi-info-circle me-1"></i>Le numéro ne peut pas être modifié</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Nom</label>
                        <input type="text" name="nom" class="form-control py-2" value="<?= esc($compte['nom'] ?? '') ?>" placeholder="Votre nom">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Prénom</label>
                        <input type="text" name="prenom" class="form-control py-2" value="<?= esc($compte['prenom'] ?? '') ?>" placeholder="Votre prénom">
                    </div>
                </form>
            </div>
            
            <button type="submit" form="profileForm" class="btn btn-mm-primary w-100 py-2 fw-medium">
                <i class="bi bi-check-lg me-1"></i> Mettre à jour le profil
            </button>
        </div>
    </div>

    <!-- Colonne Droite : Statistiques & Graphiques -->
    <div class="col-lg-8 d-flex flex-column g-4">
        <!-- Bloc des Cartes de Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
                <div class="mm-card text-center p-3 h-100 d-flex flex-column justify-content-center border-0 shadow-sm bg-white">
                    <div class="text-uppercase text-muted small fw-bold mb-1">Total</div>
                    <div class="fs-3 fw-bold text-secondary"><?= $statistiques['total'] ?></div>
                    <small class="text-muted-xs">opérations</small>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mm-card text-center p-3 h-100 d-flex flex-column justify-content-center border-0 shadow-sm bg-white">
                    <div class="text-uppercase text-muted small fw-bold mb-1">Dépôts</div>
                    <div class="fs-3 fw-bold text-success"><?= $statistiques['depot'] ?></div>
                    <small class="text-success fw-medium"><?= number_format($statistiques['montant_total_depot'], 0, '.', ' ') ?> Ar</small>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mm-card text-center p-3 h-100 d-flex flex-column justify-content-center border-0 shadow-sm bg-white">
                    <div class="text-uppercase text-muted small fw-bold mb-1">Retraits</div>
                    <div class="fs-3 fw-bold text-danger"><?= $statistiques['retrait'] ?></div>
                    <small class="text-danger fw-medium"><?= number_format($statistiques['montant_total_retrait'], 0, '.', ' ') ?> Ar</small>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mm-card text-center p-3 h-100 d-flex flex-column justify-content-center border-0 shadow-sm bg-white">
                    <div class="text-uppercase text-muted small fw-bold mb-1">Transferts</div>
                    <div class="fs-3 fw-bold text-primary"><?= $statistiques['transfert_envoye'] + $statistiques['transfert_recu'] ?></div>
                    <small class="text-muted text-xs">Envoyés: <?= $statistiques['transfert_envoye'] ?> | Reçus: <?= $statistiques['transfert_recu'] ?></small>
                </div>
            </div>
        </div>

        <!-- Bloc du Graphique d'Évolution (prend le reste de la hauteur de façon symétrique) -->
        <div class="mm-card flex-grow-1 p-4 d-flex flex-column justify-content-between">
            <div class="mm-card-title mb-3 fs-5 fw-semibold">
                <i class="bi bi-graph-up text-secondary me-2"></i>Évolution des transactions
            </div>
            <div class="position-relative w-100 flex-grow-1" style="min-height: 280px;">
                <canvas id="transactionsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
    const transactionsParMois = <?= json_encode($statistiques['transactions_par_mois']) ?>;
    const ctx = document.getElementById('transactionsChart').getContext('2d');
    
    const labels = Object.keys(transactionsParMois);
    const depotData = labels.map(date => transactionsParMois[date]?.depot || 0);
    const retraitData = labels.map(date => transactionsParMois[date]?.retrait || 0);
    const transfertData = labels.map(date => transactionsParMois[date]?.transfert || 0);
    const transfertRecuData = labels.map(date => transactionsParMois[date]?.transfert_recu || 0);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Dépôts',
                    data: depotData,
                    borderColor: '#1e8e3e',
                    backgroundColor: 'rgba(30, 142, 62, 0.04)',
                    tension: 0.25,
                    fill: true
                },
                {
                    label: 'Retraits',
                    data: retraitData,
                    borderColor: '#c5221f',
                    backgroundColor: 'rgba(197, 34, 31, 0.04)',
                    tension: 0.25,
                    fill: true
                },
                {
                    label: 'Transferts envoyés',
                    data: transfertData,
                    borderColor: '#1a56c4',
                    backgroundColor: 'rgba(26, 86, 196, 0.04)',
                    tension: 0.25,
                    fill: true
                },
                {
                    label: 'Transferts reçus',
                    data: transfertRecuData,
                    borderColor: '#07d943',
                    backgroundColor: 'rgba(7, 217, 67, 0.04)',
                    tension: 0.25,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, family: 'system-ui' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#999' },
                    grid: { color: 'rgba(0, 0, 0, 0.04)' }
                },
                x: {
                    ticks: { color: '#999' },
                    grid: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
</script>

<?= $this->endSection() ?>