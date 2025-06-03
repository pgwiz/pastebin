document.addEventListener('DOMContentLoaded', () => {
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });

            // Close mobile nav after clicking a link
            if (navbar.classList.contains('nav-active')) {
                navbar.classList.remove('nav-active');
                burger.classList.remove('toggle');
                document.querySelectorAll('.nav-links li').forEach((link, index) => {
                    link.style.animation = '';
                });
            }
        });
    });

    // Mobile Navigation Toggle
    const burger = document.querySelector('.burger-menu');
    const navbar = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links li');

    burger.addEventListener('click', () => {
        // Toggle Nav
        navbar.classList.toggle('nav-active');

        // Animate Links
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`;
            }
        });

        // Burger Animation
        burger.classList.toggle('toggle');
    });

    // Animation for navigation links when active
    // Define the keyframes for navLinkFade animation
    const styleSheet = document.createElement('style');
    styleSheet.type = 'text/css';
    styleSheet.innerText = `
        @keyframes navLinkFade {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0px);
            }
        }
    `;
    document.head.appendChild(styleSheet);


    // Header Shrink on Scroll
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 80) { // Adjust this value as needed
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }
    });

    // Add CSS for header-scrolled class dynamically
    const headerShrinkStyle = document.createElement('style');
    headerShrinkStyle.type = 'text/css';
    headerShrinkStyle.innerText = `
        .header-scrolled {
            height: 60px; /* Smaller height */
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            transition: height 0.3s ease, box-shadow 0.3s ease;
        }
        .header-scrolled .navbar {
            height: 60px;
        }
        .header-scrolled .logo a {
            font-size: 1.5em; /* Smaller logo */
        }
        .header-scrolled .nav-links a {
            font-size: 1em; /* Smaller links */
        }
    `;
    document.head.appendChild(headerShrinkStyle);


    // Form Submission (Example using Fetch API for a dummy endpoint)
    const contactForm = document.querySelector('.contact-form');
    const formMessages = document.getElementById('form-messages');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(contactForm);
            // In a real application, you'd send this data to a backend server.
            // For now, we'll simulate a response.
            const dummyEndpoint = 'https://jsonplaceholder.typicode.com/posts'; // A public fake API

            try {
                const response = await fetch(dummyEndpoint, {
                    method: 'POST',
                    body: JSON.stringify(Object.fromEntries(formData)), // Convert FormData to JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    formMessages.classList.remove('error');
                    formMessages.classList.add('success');
                    formMessages.textContent = 'Message sent successfully! We will get back to you soon.';
                    contactForm.reset();
                } else {
                    formMessages.classList.remove('success');
                    formMessages.classList.add('error');
                    formMessages.textContent = 'Oops! Something went wrong. Please try again later.';
                }
            } catch (error) {
                console.error('Form submission error:', error);
                formMessages.classList.remove('success');
                formMessages.classList.add('error');
                formMessages.textContent = 'Network error. Please check your internet connection and try again.';
            } finally {
                formMessages.style.display = 'block';
                setTimeout(() => {
                    formMessages.style.display = 'none';
                }, 5000); // Hide message after 5 seconds
            }
        });
    }

    // Dynamic Copyright Year
    const currentYearSpan = document.getElementById('current-year');
    if (currentYearSpan) {
        currentYearSpan.textContent = new Date().getFullYear();
    }


    // Intersection Observer for animations on scroll (optional, but highly recommended)
    const sections = document.querySelectorAll('section');

    const observerOptions = {
        root: null, // relative to the viewport
        threshold: 0.1, // trigger when 10% of the section is visible
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in'); // Add a class for animation
                observer.unobserve(entry.target); // Stop observing once animated
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        section.classList.add('fade-on-scroll'); // Add initial class
        observer.observe(section);
    });

    // Add CSS for fade-in effect dynamically
    const fadeInStyle = document.createElement('style');
    fadeInStyle.type = 'text/css';
    fadeInStyle.innerText = `
        .fade-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-on-scroll.fade-in {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(fadeInStyle);

});