document.addEventListener('DOMContentLoaded', function() {
    const numeroEmetteur = document.getElementById('numeroEmetteur');
    const destinatairesContainer = document.getElementById('destinatairesContainer');
    const addDestinataireBtn = document.getElementById('addDestinataireBtn');
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
    const prefixesOperateurs = Array.isArray(window.prefixesOperateurs) ? window.prefixesOperateurs : [];
    const prefixeVersOperateur = {};
    const rechercheTimers = new WeakMap();

    prefixesOperateurs.forEach(item => {
        if (item && item.prefixe) {
            prefixeVersOperateur[String(item.prefixe)] = item;
        }
    });

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

    function trouverOperateur(numero) {
        const prefixe = String(numero || '').substring(0, 3);
        return prefixeVersOperateur[prefixe] || null;
    }

    function getRows() {
        return Array.from(destinatairesContainer.querySelectorAll('.destinataire-row'));
    }

    function creerLigne(numero = '') {
        const row = document.createElement('div');
        row.className = 'card shadow-sm mb-3 destinataire-row border-light';
        row.innerHTML = `
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-secondary mb-2">
                            <i class="bi bi-person-fill text-primary me-1"></i> Destinataire N°
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-telephone-plus"></i>
                            </span>
                            <input type="text" name="numero_destinataires[]" class="form-control destinataire-input border-start-0 ps-0" placeholder="Ex : 0341234567" value="${numero}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-secondary mb-2">Identité du client</label>
                        <div class="destinataire-client small d-flex align-items-center bg-light text-muted border rounded px-3" style="height: 38px;">
                            <i class="bi bi-hourglass-split me-2"></i>En attente de saisie...
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-sm add-row-btn w-50" style="height: 38px;" title="Ajouter un destinataire">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn w-50" style="height: 38px;" title="Supprimer">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        destinatairesContainer.appendChild(row);
        attacherLigne(row);
        renumeroterLignes();
        return row;
    }

    function renumeroterLignes() {
        getRows().forEach((row, index) => {
            const label = row.querySelector('.col-md-5 .form-label');
            if (label) {
                label.innerHTML = `<i class="bi bi-person-fill text-primary me-1"></i> Destinataire N°${index + 1}`;
            }
        });

        const removeButtons = destinatairesContainer.querySelectorAll('.remove-row-btn');
        const disableRemove = getRows().length === 1;
        removeButtons.forEach(button => {
            button.disabled = disableRemove;
            if (disableRemove) {
                button.classList.add('opacity-50');
            } else {
                button.classList.remove('opacity-50');
            }
        });
    }

    function getInputValue(row) {
        const input = row.querySelector('.destinataire-input');
        return normaliserNumero(input ? input.value : '');
    }

    function setClientInfo(row, message, type = 'muted') {
        const box = row.querySelector('.destinataire-client');
        if (!box) return;

        row.dataset.lookupState = type;
        box.className = 'destinataire-client small d-flex align-items-center border rounded px-3';
        box.style.height = '38px';

        if (type === 'success') {
            box.classList.add('bg-success-subtle', 'border-success-subtle', 'text-success', 'fw-semibold');
            box.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${message}`;
        } else if (type === 'error') {
            box.classList.add('bg-danger-subtle', 'border-danger-subtle', 'text-danger');
            box.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${message}`;
        } else if (type === 'info') {
            box.classList.add('bg-info-subtle', 'border-info-subtle', 'text-info-emphasis');
            box.innerHTML = `<div class="spinner-border spinner-border-sm me-2" role="status"></div> ${message}`;
        } else {
            box.classList.add('bg-light', 'text-muted');
            box.innerHTML = `<i class="bi bi-hourglass-split me-2"></i> ${message}`;
        }
    }

    function extraireNumeros() {
        return getRows()
            .map(getInputValue)
            .filter(Boolean);
    }

    function analyserDestinataires() {
        const rows = getRows();
        const numeros = extraireNumeros();
        const erreurs = [];
        const doublons = [];
        const invalides = [];
        const soiMeme = [];
        const prefixesInconnus = [];
        const operateursDetectes = {};
        const compteParNumero = {};
        let verificationEnCours = false;
        let clientIntrouvable = false;

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
                return;
            }

            if (numero === emetteurNormalise) {
                soiMeme.push(numero);
            }

            const operateur = trouverOperateur(numero);
            if (!operateur) {
                prefixesInconnus.push(numero);
                return;
            }

            operateursDetectes[String(operateur.operateur_id)] = operateur.operateur_nom;
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

        if (prefixesInconnus.length) {
            erreurs.push(`Préfixes non reconnus : ${prefixesInconnus.join(', ')}`);
        }

        const operateurIds = Object.keys(operateursDetectes);
        if (uniques.length > 1 && operateurIds.length > 1) {
            erreurs.push('Les numéros d\'un envoi multiple doivent appartenir au même opérateur');
        }

        rows.forEach(row => {
            const numero = getInputValue(row);
            if (!numero) {
                return;
            }

            const state = row.dataset.lookupState || 'muted';
            if (state === 'info') {
                verificationEnCours = true;
            }
            if (state === 'error') {
                clientIntrouvable = true;
            }
        });

        if (!erreurs.length && verificationEnCours) {
            erreurs.push('Vérification des destinataires en cours...');
        }

        if (!erreurs.length && clientIntrouvable) {
            erreurs.push('Un ou plusieurs destinataires sont introuvables');
        }

        return {
            numeros: uniques,
            erreurs,
            operateurCommun: operateurIds.length === 1 ? operateursDetectes[operateurIds[0]] : null
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
        nouveauSolde.className = "fw-bold text-secondary";
    }

    function mettreAJourInfosDestinataires(analyse) {
        destinatairesInfo.style.display = 'none';
        destinatairesError.style.display = 'none';

        if (analyse.erreurs.length) {
            destinatairesError.innerHTML = `<i class="bi bi-exclamation-octagon-fill me-2"></i> ${analyse.erreurs.join('<br>')}`;
            destinatairesError.style.display = 'block';
            return;
        }

        if (!analyse.numeros.length) {
            return;
        }

        const suffixeOperateur = analyse.operateurCommun ? ` | <span class="badge bg-primary">${analyse.operateurCommun}</span>` : '';
        destinatairesResume.innerHTML = `<i class="bi bi-people-fill text-success me-2"></i><strong>${analyse.numeros.length}</strong> destinataire(s) prêt(s)${suffixeOperateur}`;
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
            nouveauSolde.className = "fw-bold text-secondary";
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
            montantError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${resume.erreur}`;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (resume.totalDebit > solde) {
            montantError.innerHTML = `<i class="bi bi-shield-slash-fill me-2"></i> Solde insuffisant (Débit requis : <strong>${formatNombre(resume.totalDebit)} Ar</strong>)`;
            montantError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (resume.nouveauSolde < 1000) {
            nouveauSolde.className = "fw-bold text-danger animate__animated animate__pulse";
        } else if (resume.nouveauSolde < 5000) {
            nouveauSolde.className = "fw-bold text-warning";
        } else {
            nouveauSolde.className = "fw-bold text-success";
        }

        submitBtn.disabled = false;
        return true;
    }

    function rechercherClientPourLigne(row) {
        const input = row.querySelector('.destinataire-input');
        if (!input) return;

        const numero = normaliserNumero(input.value);
        const regexNumero = /^[0-9]{8,10}$/;

        if (!numero) {
            setClientInfo(row, 'En attente de saisie...', 'muted');
            mettreAJourResume();
            return;
        }

        if (!regexNumero.test(numero)) {
            setClientInfo(row, 'Format invalide', 'error');
            mettreAJourResume();
            return;
        }

        if (numero === emetteurNormalise) {
            setClientInfo(row, 'Numéro émetteur interdit', 'error');
            mettreAJourResume();
            return;
        }

        const operateur = trouverOperateur(numero);
        if (!operateur) {
            setClientInfo(row, 'Préfixe inconnu', 'error');
            mettreAJourResume();
            return;
        }

        setClientInfo(row, `Vérification (${operateur.operateur_nom})...`, 'info');

        fetch(`/client/rechercherClient?numero=${encodeURIComponent(numero)}`)
            .then(response => response.json())
            .then(data => {
                const currentNumero = normaliserNumero(input.value);
                if (currentNumero !== numero) {
                    return;
                }

                if (data.success && data.client) {
                    const nomComplet = `${data.client.prenom || ''} ${data.client.nom || ''}`.trim();
                    setClientInfo(row, `${nomComplet} (${data.client.numero_telephone})`, 'success');
                } else {
                    setClientInfo(row, data.message || 'Client introuvable', 'error');
                }

                mettreAJourResume();
            })
            .catch(() => {
                setClientInfo(row, 'Erreur de connexion serveur', 'error');
                mettreAJourResume();
            });
    }

    function programmerRecherche(row) {
        const input = row.querySelector('.destinataire-input');
        if (!input) return;

        if (rechercheTimers.has(input)) {
            clearTimeout(rechercheTimers.get(input));
        }

        const timer = setTimeout(() => rechercherClientPourLigne(row), 350);
        rechercheTimers.set(input, timer);
    }

    function attacherLigne(row) {
        const input = row.querySelector('.destinataire-input');
        const addBtn = row.querySelector('.add-row-btn');
        const removeBtn = row.querySelector('.remove-row-btn');

        if (input) {
            input.addEventListener('input', function() {
                programmerRecherche(row);
                mettreAJourResume();
            });
            input.addEventListener('blur', function() {
                rechercherClientPourLigne(row);
            });
        }

        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const nouvelleLigne = creerLigne('');
                const nouvelInput = nouvelleLigne.querySelector('.destinataire-input');
                if (nouvelInput) {
                    nouvelInput.focus();
                }
                mettreAJourResume();
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (getRows().length === 1) {
                    const seulInput = row.querySelector('.destinataire-input');
                    if (seulInput) {
                        seulInput.value = '';
                    }
                    setClientInfo(row, 'En attente de saisie...', 'muted');
                } else {
                    row.remove();
                    renumeroterLignes();
                }

                mettreAJourResume();
            });
        }
    }

    addDestinataireBtn.addEventListener('click', function() {
        const nouvelleLigne = creerLigne('');
        const nouvelInput = nouvelleLigne.querySelector('.destinataire-input');
        if (nouvelInput) {
            nouvelInput.focus();
        }
        mettreAJourResume();
    });

    getRows().forEach(row => {
        attacherLigne(row);
        rechercherClientPourLigne(row);
    });

    montantInput.addEventListener('input', mettreAJourResume);
    inclureFraisRetrait.addEventListener('change', mettreAJourResume);

    transfertForm.addEventListener('submit', function(e) {
        if (!mettreAJourResume()) {
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Traitement de la transaction...';
    });

    renumeroterLignes();
    mettreAJourResume();
});