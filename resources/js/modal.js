document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('messageContentModal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-message-content');
    const closeModalButton = document.getElementById('close-modal');

    // Handle "Read More" link click
    document.querySelectorAll('a[data-modal-target]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const title = this.getAttribute('data-template-name');
            const content = this.getAttribute('data-content');
            
            modalTitle.textContent = title; // Populate the title
            modalContent.textContent = content; // Populate the content
            modal.classList.remove('hidden'); // Show the modal
        });
    });

    // Handle "Close" button click
    closeModalButton.addEventListener('click', function () {
        modal.classList.add('hidden'); // Hide the modal
    });
});
