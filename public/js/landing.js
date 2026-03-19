// Study Track - Duolingo-style Landing Page JS
// Interactive features: language carousel, streak counter, particles, animations

document.addEventListener('DOMContentLoaded', function() {
    // Typing animation for subtitle
    typeWriterEffect();
    
    // Language carousel
    initLanguageCarousel();
    
    // Streak counter animation
    animateStreakCounter();
    
    // Particle system
    initParticles();
    
    // Scroll animations
    initScrollAnimations();
    
    // Micro-interactions
    initMicroInteractions();
});

function typeWriterEffect() {
    const subtitle = document.querySelector('.hero-subtitle');
    const text = subtitle.textContent;
    subtitle.textContent = '';
    let i = 0;
    function type() {
        if (i < text.length) {
            subtitle.textContent += text.charAt(i);
            i++;
            setTimeout(type, 50);
        }
    }
    setTimeout(type, 500);
}

function initLanguageCarousel() {
    const carousel = document.querySelector('.language-carousel');
    if (!carousel) return;
    
    const items = carousel.querySelectorAll('.language-card');
    let current = 0;
    
    function nextSlide() {
        items[current].classList.remove('active');
        current = (current + 1) % items.length;
        items[current].classList.add('active');
    }
    
    // Auto-rotate every 4s
    setInterval(nextSlide, 4000);
    
    // Click navigation
    carousel.addEventListener('click', function(e) {
        if (e.target.classList.contains('language-card')) {
            items.forEach(item => item.classList.remove('active'));
            e.target.classList.add('active');
        }
    });
}

function animateStreakCounter() {
    const streakEl = document.querySelector('.streak-counter');
    if (!streakEl) return;
    
    let streak = 0;
    const target = 125;
    const increment = target / 100;
    const timer = setInterval(() => {
        streak += increment;
        streakEl.textContent = Math.floor(streak) + '+';
        if (streak >= target) {
            streakEl.textContent = target + '+';
            clearInterval(timer);
        }
    }, 30);
}

function initParticles() {
    const canvas = document.createElement('canvas');
    canvas.id = 'particles-canvas';
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.zIndex = '-1';
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    
    const ctx = canvas.getContext('2d');
    const particles = [];
    
    for (let i = 0; i < 50; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5,
            radius: Math.random() * 3 + 1,
            opacity: Math.random() * 0.5 + 0.2
        });
    }
    
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
            
            ctx.save();
            ctx.globalAlpha = p.opacity;
            ctx.fillStyle = '#FCD535';
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        });
        
        requestAnimationFrame(animate);
    }
    animate();
    
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

function initMicroInteractions() {
    // Button hover glow
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Mascot bounce
    const mascot = document.querySelector('.duo-mascot');
    if (mascot) {
        mascot.addEventListener('mouseenter', () => {
            mascot.style.animation = 'bounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });
        mascot.addEventListener('animationend', () => {
            mascot.style.animation = 'float 3s ease-in-out infinite';
        });
    }
}
