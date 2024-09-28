document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('messagesChart').getContext('2d');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const campusSelect = document.getElementById('campus-filter');
    const applyFiltersButton = document.getElementById('apply-filters');

    // Initialize Chart.js
    let messagesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [], // Dates will be added dynamically
            datasets: [
                {
                    label: 'Successful Messages',
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    data: [] // Success counts will be added dynamically
                },
                {
                    label: 'Failed Messages',
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    data: [] // Failed counts will be added dynamically
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Function to fetch and update the chart data
    function fetchChartData() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const campus = campusSelect.value;

        // Make an AJAX request to get chart data
        fetch(`/api/analytics/messages?start_date=${startDate}&end_date=${endDate}&campus=${campus}`)
            .then(response => response.json())
            .then(data => {
                // Update chart data
                messagesChart.data.labels = data.labels;
                messagesChart.data.datasets[0].data = data.success; // Success counts
                messagesChart.data.datasets[1].data = data.failed; // Failed counts

                // Refresh chart
                messagesChart.update();
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Initial fetch when page loads
    fetchChartData();

    // Apply filter on button click
    applyFiltersButton.addEventListener('click', fetchChartData);
});
