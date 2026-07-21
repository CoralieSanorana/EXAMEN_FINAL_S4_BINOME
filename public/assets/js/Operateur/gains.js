    function applyFilters() {
        const filter = document.getElementById('filterType').value;
        const period = document.getElementById('filterPeriod').value;
        window.location.href = `<?= base_url('operateur/gains') ?>?filter=${filter}&period=${period}`;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
                datasets: [
                    {
                        label: 'Gains Internes (Opérateur)',
                        data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                        borderColor: '#1e8e3e',
                        backgroundColor: 'rgba(30, 142, 62, 0.08)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Gains Externes (Commissions)',
                        data: [5000, 8000, 6000, 10000, 12000, 15000, 14000],
                        borderColor: '#073a4b', 
                        backgroundColor: 'rgba(111, 66, 193, 0.08)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });