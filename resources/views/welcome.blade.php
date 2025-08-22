<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ValuMate - Professional Real Estate Valuation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 2rem;
            color: #ffd700;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 120px 0 80px;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(45deg, #ffd700, #ffed4a);
            color: #333;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-card i {
            font-size: 3rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Process Section */
        .process {
            padding: 80px 0;
            background: white;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .process-step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }

        .process-step h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .process-step p {
            color: #666;
        }

        /* Property Types Section */
        .property-types {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .property-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .property-card:hover {
            transform: translateY(-5px);
        }

        .property-image {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .property-card h3 {
            padding: 1.5rem;
            font-size: 1.3rem;
            color: #333;
            text-align: center;
        }

        /* Pricing Section */
        .pricing {
            padding: 80px 0;
            background: white;
        }

        .pricing-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
        }

        .price {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .price-desc {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .pricing-features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .pricing-features li:last-child {
            border-bottom: none;
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                flex-direction: column;
                padding: 1rem 0;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .features-grid,
            .process-steps,
            .property-grid {
                grid-template-columns: 1fr;
            }

            nav {
                padding: 0 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .hero-content {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="/" class="logo">
                <i class="fas fa-home"></i>
                ValuMate
            </a>
            <ul class="nav-links" id="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/privacy-policy">Privacy Policy</a></li>
                <li><a href="/support">Contact Us</a></li>
            </ul>
            <button class="mobile-menu-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Professional Real Estate Valuation</h1>
            <p>Get accurate property valuations from certified companies in Oman. Fast, reliable, and bank-approved assessments for your real estate needs.</p>
            <a href="#features" class="cta-button">Start Your Valuation</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose ValuMate?</h2>
                <p>Our platform connects you with certified valuation companies, ensuring accurate and bank-approved property assessments.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Certified Companies</h3>
                    <p>Work with licensed and bank-approved valuation companies across Oman for reliable property assessments.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Fast Processing</h3>
                    <p>Get your valuation report quickly with our streamlined process and express service options.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Easy to Use</h3>
                    <p>Simple mobile app interface with step-by-step guidance and real-time order tracking.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Payments</h3>
                    <p>Multiple payment options including Apple Pay, Google Pay, and Thawani with automatic invoice generation.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-bell"></i>
                    <h3>Real-time Updates</h3>
                    <p>Stay informed with in-app notifications and email updates throughout the valuation process.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Get help when you need it with our in-app live chat support system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
                <p>Get your property valuation in just a few simple steps</p>
            </div>
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Select Property Type</h3>
                    <p>Choose your property type - land, house, villa, or residential apartment with location details.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Choose Company</h3>
                    <p>Select from available certified valuation companies based on pricing and bank approval.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Upload Documents</h3>
                    <p>Submit required documents with our smart image quality check system.</p>
                </div>
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h3>Make Payment</h3>
                    <p>Secure payment processing with multiple options and automatic invoice generation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Property Types Section -->
    <section class="property-types">
        <div class="container">
            <div class="section-title">
                <h2>Property Types We Value</h2>
                <p>Professional valuation services for all types of real estate properties</p>
            </div>
            <div class="property-grid">
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                    <h3>Land Plots</h3>
                </div>
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                    <h3>Houses</h3>
                </div>
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                    <h3>Villas</h3>
                </div>
                <div class="property-card">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                    <h3>Apartments</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing">
        <div class="container">
            <div class="section-title">
                <h2>Transparent Pricing</h2>
                <p>Affordable rates starting from OMR 25</p>
            </div>
            <div class="pricing-card">
                <div class="price">OMR 25</div>
                <div class="price-desc">Standard Valuation</div>
                <ul class="pricing-features">
                    <li>✓ Bank-approved companies</li>
                    <li>✓ Professional report</li>
                    <li>✓ Email & in-app notifications</li>
                    <li>✓ PDF invoice included</li>
                    <li>✓ 24/7 customer support</li>
                </ul>
                <p style="margin-top: 1rem; opacity: 0.8; font-size: 0.9rem;">Express service available for additional OMR 50</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 ValuMate. All rights reserved. Professional Real Estate Valuation Services in Oman.</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById('nav-links');
            navLinks.classList.toggle('active');
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const navLinks = document.getElementById('nav-links');
            const menuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>