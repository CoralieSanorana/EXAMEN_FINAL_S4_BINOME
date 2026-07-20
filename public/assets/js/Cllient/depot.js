document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montantInput');
    const soldeActuel = document.getElementById('soldeActuel');
    const montantError = document.getElementById('montantError');
    const soldeActuelDisplay = document.getElementById('soldeActuelDisplay');
    const montantDepot = document.getElementById('montantDepot');
    const nouveauSolde = document.getElementById('nouveauSolde');
    const submitBtn = document.getElementById('submitBtn');
    const depotForm = document.getElementById('depotForm');

    // Parse solde from text (remove spaces and convert to number)
    let solde = parseFloat(soldeActuel.textContent.replace(/\s/g, ''));
    if (isNaN(solde)) solde = 0;

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

        if (montant > 10000000) {
            montantError.textContent = 'Le montant maximum est de 10 000 000 Ar';
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        return true;
    }

    // Function to update summary
    function mettreAJourResume(montant) {
        if (!montant || montant <= 0) {
            montantDepot.textContent = '0 Ar';
            nouveauSolde.textContent = formatNombre(solde) + ' Ar';
            nouveauSolde.style.color = '#5c677d'; // Default color
            return;
        }

        const nouveauSoldeCalcule = solde + montant;

        montantDepot.textContent = formatNombre(montant) + ' Ar';
        nouveauSolde.textContent = formatNombre(nouveauSoldeCalcule) + ' Ar';

        // Change color based on new balance
        if (nouveauSoldeCalcule < 1000) {
            nouveauSolde.style.color = '#dc3545'; // Red
        } else if (nouveauSoldeCalcule < 5000) {
            nouveauSolde.style.color = '#ffc107'; // Yellow
        } else {
            nouveauSolde.style.color = '#28a745'; // Green for deposit
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
    depotForm.addEventListener('submit', function(e) {
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
