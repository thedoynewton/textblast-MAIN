document.addEventListener('DOMContentLoaded', function () {
    // Variables for the "Read More" modal
    const readMoreModal = document.getElementById('messageContentModal');
    const readMoreModalTitle = document.getElementById('modal-title');
    const readMoreModalContent = document.getElementById('modal-message-content');
    const closeReadMoreModalButton = document.getElementById('close-modal');

    // Variables for the "Immediate Messages Sent" modal
    const recipientModal = document.getElementById('recipientModal');
    const closeRecipientModalButtons = document.querySelectorAll('#closeModal, #closeModalFooter');
    const recipientContent = document.getElementById('recipientContent');

    // ***************************
    // "Read More" Modal Functionality
    // ***************************

    // Check if the "Read More" modal elements exist
    if (readMoreModal && readMoreModalTitle && readMoreModalContent && closeReadMoreModalButton) {
        // Handle "Read More" link click
        document.querySelectorAll('a[data-modal-target]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const title = this.getAttribute('data-template-name');
                const content = this.getAttribute('data-content');
                
                readMoreModalTitle.textContent = title; // Populate the title
                readMoreModalContent.textContent = content; // Populate the content
                readMoreModal.classList.remove('hidden'); // Show the modal
            });
        });

        // Handle "Close" button click for "Read More" modal
        closeReadMoreModalButton.addEventListener('click', function () {
            readMoreModal.classList.add('hidden'); // Hide the modal
        });
    }

    // ***************************
    // "Immediate Messages Sent" Modal Functionality
    // ***************************

    // Check if the recipientContent exists before attempting to use it
    if (recipientContent && recipientModal) {
        // Fetch and display recipient details when the Immediate Messages Sent card is clicked
        const immediateMessagesSentCard = document.getElementById('immediateMessagesSentCard');
        if (immediateMessagesSentCard) {
            immediateMessagesSentCard.addEventListener('click', function () {
                console.log('Immediate Messages Sent card clicked'); // Log message to confirm the card is clicked

                fetch('/admin/recipients/immediate')
                    .then(response => response.json())
                    .then(data => {
                        // Populate the modal with recipient details
                        recipientContent.innerHTML = ''; // Clear any existing content
                        if (data.length > 0) {
                            data.forEach(recipient => {
                                const recipientElement = document.createElement('div');
                                recipientElement.classList.add('border-b', 'py-2');
                                recipientElement.innerHTML = `
                                    <p><strong>Name:</strong> ${recipient.first_name} ${recipient.last_name}</p>
                                    <p><strong>Email:</strong> ${recipient.email}</p>
                                    <p><strong>Contact Number:</strong> ${recipient.contact_number}</p>
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
            });
        }

        // Close the modal when any of the close buttons are clicked for the "Immediate Messages Sent" modal
        closeRecipientModalButtons.forEach(button => {
            button.addEventListener('click', function () {
                recipientModal.classList.add('hidden'); // Hide the modal
            });
        });
    }
});
