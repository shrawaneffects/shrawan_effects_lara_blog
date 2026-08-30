/**
 * Trending Interactive Animated Background Canvas
 * High-performance, GPU-accelerated ambient particle mesh & glow orbs.
 */
(function() {
    'use strict';

    let canvas, ctx;
    let particles = [];
    let animationFrameId = null;
    let mouse = { x: null, y: null, radius: 140 };
    let isVisible = true;

    function initCanvas() {
        if (document.getElementById('ambientCanvasBg')) {
            return;
        }

        canvas = document.createElement('canvas');
        canvas.id = 'ambientCanvasBg';
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '0';
        canvas.style.opacity = '0.75';
        canvas.style.transition = 'opacity 0.6s ease';

        document.body.prepend(canvas);
        ctx = canvas.getContext('2d');

        resizeCanvas();
        window.addEventListener('resize', debounce(resizeCanvas, 150));
        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('mouseleave', () => { mouse.x = null; mouse.y = null; });

        document.addEventListener('visibilitychange', () => {
            isVisible = !document.hidden;
            if (isVisible) animate();
            else if (animationFrameId) cancelAnimationFrame(animationFrameId);
        });

        createParticles();
        animate();
    }

    function resizeCanvas() {
        if (!canvas) return;
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        createParticles();
    }

    function createParticles() {
        particles = [];
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const density = Math.min(window.innerWidth * window.innerHeight / 16000, 75);

        const colors = isDark 
            ? ['rgba(99, 102, 241, ', 'rgba(139, 92, 246, ', 'rgba(217, 70, 239, ', 'rgba(6, 182, 212, ']
            : ['rgba(99, 102, 241, ', 'rgba(129, 140, 248, ', 'rgba(168, 85, 247, ', 'rgba(14, 165, 233, '];

        for (let i = 0; i < density; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.7,
                vy: (Math.random() - 0.5) * 0.7,
                radius: Math.random() * 2.5 + 1,
                baseColor: colors[Math.floor(Math.random() * colors.length)],
                alpha: Math.random() * 0.5 + 0.2,
                pulseSpeed: Math.random() * 0.02 + 0.005,
                pulseAngle: Math.random() * Math.PI * 2
            });
        }
    }

    function onMouseMove(e) {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    }

    function animate() {
        if (!isVisible || !ctx) return;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];

            p.x += p.vx;
            p.y += p.vy;

            if (p.x < 0) p.x = canvas.width;
            if (p.x > canvas.width) p.x = 0;
            if (p.y < 0) p.y = canvas.height;
            if (p.y > canvas.height) p.y = 0;

            // Mouse interaction
            if (mouse.x !== null && mouse.y !== null) {
                const dx = mouse.x - p.x;
                const dy = mouse.y - p.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < mouse.radius) {
                    const force = (mouse.radius - dist) / mouse.radius;
                    p.x -= (dx / dist) * force * 3.5;
                    p.y -= (dy / dist) * force * 3.5;
                }
            }

            p.pulseAngle += p.pulseSpeed;
            const currentAlpha = Math.max(0.1, p.alpha + Math.sin(p.pulseAngle) * 0.2);

            // Draw glowing particle
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fillStyle = p.baseColor + currentAlpha + ')';
            ctx.shadowBlur = isDark ? 8 : 4;
            ctx.shadowColor = p.baseColor + '0.8)';
            ctx.fill();

            // Connect nearby particles
            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < 120) {
                    const lineAlpha = (1 - dist / 120) * (isDark ? 0.2 : 0.12);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = (isDark ? 'rgba(99, 102, 241, ' : 'rgba(129, 140, 248, ') + lineAlpha + ')';
                    ctx.lineWidth = 0.75;
                    ctx.stroke();
                }
            }
        }

        ctx.shadowBlur = 0;
        animationFrameId = requestAnimationFrame(animate);
    }

    function debounce(func, wait) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), wait);
        };
    }

    // Initialize when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCanvas);
    } else {
        initCanvas();
    }
})();
