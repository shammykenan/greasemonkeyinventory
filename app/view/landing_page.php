<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/inventory/assets/css/landingpage.css">
    <link rel="stylesheet" href="/inventory/assets/css/login-modal.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="/inventory/assets/images/logo.png">
    <title>Document</title>
</head>

<body>
    <nav class="navbar-custom" id="navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <ul class="navbar-links d-none d-md-flex">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#parts">Parts</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
            <a class="navbar-brand" href="#">
                <img src="/inventory/assets/landingpage-images/logo.png" alt="Orafi Logo">
            </a>
            <div class="navbar-right">
                <a href="#" class="login-link" data-open-login>
                    <i class='bx bx-user-circle'></i>
                    <span>Login</span>
                </a>
                <button class="hamburger-btn" id="menuBtn">☰</button>
            </div>
        </div>
    </nav>

    <div class="modal-overlay" id="modalOverlay"></div>

    <div class="modal-menu" id="modalMenu">
        <div class="modal-header">
            <div class="modal-logo">
                <img src="/inventory/assets/landingpage-images/logo.png" alt="Orafi Logo">
            </div>
            <div class="modal-header-right">
                <button class="close-btn" id="closeBtn">✕</button>
            </div>
        </div>
        <div class="modal-content">
            <ul class="modal-nav">
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#parts">Parts</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
        </div>
    </div>
    
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-word hero-word-back">Engine</div>

            <img src="/inventory/assets/landingpage-images/hero.png" alt="Hero Image" class="hero-image">

            <div class="hero-word hero-word-front">Works</div>
        </div>
    </section>
    <section id="services">
    <section class="services-section" id="services-trigger">
        <div class="container">


            <div class="text-center mb-5">
                <span class="badge-pill-light">
                    <i class="fa-solid fa-wrench"></i> What We Offer
                </span>
                <br>
                <h1 class="services-main-title-dark" id="servicesHeading" data-text="SERVICES"></h1>
            </div>


            <div class="row g-4">

                <!-- 1. Diagnostics -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-laptop-code"></i></div>
                        <h3 class="service-title-dark">General Diagnostics</h3>
                        <p class="service-text-dark">Utilizing Casa Level Software for precise vehicle health
                            reports and electronic system troubleshooting.</p>

                    </div>
                </div>

                <!-- 2. Overhaul -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-gears"></i></div>
                        <h3 class="service-title-dark">Engine & Transmission</h3>
                        <p class="service-text-dark">Complete engine and transmission overhaul services to restore
                            peak performance and durability.</p>

                    </div>
                </div>

                <!-- 3. Ford Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-microchip"></i></div>
                        <h3 class="service-title-dark">Ford Module Repair</h3>
                        <p class="service-text-dark">Specialized repair and replacement of Ford-specific modules
                            including PCM, TCM, and BCM.</p>

                    </div>
                </div>

                <!-- 4. Lost Key -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-key"></i></div>
                        <h3 class="service-title-dark">Lost Key Fabrication</h3>
                        <p class="service-text-dark">Emergency fabrication services for all lost keys, ensuring you
                            regain access to your vehicle safely.</p>

                    </div>
                </div>

                <!-- 5. Key Programming -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-id-card-clip"></i></div>
                        <h3 class="service-title-dark">Key Programming</h3>
                        <p class="service-text-dark">Professional duplication and advanced programming for smart
                            keys and transponder chips.</p>

                    </div>
                </div>

                <!-- 6. Odometer -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-gauge-high"></i></div>
                        <h3 class="service-title-dark">Odometer Correction</h3>
                        <p class="service-text-dark">Instrument cluster calibration and digital odometer correction
                            services for all vehicle makes.</p>

                    </div>
                </div>

                <!-- 7. Technical Services -->
                <div class="col-lg-4 col-md-6">
                    <div class="service-card-light">
                        <div class="service-icon-box-dark"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                        <h3 class="service-title-dark">Technical Services</h3>
                        <p class="service-text-dark">Comprehensive technical support and specialized repairs for
                            complex automotive systems.</p>

                    </div>
                </div>

            </div>
        </div>
    </section>
    </section>
    <section class="textfill-on-scroll" id="parts">
        <div class="scroll-text-container">
            <h1 class="scroll-heading" id="scrollHeading" data-text="PARTS">
            </h1>
        </div>
    </section>

    <section class="parts py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">ABS Module / Brake System</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="prevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="nextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="partsSlider" style="scroll-behavior: smooth;">

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/GN1Z-2B373-D.webp"
                                alt="ABS Control Module (Hydraulic Unit + ECU) GN1Z-2B373-D/GN1Z-2B373-J EcoSport (2017–2020)"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">ABS Control Module (Hydraulic Unit + ECU) GN1Z-2B373-D/GN1Z-2B373-J
                                EcoSport (2017–2020)</h4>
                            <div class="part-footer">
                                <span class="price">₱75,000</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/GN1Z-2B513-C.webp" alt="ABS Control Module GN1Z-2B513-C EcoSport"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i>
                            </div>
                            <h4 class="part-name">ABS Control Module GN1Z-2B513-C EcoSport</h4>
                            <div class="part-footer">
                                <span class="price">₱50,000</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/DN1C-2C405-AB.jpg" alt="ABS Pump Module DN1C-2C405-AB EcoSport 1.5 TDCi"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i>
                            </div>
                            <h4 class="part-name">ABS Pump Module DN1C-2C405-AB EcoSport 1.5 TDCi</h4>
                            <div class="part-footer">
                                <span class="price">₱21,000</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/D2BC-2C405-FD.webp" alt="ABS Control Module D2BC-2C405-FD Fiesta (2017–2018)"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">ABS Control Module D2BC-2C405-FD Fiesta (2017–2018)</h4>
                            <div class="part-footer">
                                <span class="price">₱58,500</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/ABS-Pump-Module-Assembly-Explorer.jpg" alt="ABS Pump Module Assembly Explorer"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bx-star'></i>
                            </div>
                            <h4 class="part-name">ABS Pump Module Assembly Explorer</h4>
                            <div class="part-footer">
                                <span class="price">₱83,500</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/ABS-Pump-Module-Assembly-Focus.png" alt="ABS Pump Module Assembly Focus"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i>
                            </div>
                            <h4 class="part-name">ABS Pump Module Assembly Focus</h4>
                            <div class="part-footer">
                                <span class="price"> ₱58,500</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/ABS-Pump-Module-Assembly-Everest.jpg" alt="ABS Pump Module Assembly Everest"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">ABS Pump Module Assembly Everest</h4>
                            <div class="part-footer">
                                <span class="price">₱78,500</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container">
                            <img src="/inventory/assets/landingpage-images/XR3Z-7H103-AB.webp"
                                alt="ABS / Wheel Speed Sensor XR3Z-7H103-AB (Mustang, Focus, Fiesta)" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars">
                                <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">ABS / Wheel Speed Sensor XR3Z-7H103-AB (Mustang, Focus, Fiesta)</h4>
                            <div class="part-footer">
                                <span class="price">₱12,000</span>
                                <button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="parts py-5" id="transmission">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Transmission</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="transPrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="transNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="transSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/AE8Z-7Z369-F.webp"
                                alt="Transmission Control Module (TCM) AE8Z-7Z369-F Focus" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Transmission Control Module (TCM) AE8Z-7Z369-F Focus</h4>
                            <div class="part-footer"><span class="price">₱31,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img
                                src="/inventory/assets/landingpage-images/transmission-Control-Module-(TCM)-7Z369-Series-Fiesta.jpg"
                                alt="Transmission Control Module (TCM) 7Z369 Series Fiesta" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Transmission Control Module (TCM) 7Z369 Series Fiesta</h4>
                            <div class="part-footer"><span class="price">₱31,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/transmission-Control-Module(TCM)-EcoSport.jpg"
                                alt="Transmission Control Module (TCM) EcoSport" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Transmission Control Module (TCM) EcoSport</h4>
                            <div class="part-footer"><span class="price">₱36,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/EJ7Z-7902-KRM.webp"
                                alt="Torque Converter EJ7Z-7902-KRM Explorer" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Torque Converter EJ7Z-7902-KRM Explorer</h4>
                            <div class="part-footer"><span class="price">₱21,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Torque-Converter-6R80 -10R80-Series-Everest.jpg"
                                alt="Torque Converter 6R80 -10R80 Series Everest" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Torque Converter 6R80 -10R80 Series Everest</h4>
                            <div class="part-footer"><span class="price">₱20,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img
                                src="/inventory/assets/landingpage-images/transmission-Speed-Sensor-XR3Z-7H103-AB-Mustang.jpg"
                                alt="Transmission Speed Sensor XR3Z-7H103-AB Mustang" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Transmission Speed Sensor XR3Z-7H103-AB Mustang</h4>
                            <div class="part-footer"><span class="price">₱28,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/6F35-Series.jpg"
                                alt="Transmission Rebuild Kit 6F35 Series Explorer, Focus" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i></div>
                            <h4 class="part-name">Transmission Rebuild Kit 6F35 Series Explorer, Focus</h4>
                            <div class="part-footer"><span class="price">₱47,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="parts py-5" id="cooling">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Water Pump / Cooling System</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="coolPrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="coolNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="coolSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/4S4Z-8501-E.webp"
                                alt="Engine Water Pump 4S4Z-8501-E EcoSport, Explorer, Focus" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Engine Water Pump 4S4Z-8501-E EcoSport, Explorer, Focus</h4>
                            <div class="part-footer"><span class="price">₱8,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/BE8Z-8501-B.jpg"
                                alt="Water Pump Assembly BE8Z-8501-B Fiesta (2011–2015)" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Water Pump Assembly BE8Z-8501-B Fiesta (2011–2015)</h4>
                            <div class="part-footer"><span class="price">₱12,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/7S7Z-8501-C.jpg"
                                alt="Water Pump Assembly 7S7Z-8501-L / 7S7Z-8501-C Fiesta (2014–2019)"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Water Pump Assembly 7S7Z-8501-L / 7S7Z-8501-C Fiesta (2014–2019)</h4>
                            <div class="part-footer"><span class="price">₱13,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/C1BZ-8C419-A.jpg"
                                alt="Auxiliary Water Pump C1BZ-8C419-A Fiesta 1.0L EcoBoost" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Auxiliary Water Pump C1BZ-8C419-A Fiesta 1.0L EcoBoost</h4>
                            <div class="part-footer"><span class="price">₱6,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Ranger-based-pump.webp"
                                alt="Engine Water Pump Ranger-based pump Everest" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Engine Water Pump Ranger-based pump Everest</h4>
                            <div class="part-footer"><span class="price">₱8,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/DR3Z-8501-A.jpg"
                                alt="Water Pump DR3Z-8501-A Mustang" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Water Pump DR3Z-8501-A Mustang</h4>
                            <div class="part-footer"><span class="price">₱25,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/CM5Z-00812-E.jpg"
                                alt="Water Pump Bolt / Hardware CM5Z-00812-E EcoSport, Fiesta, Focus" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Water Pump Bolt / Hardware CM5Z-00812-E EcoSport, Fiesta, Focus</h4>
                            <div class="part-footer"><span class="price">₱12,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="parts py-5" id="oil-fluids">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Engine Oil / Fluids</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="oilPrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="oilNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="oilSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/5W-30.webp"
                                alt="Engine Oil 5W-30 (EcoBoost/Diesel), 5W-20 All models" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Engine Oil 5W-30 (EcoBoost/Diesel), 5W-20 All models</h4>
                            <div class="part-footer"><span class="price">₱650 – ₱850 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Mercon-LV.jpg"
                                alt="Automatic Transmission Fluid Mercon LV / Mercon ULV EcoSport, Fiesta, Focus, Explorer, Everest"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Automatic Transmission Fluid Mercon LV / Mercon ULV EcoSport, Fiesta,
                                Focus, Explorer, Everest</h4>
                            <div class="part-footer"><span class="price">₱900 – ₱1,200 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/75W-90.jpg"
                                alt="Manual Transmission Oil 75W-90 Fiesta, Focus, Mustang (MT)" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Manual Transmission Oil 75W-90 Fiesta, Focus, Mustang (MT)</h4>
                            <div class="part-footer"><span class="price">₱750 – ₱1,000 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/75W-140.jpg"
                                alt="Differential Oil 75W-140 Everest, Explorer, Mustang" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Differential Oil 75W-140 Everest, Explorer, Mustang</h4>
                            <div class="part-footer"><span class="price">₱1,100 – ₱1,500 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/DOT-4.jpg" alt="Brake Fluid DOT-4 All models"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Fluid DOT-4 All models</h4>
                            <div class="part-footer"><span class="price">₱450 – ₱650 / bottle</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Mercon-ATF.webp"
                                alt="Power Steering Fluid Mercon ATF Explorer, Everest" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Power Steering Fluid Mercon ATF Explorer, Everest</h4>
                            <div class="part-footer"><span class="price">₱800 – ₱1,100 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Coolant.webp"
                                alt="Coolant Ford Orange / Yellow All models" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Coolant Ford Orange / Yellow All models</h4>
                            <div class="part-footer"><span class="price">₱650 – ₱900 / L</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>

    <section class="parts py-5" id="timing">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Timing Belt / Chain Components</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="timingPrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="timingNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="timingSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Timing-Belt.webp"
                                alt="Timing Belt Fiesta, EcoSport" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Timing Belt Fiesta, EcoSport</h4>
                            <div class="part-footer"><span class="price">₱16,000 </span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Timing-Chain.webp"
                                alt="Timing Chain Focus, Explorer, Mustang" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Timing Chain Focus, Explorer, Mustang</h4>
                            <div class="part-footer"><span class="price">₱46,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Timing-Belt-Tensioner.webp"
                                alt="Timing Belt Tensioner Fiesta, EcoSport=" img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Timing Belt Tensioner Fiesta, EcoSport</h4>
                            <div class="part-footer"><span class="price">₱14,000
                                </span><button class="add-to-cart"><i class='bx bx-message-square-detail'></i></button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Timing-Chain-Tensioner.webp"
                                alt="Timing Chain Tensioner Focus, Explorer, Mustang" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Timing Chain Tensioner Focus, Explorer, Mustang</h4>
                            <div class="part-footer"><span class="price">₱31,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Timing-Idler-Pulley.jpg"
                                alt="Timing Idler Pulley Fiesta, EcoSport" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Timing Idler Pulley Fiesta, EcoSport</h4>
                            <div class="part-footer"><span class="price">₱5,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Crankshaft-Pulley.webp" alt="Crankshaft Pulley"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Crankshaft Pulley</h4>
                            <div class="part-footer"><span class="price">₱18,000 – ₱24,000
                                    (engine-dependent)</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <section class="parts py-5" id="brakes">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Brake System / Sensors</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="brakePrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="brakeNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="brakeSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/brake-pads.webp" alt="Brake Pads"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Pads (Front/Rear)</h4>
                            <div class="part-footer"><span class="price">₱6,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Brake-RotorsDiscs.webp" alt="Brake Rotors/Discs"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Rotors / Discs</h4>
                            <div class="part-footer"><span class="price">₱9,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Brake-Calipers.webp"
                                alt="Brake Calipers  Explorer, Everest" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Calipers (Explorer, Everest)</h4>
                            <div class="part-footer"><span class="price">₱18,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Brake-Master-Cylinder.webp"
                                alt="Brake Master Cylinder" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Master Cylinder</h4>
                            <div class="part-footer"><span class="price">₱15,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Brake-Hoses.webp" alt="Brake Hoses"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Brake Hoses</h4>
                            <div class="part-footer"><span class="price">₱3,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/ABS-Wheel-Speed-Sensors.webp"
                                alt="ABS Wheel Speed Sensors" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">ABS Wheel Speed Sensors</h4>
                            <div class="part-footer"><span class="price">₱12,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="parts py-5" id="cooling-extra">

        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="parts-title">Cooling System (Additional)</h2>
                </div>
                <div class="nav-buttons">
                    <button class="nav-btn" id="coolExtraPrevBtn"><i class='bx bx-chevron-left'></i></button>
                    <button class="nav-btn" id="coolExtraNextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>
            </div>

            <div class="row g-4 flex-nowrap overflow-hidden" id="coolExtraSlider" style="scroll-behavior: smooth;">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Radiator.webp" alt="Radiator" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Radiator</h4>
                            <div class="part-footer"><span class="price">₱15,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Radiator-Fan-Assembly.webp"
                                alt="Radiator Fan Assembly" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Radiator Fan Assembly</h4>
                            <div class="part-footer"><span class="price">₱18,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Thermostat.webp" alt="Thermostat"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Thermostat</h4>
                            <div class="part-footer"><span class="price">₱4,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Coolant-Expansion-Tank.webp"
                                alt="Coolant Expansion Tank Fiesta, EcoSport" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Coolant Expansion Tank Fiesta, EcoSport</h4>
                            <div class="part-footer"><span class="price">₱6,500</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Heater-Control-Valve.jpg"
                                alt="Heater Control Valve Explorer, Everest" class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Heater Control Valve Explorer, Everest</h4>
                            <div class="part-footer"><span class="price">₱12,000</span><button class="add-to-cart"><i
                                        class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>


                <div class="col-6 col-md-4 col-lg-3">
                    <div class="part-card">
                        <div class="part-img-container"><img src="/inventory/assets/landingpage-images/Coolant-Hoses.webp" alt="Coolant Hoses"
                                class="img-fluid">
                        </div>
                        <div class="part-info">
                            <div class="stars"><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i
                                    class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
                            </div>
                            <h4 class="part-name">Coolant Hoses (Upper / Lower)</h4>
                            <div class="part-footer"><span class="price">₱1,500 – ₱3,000 each</span><button
                                    class="add-to-cart"><i class='bx bx-message-square-detail'></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="marquee-container">
        <div class="marquee-wrapper">

            <div class="marquee-row">
                <div class="marquee-content">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
                <div class="marquee-content" aria-hidden="true">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
            </div>


            <div class="marquee-row reverse">
                <div class="marquee-content">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
                <div class="marquee-content" aria-hidden="true">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
            </div>


            <div class="marquee-row">
                <div class="marquee-content">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
                <div class="marquee-content" aria-hidden="true">
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                    <span>GREASE MONKEY FORD SPECIALIST</span>
                </div>
            </div>
        </div>
    </section>

    <section class="carousel">
        <div class="carousel-bg">
            <img src="/inventory/assets/landingpage-images/modern-empty-room.jpg" id="main-bg">
            <div class="overlay"></div>
        </div>

        <div class="carousel-content">
            <div class="text-area">
                <h1 id="slide-title">Inazuma</h1>
                <p id="slide-desc">is characterized by its stunning landscapes...</p>
            </div>

            <div class="nav-container">
                <button class="nav-btn" id="prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="nav-btn" id="next-btn"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="fullscreen-modal" id="fullscreen-modal">
            <button class="close-modal" id="close-modal">&times;</button>
            <img src="" id="fullscreen-img">
        </div>
    </section>

    <section class="horizontal-section" id="about">
        <div class="pin-wrapper">
            <div class="horizontal-content">

                <div class="about-slide">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-9 text-center">
                                <h2 class="slide-title">About Us</h2>
                                <p class="highlight-text text-about">
                                    Ford Grease Monkey Automotive Repair is a trusted automotive service center
                                    specializing in Ford vehicles. We are committed to delivering reliable, high-quality
                                    repairs using the right tools, proper diagnostics, and industry-standard procedures
                                    to keep your vehicle performing at its best.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="about-slide">
                    <div class="container-fluid px-5">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <img src="/inventory/assets/landingpage-images/mission.png" class="slide-icon" alt="Mission">
                            </div>
                            <div class="col-md-7">
                                <h2 class="slide-title">Mission</h2>
                                <p class="highlight-text text-mission">
                                    To provide trusted, high-quality automotive repair services for Ford vehicles by combining 
                                    expert diagnostics, proper tools, and industry-standard procedures, ensuring every customer’s 
                                    vehicle performs safely and reliably.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="about-slide">
                    <div class="container-fluid px-5">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <img src="/inventory/assets/landingpage-images/Vision.png" class="slide-icon" alt="Vision">
                            </div>
                            <div class="col-md-7">
                                <h2 class="slide-title">Vision</h2>
                                <p class="highlight-text text-vision">
                                    To be the leading Ford-focused automotive service center recognized for excellence, 
                                    reliability, and customer satisfaction, where every vehicle receives care as precise 
                                    as its engineering.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-container" id="faq">
        <div class="faq-content">
            <h2 class="faq-main-title">Frequently Asked Question</h2>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What are the common problems with modules?</span>
                    <div class="faq-icon-wrapper">
                        <span class="plus">+</span>
                        <span class="minus">−</span>
                    </div>
                </button>
                <div class="faq-answer">
                    <div class="answer-inner">
                        Ford is heavy on electronics thats, so Car battery is one of the main problems that ford
                        cars
                        face.
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>Why is the retail price on ford parts so expensive?</span>
                    <div class="faq-icon-wrapper">
                        <span class="plus">+</span>
                        <span class="minus">−</span>
                    </div>
                </button>
                <div class="faq-answer">
                    <div class="answer-inner">
                        Ford is a US cars and is manifactured on mexico, the lack of supply and the high demand in
                        the
                        philippines causes Ford car parts to inflate in the philippines
                    </div>
                </div>
            </div>


            <div class="faq-item">
                <button class="faq-question">
                    <span>How can I Trust Ford Grease Monkey to service and repair our vehicle</span>
                    <div class="faq-icon-wrapper">
                        <span class="plus">+</span>
                        <span class="minus">−</span>
                    </div>
                </button>
                <div class="faq-answer">
                    <div class="answer-inner">
                        Ford Grease Monkey is a highly well known company that is affiliated with Ford Philippines
                        and
                        is the only known company that can repair modules.
                    </div>
                </div>
            </div>


            <div class="faq-item">
                <button class="faq-question">
                    <span>How long does Repairing modules and doing maintenance take?</span>
                    <div class="faq-icon-wrapper">
                        <span class="plus">+</span>
                        <span class="minus">−</span>
                    </div>
                </button>
                <div class="faq-answer">
                    <div class="answer-inner">
                        The average replacement and repair of module can take from 2h all the way to 6h, it wont
                        take no
                        more than a day to completely fix your car.
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="login-modal-overlay" id="loginModalOverlay"></div>
            <!-- Modal wrapper (keeps it centered) -->
            <div class="login-modal-wrapper">
                <div class="login-modal-box" id="loginModalBox">

                    <!-- Close Button -->
                    <button class="login-modal-close" id="loginModalClose" aria-label="Close login">✕</button>

                    <!-- ── HEADER (shared) ── -->
                    <div class="lm-header">
                        <div class="lm-logo-row">
                            <img src="/inventory/assets/landingpage-images/logo.png" alt="Grease Monkey" class="lm-logo-img">
                            <p class="lm-brand-name">Grease Monkey</p>
                        </div>

                        <div id="lmPanelLoginHeader" class="lm-panel active text-center">
                            <h2 class="lm-title">Inventory System</h2>
                            <p class="lm-subtitle text-danger fw-semibold mt-2">
                                <i class="bi bi-shield-exclamation-fill me-1"></i> <!-- Bootstrap icon -->
                                Only authorized personnel can access this system.
                            </p>
                        </div>

                        <!-- Forgot password panel header -->
                        <div id="lmPanelForgotHeader" class="lm-panel">
                            <h2 class="lm-title">🔑 Reset Password</h2>
                			<p class="lm-subtitle text-danger fw-semibold mt-2">
                                <i class="bi bi-shield-exclamation-fill me-1"></i>
                                Password reset is restricted to the registered administrator email.
                            </p>                     
                        </div>
                    </div>

                    <!-- ── LOGIN PANEL ── -->
                    <div id="lmPanelLogin" class="lm-panel active">
                        <div class="lm-body">

                            <?php if (!empty($error)): ?>
                            <div class="lm-alert lm-alert-error" id="lmServerError">
                                <span>⚠️</span>
                                <div><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="index.php?page=landing_page">

                                <div class="lm-field">
                                    <label for="lmUsername" class="lm-label">Username</label>
                                    <div class="lm-input-wrap">
                                        <span class="lm-input-icon">👤</span>
                                        <input
                                            type="text"
                                            name="username"
                                            id="lmUsername"
                                            class="lm-input"
                                            placeholder="Enter your username"
                                            required
                                            autocomplete="username"
                                        >
                                    </div>
                                </div>

                                <div class="lm-field">
                                    <label for="lmPassword" class="lm-label">Password</label>
                                    <div class="lm-input-wrap">
                                        <span class="lm-input-icon">🔒</span>
                                        <input
                                            type="password"
                                            name="password"
                                            id="lmPassword"
                                            class="lm-input"
                                            placeholder="Enter your password"
                                            required
                                            autocomplete="current-password"
                                        >
                                        <span class="lm-eye-toggle" id="lmEyeToggle" title="Toggle password">👁️</span>
                                    </div>
                                </div>

                                <button type="submit" name="login" class="lm-btn">Login to Account</button>

                            </form>
                        </div>

                        <div class="lm-footer">
                            <p class="lm-footer-text">
                                Forgot password?
                                <a class="lm-switch-link" onclick="lmShowForgot()">Reset password</a>
                            </p>
                        </div>
                    </div>

                    <!-- ── FORGOT PASSWORD PANEL ── -->
                    <div id="lmPanelForgot" class="lm-panel">
                        <div class="lm-body">
							
                            <form method="POST" action="index.php?page=forgot_password">

                                <div class="lm-field">
                                    <label for="lmEmail" class="lm-label">Email Address</label>
                                    <div class="lm-input-wrap">
                                        <span class="lm-input-icon">📧</span>
                                        <input
                                            type="email"
                                            name="email"
                                            id="lmEmail"
                                            class="lm-input"
                                            placeholder="Enter your email address"
                                            required
                                            autocomplete="email"
                                        >
                                    </div>
                                </div>

                                <button type="submit" class="lm-btn">Send Reset Link</button>

                                <p class="lm-info-note">
                                    We'll send you an email with instructions to reset your password.
                                </p>

                            </form>
                        </div>

                        <div class="lm-footer">
                            <p class="lm-footer-text">
                                <a class="lm-switch-link" onclick="lmShowLogin()">← Back to login</a>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
