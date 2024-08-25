@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">Analytics</h1>

        <!-- Date Range Filter -->
        <div class="mb-4">
            <label for="date-range" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
            <select id="date-range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="last_3_months">Last 3 Months</option>
            </select>
        </div>

        <!-- Number of Messages and Balance -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Total Messages Sent</h2>
                <p class="text-2xl font-semibold" id="total-sent">0</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Failed Messages</h2>
                <p class="text-2xl font-semibold" id="total-failed">0</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Scheduled Messages</h2>
                <p class="text-2xl font-semibold" id="total-scheduled">0</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Immediate Messages</h2>
                <p class="text-2xl font-semibold" id="total-immediate">0</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h2 class="text-xl font-bold">Remaining Balance</h2>
                <p class="text-2xl font-semibold" id="remaining-balance">{{ $balance }}</p>
            </div>
            
        </div>

        <!-- Chart View -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Messages Sent Over Time</h2>
            <canvas id="messagesChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
</script>

@endsection
