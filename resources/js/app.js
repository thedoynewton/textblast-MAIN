import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './userManagement';

// Theme functionality
document.addEventListener("DOMContentLoaded", function () {
    // Function to toggle the visibility of the dropdown
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdown");
        if (dropdown) {
            dropdown.classList.toggle("hidden");
        }
    }
    // Function to set the theme and store it in localStorage
    function setTheme(theme) {
        if (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            hideDropdown(); // Hide the dropdown after selection
        }
    }
    // Function to load the stored theme from localStorage
    function loadTheme() {
        const storedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', storedTheme);
    }
    // Function to hide the dropdown explicitly
    function hideDropdown() {
        const dropdown = document.getElementById("dropdown");
        if (dropdown && !dropdown.classList.contains("hidden")) {
            dropdown.classList.add("hidden");
        }
    }
    // Event listener for window load to ensure theme is loaded correctly
    window.onload = loadTheme;
    // Attach the functions to the window object for accessibility
    window.toggleDropdown = toggleDropdown;
    window.setTheme = setTheme;
});
// Wave Asset in Layouts
document.addEventListener("DOMContentLoaded", function () {
    const waveEffect = document.getElementById("waveEffect");
    if (waveEffect) { // Check if the waveEffect element exists
        const adjustWavePosition = () => {
            const contentHeight = document.body.scrollHeight;
            const viewportHeight = window.innerHeight;
            if (contentHeight > viewportHeight) {
                // If the page content is scrollable, position wave at the bottom of the page
                waveEffect.style.position = "relative";
                waveEffect.style.marginTop = "auto";
                waveEffect.style.bottom = "0";
            } else {
                // If the page content is not scrollable, stick the wave effect at the bottom
                waveEffect.style.position = "fixed";
                waveEffect.style.bottom = "0";
            }
        };
        // Adjust wave position on page load and when window is resized
        adjustWavePosition();
        window.addEventListener("resize", adjustWavePosition);
    }
});
// Handles Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('broadcast-form');
    // Add validation for form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent form submission until validation passes
        // Clear previous error messages
        clearErrorMessages();
        var filtersValid = true;
        var broadcastType = document.getElementById('broadcast_type').value;
        var campus = document.getElementById('campus');
        var message = document.getElementById('message');
        // Validate ALL tab
        if (broadcastType === 'all') {
            if (!campus.value) {
                showError(campus, 'Please select a campus.');
                filtersValid = false;
            }
            if (!message.value.trim()) {
                showError(message, 'Please enter a message.');
                filtersValid = false;
            }
        }
        // Validate STUDENTS tab
        if (broadcastType === 'students') {
            var college = document.getElementById('college');
            var program = document.getElementById('program');
            var year = document.getElementById('year');
            if (!campus.value) {
                showError(campus, 'Please select a campus.');
                filtersValid = false;
            }
            if (!college.value) {
                showError(college, 'Please select a college.');
                filtersValid = false;
            }
            if (!program.value) {
                showError(program, 'Please select a program.');
                filtersValid = false;
            }
            if (!year.value) {
                showError(year, 'Please select a year level.');
                filtersValid = false;
            }
            if (!message.value.trim()) {
                showError(message, 'Please enter a message.');
                filtersValid = false;
            }
        }
        // Validate EMPLOYEES tab
        if (broadcastType === 'employees') {
            var office = document.getElementById('office');
            var status = document.getElementById('status');
            var type = document.getElementById('type');
            if (!campus.value) {
                showError(campus, 'Please select a campus.');
                filtersValid = false;
            }
            if (!office.value) {
                showError(office, 'Please select an office.');
                filtersValid = false;
            }
            if (!status.value) {
                showError(status, 'Please select a status.');
                filtersValid = false;
            }
            if (!type.value) {
                showError(type, 'Please select a type.');
                filtersValid = false;
            }
            if (!message.value.trim()) {
                showError(message, 'Please enter a message.');
                filtersValid = false;
            }
        }
        // Submit the form if all validations pass
        if (filtersValid) {
            this.submit();
        }
    });
    // Event listeners to remove error message when the field is filled
    form.querySelectorAll('select, textarea').forEach(function(input) {
        input.addEventListener('input', function() {
            if (input.value) {
                removeError(input);
            }
        });
    });
    function clearErrorMessages() {
        var errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(function(element) {
            element.remove();
        });
        var fields = document.querySelectorAll('.error');
        fields.forEach(function(field) {
            field.classList.remove('error');
        });
    }
    function showError(input, message) {
        input.classList.add('error');  // Highlight the input field with an error
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message text-red-600 text-sm mt-1';
        errorMessage.innerText = message;
        input.parentNode.appendChild(errorMessage);
    }
    function removeError(input) {
        input.classList.remove('error');  // Remove the error class from the input field
        var errorElement = input.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();  // Remove the error message
        }
    }
});