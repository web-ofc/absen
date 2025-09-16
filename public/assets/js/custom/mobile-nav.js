// File: public/assets/js/custom/mobile-nav.js
// Atau tambahkan di _scripts.blade.php

document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi mobile navigation
    initMobileNavigation();

    // Handle window resize
    window.addEventListener("resize", function () {
        handleMobileNavVisibility();
    });

    // Handle theme changes
    document.addEventListener("kt.thememode.change", function () {
        updateMobileNavTheme();
    });
});

function initMobileNavigation() {
    const mobileNav = document.getElementById("mobile-bottom-nav");
    if (!mobileNav) return;

    // Set initial visibility
    handleMobileNavVisibility();

    // Add click handlers for navigation items
    const navItems = mobileNav.querySelectorAll(".nav-item");
    navItems.forEach((item) => {
        item.addEventListener("click", function (e) {
            // Handle navigation logic jika diperlukan
            handleNavItemClick(this, e);
        });
    });

    // Add touch feedback
    addTouchFeedback();
}

function handleMobileNavVisibility() {
    const mobileNav = document.getElementById("mobile-bottom-nav");
    const hamburgerBtn = document.getElementById(
        "kt_app_sidebar_mobile_toggle"
    );

    if (!mobileNav) return;

    const isMobile = window.innerWidth <= 991.98;

    if (isMobile) {
        // Show mobile nav
        mobileNav.style.display = "block";

        // Hide hamburger button karena kita pakai bottom nav
        if (hamburgerBtn) {
            hamburgerBtn.style.display = "none";
        }

        // Biarkan Metronic handle sidebar secara natural
    } else {
        // Hide mobile nav
        mobileNav.style.display = "none";

        // Show hamburger button kembali untuk desktop
        if (hamburgerBtn) {
            hamburgerBtn.style.display = "";
        }
    }
}

function handleNavItemClick(element, event) {
    // Remove active class from all nav items
    const allNavItems = document.querySelectorAll(
        ".mobile-bottom-nav .nav-item"
    );
    allNavItems.forEach((item) => item.classList.remove("active"));

    // Add active class to clicked item
    if (!element.getAttribute("data-bs-toggle")) {
        element.classList.add("active");
    }

    // Add ripple effect
    createRippleEffect(element, event);

    // Haptic feedback untuk mobile devices
    if ("vibrate" in navigator) {
        navigator.vibrate(50);
    }
}

function createRippleEffect(element, event) {
    const ripple = document.createElement("div");
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(var(--bs-primary-rgb), 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        pointer-events: none;
    `;

    element.style.position = "relative";
    element.appendChild(ripple);

    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function addTouchFeedback() {
    const style = document.createElement("style");
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .mobile-bottom-nav .nav-item:active {
            transform: scale(0.95);
        }
    `;
    document.head.appendChild(style);
}

function updateMobileNavTheme() {
    // Update theme-related styling jika diperlukan
    const mobileNav = document.getElementById("mobile-bottom-nav");
    if (!mobileNav) return;

    // Logika untuk update theme bisa ditambahkan di sini
    // Metronic sudah handle theme secara otomatis dengan CSS variables
}

// Utility functions
function setActiveNavItem(route) {
    const navItems = document.querySelectorAll(".mobile-bottom-nav .nav-item");
    navItems.forEach((item) => {
        item.classList.remove("active");
        if (
            item.getAttribute("href") === route ||
            item.getAttribute("href").includes(route)
        ) {
            item.classList.add("active");
        }
    });
}

function addNotificationBadge(navItemSelector, count) {
    const navItem = document.querySelector(navItemSelector);
    if (!navItem) return;

    const icon = navItem.querySelector(".nav-icon");
    if (!icon) return;

    // Remove existing badge
    const existingBadge = icon.querySelector(".nav-badge");
    if (existingBadge) {
        existingBadge.remove();
    }

    if (count > 0) {
        const badge = document.createElement("span");
        badge.className = "nav-badge";
        badge.textContent = count > 99 ? "99+" : count;
        icon.appendChild(badge);
    }
}

// Export functions untuk digunakan di tempat lain
window.mobileNav = {
    setActive: setActiveNavItem,
    addBadge: addNotificationBadge,
    init: initMobileNavigation,
};
