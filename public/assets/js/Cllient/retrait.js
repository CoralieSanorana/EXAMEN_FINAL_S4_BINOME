document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montantInput');
    const soldeDisponible = document.getElementById('soldeDisponible');
    const montantError = document.getElementById('montantError');
    const soldeActuel = document.getElementById('soldeActuel');
    const montantDemande = document.getElementById('montantDemande');
    const fraisApplicables = document.getElementById('fraisApplicables');
    const nouveauSolde = document.getElementById('nouveauSolde');
    const submitBtn = document.getElementById('submitBtn');
    const retraitForm = document.getElementById('retraitForm');

    // Parse solde from text (remove spaces and convert to number)
    let solde = parseFloat(soldeDisponible.textContent.replace(/\s/g, ''));
    if (isNaN(solde)) solde = 0;

    // Barème des frais pour retrait (basé sur base.sql)
    const baremeFrais = [
        { min: 100, max: 1000, frais: 50 },
        { min: 1001, max: 5000, frais: 50 },
        { min: 5001, max: 10000, frais: 100 },
        { min: 10001, max: 25000, frais: 200 },
        { min: 25001, max: 50000, frais: 400 },
        { min: 50001, max: 100000, frais: 800 },
        { min: 100001, max: 250000, frais: 1500 },
        { min: 250001, max: 500000, frais: 1500 },
        { min: 500001, max: 1000000, frais: 2500 },
        { min: 1000001, max: 2000000, frais: 3000 }
    ];

    // Function to calculate fees based on amount
    function calculerFrais(montant) {
        for (const tranche of baremeFrais) {
            if (montant >= tranche.min && montant <= tranche.max) {
                return tranche.frais;
            }
        }
        // Default fee for amounts above the highest bracket
        return 3000;
    }

    // Function to format number with spaces
    function formatNombre(nombre) {
        return nombre.toLocaleString('fr-FR');
    }

    // Function to validate amount
    function validerMontant(montant) {
        montantError.style.display = 'none';
        submitBtn.disabled = false;

        if (!montant || montant <= 0) {
            return true; // Let HTML5 validation handle empty
        }

        if (montant < 100) {
            montantError.textContent = 'Le montant minimum est de 100 Ar';
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        const frais = calculerFrais(montant);
        const totalDebit = montant + frais;

        if (montant > solde) {
            montantError.textContent = 'Solde insuffisant pour effectuer ce retrait';
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (totalDebit > solde) {
            montantError.textContent = `Solde insuffisant (incluant les frais de ${formatNombre(frais)} Ar)`;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        return true;
    }

    // Function to update summary
    function mettreAJourResume(montant) {
        if (!montant || montant <= 0) {
            montantDemande.textContent = '0 Ar';
            fraisApplicables.textContent = '0 Ar';
            nouveauSolde.textContent = formatNombre(solde) + ' Ar';
            return;
        }

        const frais = calculerFrais(montant);
        const totalDebit = montant + frais;
        const nouveauSoldeCalcule = solde - totalDebit;

        montantDemande.textContent = formatNombre(montant) + ' Ar';
        fraisApplicables.textContent = formatNombre(frais) + ' Ar';
        nouveauSolde.textContent = formatNombre(nouveauSoldeCalcule) + ' Ar';

        // Change color if balance is low
        if (nouveauSoldeCalcule < 1000) {
            nouveauSolde.style.color = '#dc3545'; // Red
        } else if (nouveauSoldeCalcule < 5000) {
            nouveauSolde.style.color = '#ffc107'; // Yellow
        } else {
            nouveauSolde.style.color = '#5c677d'; // Default
        }
    }

    // Event listener for amount input
    montantInput.addEventListener('input', function() {
        const montant = parseFloat(this.value);
        
        if (isNaN(montant) || montant <= 0) {
            montantError.style.display = 'none';
            submitBtn.disabled = false;
            mettreAJourResume(0);
            return;
        }

        validerMontant(montant);
        mettreAJourResume(montant);
    });

    // Form submission validation
    retraitForm.addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value);
        
        if (!validerMontant(montant)) {
            e.preventDefault();
            return;
        }

        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement en cours...';
    });

    // Initialize
    mettreAJourResume(0);
});
