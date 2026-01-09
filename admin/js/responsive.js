/**
 * ============================================
 * ADMIN RESPONSIVE & MOBILE MENU
 * ============================================
 * 
 * Handle mobile sidebar toggle, responsive interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // ========== MOBILE SIDEBAR TOGGLE ==========
    
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;
    let mobileMenuBtn = document.querySelector('.mobile-hamburger');
    let sidebarOverlay = document.querySelector('.sidebar-overlay');
    
    // Create mobile overlay if not exists
    if (!sidebarOverlay && window.innerWidth < 768) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        body.appendChild(sidebarOverlay);
    }
    
    // Create hamburger button if not exists (for mobile)
    if (!mobileMenuBtn && window.innerWidth < 768) {
        mobileMenuBtn = document.createElement('button');
        mobileMenuBtn.className = 'mobile-hamburger';
        mobileMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
        body.appendChild(mobileMenuBtn);
    }
    
    // Toggle sidebar on hamburger click
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar) {
                sidebar.classList.toggle('mobile-active');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('active');
                }
                body.style.overflow = sidebar.classList.contains('mobile-active') ? 'hidden' : '';
            }
        });
    }
    
    // Close sidebar on overlay click
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeMobileSidebar();
        });
    }
    
    // Close sidebar on nav link click
    if (sidebar) {
        const navLinks = sidebar.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeMobileSidebar();
                }
            });
        });
    }
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        if (sidebar && sidebar.classList.contains('mobile-active')) {
            const isClickInside = sidebar.contains(event.target) || 
                                 (mobileMenuBtn && mobileMenuBtn.contains(event.target));
            if (!isClickInside) {
                closeMobileSidebar();
            }
        }
    });
    
    // Function to close sidebar
    function closeMobileSidebar() {
        if (sidebar) {
            sidebar.classList.remove('mobile-active');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            body.style.overflow = '';
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            // Tablet and up: remove mobile styles
            closeMobileSidebar();
            if (mobileMenuBtn) mobileMenuBtn.style.display = 'none';
            if (sidebarOverlay) sidebarOverlay.style.display = 'none';
        } else {
            // Mobile: show hamburger
            if (mobileMenuBtn) mobileMenuBtn.style.display = 'flex';
        }
    });
    
    // ========== TABLE RESPONSIVE SCROLL ==========
    
    const dataTables = document.querySelectorAll('.data-table');
    dataTables.forEach(table => {
        if (window.innerWidth < 992) {
            const container = table.closest('.table-container');
            if (container) {
                container.style.overflowX = 'auto';
            }
        }
    });
    
    // ========== FORM RESPONSIVE ==========
    
    const formGroups = document.querySelectorAll('.form-group');
    if (window.innerWidth < 768) {
        formGroups.forEach(group => {
            const label = group.querySelector('label');
            const input = group.querySelector('input, textarea, select');
            
            if (label && input) {
                label.style.marginBottom = '6px';
                input.style.padding = '10px 12px';
            }
        });
    }
    
    // ========== MODAL RESPONSIVE ==========
    
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        const content = modal.querySelector('.modal-content');
        if (content) {
            if (window.innerWidth < 768) {
                content.style.maxWidth = '90vw';
            }
        }
    });
    
    // ========== STAT CARDS RESPONSIVE ==========
    
    const statCards = document.querySelectorAll('.stat-card');
    if (window.innerWidth < 768) {
        statCards.forEach(card => {
            const value = card.querySelector('.card-value');
            const icon = card.querySelector('.card-icon');
            
            if (value) value.style.fontSize = '24px';
            if (icon) {
                icon.style.width = '40px';
                icon.style.height = '40px';
            }
        });
    }
});

// ========== DETECT MOBILE VIEWPORT CHANGES ==========

let lastWidth = window.innerWidth;
window.addEventListener('orientationchange', function() {
    setTimeout(function() {
        const newWidth = window.innerWidth;
        if ((lastWidth < 768 && newWidth >= 768) || 
            (lastWidth >= 768 && newWidth < 768)) {
            location.reload(); // Reload to properly adjust layout
        }
        lastWidth = newWidth;
    }, 100);
});
