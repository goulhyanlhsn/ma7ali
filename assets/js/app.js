document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    var counters = document.querySelectorAll('[data-count]');
    counters.forEach(function(counter) {
        var target = parseInt(counter.getAttribute('data-count'));
        var duration = 2000;
        var step = target / (duration / 16);
        var current = 0;
        var timer = setInterval(function() {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });

    // Navbar scroll effect
    var navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 8px 40px rgba(0,0,0,0.4)';
        } else {
            navbar.style.boxShadow = '0 4px 30px rgba(0,0,0,0.3)';
        }
    });

    // Cart badge pulse animation
    var cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        function pulseCartBadge() {
            cartBadge.classList.remove('pulse');
            void cartBadge.offsetWidth;
            cartBadge.classList.add('pulse');
            setTimeout(function() { cartBadge.classList.remove('pulse'); }, 500);
        }

        var cartForms = document.querySelectorAll('form[action="cart.php"]');
        cartForms.forEach(function(form) {
            var actionInput = form.querySelector('input[name="action"]');
            if (actionInput && actionInput.value === 'add') {
                form.addEventListener('submit', function() {
                    setTimeout(pulseCartBadge, 300);
                });
            }
        });
    }

    // Auto-dismiss flash messages
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.4s ease';
            setTimeout(function() { alert.remove(); }, 400);
        }, 4000);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
