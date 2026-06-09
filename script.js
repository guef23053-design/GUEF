// ===== PAGE LOADER =====
window.addEventListener('load', () => {
    const loader = document.querySelector('.loader-wrapper');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('fade-out');
        }, 500);
    }
});

// ===== SCROLL ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
});

// ===== SLIDESHOW =====
let slideIndex = 0;
const slides = document.getElementsByClassName("slide");
const dots = document.getElementsByClassName("dot");
let slideInterval;

function showSlides() {
    if (slides.length === 0) return;
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
        if (dots[i]) dots[i].classList.remove("active");
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1; }
    if (slides[slideIndex - 1]) slides[slideIndex - 1].style.display = "block";
    if (dots[slideIndex - 1]) dots[slideIndex - 1].classList.add("active");
    slideInterval = setTimeout(showSlides, 5000);
}

if (slides.length > 0) {
    showSlides();
}

if (dots.length > 0) {
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            clearTimeout(slideInterval);
            slideIndex = index;
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
                dots[i].classList.remove("active");
            }
            if (slides[slideIndex]) slides[slideIndex].style.display = "block";
            if (dots[slideIndex]) dots[slideIndex].classList.add("active");
            slideIndex++;
            slideInterval = setTimeout(showSlides, 5000);
        });
    });
}

const heroSection = document.querySelector('.hero');
if (heroSection) {
    heroSection.addEventListener('mouseenter', () => clearTimeout(slideInterval));
    heroSection.addEventListener('mouseleave', () => {
        clearTimeout(slideInterval);
        slideInterval = setTimeout(showSlides, 5000);
    });
}

// ===== COUNTER ANIMATION =====
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 20);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (target && !counter.classList.contains('counted')) {
                    animateCounter(counter, target);
                    counter.classList.add('counted');
                }
            });
            counterObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const impactSection = document.querySelector('.impact-stats');
if (impactSection) {
    counterObserver.observe(impactSection);
}

// ===== TESTIMONIAL SLIDER =====
const testimonialSlides = document.querySelector('.testimonial-slides');
const testimonialPrev = document.querySelector('.testimonial-prev');
const testimonialNext = document.querySelector('.testimonial-next');
const testimonialDotsContainer = document.querySelector('.testimonial-dots');

if (testimonialSlides) {
    const slides = document.querySelectorAll('.testimonial-slide');
    let currentTestimonial = 0;
    const totalSlides = slides.length;
    let testimonialInterval;

    if (testimonialDotsContainer) {
        slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('testimonial-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            testimonialDotsContainer.appendChild(dot);
        });
    }

    const dots = document.querySelectorAll('.testimonial-dot');

    function goToSlide(index) {
        currentTestimonial = index;
        testimonialSlides.style.transform = `translateX(-${currentTestimonial * 100}%)`;
        
        if (dots.length > 0) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentTestimonial);
            });
        }
    }

    function nextSlide() {
        currentTestimonial = (currentTestimonial + 1) % totalSlides;
        goToSlide(currentTestimonial);
    }

    function prevSlide() {
        currentTestimonial = (currentTestimonial - 1 + totalSlides) % totalSlides;
        goToSlide(currentTestimonial);
    }

    if (testimonialNext) {
        testimonialNext.addEventListener('click', () => {
            clearInterval(testimonialInterval);
            nextSlide();
            testimonialInterval = setInterval(nextSlide, 6000);
        });
    }
    
    if (testimonialPrev) {
        testimonialPrev.addEventListener('click', () => {
            clearInterval(testimonialInterval);
            prevSlide();
            testimonialInterval = setInterval(nextSlide, 6000);
        });
    }

    testimonialInterval = setInterval(nextSlide, 6000);
    
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        testimonialSlider.addEventListener('mouseenter', () => clearInterval(testimonialInterval));
        testimonialSlider.addEventListener('mouseleave', () => {
            testimonialInterval = setInterval(nextSlide, 6000);
        });
    }
}

// ===== SIDEBAR FUNCTIONALITY =====
const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarClose = document.getElementById('sidebarClose');
const sidebarLinks = document.querySelectorAll('.sidebar-link');

