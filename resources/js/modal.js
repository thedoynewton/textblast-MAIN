document.addEventListener('DOMContentLoaded', function () {
    // Variables for the "Read More" modal
    const readMoreModal = document.getElementById('messageContentModal');
    const readMoreModalTitle = document.getElementById('modal-title');
    const readMoreModalContent = document.getElementById('modal-message-content');
    const closeReadMoreModalButton = document.getElementById('close-modal');

    // Variables for the "Immediate Messages Sent," "Failed Messages," and "Scheduled Messages Sent" modals
    const recipientModal = document.getElementById('recipientModal');
    const closeRecipientModalButtons = document.querySelectorAll('#closeModal, #closeModalFooter');
    const recipientContent = document.getElementById('recipientContent');

    // The base URL is provided by the Blade view through a script tag.
    // Ensure this variable is declared in the Blade view.
    // Example: <script>const baseUrl = "/admin/recipients";</script>

    // ***************************
    // "Read More" Modal Functionality
    // ***************************

    // Check if the "Read More" modal elements exist
    if (readMoreModal && readMoreModalTitle && readMoreModalContent && closeReadMoreModalButton) {
        document.querySelectorAll('a[data-modal-target]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const title = this.getAttribute('data-template-name');
                const content = this.getAttribute('data-content');
                
                readMoreModalTitle.textContent = title;
                readMoreModalContent.textContent = content;
                readMoreModal.classList.remove('hidden');
            });
        });

        closeReadMoreModalButton.addEventListener('click', function () {
            readMoreModal.classList.add('hidden');
        });
    }

    // ***************************
    // "Immediate Messages Sent," "Failed Messages," and "Scheduled Messages Sent" Modal Functionality
    // ***************************

    // Check if the recipientContent exists before attempting to use it
    if (recipientContent && recipientModal) {
        
        // Common function to fetch and display recipient details for the given URL
        function fetchAndDisplayRecipients(url, isFailed = false) {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    recipientContent.innerHTML = ''; // Clear existing content
                    if (data.length > 0) {
                        data.forEach(recipient => {
                            const recipientElement = document.createElement('div');
                            recipientElement.classList.add('border-b', 'py-2');
                            recipientElement.innerHTML = `
                                <p><strong>Name:</strong> ${recipient.first_name} ${recipient.last_name}</p>
                                <p><strong>Email:</strong> ${recipient.email}</p>
                                <p><strong>Contact Number:</strong> ${recipient.contact_number}</p>
                                ${isFailed && recipient.failure_reason ? `<p><strong>Failure Reason:</strong> ${recipient.failure_reason}</p>` : ''}
                            `;
                            recipientContent.appendChild(recipientElement);
                        });
                    } else {
                        recipientContent.innerHTML = '<p class="text-gray-500">No recipients found.</p>';
                    }
                    recipientModal.classList.remove('hidden'); // Show the modal
                })
                .catch(error => {
                    console.error('Error fetching recipient details:', error);
                });
        }

        // Fetch and display recipient details when the "Immediate Messages Sent" card is clicked
        const immediateMessagesSentCard = document.getElementById('immediateMessagesSentCard');
        if (immediateMessagesSentCard) {
            immediateMessagesSentCard.addEventListener('click', function () {
                console.log('Immediate Messages Sent card clicked'); 
                fetchAndDisplayRecipients(`${baseUrl}/immediate`);
            });
        }

        // Fetch and display recipient details when the "Failed Messages" card is clicked
        const failedMessagesCard = document.getElementById('failedMessagesCard');
        if (failedMessagesCard) {
            failedMessagesCard.addEventListener('click', function () {
                console.log('Failed Messages card clicked');
                fetchAndDisplayRecipients(`${baseUrl}/failed`, true); 
            });
        }

        // Fetch and display recipient details when the "Scheduled Messages Sent" card is clicked
        const scheduledMessagesSentCard = document.getElementById('scheduledMessagesSentCard');
        if (scheduledMessagesSentCard) {
            scheduledMessagesSentCard.addEventListener('click', function () {
                console.log('Scheduled Messages Sent card clicked');
                fetchAndDisplayRecipients(`${baseUrl}/scheduled`);
            });
        }

        // Close the modal when any of the close buttons are clicked for the recipient modal
        closeRecipientModalButtons.forEach(button => {
            button.addEventListener('click', function () {
                recipientModal.classList.add('hidden'); 
            });
        });
    }
});
