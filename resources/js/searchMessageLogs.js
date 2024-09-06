document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const recipientTypeFilter = document.getElementById('recipientType');
    const messageTypeFilter = document.getElementById('messageType');
    const tableRows = document.querySelectorAll('#messageLogsTable tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const recipientType = recipientTypeFilter.value;
        const messageType = messageTypeFilter.value;

        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let rowText = '';
            let matchFound = false;
            let recipientTypeMatch = true;
            let messageTypeMatch = true;

            // Check if the row matches the selected recipient type
            const recipientTypeCell = row.querySelector('td:nth-child(2)'); // Assuming the recipient type is in the second column
            if (recipientType !== 'all' && recipientTypeCell.textContent.toLowerCase() !== recipientType) {
                recipientTypeMatch = false;
            }

            // Check if the row matches the selected message type
            const messageTypeCell = row.querySelector('td:nth-child(4)'); // Assuming the message type is in the fourth column
            if (messageType !== 'all' && messageTypeCell.textContent.toLowerCase() !== messageType) {
                messageTypeMatch = false;
            }

            cells.forEach(cell => {
                const cellText = cell.textContent.toLowerCase();
                if (cellText.includes(searchTerm)) {
                    matchFound = true;
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    cell.innerHTML = cell.textContent.replace(regex, '<span class="highlight">$1</span>');
                } else {
                    cell.innerHTML = cell.textContent; // Reset if no match
                }
                rowText += cellText;
            });

            if (matchFound && recipientTypeMatch && messageTypeMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Attach event listeners to the search input, recipient type filter, and message type filter
    searchInput.addEventListener('keyup', filterTable);
    recipientTypeFilter.addEventListener('change', filterTable);
    messageTypeFilter.addEventListener('change', filterTable);
});