function openSidebar() {
    sidebar.classList.add('active');
    sidebarOverlay.classList.add('active');
    document.body.classList.add('sidebar-open');
    if (hamburger) hamburger.classList.add('active');
}

function closeSidebar() {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
    document.body.classList.remove('sidebar-open');
    if (hamburger) hamburger.classList.remove('active');
}

if (hamburger) {
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });
}

if (sidebarClose) {
    sidebarClose.addEventListener('click', closeSidebar);
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeSidebar);
}

// Handle sidebar link clicks
sidebarLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        
        // Update active state
        sidebarLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        
        // Close sidebar after a small delay
        setTimeout(() => {
            closeSidebar();
        }, 200);
        
        // Smooth scroll to section
        if (href && href !== '#' && href.startsWith('#')) {
            e.preventDefault();
            const targetElement = document.querySelector(href);
            if (targetElement) {
                setTimeout(() => {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }
        }
    });
});

// Close sidebar on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('active')) {
        closeSidebar();
    }
});

// Close sidebar on window resize if open
window.addEventListener('resize', () => {
    if (window.innerWidth > 767) {
        closeSidebar();
    }
});

// ===== BACK TO TOP BUTTON =====
const backToTop = document.getElementById('backToTop');

if (backToTop) {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===== THEME TOGGLE (DARK MODE) =====
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    const themeIcon = themeToggle.querySelector('i');

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeIcon) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }

    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        
        if (themeIcon) {
            if (document.body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        }
    });
}

// ===== NEWSLETTER FORM =====
const newsletterForm = document.getElementById('newsletterForm');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        if (emailInput) {
            const email = emailInput.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }
            alert(`Thank you for subscribing with: ${email}`);
            newsletterForm.reset();
        }
    });
}

// ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#' || href === '#home') {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});

// ===== HEADER SCROLL EFFECT =====
const header = document.querySelector('header');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (header) {
        if (currentScroll > 100) {
            header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
        } else {
            header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        }
    }
});

// ===== ACTIVE SECTION HIGHLIGHT =====
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link, .sidebar-link');

function updateActiveSection() {
    const scrollPosition = window.scrollY + 100;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        const sectionId = section.getAttribute('id');
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (href === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
}

window.addEventListener('scroll', updateActiveSection);
window.addEventListener('load', updateActiveSection);

// ===== TOUCH SWIPE FOR SIDEBAR =====
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
}, { passive: true });

document.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipeGesture();
}, { passive: true });

function handleSwipeGesture() {
    const swipeThreshold = 50;
    // Swipe left to close sidebar
    if (touchStartX - touchEndX > swipeThreshold && sidebar.classList.contains('active')) {
        closeSidebar();
    }
    // Swipe right from edge to open sidebar
    if (touchEndX - touchStartX > swipeThreshold && touchStartX < 30 && !sidebar.classList.contains('active')) {
        openSidebar();
    }
}

// ===== PREVENT DEFAULT ON EMPTY LINKS =====
document.querySelectorAll('a[href="#"]').forEach(link => {
    link.addEventListener('click', (e) => e.preventDefault());
});

// ===== PAGE VISIBILITY API =====
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        if (slideInterval) clearTimeout(slideInterval);
        if (typeof testimonialInterval !== 'undefined') clearInterval(testimonialInterval);
    } else {
        if (slides.length > 0) {
            clearTimeout(slideInterval);
            slideInterval = setTimeout(showSlides, 5000);
        }
        if (typeof testimonialInterval !== 'undefined' && typeof nextSlide === 'function') {
            clearInterval(testimonialInterval);
            testimonialInterval = setInterval(nextSlide, 6000);
        }
    }
});

// ===== CONSOLE WELCOME MESSAGE =====
console.log('%c🚀 Great United Eastern Foundations', 'font-size: 20px; font-weight: bold; color: #2d6a4f;');
console.log('%cEmpowering Communities for a Better Future', 'font-size: 14px; color: #40916c;');