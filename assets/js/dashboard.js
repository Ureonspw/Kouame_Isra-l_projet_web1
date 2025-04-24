// Get all the background divs
var backgrounds = document.querySelectorAll('.background');
// Get the slider and the images
const slider = document.querySelector('.slider-images');
const images = Array.from(slider.children);

// Set the initial image index
let imageIndex = 0;

// Update the slider
function updateSlider() {
    // Remove the 'active', 'previous', 'next', and 'inactive' classes from all images
    images.forEach(image => {
        image.classList.remove('active', 'previous', 'next', 'inactive');
    });

    // Add the 'active' class to the current image
    images[imageIndex].classList.add('active');

    // Add the 'previous' class to the image before the current one
    if (imageIndex - 1 >= 0) {
        images[imageIndex - 1].classList.add('previous');
    } else {
        images[images.length - 1].classList.add('previous');
    }

    // Add the 'next' class to the image after the current one
    if (imageIndex + 1 < images.length) {
        images[imageIndex + 1].classList.add('next');
    } else {
        images[0].classList.add('next');
    }

    // Add the 'inactive' class to the other images
    images.forEach((image, index) => {
        if (index !== imageIndex && index !== (imageIndex - 1 + images.length) % images.length && index !== (imageIndex + 1) % images.length) {
            image.classList.add('inactive');
        }
    });

    // Set the opacity of all the background divs to 0
    backgrounds.forEach((background) => {
        background.style.opacity = 0;
    });
    // If the current image is active, set the opacity of the corresponding background div to 1
    if (images[imageIndex].classList.contains('active')) {
        backgrounds[imageIndex].style.opacity = 1;
    }
    // Update the image index
    imageIndex = (imageIndex + 1) % images.length;
}
updateSlider();
// Update the slider every 3 seconds
setInterval(updateSlider, 10000);

images[1].classList.add('next');
images[2].classList.add('inactive');
images[3].classList.add('inactive');
images[4].classList.add('previous');
images[0].classList.add('active');


    // Menu toggle functionality
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('menu-active');
    });

    // Navigation without page reload
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.currentTarget.getAttribute('data-target');
            
            // Update active menu item
            document.querySelectorAll('.sidebar-menu li').forEach(item => {
                item.classList.remove('active');
            });
            e.currentTarget.parentElement.classList.add('active');
            
            // Show target section
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(target).classList.add('active');
            
            // Close menu on mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                mainContent.classList.remove('menu-active');
            }
        });
    });

    // Close menu when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !menuToggle.contains(e.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            mainContent.classList.remove('menu-active');
        }
    });

// Fonctions pour la modal de changement de mot de passe
function showPasswordModal() {
    document.getElementById('passwordModal').style.display = 'block';
}

function hidePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('changePasswordForm').reset();
}

// Fermer la modal en cliquant en dehors
window.onclick = function(event) {
    const modal = document.getElementById('passwordModal');
    if (event.target == modal) {
        hidePasswordModal();
    }
}

// Gérer la soumission du formulaire
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        alert('Les nouveaux mots de passe ne correspondent pas');
        return;
    }
    
    // Ici, vous pouvez ajouter la logique pour envoyer les données au serveur
    // Par exemple :
    /*
    fetch('change_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            currentPassword: currentPassword,
            newPassword: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mot de passe changé avec succès');
            hidePasswordModal();
        } else {
            alert(data.message || 'Erreur lors du changement de mot de passe');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
    */
    
    // Pour l'instant, on affiche juste un message
    alert('Fonctionnalité de changement de mot de passe à implémenter');
    hidePasswordModal();
});
