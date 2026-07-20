document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const telephoneInput = document.getElementById('telephoneInput');
    const telephoneError = document.getElementById('telephoneError');
    const submitBtn = document.getElementById('submitBtn');

    // Function to validate phone number
    function validerTelephone(telephone) {
        telephoneError.style.display = 'none';
        submitBtn.disabled = false;

        if (!telephone) {
            return true; // Let HTML5 validation handle empty
        }

        // Remove spaces and special characters
        const cleaned = telephone.replace(/\s/g, '').replace(/[-.]/g, '');

        // Check if it's a valid Madagascar phone number (starts with 033, 034, 038, 037, etc.)
        const validPrefixes = ['033', '034', '038', '037', '032', '030', '039'];
        const prefix = cleaned.substring(0, 3);

        if (!validPrefixes.includes(prefix)) {
            telephoneError.textContent = 'Le numéro doit commencer par un préfixe valide (033, 034, 038, 037, 032, 030, 039)';
            telephoneError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (cleaned.length !== 10) {
            telephoneError.textContent = 'Le numéro doit contenir 10 chiffres';
            telephoneError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        if (!/^\d+$/.test(cleaned)) {
            telephoneError.textContent = 'Le numéro ne doit contenir que des chiffres';
            telephoneError.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }

        return true;
    }

    // Format phone number as user types (add spaces for readability)
    function formaterTelephone(value) {
        // Remove all non-digit characters
        const cleaned = value.replace(/\D/g, '');
        
        // Format: 033 12 345 67
        if (cleaned.length >= 3) {
            let formatted = cleaned.substring(0, 3);
            if (cleaned.length > 3) {
                formatted += ' ' + cleaned.substring(3, 5);
            }
            if (cleaned.length > 5) {
                formatted += ' ' + cleaned.substring(5, 8);
            }
            if (cleaned.length > 8) {
                formatted += ' ' + cleaned.substring(8, 10);
            }
            return formatted;
        }
        
        return cleaned;
    }

    // Event listener for phone input
    telephoneInput.addEventListener('input', function() {
        const value = this.value;
        
        // Format the number
        this.value = formaterTelephone(value);
        
        // Validate
        validerTelephone(this.value);
    });

    // Form submission validation
    loginForm.addEventListener('submit', function(e) {
        const telephone = telephoneInput.value;
        
        if (!validerTelephone(telephone)) {
            e.preventDefault();
            return;
        }

        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Connexion en cours...';
    });

    // Initialize with validation
    if (telephoneInput.value) {
        validerTelephone(telephoneInput.value);
    }
});
