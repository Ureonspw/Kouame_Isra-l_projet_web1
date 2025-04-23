document.addEventListener('DOMContentLoaded', function() {
    // Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Section Navigation
    const menuItems = document.querySelectorAll('.sidebar-menu li');
    const sections = document.querySelectorAll('.content-section');

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            // Remove active class from all menu items and sections
            menuItems.forEach(i => i.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));

            // Add active class to clicked menu item
            item.classList.add('active');

            // Show corresponding section
            const sectionId = item.getAttribute('data-section');
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }

            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Notification System
    const notificationBtn = document.querySelector('.notification-btn');
    const createNotification = (message, type = 'info') => {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    };

    notificationBtn.addEventListener('click', () => {
        createNotification('Vous avez de nouvelles notifications', 'info');
    });

    // Calendar Widget
    const calendar = document.querySelector('.calendar');
    if (calendar) {
        const today = new Date();
        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1).getDay();

        let calendarHTML = '';
        
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += '<div class="calendar-day"></div>';
        }

        // Add days of the month
        for (let i = 1; i <= daysInMonth; i++) {
            const isToday = i === today.getDate();
            const hasEvent = i % 5 === 0; // Example: events on every 5th day
            const classes = ['calendar-day'];
            if (isToday) classes.push('today');
            if (hasEvent) classes.push('event');
            
            calendarHTML += `<div class="${classes.join(' ')}">${i}</div>`;
        }

        calendar.innerHTML = calendarHTML;
    }

    // Tooltips
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = element.getAttribute('data-tooltip');
        element.appendChild(tooltip);

        element.addEventListener('mouseenter', () => {
            tooltip.classList.add('show');
        });

        element.addEventListener('mouseleave', () => {
            tooltip.classList.remove('show');
        });
    });

    // Responsive adjustments
    function handleResize() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();
}); 