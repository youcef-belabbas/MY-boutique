/**
 * MY Clothing - Main JavaScript
 * Handles navigation menu toggling and other UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    window.toggleMenu = function() {
        document.querySelector('.nav-menu').classList.toggle('active');
    };
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const navMenu = document.querySelector('.nav-menu');
        const navToggle = document.querySelector('.nav-toggle');
        
        if (navMenu.classList.contains('active') && 
            !navMenu.contains(event.target) && 
            event.target !== navToggle) {
            navMenu.classList.remove('active');
        }
    });
    
    // Close menu when clicking on a link (for mobile)
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.remove('active');
        });
    });
    
    // Adjust padding when the window is resized
    function adjustPadding() {
        const navbar = document.querySelector('.navbar');
        const spacer = document.querySelector('.navbar-spacer');
        
        if (navbar && spacer) {
            // Get the actual height of the navbar
            const navHeight = navbar.offsetHeight;
            spacer.style.height = navHeight + 'px';
        }
    }
    
    // Call once on load and on window resize
    adjustPadding();
    window.addEventListener('resize', adjustPadding);
    
    // Handle active navigation item highlighting
    function highlightCurrentPage() {
        const currentPage = window.location.href;
        const navLinks = document.querySelectorAll('.nav-menu a');
        
        navLinks.forEach(link => {
            if (currentPage.includes(link.getAttribute('href'))) {
                link.classList.add('active-nav');
            }
        });
    }
    
    highlightCurrentPage();
});

function addToCart(productId) {
    fetch('index.php?controller=product&action=addToCart&product_id=' + productId, { method: 'POST' })
        .then(() => location.reload());
}

function updateCart(productId, change) {
    fetch('index.php?controller=product&action=updateCart&product_id=' + productId + '&change=' + change, { method: 'POST' })
        .then(() => location.reload());
}

function removeFromCart(productId) {
    fetch('index.php?controller=product&action=removeFromCart&product_id=' + productId, { method: 'POST' })
        .then(() => location.reload());
}

function checkout() {
    fetch('index.php?controller=product&action=checkout', { method: 'POST' })
        .then(() => {
            console.log('Purchase completed! Email notification sent to user.');
            location.reload();
        });
}