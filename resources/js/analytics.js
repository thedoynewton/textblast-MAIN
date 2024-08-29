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
                document.getElementById('total-cancelled').textContent = data.total_cancelled; // Update cancelled messages
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

    // Toggle the dropdown visibility
    const exportButton = document.getElementById('exportChartButton');
    const dropdown = document.getElementById('exportDropdown');

    exportButton.addEventListener('click', function (event) {
        dropdown.classList.toggle('hidden');
        event.stopPropagation(); // Prevent click event from propagating to document
    });

    // Close the dropdown if clicked outside
    document.addEventListener('click', function (event) {
        if (!dropdown.classList.contains('hidden') && !dropdown.contains(event.target) && !exportButton.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Export as PNG
    document.getElementById('exportPNG').addEventListener('click', function () {
        const canvas = document.getElementById('messagesChart');
        const ctx = canvas.getContext('2d');
        
        // Save the current canvas state
        ctx.save();
        
        // Set the background color to white
        ctx.globalCompositeOperation = 'destination-over';
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    
        // Export the image
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = 'messages_chart.png';
        link.click();
        
        // Restore the original state
        ctx.restore();
    });

    // Export as Excel (using a library like SheetJS)
    document.getElementById('exportExcel').addEventListener('click', function () {
        const workbook = XLSX.utils.book_new();
        const worksheetData = [
            ['Date', 'Messages Sent'],
            ...messagesChart.data.labels.map((label, index) => [label, messagesChart.data.datasets[0].data[index]])
        ];
        const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Messages Sent');
        XLSX.writeFile(workbook, 'messages_chart.xlsx');
    });

});
