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
