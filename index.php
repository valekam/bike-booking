<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike/vaya.png" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="#home">HOME</a>
            <a href="#services">SERVICES</a>
            <a href="#about">ABOUT</a> 
            <a href="#contact">CONTACT US</a>  
            <a href="sign_up.php">SIGN UP</a> <!-- Link to the signup page -->
        </nav>
    </header>

    <section class="home" id="home">
        <div class="container">
            <div class="description">
                <h1 class="typing-effect">Welcome To <span class="highlight">Secure Bike Booking System</span></h1>
                <p>Are you ready to embark on your next <span class="italic">bike adventure</span>? Our seamless booking system ensures you have everything you need for your journey. With a variety of bikes to choose from, <span class="bold">user-friendly features</span>, and 24/7 support, we make your biking experience unforgettable!</p>
                <p>Whether you're a seasoned cyclist or a first-timer, <span class="underline">we cater to all levels</span>. Enjoy the freedom of the open road and the thrill of exploration with our easy-to-navigate platform. Let’s pedal into your next adventure together!</p>
            </div>

            <div class="bike-card" id="bike1">
                <div class="bike-image">
                    <img src="bike/yamaha.jpg" alt="Bike Image">
                </div>
                <form action="sign_up.php" class="button-form">
                    <button type="submit" class="btn">Explore To Find A Ride</button>
                </form>
            </div>
        </div>
    </section>

    <hr class="divider">

    <section class="video-section">
        <div class="video-container">
            <video autoplay muted loop class="fullscreen-video">
                <source src="bike/bike.mp4" type="video/mp4"> <!-- Replace with your video file -->
                Your browser does not support the video tag.
            </video>
            <div class="overlay">
                <h2>Need Assistance?</h2>
                <p>If you have any questions about our services, feel free to reach out!</p>
                <a href="https://wa.me/0796009283" target="_blank" class="whatsapp-button">Contact Us on WhatsApp</a> <!-- Replace with your WhatsApp number -->
            </div>
        </div>
    </section>

<hr class="divider">

    <section class="services" id="services">
        <div class="service-container">
            <div class="image-card">
                <div class="main-image">
                    <img src="bike/yamaha 1.jpg" alt="Main Image" class="oval-image">
                </div>
            </div>
            <div class="service-info">
                <h1>OUR SERVICES</h1>
                <h4>We offer various services for your bike that includes:</h4>
                <p class="bend-text">*Bike Booking</p>
                <p class="bend-text">*Bike Adventures</p>
                <p class="bend-text">*Bike Road Trip</p>
            </div>
        </div>
    </section>

    <section class="about" id="about">
        <h1>ABOUT US</h1>
        <p>Welcome To Secure Bike Booking.</p>
        <div class="row company-info">
            <h3>Our Story</h3>
            <p>Founded on principles of reliability and customer-centric service, FREBUDDZ provides a seamless bike booking experience. Our mission is to make biking accessible and enjoyable for everyone, whether you’re commuting, exploring, or exercising. With our user-friendly platform, you can easily reserve bikes tailored to your needs, ensuring a hassle-free journey.</p>
            <h3>Why Choose Us?</h3>
            <ul>
                <li>✅ Wide Range of Bikes: From mountain bikes to city cruisers, we have something for everyone.</li>
                <li>✅ Secure Transactions: Your safety is our priority; enjoy worry-free payments.</li>
                <li>✅ 24/7 Customer Support: Our team is always here to help you, day or night.</li>
                <li>✅ Eco-Friendly Options: Contribute to a greener planet by choosing biking.</li>
            </ul>
        </div>
    </section>

    <section class="contact" id="contact">
        <h1>Contact Us</h1>
        <div class="contact-info">
            <div class="contact-item">
                <img src="bike/phone icon.jpg" alt="Phone Icon" class="icon">
                <h3>Phone</h3>
                <p>+254796009283</p>
            </div>
            <div class="contact-item">
                <img src="bike/gmail.png" alt="Email Icon" class="icon">
                <h3>Email</h3>
                <p>fredkamau356@gmail.com</p>
            </div>
            <div class="contact-item">
                <img src="bike/location.png" alt="Location Icon" class="icon">
                <h3>Location</h3>
                <p>Muranga University, Opposite Mwangaza Shop.</p>
            </div>
        </div>
        <div class="contact-form">
            <h3>Get in Touch</h3>
            <form id="contactForm" method="post">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                <button type="submit" class="bt">Send Message</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <span>Copyright © 2024 All Rights Reserved | Fred Kamau</span>
            <span class="link">
                <a href="#home">Home</a>
                <a href="#contact">Contact</a>
            </span>
            <div class="social-links">
                <a href="#" aria-label="Facebook" title="Facebook">
                    <img src="bike/facebook.png" alt="Facebook Logo" />
                </a>
                <a href="#" aria-label="WhatsApp" title="WhatsApp">
                    <img src="bike/whatup.png" alt="WhatsApp Logo" />
                </a>
                <a href="#" aria-label="Instagram" title="Instagram">
                    <img src="bike/ig.jpg" alt="Instagram Logo" />
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
