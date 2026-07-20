document.addEventListener('DOMContentLoaded', function() {
    const numeroDestinataire = document.getElementById('numeroDestinataire');
    const destinataireInfo = document.getElementById('destinataireInfo');
    const destinataireNom = document.getElementById('destinataireNom');
    const destinataireError = document.getElementById('destinataireError');
    const destinataireId = document.getElementById('destinataireId');
    const montantInput = document.getElementById('montantInput');
    const soldeDisponible = document.getElementById('soldeDisponible');
    const montantError = document.getElementById('montantError');
    const soldeActuel = document.getElementById('soldeActuel');
    const montantTransfert = document.getElementById('montantTransfert');
    const fraisApplicables = document.getElementById('fraisApplicables');
    const nouveauSolde = document.getElementById('nouveauSolde');
    const submitBtn = document.getElementById('submitBtn');
    const transfertForm = document.getElementById('transfertForm');

    // Parse solde from text (remove spaces and convert to number)
    let solde = parseFloat(soldeDisponible.textContent.replace(/\s/g, ''));
    if (isNaN(solde)) solde = 0;

    // Barème des frais pour transfert (basé sur base.sql)
    const baremeFrais = [
        { min: 100, max: 1000, frais: 50 },
        { min: 1001, max: 5000, frais: 50 },
        { min: 5001, max: 10000, frais: 100 },
        { min: 10001, max: 25000, frais: 200 },
        { min: 25001, max: 50000, frais: 400 },
        { min: 50011, max: 100000, frais: 800 },
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

    // Debounce function for AJAX
    let debounceTimer;
    function debounce(func, delay) {
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Function to search for recipient
    const rechercherDestinataire = debounce(function() {
        const numero = numeroDestinataire.value.replace(/\s/g, '');
        
        if (numero.length < 8) {
            destinataireInfo.style.display = 'none';
            destinataireError.style.display = 'none';
            destinataireId.value = '';
            submitBtn.disabled = true;
            return;
        }

        destinataireInfo.style.display = 'none';
        destinataireError.style.display = 'none';
        destinataireError.textContent = 'Recherche en cours...';
        destinataireError.style.display = 'block';
        submitBtn.disabled = true;

        fetch(`/client/rechercherClient?numero=${numero}`)
            .then(response => response.json())
            .then(data => {
                destinataireError.style.display = 'none';
                
                if (data.success) {
                    destinataireNom.textContent = `${data.client.prenom} ${data.client.nom} (${data.client.numero_telephone})`;
                    destinataireInfo.style.display = 'block';
                    destinataireId.value = data.client.id;
                    
                    // Enable submit if amount is valid
                    validerMontant(parseFloat(montantInput.value));
                } else {
                    destinataireError.textContent = data.message || 'Client introuvable';
                    destinataireError.style.display = 'block';
                    destinataireInfo.style.display = 'none';
                    destinataireId.value = '';
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                destinataireError.textContent = 'Erreur lors de la recherche';
                destinataireError.style.display = 'block';
                destinataireInfo.style.display = 'none';
                destinataireId.value = '';
                submitBtn.disabled = true;
            });
    }, 500);

    // Function to validate amount
    function validerMontant(montant) {
        montantError.style.display = 'none';
        
        if (!destinataireId.value) {
            submitBtn.disabled = true;
            return false;
        }

        if (!montant || montant <= 0) {
            submitBtn.disabled = true;
            return false;
        }

        if (montant < 100) {
            montantError.textContent = 'Le montant minimum est de 100 Ar';
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        const frais = calculerFrais(montant);
        const totalDebit = montant + frais;

        if (totalDebit > solde) {
            montantError.textContent = `Solde insuffisant (incluant les frais de ${formatNombre(frais)} Ar)`;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        submitBtn.disabled = false;
        return true;
    }

    // Function to update summary
    function mettreAJourResume(montant) {
        if (!montant || montant <= 0) {
            montantTransfert.textContent = '0 Ar';
            fraisApplicables.textContent = '0 Ar';
            nouveauSolde.textContent = formatNombre(solde) + ' Ar';
            nouveauSolde.style.color = '#5c677d'; // Default color
            return;
        }

        const frais = calculerFrais(montant);
        const totalDebit = montant + frais;
        const nouveauSoldeCalcule = solde - totalDebit;

        montantTransfert.textContent = formatNombre(montant) + ' Ar';
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

    // Event listener for recipient number input
    numeroDestinataire.addEventListener('input', rechercherDestinataire);

    // Event listener for amount input
    montantInput.addEventListener('input', function() {
        const montant = parseFloat(this.value);
        
        if (isNaN(montant) || montant <= 0) {
            montantError.style.display = 'none';
            submitBtn.disabled = !destinataireId.value;
            mettreAJourResume(0);
            return;
        }

        validerMontant(montant);
        mettreAJourResume(montant);
    });

    // Form submission validation
    transfertForm.addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value);
        
        if (!validerMontant(montant) || !destinataireId.value) {
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