<footer class="site-footer">
        <div class="footer-container">


            <div class="footer-logo">
                <img src="/inventory/assets/landingpage-images/logo.png" alt="Grease monkey" />
            </div>


            <div class="footer-title">
                <h2>GREASE MONKEY</h2>
            </div>


            <div class="footer-links" style="width: 100%;">


                <div class="footer-top-details">

                    <div class="detail-col">
                        <h3>QUICK LINKS</h3>
                        <ul>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#home">Home</a></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#about">About Us</a></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#parts">Parts Catalog</a></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#services">Our Services</a></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#faq">FAQ</a></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <a href="#contact">Contact</a></li>
                        </ul>
                    </div>


                    <div class="detail-col">
                        <h3>SERVICES</h3>
                        <ul>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>General Diagnostics</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Engine & Transmission</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Ford Module Repair</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Lost Key Fabrication</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Key Programming</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Odometer Correction</span></li>
                            <li><i class="fa-solid fa-wrench wrench-icon"></i> <span>Technical Services</span></li>
                        </ul>
                    </div>


                    <div class="detail-col">
                        <h3>CONTACT US</h3>
                        <ul>
                            <li><i class="fa-solid fa-location-dot pin-icon"></i> <span>Gmonkey Ford Valenzuela
                                </span></li>
                            <li><i class="fa-solid fa-phone phone-icon"></i> <span>0995 573 3693</span></li>
                            <li><i class="fa-regular fa-envelope mail-icon"></i> <span>@vinzacosta111@gmail.com
                                </span>
                            </li>
                            <li><i class="fa-regular fa-clock clock-icon"></i> <span> Monday-sunday open 7am-9pm</span>
                            </li>
                        </ul>
                    </div>
                </div>


                <div class="footer-column">
                    <ul class="social-icons">
                        <li>
                            <a href="https://www.facebook.com/profile.php?id=61554345268520" aria-label="Facebook" target="_blank">
                                <img src="/inventory/assets/landingpage-images/facebook.png" alt="Facebook" />
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 GREASE MONKEY. ALL RIGHTS RESERVED</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>



    <script src="https://unpkg.com/split-type"></script>
    <script src="/inventory/assets/js/nav.js"></script>
    <script src="/inventory/assets/js/parts.js"></script>
    <script src="/inventory/assets/js/aboutus.js"></script>
    <script src="/inventory/assets/js/text-animation.js"></script>
    <script src="/inventory/assets/js/faq.js"></script>
    <script src="/inventory/assets/js/carousel.js"></script>
    <script src="/inventory/assets/js/landingpage.js"></script>
</body>

</html>