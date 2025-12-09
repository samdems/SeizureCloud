<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Epilepsy Diary - Track Your Journey</title>
    <meta name="description" content="Comprehensive epilepsy management app for tracking seizures, medications, vitals. Take control of your epilepsy journey with confidence.">
    <meta name="keywords" content="epilepsy, seizure tracker, medication management, health tracking">
    <meta name="author" content="Epilepsy Diary">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Epilepsy Diary - Track Your Journey">
    <meta property="og:description" content="Comprehensive epilepsy management app for tracking seizures, medications, vitals.">
    <meta property="og:site_name" content="Epilepsy Diary">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Epilepsy Diary - Track Your Journey">
    <meta name="twitter:description" content="Comprehensive epilepsy management app for tracking seizures, medications, vitals.">

    <!-- Performance optimizations -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument+sans:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-100 font-sans antialiased">
    <!-- Navigation -->
    <nav class="navbar bg-base-200/80 backdrop-blur-md shadow-lg border-b border-base-300/20 sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-8 fill-current text-primary" />
                    </span>
                    <span class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">{{ config('app.name') }}</span>
                </a>
            </div>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm lg:btn-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm lg:btn-md">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm lg:btn-md">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero min-h-[70vh] bg-gradient-to-br from-primary/10 to-secondary/10">
        <div class="hero-content container mx-auto px-6 py-12">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                        Take Control of Your
                        <span class="text-primary">Epilepsy Journey</span>
                    </h1>
                    <p class="text-lg lg:text-xl mb-8 opacity-80 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Track seizures, manage medications, monitor vitals, and share trusted access with caregivers.
                        Your comprehensive epilepsy management tool.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Start Your Journey
                            </a>

                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Go to Dashboard
                            </a>

                        @endauth
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="mockup-phone border-primary">
                        <div class="camera"></div>
                        <div class="display">
                            <div class="artboard artboard-demo phone-1 bg-base-100 p-0 overflow-hidden">
                                <img src="/Screenshot from 2025-12-09 14-54-10.png"
                                     alt="App Screenshot"
                                     class="w-full h-full object-cover object-top">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-base-200">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 bg-gradient-to-r from-base-content to-primary bg-clip-text text-transparent">Comprehensive Epilepsy Management</h2>
                <p class="text-xl opacity-80 max-w-3xl mx-auto leading-relaxed">Everything you need to track and manage your epilepsy in one place</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Seizure Tracking -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body text-center">
                        <div class="text-5xl mb-4">📊</div>
                        <h3 class="card-title justify-center text-2xl mb-2">Seizure Tracking</h3>
                        <p class="opacity-70 mb-4">Comprehensive seizure logging with trigger analysis - connects with medications and vitals data</p>
                        <div class="badge badge-primary">Comprehensive Logging</div>
                    </div>
                </div>

                <!-- Medication Management -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body text-center">
                        <div class="text-5xl mb-4">💊</div>
                        <h3 class="card-title justify-center text-2xl mb-2">Medication Management</h3>
                        <p class="opacity-70 mb-4">Track medications and their effects on seizure patterns - helping identify triggers and optimize treatments</p>
                        <div class="badge badge-success">Trigger Analysis</div>
                    </div>
                </div>

                <!-- Vitals Monitoring -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body text-center">
                        <div class="text-5xl mb-4">💗</div>
                        <h3 class="card-title justify-center text-2xl mb-2">Vitals Monitoring</h3>
                        <p class="opacity-70 mb-4">Monitor vitals to identify seizure triggers - track patterns between health metrics and seizure activity</p>
                        <div class="badge badge-info">Trigger Insights</div>
                    </div>
                </div>

                <!-- Trusted Access -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:opacity-95">
                    <div class="card-body text-center">
                        <div class="text-5xl mb-4">👥</div>
                        <h3 class="card-title justify-center text-2xl mb-2">Trusted Contacts</h3>
                        <p class="opacity-70 mb-4">Grant secure access to caregivers, family members, and healthcare providers</p>
                        <div class="badge badge-warning">Secure Sharing</div>
                    </div>
                </div>

                <!-- Account Types -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center">
                        <div class="text-5xl mb-4">🏥</div>
                        <h3 class="card-title justify-center text-2xl mb-2">Multiple Account Types</h3>
                        <p class="opacity-70 mb-4">Patient, caregiver, and medical professional accounts with appropriate access levels</p>
                        <div class="badge badge-accent">Role-Based Access</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trigger Identification Section -->
    <section class="py-20 bg-gradient-to-r from-primary/5 via-secondary/5 to-accent/5">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Discover Your Seizure Triggers</h2>
                <p class="text-xl opacity-80 max-w-3xl mx-auto leading-relaxed">Our integrated tracking system connects seizures, medications, and vitals to help identify patterns and triggers</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 mb-12">
                <!-- Medication Connection -->
                <div class="card bg-base-100 shadow-lg border-l-4 border-l-success">
                    <div class="card-body">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-4xl">💊➡️📊</div>
                            <h3 class="text-xl font-bold">Medication Patterns</h3>
                        </div>
                        <p class="text-sm opacity-70">Track how medication timing, dosage changes, and adherence correlate with seizure frequency and severity.</p>
                        <div class="mt-4">
                            <div class="badge badge-success badge-sm">Dosage Analysis</div>
                            <div class="badge badge-success badge-sm ml-2">Timing Correlation</div>
                        </div>
                    </div>
                </div>

                <!-- Vitals Connection -->
                <div class="card bg-base-100 shadow-lg border-l-4 border-l-info">
                    <div class="card-body">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-4xl">💗➡️📊</div>
                            <h3 class="text-xl font-bold">Health Metrics</h3>
                        </div>
                        <p class="text-sm opacity-70">Monitor how blood pressure, heart rate, sleep patterns, and stress levels relate to seizure activity.</p>
                        <div class="mt-4">
                            <div class="badge badge-info badge-sm">Pattern Recognition</div>
                            <div class="badge badge-info badge-sm ml-2">Early Warning</div>
                        </div>
                    </div>
                </div>

                <!-- Combined Insights -->
                <div class="card bg-base-100 shadow-lg border-l-4 border-l-warning">
                    <div class="card-body">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-4xl">🧠💡</div>
                            <h3 class="text-xl font-bold">Smart Insights</h3>
                        </div>
                        <p class="text-sm opacity-70">Get comprehensive reports showing connections between all your health data to identify triggers and optimize care.</p>
                        <div class="mt-4">
                            <div class="badge badge-warning badge-sm">Data Integration</div>
                            <div class="badge badge-warning badge-sm ml-2">Trigger Reports</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center bg-base-100 p-8 rounded-2xl shadow-lg mt-8">
                <h3 class="text-2xl font-bold mb-4">Take Control of Your Epilepsy</h3>
                <p class="text-lg opacity-80 mb-6 max-w-2xl mx-auto">Understanding your triggers is the first step to better seizure management. Our connected tracking system helps you and your healthcare team make informed decisions.</p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-success">✓</span>
                        <span>Identify medication effectiveness</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-success">✓</span>
                        <span>Spot health pattern triggers</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-success">✓</span>
                        <span>Share insights with doctors</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-success">✓</span>
                        <span>Optimize treatment plans</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Account Types Section -->
    <section class="py-20 bg-gradient-to-b from-base-100 to-base-200">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 bg-gradient-to-r from-base-content to-secondary bg-clip-text text-transparent">Choose Your Account Type</h2>
                <p class="text-xl opacity-80 max-w-3xl mx-auto leading-relaxed">Different account types designed for different roles in epilepsy care</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 animate-fade-in">
                <!-- Patient Account -->
                <div class="card bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/20 hover:from-primary/15 hover:to-primary/10 transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="avatar">
                                <div class="w-16 rounded-full bg-primary/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold mt-4 mb-2">Patient Account</h3>
                            <p class="opacity-70 mb-4">Full access to track your own health data</p>
                        </div>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Complete seizure tracking</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Medication management</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Vitals monitoring</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Grant trusted access</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Caregiver Account -->
                <div class="card bg-gradient-to-br from-accent/10 to-accent/5 border border-accent/20 hover:from-accent/15 hover:to-accent/10 transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="avatar">
                                <div class="w-16 rounded-full bg-accent/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold mt-4 mb-2">Caregiver Account</h3>
                            <p class="opacity-70 mb-4">Help manage patient care through trusted access</p>
                        </div>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">View patient data</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Emergency monitoring</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Assist with tracking</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-warning">✗</span>
                                <span class="text-sm opacity-60">Own health tracking</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Medical Professional Account -->
                <div class="card bg-gradient-to-br from-success/10 to-success/5 border border-success/20 hover:from-success/15 hover:to-success/10 transition-all duration-300 hover:opacity-95">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="avatar">
                                <div class="w-16 rounded-full bg-success/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold mt-4 mb-2">Medical Professional</h3>
                            <p class="opacity-70 mb-4">Clinical access for healthcare providers</p>
                        </div>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Professional patient access</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Clinical data review</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-success">✓</span>
                                <span class="text-sm">Healthcare team coordination</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-warning">✗</span>
                                <span class="text-sm opacity-60">Own health tracking</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-r from-primary to-secondary relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="animate-fade-in">
                <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">Ready to Take Control?</h2>
                <p class="text-xl lg:text-2xl text-white/90 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Join people managing their epilepsy with confidence. Start tracking today and gain valuable insights into your health journey.
                </p>
                @guest
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                        <a href="{{ route('register') }}" class="btn btn-lg bg-white text-primary hover:bg-base-100 hover:opacity-95 transition-all duration-300 shadow-xl border-0 px-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Start Your Journey
                        </a>

                    </div>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-lg bg-white text-primary hover:bg-base-100 hover:opacity-95 transition-all duration-300 shadow-xl border-0 px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Continue to Dashboard
                    </a>
                @endguest
            </div>
        </div>
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full animate-pulse"></div>
            <div class="absolute top-32 right-20 w-16 h-16 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-10 right-1/3 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-base-300 text-base-content">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                <!-- Brand section -->
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-4">
                        <div class="text-3xl animate-pulse">📊</div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Epilepsy Diary</span>
                    </div>
                    <p class="text-base-content/80 mb-2 leading-relaxed">Your trusted companion for epilepsy management</p>
                    <p class="text-base-content/60 text-sm">Take control of your health journey with confidence</p>
                </div>

                <!-- Navigation links -->
                <div class="text-center">
                    <h4 class="font-semibold text-lg mb-4">Quick Links</h4>
                    <div class="space-y-2">
                        @guest
                            <a href="{{ route('login') }}" class="block link link-hover text-base-content/80 hover:text-primary transition-colors">Sign In</a>
                            <a href="{{ route('register') }}" class="block link link-hover text-base-content/80 hover:text-primary transition-colors">Register</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block link link-hover text-base-content/80 hover:text-primary transition-colors">Dashboard</a>
                            <a href="{{ route('settings.profile') }}" class="block link link-hover text-base-content/80 hover:text-primary transition-colors">Settings</a>
                        @endguest
                    </div>
                </div>

                <!-- Emergency section -->

            </div>

            <!-- Copyright -->
            <div class="divider mt-8 mb-4"></div>
            <div class="text-center">
                <p class="text-base-content/60 text-sm">
                    © {{ date('Y') }} Epilepsy Diary. Built with ❤️ for the epilepsy community.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
