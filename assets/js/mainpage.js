// Déclaration des variables globales
let loginButton;
let popover;
let togglePassword;
let passwordInput;
let socialButtons;
let loginForm;
let errorMessage;

// Initialisation des éléments du DOM
function initializeElements() {
    loginButton = document.getElementById('loginButton');
    popover = document.getElementById('popover');
    togglePassword = document.querySelector('.toggle-password');
    passwordInput = document.querySelector('#password');
    socialButtons = document.querySelectorAll('.social-button');
    loginForm = document.querySelector('.login-form');
    errorMessage = document.querySelector('#error-message');
}

// Configuration du popup de connexion
function setupLoginPopup() {
    if (loginButton && popover) {
        loginButton.addEventListener('click', () => {
            popover.showPopover();
        });

        document.addEventListener('click', (event) => {
            if (!popover.contains(event.target) && !loginButton.contains(event.target)) {
                popover.hidePopover();
            }
        });
    }
}

// Gestion de la visibilité du mot de passe
function setupPasswordToggle() {
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = togglePassword.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
}

// Gestion du formulaire de connexion
function setupLoginForm() {
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Désactiver le bouton pendant la soumission
            const submitButton = loginForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.querySelector('span').textContent = 'Connexion...';
            
            // Récupérer les données du formulaire
            const formData = new FormData(loginForm);
            
            // Envoyer la requête AJAX
            fetch('auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirection vers la page principale
                    window.location.href = 'mainpagecon.php';
                } else {
                    // Afficher le message d'erreur
                    if (errorMessage) {
                        errorMessage.textContent = data.message;
                        errorMessage.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                if (errorMessage) {
                    errorMessage.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                    errorMessage.style.display = 'block';
                }
            })
            .finally(() => {
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.querySelector('span').textContent = 'Connexion';
            });
        });
    }
}

// Animation des boutons sociaux
function setupSocialButtons() {
    socialButtons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            button.style.transform = 'translateY(-5px)';
            button.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
        });

        button.addEventListener('mouseleave', () => {
            button.style.transform = 'translateY(0)';
            button.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
        });
    });
}

// Configuration du formulaire de rendez-vous
function setupAppointmentForm() {
    const appointmentForm = document.querySelector('.appointment-form');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(appointmentForm);
            const data = Object.fromEntries(formData.entries());
            
            // Ici, vous pouvez ajouter la logique de traitement du formulaire
            console.log('Données du formulaire:', data);
        });
    }
}

// Animation au défilement
function setupScrollAnimation() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(element => observer.observe(element));
}

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', () => {
    initializeElements();
    setupLoginPopup();
    setupPasswordToggle();
    setupLoginForm();
    setupSocialButtons();
    setupAppointmentForm();
    setupScrollAnimation();
}); 