// Gestion de la sidebar
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarContainer = document.querySelector('.sidebar-container');

    if (sidebarToggle && sidebarContainer) {
        sidebarToggle.addEventListener('click', function() {
            sidebarContainer.classList.toggle('show');
        });
    }
});
