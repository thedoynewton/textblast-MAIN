// resources/js/analytics.js

document.addEventListener('DOMContentLoaded', function () {
    const dateRangeSelect = document.getElementById('date-range');

    function updateAnalytics() {
        const dateRange = dateRangeSelect.value;

        fetch(`/api/analytics?date_range=${dateRange}`)
            .then(response => response.json())
            .then(data => {
                // Update the numbers
                document.getElementById('total-sent').textContent = data.total_sent;
                document.getElementById('total-failed').textContent = data.total_failed;
                document.getElementById('total-scheduled').textContent = data.total_scheduled;
                document.getElementById('total-immediate').textContent = data.total_immediate;
                document.getElementById('remaining-balance').textContent = data.balance;

                // Update the chart
                updateChart(data.chart_data);
            })
            .catch(error => console.error('Error fetching analytics data:', error));
    }

    const ctx = document.getElementById('messagesChart').getContext('2d');
    let messagesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Messages Sent',
                data: [],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'category',
                    time: {
                        unit: 'day'
                    },
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateChart(chartData) {
        messagesChart.data.labels = chartData.labels;
        messagesChart.data.datasets[0].data = chartData.data;
        messagesChart.update();
    }

    // Fetch data on load
    updateAnalytics();

    // Fetch data on date range change
    dateRangeSelect.addEventListener('change', updateAnalytics);
});
