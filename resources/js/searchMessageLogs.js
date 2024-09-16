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
            let matchFound = false;
            let recipientTypeMatch = true;
            let messageTypeMatch = true;

            // Check if the row matches the selected recipient type
            const recipientTypeCell = row.querySelector('td:nth-child(2)'); // Adjust the index if necessary
            if (recipientType !== 'all' && recipientTypeCell.textContent.toLowerCase() !== recipientType) {
                recipientTypeMatch = false;
            }

            // Check if the row matches the selected message type in both Message Type and Status columns
            const messageTypeCell = row.querySelector('td:nth-child(4)'); // Assuming Message Type is in the 4th column
            const statusCell = row.querySelector('td:nth-child(9)'); // Assuming Status is in the 9th column
            
            if (messageType !== 'all') {
                if (messageTypeCell.textContent.toLowerCase() !== messageType &&
                    statusCell.textContent.toLowerCase() !== messageType) {
                    messageTypeMatch = false;
                }
            }

            // Highlight search term if found
            cells.forEach(cell => {
                const cellText = cell.textContent.toLowerCase();
                if (cellText.includes(searchTerm)) {
                    matchFound = true;
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    cell.innerHTML = cell.textContent.replace(regex, '<span class="highlight">$1</span>');
                } else {
                    cell.innerHTML = cell.textContent; // Reset if no match
                }
            });

            // Show or hide the row based on matching criteria
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
