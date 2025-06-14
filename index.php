<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Fetch active programs and locations for the form
$programs = $pdo->query("SELECT * FROM program_types WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
$locations = $pdo->query("SELECT * FROM locations WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitLife - Book Your Fitness Session</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="assets/css/animate.css">
    
    <!-- Custom CSS -->
    <style>
        /* Left Side Styles */
        .booking-form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .program-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Right Side Styles */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('assets/images/hero.jpg') center/cover no-repeat;
            height: 100%;
            position: relative;
            overflow: hidden;
            animation: zoomPan 20s infinite alternate;
        }
        
        @keyframes zoomPan {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }
        
        .logo-container {
            position: absolute;
            top: 2rem;
            left: 0;
            right: 0;
            text-align: center;
            animation: fadeInDown 1s both;
        }
        
        .logo-img {
            max-width: 200px;
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5));
        }
        
        .hero-content {
            position: absolute;
            bottom: 3rem;
            left: 0;
            right: 0;
            color: white;
            text-align: center;
            padding: 0 2rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero-section {
                height: 300px;
            }
        }
        .left-section{
            background-color:#9e22ba;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Left Side - Booking Form -->
            <div class="col-lg-5 col-12 left-section text-white p-5 position-relative">
                <div class="booking-form-container">
                    <img src="assets/images/logo.png" alt="" width="100" height="100">
                    <h1 class="display-4 mb-4 animate__animated animate__fadeInDown">Book Your Session</h1>
                    <p class="lead mb-5 animate__animated animate__fadeIn animate__delay-1s">Start your fitness journey with us today!</p>
                    
                    <!-- Multi-step Form (unchanged from your original) -->
                    <form id="bookingForm" class="needs-validation" novalidate>
                        <!-- Step 1: Program Selection -->
                        <div class="booking-step animate__animated animate__fadeIn animate__delay-1s" data-step="1">
                            <h3 class="mb-4">Choose Your Program</h3>
                            <div class="row g-3">
                                <?php foreach ($programs as $program): ?>
                                <div class="col-md-6">
                                    <div class="form-check card program-card">
                                        <input class="form-check-input" type="radio" name="program_type" 
                                               id="program-<?= $program['program_type_id'] ?>" 
                                               value="<?= $program['program_type_id'] ?>" required>
                                        <label class="form-check-label card-body" for="program-<?= $program['program_type_id'] ?>">
                                            <h5><?= htmlspecialchars($program['name']) ?></h5>
                                            <p class="text-muted"><?= htmlspecialchars($program['description']) ?></p>
                                            <span class="badge bg-dark">$<?= number_format($program['base_price'], 2) ?></span>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-light next-step" data-next="2">Next</button>
                            </div>
                        </div>
                        
                        <!-- Step 2: Location Selection -->
                        <div class="booking-step d-none animate__animated" data-step="2">
                            <h3 class="mb-4">Select Location</h3>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="locationSelect" name="location" required>
                                            <option value="" selected disabled>Choose a location</option>
                                            <?php foreach ($locations as $location): ?>
                                            <option value="<?= $location['location_id'] ?>">
                                                <?= htmlspecialchars($location['name']) ?> - <?= htmlspecialchars($location['city']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="locationSelect">Fitness Center</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div id="locationMap" style="height: 300px; width: 100%;" class="rounded"></div>
                                    <small class="text-white-50">You can also drag the marker to your preferred location</small>
                                </div>
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light prev-step" data-prev="1">Back</button>
                                <button type="button" class="btn btn-light next-step" data-next="3">Next</button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Personal Information -->
                        <div class="booking-step d-none animate__animated" data-step="3">
                            <h3 class="mb-4">Your Information</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                                        <label for="firstName">First Name</label>
                                        <div class="invalid-feedback">Please provide your first name.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                                        <label for="lastName">Last Name</label>
                                        <div class="invalid-feedback">Please provide your last name.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <label for="email">Email Address</label>
                                        <div class="invalid-feedback">Please provide a valid email.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <label for="phone">Phone Number</label>
                                        <div class="invalid-feedback">Please provide your phone number.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light prev-step" data-prev="2">Back</button>
                                <button type="button" class="btn btn-light next-step" data-next="4">Next</button>
                            </div>
                        </div>
                        
                             <!-- Step 4: Schedule Selection -->
                             <div class="booking-step d-none animate__animated" data-step="4">
                            <h3 class="mb-4">Select Date & Time</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="bookingDate" name="booking_date" required>
                                        <label for="bookingDate">Preferred Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="timeSlot" name="time_slot" required>
                                            <option value="" selected disabled>Select time slot</option>
                                            <!-- Will be populated via AJAX -->
                                        </select>
                                        <label for="timeSlot">Time Slot</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            I agree to the terms and conditions
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light prev-step" data-prev="3">Back</button>
                                <button type="submit" class="btn btn-success">Complete Booking</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Success Message -->
                    <div id="bookingSuccess" class="d-none animate__animated animate__fadeIn">
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle"></i> Booking Confirmed!</h4>
                            <p>Thank you for your booking. We've sent a confirmation to your email.</p>
                            <a href="#" class="btn btn-outline-light" id="newBooking">Book Another Session</a>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="position-absolute bottom-0 start-0 p-3 w-100">
                    <p class="text-center text-white-50 mb-0">&copy; <?= date('Y') ?> FitLife Fitness. All rights reserved.</p>
                </div>
            </div>
            
            <!-- Right Side - Hero Image with Logo -->
            <div class="col-lg-7 d-none d-lg-block p-0">
                <div class="hero-section h-100">
                    <div class="logo-container">
                        <img src="assets/images/logo.png" alt="FitLife Logo" class="logo-img">
                        <h2 class="text-white mt-2 animate__animated animate__fadeIn animate__delay-1s">HOUSE OF ADUKE</h2>
                    </div>
                    
                    <div class="hero-content animate__animated animate__fadeIn animate__delay-2s">
                        <h3 class="display-5 mb-3">Transform Your Body</h3>
                        <p class="lead mb-4">Professional trainers and state-of-the-art facilities</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="btn btn-outline-light btn-lg">Our Programs</a>
                            <a href="#" class="btn left-section btn-lg">Meet Trainers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/booking-steps.js"></script>
    
    <script>
        // Background image animation
        document.addEventListener('DOMContentLoaded', function() {
            // Multiple background images (optional)
            const bgImages = [
                'assets/images/hero-bg.jpg',
                'assets/images/hero.jpg',
                'assets/images/hero-bg.jpg'
            ];
            
            // Uncomment to enable image rotation
            /*
            let currentBg = 0;
            const heroSection = document.querySelector('.hero-section');
            
            setInterval(() => {
                currentBg = (currentBg + 1) % bgImages.length;
                heroSection.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${bgImages[currentBg]})`;
                heroSection.classList.add('animate__animated', 'animate__fadeIn');
                
                setTimeout(() => {
                    heroSection.classList.remove('animate__animated', 'animate__fadeIn');
                }, 1000);
            }, 8000);
            */
        });
    </script>
</body>
</html>