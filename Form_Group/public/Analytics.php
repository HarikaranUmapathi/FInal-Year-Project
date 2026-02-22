<?php
// Remove this if you have a header file that starts session
session_start();

// Authorization Check
if (!isset($_SESSION['User'])) {
    header("Location: ../Login.php");
    exit;
}

// Basic Role Check
$role = $_SESSION['Role'] ?? '';
$allowed_roles = ['Faculty', 'Admin', 'Principal'];
if (!in_array($role, $allowed_roles)) {
    echo "Access Denied";
    exit;
}

// Default filter values from session (if any) or defaults
$dept = $_SESSION['dept'] ?? ''; // Faculty sees their dept default?
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* CustomScrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600 tracking-tight">📊 Analytics Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-medium hidden md:block">Welcome, <?= htmlspecialchars($_SESSION['User']) ?> (<?= htmlspecialchars($role) ?>)</span>
                <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6 space-y-8">

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                
                <!-- Feedback Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Form</label>
                    <select id="filter_form" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="facilities">Facilities Feedback</option>
                        <option value="alumni">Alumni Feedback</option>
                        <option value="course_end">Course End Survey</option>
                        <option value="guest">Guest Lecture Feedback</option>
                        <option value="parent">Parent Feedback</option>
                        <option value="exit">Student Exit Survey</option>
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="filter_dept" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" <?= $role === 'Faculty' ? 'disabled' : '' ?>>
                        <option value="">All Departments</option>
                        <option value="CSE" <?= ($dept == 'CSE' || ($role === 'Faculty' && $dept == 'CSE')) ? 'selected' : '' ?>>CSE</option>
                        <option value="IT" <?= ($dept == 'IT' || ($role === 'Faculty' && $dept == 'IT')) ? 'selected' : '' ?>>IT</option>
                        <option value="ECE" <?= ($dept == 'ECE' || ($role === 'Faculty' && $dept == 'ECE')) ? 'selected' : '' ?>>ECE</option>
                        <option value="EEE" <?= ($dept == 'EEE' || ($role === 'Faculty' && $dept == 'EEE')) ? 'selected' : '' ?>>EEE</option>
                        <option value="MECH" <?= ($dept == 'MECH' || ($role === 'Faculty' && $dept == 'MECH')) ? 'selected' : '' ?>>MECH</option>
                        <option value="CIVIL" <?= ($dept == 'CIVIL' || ($role === 'Faculty' && $dept == 'CIVIL')) ? 'selected' : '' ?>>CIVIL</option>
                        <!-- Add dynamic options via PHP if possible -->
                    </select>
                </div>

                <!-- Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year / Batch</label>
                    <input type="text" id="filter_year" placeholder="e.g. 2024 or 3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>

                <!-- Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select id="filter_section" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        <option value="">All Sections</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" id="filter_start_date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" id="filter_end_date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                </div>

            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="fetchData()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition shadow-md flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Charts Section -->
        <div id="charts_container" class="space-y-6">
            <!-- Charts will be dynamically added here -->
        </div>

        <!-- Summary and Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Summary Card -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Submission Stats</h3>
                <div class="flex items-center justify-between mt-4">
                    <span class="text-gray-500">Total Responses</span>
                    <span class="text-2xl font-bold text-gray-900" id="total_responses">-</span>
                </div>
                 <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 70%"></div>
                </div>
            </div>
            
             <!-- Export Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                <button class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg mb-2 shadow-sm transition">
                    Export CSV
                </button>
                <button class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition">
                    Print Report
                </button>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Individual Submissions</h3>
                <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Showing last 100</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th class="p-4 border-b">Student Name</th>
                            <th class="p-4 border-b">Department</th>
                            <th class="p-4 border-b">Year</th>
                            <th class="p-4 border-b">Section</th>
                            <th class="p-4 border-b">Feedback Type</th>
                            <th class="p-4 border-b">Submission Date</th>
                            <th class="p-4 border-b text-center">Overall Score (%)</th>
                        </tr>
                    </thead>
                    <tbody id="table_body" class="text-sm text-gray-700 divide-y divide-gray-100">
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const userRole = '<?= $role ?>';
        let chartInstances = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchData();
        });

        async function fetchData() {
            const form = document.getElementById('filter_form').value;
            const dept = document.getElementById('filter_dept').value;
            const year = document.getElementById('filter_year').value;
            const section = document.getElementById('filter_section').value;
            const start_date = document.getElementById('filter_start_date').value;
            const end_date = document.getElementById('filter_end_date').value;

            // Clear existing charts
            const container = document.getElementById('charts_container');
            container.innerHTML = '';
            chartInstances.forEach(instance => instance.destroy());
            chartInstances = [];

            const baseParams = {
                form: form,
                dept: dept,
                start_date: start_date,
                end_date: end_date
            };

            try {
                if (userRole === 'Faculty') {
                    // For Faculty, show charts for each year and section
                    const years = ['1', '2', '3', '4'];
                    const sections = ['A', 'B'];

                    for (const y of years) {
                        for (const s of sections) {
                            const params = new URLSearchParams({
                                ...baseParams,
                                year: y,
                                section: s,
                                type: 'aggregated'
                            });

                            const res = await fetch(`AnalyticsAPI.php?${params.toString()}`);
                            const data = await res.json();

                            if (!data.error) {
                                renderChart(data.labels, data.data, `${dept} ${y} Year ${s} Section`);
                            }
                        }
                    }
                } else {
                    // For Admin/Principal, show single chart
                    const params = new URLSearchParams({
                        ...baseParams,
                        year: year,
                        section: section,
                        type: 'aggregated'
                    });

                    const aggRes = await fetch(`AnalyticsAPI.php?${params.toString()}`);
                    const aggData = await aggRes.json();
                    
                    if (aggData.error) {
                        alert(aggData.error);
                    } else {
                        renderChart(aggData.labels, aggData.data, 'Average Ratings per Question');
                    }
                }

                // Fetch Individual Data (same for all)
                const indParams = new URLSearchParams({
                    ...baseParams,
                    year: year,
                    section: section,
                    type: 'individual'
                });

                const indRes = await fetch(`AnalyticsAPI.php?${indParams.toString()}`);
                const indData = await indRes.json();
                
                if (indData.submissions) {
                    renderTable(indData.submissions);
                    document.getElementById('total_responses').innerText = indData.submissions.length;
                }

            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function renderChart(labels, data, title) {
            const container = document.getElementById('charts_container');

            const chartDiv = document.createElement('div');
            chartDiv.className = 'bg-white rounded-xl shadow-sm border border-gray-100 p-6';
            chartDiv.innerHTML = `
                <h3 class="text-lg font-semibold text-gray-800 mb-4">${title}</h3>
                <div class="relative h-80 w-full">
                    <canvas></canvas>
                </div>
            `;

            container.appendChild(chartDiv);

            const canvas = chartDiv.querySelector('canvas');
            const ctx = canvas.getContext('2d');

            const chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Rating (1-5)',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        hoverBackgroundColor: 'rgba(37, 99, 235, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            bodyFont: {
                                size: 13
                            }
                        }
                    }
                }
            });

            chartInstances.push(chartInstance);
        }

        function renderTable(rows) {
            const tbody = document.getElementById('table_body');
            tbody.innerHTML = '';

            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-4 text-center text-gray-500">No records found</td></tr>';
                return;
            }

            rows.forEach(row => {
                const scoreClass = row.score >= 80 ? 'text-green-600 font-bold' : (row.score >= 60 ? 'text-yellow-600 font-bold' : 'text-red-600 font-bold');

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition border-b border-gray-50 last:border-b-0';
                tr.innerHTML = `
                    <td class="p-4 text-gray-700 font-medium">${row.name || '-'}</td>
                    <td class="p-4 text-gray-600">${row.department || '-'}</td>
                    <td class="p-4 text-gray-600">${row.year || '-'}</td>
                    <td class="p-4 text-gray-600">${row.section || '-'}</td>
                    <td class="p-4 text-gray-600">${row.type || '-'}</td>
                    <td class="p-4 text-gray-600 font-mono text-xs">${row.date || '-'}</td>
                    <td class="p-4 text-center ${scoreClass}">${row.score || 0}%</td>
                `;
                tbody.appendChild(tr);
            });
        }
    </script>
</body>
</html>
