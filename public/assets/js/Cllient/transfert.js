document.addEventListener('DOMContentLoaded', function() {
    const numeroEmetteur = document.getElementById('numeroEmetteur');
    const numerosDestinataires = document.getElementById('numerosDestinataires');
    const destinatairesInfo = document.getElementById('destinatairesInfo');
    const destinatairesResume = document.getElementById('destinatairesResume');
    const destinatairesPreview = document.getElementById('destinatairesPreview');
    const destinatairesError = document.getElementById('destinatairesError');
    const montantInput = document.getElementById('montantInput');
    const soldeDisponible = document.getElementById('soldeDisponible');
    const montantError = document.getElementById('montantError');
    const nombreDestinataires = document.getElementById('nombreDestinataires');
    const montantBase = document.getElementById('montantBase');
    const partParDestinataire = document.getElementById('partParDestinataire');
    const fraisRetrait = document.getElementById('fraisRetrait');
    const montantTransfert = document.getElementById('montantTransfert');
    const fraisApplicables = document.getElementById('fraisApplicables');
    const totalDebit = document.getElementById('totalDebit');
    const nouveauSolde = document.getElementById('nouveauSolde');
    const inclureFraisRetrait = document.getElementById('inclureFraisRetrait');
    const submitBtn = document.getElementById('submitBtn');
    const transfertForm = document.getElementById('transfertForm');

    let solde = parseFloat(soldeDisponible.textContent.replace(/\s/g, '').replace(',', '.'));
    if (isNaN(solde)) solde = 0;

    const emetteurNormalise = normaliserNumero(numeroEmetteur.value);

    const baremeRetrait = [
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

    const baremeTransfert = [
        { min: 100, max: 1000, frais: 50 },
        { min: 1001, max: 5000, frais: 50 },
        { min: 5001, max: 10000, frais: 100 },
        { min: 10011, max: 25000, frais: 200 },
        { min: 25001, max: 50000, frais: 400 },
        { min: 50011, max: 100000, frais: 800 },
        { min: 100001, max: 250000, frais: 1500 },
        { min: 250001, max: 500000, frais: 1500 },
        { min: 500001, max: 1000000, frais: 2500 },
        { min: 1000001, max: 2000000, frais: 3000 }
    ];

    function normaliserNumero(numero) {
        return String(numero || '').replace(/[\s\-.]/g, '').trim();
    }

    function formatNombre(nombre) {
        const valeur = Number(nombre) || 0;
        const avecDecimales = Math.abs(valeur - Math.round(valeur)) > 0.009;

        return valeur.toLocaleString('fr-FR', {
            minimumFractionDigits: avecDecimales ? 2 : 0,
            maximumFractionDigits: 2
        });
    }

    function calculerFrais(montant, bareme) {
        if (!montant || montant <= 0) {
            return 0;
        }

        for (const tranche of bareme) {
            if (montant >= tranche.min && montant <= tranche.max) {
                return tranche.frais;
            }
        }

        return bareme.length ? bareme[bareme.length - 1].frais : 0;
    }

    function extraireNumeros() {
        return (numerosDestinataires.value || '')
            .split(/[\n,;]+/)
            .map(normaliserNumero)
            .filter(Boolean);
    }

    function analyserDestinataires() {
        const numeros = extraireNumeros();
        const erreurs = [];
        const doublons = [];
        const invalides = [];
        const soiMeme = [];
        const compteParNumero = {};

        numeros.forEach(numero => {
            compteParNumero[numero] = (compteParNumero[numero] || 0) + 1;
        });

        Object.entries(compteParNumero).forEach(([numero, total]) => {
            if (total > 1) {
                doublons.push(numero);
            }
        });

        const uniques = [...new Set(numeros)];
        const regexNumero = /^[0-9]{8,10}$/;

        uniques.forEach(numero => {
            if (!regexNumero.test(numero)) {
                invalides.push(numero);
            }

            if (numero === emetteurNormalise) {
                soiMeme.push(numero);
            }
        });

        if (doublons.length) {
            erreurs.push(`Numéros en doublon : ${doublons.join(', ')}`);
        }

        if (invalides.length) {
            erreurs.push(`Format invalide : ${invalides.join(', ')}`);
        }

        if (soiMeme.length) {
            erreurs.push(`Vous ne pouvez pas vous transférer à vous-même : ${soiMeme.join(', ')}`);
        }

        return {
            numeros: uniques,
            erreurs
        };
    }

    function calculerResume(montant, totalDestinataires, inclureRetrait) {
        if (!montant || montant <= 0 || totalDestinataires <= 0) {
            return null;
        }

        const partBase = montant / totalDestinataires;

        if (partBase < 100) {
            return {
                erreur: 'La part par destinataire doit être au moins de 100 Ar'
            };
        }

        const fraisRetraitUnitaire = inclureRetrait ? calculerFrais(partBase, baremeRetrait) : 0;
        const montantTransfertUnitaire = partBase + fraisRetraitUnitaire;
        const fraisTransfertUnitaire = calculerFrais(montantTransfertUnitaire, baremeTransfert);
        const totalDebitUnitaire = montantTransfertUnitaire + fraisTransfertUnitaire;

        return {
            partBase,
            fraisRetraitTotal: fraisRetraitUnitaire * totalDestinataires,
            montantTransfertTotal: montantTransfertUnitaire * totalDestinataires,
            fraisTransfertTotal: fraisTransfertUnitaire * totalDestinataires,
            totalDebit: totalDebitUnitaire * totalDestinataires,
            nouveauSolde: solde - (totalDebitUnitaire * totalDestinataires)
        };
    }

    function reinitialiserResume() {
        nombreDestinataires.textContent = '0';
        montantBase.textContent = '0 Ar';
        partParDestinataire.textContent = '0 Ar';
        fraisRetrait.textContent = '0 Ar';
        montantTransfert.textContent = '0 Ar';
        fraisApplicables.textContent = '0 Ar';
        totalDebit.textContent = '0 Ar';
        nouveauSolde.textContent = formatNombre(solde) + ' Ar';
        nouveauSolde.style.color = '#5c677d';
    }

    function mettreAJourInfosDestinataires(analyse) {
        destinatairesInfo.style.display = 'none';
        destinatairesError.style.display = 'none';

        if (analyse.erreurs.length) {
            destinatairesError.innerHTML = analyse.erreurs.join('<br>');
            destinatairesError.style.display = 'block';
            return;
        }

        if (!analyse.numeros.length) {
            return;
        }

        destinatairesResume.textContent = `${analyse.numeros.length} destinataire(s) prêt(s) à recevoir le transfert.`;
        destinatairesPreview.textContent = analyse.numeros.join(', ');
        destinatairesInfo.style.display = 'block';
    }

    function mettreAJourResume() {
        const analyse = analyserDestinataires();
        const montant = parseFloat(montantInput.value);
        const inclureRetrait = inclureFraisRetrait.checked;

        mettreAJourInfosDestinataires(analyse);
        montantError.style.display = 'none';

        if (analyse.erreurs.length || !analyse.numeros.length) {
            reinitialiserResume();
            submitBtn.disabled = true;
            return false;
        }

        const resume = calculerResume(montant, analyse.numeros.length, inclureRetrait);

        if (!resume) {
            nombreDestinataires.textContent = String(analyse.numeros.length);
            montantBase.textContent = formatNombre(montant || 0) + ' Ar';
            partParDestinataire.textContent = '0 Ar';
            fraisRetrait.textContent = '0 Ar';
            montantTransfert.textContent = '0 Ar';
            fraisApplicables.textContent = '0 Ar';
            totalDebit.textContent = '0 Ar';
            nouveauSolde.textContent = formatNombre(solde) + ' Ar';
            nouveauSolde.style.color = '#5c677d';
            submitBtn.disabled = true;
            return false;
        }

        nombreDestinataires.textContent = String(analyse.numeros.length);
        montantBase.textContent = formatNombre(montant) + ' Ar';
        partParDestinataire.textContent = formatNombre(resume.partBase) + ' Ar';
        fraisRetrait.textContent = formatNombre(resume.fraisRetraitTotal) + ' Ar';
        montantTransfert.textContent = formatNombre(resume.montantTransfertTotal) + ' Ar';
        fraisApplicables.textContent = formatNombre(resume.fraisTransfertTotal) + ' Ar';
        totalDebit.textContent = formatNombre(resume.totalDebit) + ' Ar';
        nouveauSolde.textContent = formatNombre(resume.nouveauSolde) + ' Ar';

        if (resume.erreur) {
            montantError.textContent = resume.erreur;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (resume.totalDebit > solde) {
            montantError.textContent = `Solde insuffisant (débit total estimé : ${formatNombre(resume.totalDebit)} Ar)`;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (resume.nouveauSolde < 1000) {
            nouveauSolde.style.color = '#dc3545';
        } else if (resume.nouveauSolde < 5000) {
            nouveauSolde.style.color = '#ffc107';
        } else {
            nouveauSolde.style.color = '#5c677d';
        }

        submitBtn.disabled = false;
        return true;
    }

    numerosDestinataires.addEventListener('input', mettreAJourResume);
    montantInput.addEventListener('input', mettreAJourResume);
    inclureFraisRetrait.addEventListener('change', mettreAJourResume);

    transfertForm.addEventListener('submit', function(e) {
        if (!mettreAJourResume()) {
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement en cours...';
    });

    mettreAJourResume();
});
