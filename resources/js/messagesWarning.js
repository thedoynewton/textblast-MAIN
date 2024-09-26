document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('broadcast-form');
    const messageInput = document.getElementById('message');
    const charCountDisplay = document.getElementById('charCount');
    const charWarning = document.getElementById('charWarning');
    const maxCharLimit = 160;
    const templateSelect = document.getElementById('template'); // Message template dropdown

    // Function to update character count and display warning if needed
    function updateCharacterCount() {
        const messageLength = messageInput.value.length;
        charCountDisplay.textContent = messageLength;

        if (messageLength > maxCharLimit) {
            charWarning.classList.remove('hidden'); // Show warning message
        } else {
            charWarning.classList.add('hidden'); // Hide warning message
        }
    }

    // Update character count when the message input changes
    messageInput.addEventListener('input', updateCharacterCount);

    // Handle the selection of a message template
    if (templateSelect) {
        templateSelect.addEventListener('change', function () {
            // Set the message input to the selected template's content
            const selectedTemplateContent = templateSelect.value;
            messageInput.value = selectedTemplateContent;

            // Update the character count based on the selected template
            updateCharacterCount();
        });
    }

    // Add validation for form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent form submission until validation passes

        // Clear previous error messages
        clearErrorMessages();

        let filtersValid = true;
        const broadcastType = document.getElementById('broadcast_type').value;
        const campus = document.getElementById('campus');
        const message = document.getElementById('message');

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
            const college = document.getElementById('college');
            const program = document.getElementById('program');
            const year = document.getElementById('year');

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
            const office = document.getElementById('office');
            const status = document.getElementById('status');
            const type = document.getElementById('type');

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

        // Ensure message length doesn't exceed maxCharLimit
        if (message.value.length > maxCharLimit) {
            showError(message, 'Your message exceeds 160 characters.');
            filtersValid = false;
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
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(function(element) {
            element.remove();
        });

        const fields = document.querySelectorAll('.error');
        fields.forEach(function(field) {
            field.classList.remove('error');
        });
    }

    function showError(input, message) {
        input.classList.add('error');  // Highlight the input field with an error

        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message text-red-600 text-sm mt-1';
        errorMessage.innerText = message;

        input.parentNode.appendChild(errorMessage);
    }

    function removeError(input) {
        input.classList.remove('error');  // Remove the error class from the input field
        const errorElement = input.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();  // Remove the error message
        }
    }
});
