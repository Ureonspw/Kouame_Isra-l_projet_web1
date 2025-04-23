document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const sections = document.querySelectorAll('.form-section');
    const indicators = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    let currentStep = 0;

    // Validation des champs email et mot de passe
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('mot_de_passe');
    const confirmPasswordInput = document.getElementById('confirm_password');

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = re.test(email);
        console.log("Validation email:", email, "Résultat:", isValid);
        return isValid;
    }

    function validatePassword(password) {
        const isValid = password.length >= 8;
        console.log("Validation mot de passe:", password.length, "caractères, Résultat:", isValid);
        return isValid;
    }

    function validateConfirmPassword(password, confirmPassword) {
        const isValid = password === confirmPassword;
        console.log("Validation confirmation mot de passe:", isValid);
        return isValid;
    }

    function showStep(step) {
        sections.forEach((section, index) => {
            section.classList.toggle('active', index === step);
        });
        
        indicators.forEach((indicator, index) => {
            indicator.classList.remove('active', 'completed');
            if (index < step) {
                indicator.classList.add('completed');
            } else if (index === step) {
                indicator.classList.add('active');
            }
        });

        prevBtn.style.display = step === 0 ? 'none' : 'block';
        nextBtn.style.display = step === sections.length - 1 ? 'none' : 'block';
        submitBtn.style.display = step === sections.length - 1 ? 'block' : 'none';
    }

    function validateStep(step) {
        const currentSection = sections[step];
        const inputs = currentSection.querySelectorAll('input[required], select[required]');
        let isValid = true;

        // Validation spéciale pour l'étape 1
        if (step === 0) {
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            console.log("Validation étape 1 - Email:", email);
            console.log("Validation étape 1 - Mot de passe:", password.length, "caractères");
            console.log("Validation étape 1 - Confirmation:", confirmPassword);

            if (!validateEmail(email)) {
                document.getElementById('email-error').textContent = 'Format d\'email invalide';
                emailInput.classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('email-error').textContent = '';
                emailInput.classList.remove('is-invalid');
            }

            if (!validatePassword(password)) {
                document.getElementById('password-error').textContent = 'Le mot de passe doit contenir au moins 8 caractères';
                passwordInput.classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('password-error').textContent = '';
                passwordInput.classList.remove('is-invalid');
            }

            if (!validateConfirmPassword(password, confirmPassword)) {
                document.getElementById('confirm-password-error').textContent = 'Les mots de passe ne correspondent pas';
                confirmPasswordInput.classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('confirm-password-error').textContent = '';
                confirmPasswordInput.classList.remove('is-invalid');
            }
        }

        // Validation des autres champs requis
        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', () => {
        currentStep--;
        showStep(currentStep);
    });

    form.addEventListener('submit', (e) => {
        if (!validateStep(currentStep)) {
            e.preventDefault();
        }
    });

    // Validation en temps réel
    form.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', () => {
            if (input.hasAttribute('required')) {
                if (input.value) {
                    input.classList.remove('is-invalid');
                } else {
                    input.classList.add('is-invalid');
                }
            }
        });
    });

    // Gestion de l'affichage du type de permis
    const permisSelect = document.getElementById('permis');
    const typePermisContainer = document.getElementById('type_permis_container');
    const typePermisSelect = document.getElementById('type_permis');

    permisSelect.addEventListener('change', function() {
        if (this.value === '1') {
            typePermisContainer.style.display = 'block';
            typePermisSelect.required = true;
        } else {
            typePermisContainer.style.display = 'none';
            typePermisSelect.required = false;
            typePermisSelect.value = '';
        }
    });

    // Gestion de l'ajout de diplômes
    const diplomesContainer = document.getElementById('diplomes-container');
    const addDiplomeBtn = document.getElementById('add-diplome');
    let diplomeCount = 1;

    addDiplomeBtn.addEventListener('click', function() {
        diplomeCount++;
        const newDiplome = document.createElement('div');
        newDiplome.className = 'diplome-section mb-4';
        newDiplome.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="diplome_nom[]" class="form-label required-field">Nom du diplôme</label>
                    <select class="form-select" name="diplome_nom[]" required>
                        <option value="">Sélectionner</option>
                        <option value="CEPE">CEPE</option>
                        <option value="BEPC">BEPC</option>
                        <option value="BAC">BAC</option>
                        <option value="BAC+1">BAC+1</option>
                        <option value="BAC+2">BAC+2</option>
                        <option value="BAC+3">BAC+3</option>
                        <option value="BAC+4">BAC+4</option>
                        <option value="BAC+5">BAC+5</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="diplome_annee[]" class="form-label required-field">Année d'obtention</label>
                    <input type="number" class="form-control" name="diplome_annee[]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="diplome_etablissement[]" class="form-label required-field">Établissement</label>
                    <input type="text" class="form-control" name="diplome_etablissement[]" required>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="document-upload">
                        <i class="fas fa-file-pdf"></i>
                        <label for="diplome_scan[]" class="form-label required-field">Scan du diplôme</label>
                        <input type="file" class="form-control" name="diplome_scan[]" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="document-info">Format accepté : PDF, JPG, PNG</div>
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-diplome">
                        <i class="fas fa-trash"></i> Supprimer ce diplôme
                    </button>
                </div>
            </div>
        `;
        diplomesContainer.appendChild(newDiplome);

        // Ajout de l'événement de suppression
        newDiplome.querySelector('.remove-diplome').addEventListener('click', function() {
            newDiplome.remove();
        });
    });
}); 