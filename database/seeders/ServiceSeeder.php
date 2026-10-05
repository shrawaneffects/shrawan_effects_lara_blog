<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Fullstack Website Development',
                'slug' => 'fullstack-website-development',
                'icon' => 'bi-laptop',
                'short_description' => 'High-performance, SEO-optimized custom web applications built with Laravel, PHP, React, Vue, and modern responsive architectures.',
                'content' => '<p>At <strong>Shrawan Effects</strong>, we engineer tailored web applications designed for scalability, speed, and conversion. Based in Faridabad, Haryana, our full-stack engineering team builds robust applications using the Laravel framework, modern front-end libraries, and clean architectural design patterns.</p><h4>What We Deliver</h4><ul><li>Custom responsive web application tailored to your exact business workflow.</li><li>Clean, modular, and maintainable MVC codebase adhering to PSR standards.</li><li>High-contrast UI designed for optimal user experience across mobile, tablet, and desktop.</li><li>Database architecture and migration pipelines optimized for query speed.</li><li>Search engine friendly HTML5 semantic layout with schema structured data.</li><li>Full deployment to your production server with SSL and domain configuration.</li></ul>',
                'features' => [
                    'Custom Fullstack Architecture (Laravel & Modern JS)',
                    'Responsive UI/UX with High-Speed Rendering',
                    'Database Modeling & Query Optimization',
                    'Secure Authentication & Role-Based Access',
                    'Production Deployment & Server Hardening',
                ],
                'technologies' => 'Laravel, PHP 8.3, React, Bootstrap 5.3, MySQL, Redis',
                'pricing_starts_at' => 'Starting from ₹40,000 / project',
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',
                'order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Custom Software & SaaS Architecture',
                'slug' => 'custom-software-saas-development',
                'icon' => 'bi-cpu',
                'short_description' => 'Scalable multi-tenant enterprise software, management portals, ERPs, CRM systems, and subscription billing engines.',
                'content' => '<p>Turn your complex operational workflows into seamless cloud software. Shrawan Effects specializes in building mission-critical enterprise systems, subscription SaaS platforms, and internal business management portals that automate operations and maximize team productivity.</p><h4>Core Capabilities</h4><ul><li>Multi-tenant SaaS data isolation and tenant onboarding workflows.</li><li>Granular Role-Based Access Control (RBAC) and security audit logs.</li><li>Automated recurring billing, invoicing, and subscription tier management.</li><li>Background job queues and asynchronous task processing.</li><li>Real-time telemetry dashboards and operational analytics.</li></ul>',
                'features' => [
                    'Multi-Tenant SaaS Architecture',
                    'Enterprise Role-Based Access Control (RBAC)',
                    'Automated Subscription & Invoice Pipelines',
                    'Interactive Real-Time Analytics Dashboards',
                    'Background Queue Processing & Scheduled Jobs',
                ],
                'technologies' => 'Laravel, Livewire, Redis, PostgreSQL, Docker, AWS',
                'pricing_starts_at' => 'Custom Scope Quote',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
                'order' => 2,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'E-Commerce Solutions & Online Stores',
                'slug' => 'ecommerce-solutions',
                'icon' => 'bi-cart3',
                'short_description' => 'High-conversion digital storefronts with automated inventory, instant payment gateways, order management, and customer analytics.',
                'content' => '<p>Launch an online storefront that converts visitors into recurring customers. We develop secure, fast, and feature-complete e-commerce platforms with smooth checkout experiences, real-time inventory tracking, and complete payment gateway integrations.</p><h4>Included Capabilities</h4><ul><li>Fast, frictionless mobile checkout experience.</li><li>Seamless integration with Razorpay, Stripe, Paytm, and Cashfree.</li><li>Automated tax calculation, shipping rate estimators, and invoice generation.</li><li>Coupon codes, promotional discounting, and referral mechanisms.</li><li>Order status tracking, SMS/Email notifications, and customer self-service portal.</li></ul>',
                'features' => [
                    'Seamless Checkout & Cart Optimization',
                    'Razorpay, Stripe & UPI Payment Gateways',
                    'Inventory Management & Real-Time Stock Tracking',
                    'Coupon & Promotional Campaign Engines',
                    'Automated Invoicing & Shipping Label Integration',
                ],
                'technologies' => 'Laravel, WooCommerce, MySQL, Tailwind CSS, Razorpay, Stripe',
                'pricing_starts_at' => 'Starting from ₹35,000 / project',
                'image' => 'https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=1200&q=80',
                'order' => 3,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'REST API Engineering & System Integration',
                'slug' => 'api-development-system-integration',
                'icon' => 'bi-diagram-3',
                'short_description' => 'Robust RESTful and GraphQL APIs, webhook engines, third-party ERP/CRM connections, and automated microservice communication.',
                'content' => '<p>Connect your disparate applications, mobile front-ends, and external vendor partners with secure, high-performance API backends designed for high throughput and military-grade reliability.</p><h4>Integration Specializations</h4><ul><li>RESTful & GraphQL API architecture with JSON schema validation.</li><li>Token authentication (Bearer, Sanctum, JWT, OAuth2).</li><li>Webhook listeners and automated outgoing event dispatchers.</li><li>Integration with third-party APIs (WhatsApp Business, payment gateways, ERPs, CRM).</li><li>Comprehensive OpenAPI / Swagger interactive documentation.</li></ul>',
                'features' => [
                    'High-Throughput RESTful & GraphQL Endpoints',
                    'OAuth2, JWT & Laravel Sanctum Authentication',
                    'Automated Webhook Engines & Event Streaming',
                    'Third-Party CRM / ERP Data Synchronization',
                    'Interactive OpenAPI / Swagger Documentation',
                ],
                'technologies' => 'REST, GraphQL, Laravel Sanctum, Redis, Postman, Webhooks',
                'pricing_starts_at' => 'Starting from ₹20,000 / project',
                'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80',
                'order' => 4,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Technical SEO & Organic Search Growth',
                'slug' => 'seo-performance-optimization',
                'icon' => 'bi-graph-up-arrow',
                'short_description' => 'Deep technical on-page SEO, Google Business local ranking for Faridabad & NCR, Schema markup, Core Web Vitals speed tuning.',
                'content' => '<p>Dominate search results with data-driven technical search engine optimization. We address Core Web Vitals, site speed, indexation health, and schema markup to rank your business higher in search engines.</p><h4>SEO Implementation Suite</h4><ul><li>Complete technical SEO audit and architectural crawl analysis.</li><li>Core Web Vitals acceleration targeting 90+ mobile Lighthouse scores.</li><li>Schema.org JSON-LD structured data for rich snippets (LocalBusiness, Articles, FAQs).</li><li>Local Google Business Profile ranking optimization in Faridabad and NCR.</li><li>Canonical URLs, automated sitemap.xml generators, and robots.txt rules.</li></ul>',
                'features' => [
                    'Core Web Vitals & PageSpeed 95+ Tuning',
                    'Schema.org JSON-LD Structured Data Implementation',
                    'Local Google Business Optimization (Faridabad & NCR)',
                    'Automated XML Sitemaps & Search Console Configuration',
                    'Keyword Strategy & Content Architecture',
                ],
                'technologies' => 'Google Search Console, Lighthouse, Schema.org, SEMrush',
                'pricing_starts_at' => 'Starting from ₹10,000 / month',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                'order' => 5,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'title' => 'Cloud DevOps, Containerization & Maintenance',
                'slug' => 'cloud-devops-infrastructure',
                'icon' => 'bi-cloud-check',
                'short_description' => 'Automated CI/CD pipelines, Docker containerization, VPS & Dedicated server configuration, SSL, zero-downtime deployments.',
                'content' => '<p>Eliminate downtime, automate deployment workflows, and ensure your infrastructure scales effortlessly under traffic surges. Shrawan Effects provides complete cloud system administration and DevOps management.</p><h4>Cloud Services</h4><ul><li>VPS and Dedicated Server provisioning (Linux / Ubuntu, Nginx, PHP-FPM, MySQL).</li><li>Docker containerization for reproducible development and production environments.</li><li>Automated CI/CD deployment pipelines using GitHub Actions.</li><li>Automated daily off-site encrypted database backups.</li><li>Security hardening: UFW firewall, fail2ban, SSL certificates, brute-force shield.</li></ul>',
                'features' => [
                    'Nginx, Apache & PHP-FPM Performance Hardening',
                    'Docker Containerization & Multi-Environment Setup',
                    'CI/CD Automated Deployments via GitHub Actions',
                    'Automated Daily Off-Site Encrypted Backups',
                    '24/7 Server Telemetry Monitoring & Uptime Protection',
                ],
                'technologies' => 'Docker, Nginx, Ubuntu Linux, GitHub Actions, AWS, DigitalOcean',
                'pricing_starts_at' => 'Starting from ₹15,000 / setup',
                'image' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=1200&q=80',
                'order' => 6,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Mobile Application Development (Android & iOS)',
                'slug' => 'mobile-app-development',
                'icon' => 'bi-phone',
                'short_description' => 'Cross-platform iOS and Android mobile apps with slick UI, offline caching, push notifications, and seamless cloud syncing.',
                'content' => '<p>Deliver a world-class mobile app to your users. We engineer high-performance mobile applications that run smoothly on both Android and iOS devices from a unified codebase, saving development costs and time-to-market.</p><h4>Mobile App Deliverables</h4><ul><li>Cross-platform Flutter / React Native architecture.</li><li>Biometric authentication (Fingerprint, Face ID).</li><li>Push notification integration via Firebase Cloud Messaging (FCM).</li><li>Offline-first data caching with local SQLite database synchronization.</li><li>App Store and Google Play Store submission and release management.</li></ul>',
                'features' => [
                    'Cross-Platform iOS & Android Native Performance',
                    'Firebase Push Notifications & Real-Time Sync',
                    'Biometric Security & Social Sign-In Support',
                    'Offline-First Data Storage & Sync Engine',
                    'App Store & Play Store Publishing Management',
                ],
                'technologies' => 'Flutter, Dart, React Native, Firebase, REST APIs, SQLite',
                'pricing_starts_at' => 'Starting from ₹45,000 / project',
                'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80',
                'order' => 7,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Online Promotion & Digital Visibility',
                'slug' => 'online-promotions-digital-marketing',
                'icon' => 'bi-megaphone',
                'short_description' => 'Targeted business promotion, lead generation funnels, conversion rate optimization, and brand authority building.',
                'content' => '<p>Drive qualified traffic, acquire high-value leads, and amplify your digital brand presence with targeted promotion campaigns and conversion-focused marketing funnels.</p><h4>Promotional Strategies</h4><ul><li>Targeted lead generation funnels with dedicated landing pages.</li><li>Meta (Facebook/Instagram) and Google Ads campaign architecture.</li><li>Conversion Rate Optimization (CRO) and user journey audits.</li><li>Email marketing automation and customer re-engagement sequences.</li></ul>',
                'features' => [
                    'High-Converting Landing Page Design & Testing',
                    'Targeted Lead Generation & Ad Campaign Setup',
                    'Conversion Rate Optimization (CRO)',
                    'Automated Email Marketing & Lead Nurturing',
                    'Comprehensive Traffic & ROI Analytics',
                ],
                'technologies' => 'Google Ads, Meta Business Suite, Google Analytics 4, Automation',
                'pricing_starts_at' => 'Starting from ₹5,000 / month',
                'image' => 'https://images.unsplash.com/photo-1533750349088-cd871a92f312?auto=format&fit=crop&w=1200&q=80',
                'order' => 8,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($services as $svc) {
            Service::updateOrCreate(
                ['slug' => $svc['slug']],
                $svc
            );
        }
    }
}
