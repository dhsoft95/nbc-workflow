<!doctype html>
<html lang="en">
<head>
    <title>{{ config('app.name') }} :: @yield('title', 'Login')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Iconic Bootstrap 4.5.0 Admin Template">
    <meta name="author" content="WrapTheme, design by: ThemeMakker.com">

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        /* The canvas needs to sit between the two backgrounds */
        .auth-main {
            position: relative; /* Ensures pseudo-elements position correctly */
        }

        /* This will make our canvas sit between the two background layers */
        .canvas-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1.5; /* Between -1 and -2 to sit between your backgrounds */
            pointer-events: none; /* Allow clicks to pass through to elements below */
        }

        /* Update the z-index of the original backgrounds to ensure they're positioned correctly */
        .auth-main::before {
            z-index: -1;
        }

        .auth-main::after {
            z-index: -2;
        }
    </style>
</head>
<body data-theme="light" class="font-nunito">
<!-- WRAPPER -->
<div id="wrapper" class="theme-cyan">
    <div class="vertical-align-wrap">
        <div class="vertical-align-middle auth-main">
            <!-- Add canvas between backgrounds -->
            <div class="canvas-layer">
                <canvas id="canvas-overlay"></canvas>
            </div>
            <div class="auth-box">
                <div class="top">
                    {{--                    <img src="{{ asset('assets/images/logo.png') }}" alt="Iconic">--}}
                </div>
                <div class="card">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END WRAPPER -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element
        const canvas = document.getElementById('canvas-overlay');
        const container = document.querySelector('.canvas-layer');
        const ctx = canvas.getContext('2d');

        // Set canvas dimensions to match container size
        function resizeCanvas() {
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
        }

        // Initialize canvas size
        resizeCanvas();

        // Update canvas size on window resize
        window.addEventListener('resize', resizeCanvas);

        // Colors array - beautiful shades of red and blue
        const colors = [
            'rgba(255, 0, 68, 0.7)',    // Vibrant red
            'rgba(236, 72, 153, 0.7)',  // Pink/red
            'rgba(214, 51, 108, 0.7)',  // Rose red
            'rgba(0, 83, 238, 0.7)',    // Bright blue
            'rgba(59, 130, 246, 0.7)',  // Medium blue
            'rgba(99, 102, 241, 0.7)'   // Indigo blue
        ];

        // Particle configuration
        const particles = [];
        const particleCount = 80;

        // Create particles
        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: Math.random() * 3 + 1,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedX: Math.random() * 0.8 - 0.4,
                speedY: Math.random() * 0.8 - 0.4,
                pulseDirection: 1,
                pulseSpeed: Math.random() * 0.01 + 0.005,
                pulseSize: 0
            });
        }

        // Animation function
        function animate() {
            // Clear canvas with a very subtle background tone
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Update and draw particles
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                // Move particle
                p.x += p.speedX;
                p.y += p.speedY;

                // Bounce off edges
                if (p.x < 0 || p.x > canvas.width) {
                    p.speedX *= -1;
                }
                if (p.y < 0 || p.y > canvas.height) {
                    p.speedY *= -1;
                }

                // Pulsating effect
                p.pulseSize += p.pulseSpeed * p.pulseDirection;
                if (p.pulseSize > 0.3 || p.pulseSize < -0.3) {
                    p.pulseDirection *= -1;
                }

                const radius = p.radius * (1 + p.pulseSize);

                // Draw particle
                ctx.beginPath();
                ctx.arc(p.x, p.y, radius, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.fill();

                // Draw glow effect
                const gradient = ctx.createRadialGradient(p.x, p.y, radius * 0.25, p.x, p.y, radius * 2);
                gradient.addColorStop(0, p.color);
                gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

                ctx.beginPath();
                ctx.arc(p.x, p.y, radius * 2, 0, Math.PI * 2);
                ctx.fillStyle = gradient;
                ctx.fill();

                // Draw connections
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x;
                    const dy = p.y - p2.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 120) {
                        // Create gradient for connection line
                        const gradient = ctx.createLinearGradient(p.x, p.y, p2.x, p2.y);
                        gradient.addColorStop(0, p.color);
                        gradient.addColorStop(1, p2.color);

                        ctx.beginPath();
                        ctx.strokeStyle = gradient;
                        ctx.globalAlpha = 0.2 * (1 - distance / 120);
                        ctx.lineWidth = 1;
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                        ctx.globalAlpha = 1;
                    }
                }
            }

            // Continue animation
            requestAnimationFrame(animate);
        }

        // Mouse interaction
        let mouseX = 0;
        let mouseY = 0;
        let mouseActive = false;

        container.addEventListener('mousemove', function(e) {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
            mouseActive = true;

            // Create ripple effect
            if (Math.random() > 0.85) {
                particles.push({
                    x: mouseX,
                    y: mouseY,
                    radius: Math.random() * 2 + 1,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    speedX: Math.random() * 2 - 1,
                    speedY: Math.random() * 2 - 1,
                    pulseDirection: 1,
                    pulseSpeed: Math.random() * 0.01 + 0.005,
                    pulseSize: 0,
                    lifespan: 50
                });

                // Limit total particles
                if (particles.length > 120) {
                    particles.shift();
                }
            }
        });

        container.addEventListener('mouseleave', function() {
            mouseActive = false;
        });

        // Double-click to create burst of particles
        container.addEventListener('dblclick', function(e) {
            const rect = canvas.getBoundingClientRect();
            const burstX = e.clientX - rect.left;
            const burstY = e.clientY - rect.top;

            // Create burst of particles
            for (let i = 0; i < 15; i++) {
                const angle = Math.random() * Math.PI * 2;
                const speed = Math.random() * 2 + 1;
                particles.push({
                    x: burstX,
                    y: burstY,
                    radius: Math.random() * 3 + 1,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    speedX: Math.cos(angle) * speed,
                    speedY: Math.sin(angle) * speed,
                    pulseDirection: 1,
                    pulseSpeed: Math.random() * 0.01 + 0.005,
                    pulseSize: 0
                });

                // Limit total particles
                if (particles.length > 120) {
                    particles.shift();
                }
            }
        });

        // Start animation
        animate();
    });
</script>
</body>
</html>
