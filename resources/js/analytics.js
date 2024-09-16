document.addEventListener('DOMContentLoaded', function () {
    const dateRangeSelect = document.getElementById('date-range');
    const recipientTypeSelect = document.getElementById('recipient-type');
    const studentFilters = document.getElementById('student-filters');
    const employeeFilters = document.getElementById('employee-filters');
    const campusSelect = document.getElementById('campus');
    const collegeSelect = document.getElementById('college');
    const programSelect = document.getElementById('program');
    const yearSelect = document.getElementById('year');
    const officeSelect = document.getElementById('office');
    const statusSelect = document.getElementById('status');
    const typeSelect = document.getElementById('type');

    // Function to update analytics data
    function updateAnalytics() {
        const dateRange = dateRangeSelect.value;
        const recipientType = recipientTypeSelect ? recipientTypeSelect.value : null;

        fetch(`/api/analytics?date_range=${dateRange}&recipient_type=${recipientType}`)
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
        type: 'bar',
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

    // Handle recipient type change and toggle filters
    if (recipientTypeSelect) {
        recipientTypeSelect.addEventListener('change', function () {
            const recipientType = recipientTypeSelect.value;

            if (recipientType === 'student') {
                studentFilters.classList.remove('hidden');
                employeeFilters.classList.add('hidden');
            } else if (recipientType === 'employee') {
                studentFilters.classList.add('hidden');
                employeeFilters.classList.remove('hidden');
            } else if (recipientType === 'both') {
                studentFilters.classList.remove('hidden');
                employeeFilters.classList.remove('hidden');
            }

            // Fetch new analytics data on recipient change
            updateAnalytics();
        });
    }

    // Handle campus change and populate college dropdown
    if (campusSelect) {
        campusSelect.addEventListener('change', function () {
            const campusId = this.value;
            if (campusId) {
                fetch(`/analytics/colleges?campus_id=${campusId}`)
                    .then(response => response.json())
                    .then(data => {
                        collegeSelect.innerHTML = '<option value="" disabled selected>Select College</option>';
                        data.forEach(college => {
                            collegeSelect.innerHTML += `<option value="${college.college_id}">${college.college_name}</option>`;
                        });
                        collegeSelect.disabled = false;
                    })
                    .catch(error => console.error('Error fetching colleges:', error));
            }
        });
    }

    // Handle college change and populate program dropdown
    if (collegeSelect) {
        collegeSelect.addEventListener('change', function () {
            const collegeId = this.value;
            if (collegeId) {
                fetch(`/analytics/programs?college_id=${collegeId}`)
                    .then(response => response.json())
                    .then(data => {
                        programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
                        data.forEach(program => {
                            programSelect.innerHTML += `<option value="${program.program_id}">${program.program_name}</option>`;
                        });
                        programSelect.disabled = false;
                    })
                    .catch(error => console.error('Error fetching programs:', error));
            }
        });
    }

    // Fetch year data on page load
    if (yearSelect) {
        fetch('/analytics/years')
            .then(response => response.json())
            .then(data => {
                yearSelect.innerHTML = '<option value="" disabled selected>Select Year</option>';
                data.forEach(year => {
                    yearSelect.innerHTML += `<option value="${year.year_id}">${year.year_name}</option>`;
                });
            })
            .catch(error => console.error('Error fetching years:', error));
    }

    // Fetch data on load and for date range changes
    updateAnalytics();
    dateRangeSelect.addEventListener('change', updateAnalytics);

    // Toggle the export dropdown visibility
    const exportButton = document.getElementById('exportChartButton');
    const dropdown = document.getElementById('exportDropdown');

    if (exportButton && dropdown) {
        exportButton.addEventListener('click', function (event) {
            dropdown.classList.toggle('hidden');
            event.stopPropagation();
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', function (event) {
            if (!dropdown.classList.contains('hidden') && !dropdown.contains(event.target) && !exportButton.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // Export chart as PNG
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

    // Export chart as Excel (using a library like SheetJS)
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