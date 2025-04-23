document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const menuToggle = document.getElementById('menuToggle');
    const adminMenu = document.querySelector('.admin-menu');
    const menuSections = document.querySelectorAll('.menu-section');
    const menuItems = document.querySelectorAll('.admin-menu li');
    const closeSidebar = document.getElementById('closeSidebar');
    const adminSidebar = document.querySelector('.admin-sidebar');

    // Menu Toggle
    menuToggle.addEventListener('click', function() {
        adminMenu.classList.toggle('active');
        this.classList.toggle('active');
    });

    // Close Sidebar Button
    closeSidebar.addEventListener('click', function() {
        adminSidebar.classList.remove('active');
    });

    // Menu Sections Toggle
    menuSections.forEach(section => {
        const header = section.querySelector('h3');
        const submenu = section.querySelector('.submenu');
        const icon = header.querySelector('i');

        // Fermer tous les sous-menus au chargement
        submenu.classList.remove('active');
        icon.classList.add('fa-chevron-down');
        icon.classList.remove('fa-chevron-up');

        header.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Si le sous-menu est déjà actif, on le ferme
            if (submenu.classList.contains('active')) {
                submenu.classList.remove('active');
                icon.classList.add('fa-chevron-down');
                icon.classList.remove('fa-chevron-up');
            } else {
                // Fermer tous les autres sous-menus
                menuSections.forEach(otherSection => {
                    const otherSubmenu = otherSection.querySelector('.submenu');
                    const otherIcon = otherSection.querySelector('h3 i');
                    otherSubmenu.classList.remove('active');
                    otherIcon.classList.add('fa-chevron-down');
                    otherIcon.classList.remove('fa-chevron-up');
                });

                // Ouvrir le sous-menu actuel
                submenu.classList.add('active');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });

    // Menu Items Click
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Remove active class from all menu items
            document.querySelectorAll('.admin-menu li').forEach(li => {
                li.classList.remove('active');
            });
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            const sectionId = this.getAttribute('data-section');
            if (sectionId) {
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            }

            // Close sidebar on mobile after clicking a menu item
            if (window.innerWidth <= 1024) {
                adminSidebar.classList.remove('active');
            }
        });
    });

    // Initialize charts
    let inscriptionsChart = null;
    let repartitionChart = null;

    const initializeCharts = () => {
        // Destroy existing charts if they exist
        if (inscriptionsChart) {
            inscriptionsChart.destroy();
            inscriptionsChart = null;
        }
        if (repartitionChart) {
            repartitionChart.destroy();
            repartitionChart = null;
        }

        const inscriptionsCtx = document.getElementById('inscriptionsChart');
        const repartitionCtx = document.getElementById('repartitionChart');

        if (inscriptionsCtx) {
            inscriptionsChart = new Chart(inscriptionsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Inscriptions',
                        data: [120, 190, 150, 220, 180, 250],
                        borderColor: '#3498db',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(52, 152, 219, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        if (repartitionsCtx) {
            repartitionChart = new Chart(repartitionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['ENA', 'INP-HB', 'ENS', 'Autres'],
                    datasets: [{
                        data: [40, 25, 20, 15],
                        backgroundColor: [
                            '#ff9f43',
                            '#3498db',
                            '#28c76f',
                            '#ea5455'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    };

    // Initialize charts on load
    initializeCharts();

    // Reinitialize charts on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initializeCharts();
        }, 250);
    });
});