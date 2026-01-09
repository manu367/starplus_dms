<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Traffic Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-950 text-slate-200 h-screen flex overflow-hidden">

<?php include "./ui/asidebar.php"; ?>

<main class="flex-1 overflow-y-auto">

    <header class="bg-slate-900 border-b border-slate-800 px-8 py-4">
        <h2 class="text-lg font-semibold">Traffic Analytics</h2>
        <p class="text-xs text-slate-400">
            Deep traffic analysis & request behavior
        </p>
    </header>

    <section class="p-8 space-y-10">


        <!-- =========================
 GLOBAL FILTER BAR
========================= -->
        <div class="sticky top-0 z-20 bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">

                <input type="date" class="bg-slate-800 border border-slate-700 rounded px-3 py-2">

                <select class="bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option>Hour</option>
                    <option>Minute</option>
                </select>

                <select class="bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option>ALL Methods</option>
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                </select>

                <select class="bg-slate-800 border border-slate-700 rounded px-3 py-2">
                    <option>Status Code</option>
                    <option>2xx</option>
                    <option>4xx</option>
                    <option>5xx</option>
                </select>

                <button class="bg-blue-600 hover:bg-blue-500 rounded px-4 py-2">
                    Apply
                </button>

            </div>
        </div>

        <!-- =========================
         TRAFFIC SUMMARY STRIP
        ========================= -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

            <div class="bg-slate-900 p-5 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-400">Total Requests</p>
                <p class="text-2xl font-bold">14,982</p>
            </div>

            <div class="bg-slate-900 p-5 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-400">Unique IPs</p>
                <p class="text-2xl font-bold">1,142</p>
            </div>

            <div class="bg-slate-900 p-5 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-400">Avg Response</p>
                <p class="text-2xl font-bold">240ms</p>
            </div>

            <div class="bg-slate-900 p-5 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-400">Peak Minute</p>
                <p class="text-2xl font-bold">14:32</p>
            </div>

            <div class="bg-slate-900 p-5 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-400">Spike Detected</p>
                <p class="text-2xl font-bold text-red-400">YES</p>
            </div>

        </div>

        <!-- =========================
 TRAFFIC SPIKES
========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="font-semibold mb-4 text-red-400">🚨 Traffic Spikes Detected</h3>

            <table class="w-full text-sm">
                <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th class="text-left py-2">Time</th>
                    <th class="text-left py-2">Endpoint</th>
                    <th class="text-left py-2">Severity</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                <tr>
                    <td>14:32</td>
                    <td>/upload_ajax</td>
                    <td class="text-red-400">HIGH</td>
                </tr>
                <tr>
                    <td>11:12</td>
                    <td>/login</td>
                    <td class="text-orange-400">MEDIUM</td>
                </tr>
                </tbody>
            </table>
        </div>


        <!-- =========================
          REQUESTS PER HOUR
        ========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="font-semibold mb-4">Requests Per Hour</h3>
            <canvas id="hourlyTraffic"></canvas>
        </div>

        <!-- =========================
          ENDPOINT WISE TRAFFIC
        ========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="font-semibold mb-4">Traffic by Endpoint</h3>

            <table class="w-full text-sm">
                <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th class="text-left py-2">Endpoint</th>
                    <th class="text-left py-2">Requests</th>
                    <th class="text-left py-2">Avg Time</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                <tr>
                    <td>/login</td>
                    <td>3,420</td>
                    <td>120ms</td>
                </tr>
                <tr>
                    <td>/upload_ajax</td>
                    <td>2,890</td>
                    <td class="text-red-400">680ms</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- =========================
 ENDPOINT PERFORMANCE
========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="font-semibold mb-4">Endpoint Performance</h3>

            <table class="w-full text-sm">
                <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th>Endpoint</th>
                    <th>Requests</th>
                    <th>Avg Time</th>
                    <th>P95</th>
                    <th>Error %</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                <tr class="bg-red-500/10">
                    <td>/upload_ajax</td>
                    <td>2,890</td>
                    <td class="text-red-400">680ms</td>
                    <td>1.2s</td>
                    <td class="text-red-400">4.2%</td>
                </tr>
                <tr>
                    <td>/login</td>
                    <td>3,420</td>
                    <td>120ms</td>
                    <td>220ms</td>
                    <td>0.3%</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- =========================
 METHOD + STATUS
========================= -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="font-semibold mb-4">HTTP Methods</h3>
                <canvas id="methodChart"></canvas>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="font-semibold mb-4">Status Codes</h3>
                <canvas id="statusChart"></canvas>
            </div>

        </div>

        <!-- =========================
 TOP CLIENTS
========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="font-semibold mb-4">Top Clients</h3>

            <table class="w-full text-sm">
                <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th>IP</th>
                    <th>Requests</th>
                    <th>User Agent</th>
                    <th>Flag</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                <tr>
                    <td>192.168.1.10</td>
                    <td>4,120</td>
                    <td>Chrome</td>
                    <td class="text-red-400">🚩</td>
                </tr>
                <tr>
                    <td>10.0.0.21</td>
                    <td>980</td>
                    <td>Firefox</td>
                    <td class="text-slate-400">—</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- =========================
 SLOW REQUESTS
========================= -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Slow Requests</h3>
                <select class="bg-slate-800 border border-slate-700 rounded px-3 py-1 text-sm">
                    <option>> 500ms</option>
                    <option>> 1s</option>
                </select>
            </div>

            <table class="w-full text-sm">
                <thead class="text-slate-400 border-b border-slate-700">
                <tr>
                    <th>Endpoint</th>
                    <th>Time</th>
                    <th>Latency</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                <tr>
                    <td>/upload_ajax</td>
                    <td>14:32</td>
                    <td class="text-red-400">1.2s</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- =========================
 EXPORT & REALTIME
========================= -->
        <div class="flex justify-between items-center bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="space-x-3">
                <button class="bg-slate-800 px-4 py-2 rounded">Export CSV</button>
                <button class="bg-slate-800 px-4 py-2 rounded">Export JSON</button>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400">Real-time</span>
                <input type="checkbox" class="accent-blue-500">
            </div>
        </div>


    </section>
</main>

<script>
    new Chart(document.getElementById('hourlyTraffic'), {
        type: 'line',
        data: {
            labels: ['00','02','04','06','08','10','12','14','16','18','20','22'],
            datasets: [{
                label: 'Requests',
                data: [120,200,400,900,1500,2200,2800,3000,2600,1900,1200,500],
                borderColor: '#38bdf8',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(56,189,248,0.15)'
            }]
        }
    });
</script>

</body>
</html>
