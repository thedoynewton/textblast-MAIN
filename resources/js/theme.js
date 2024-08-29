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
