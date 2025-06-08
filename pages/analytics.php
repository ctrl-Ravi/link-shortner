<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

$userId = getCurrentUserId();

// Get date range from query parameters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Create datetime strings for queries
$startDateTime = $startDate . ' 00:00:00';
$endDateTime = $endDate . ' 23:59:59';

// Get analytics data
$clickStats = getClickAnalytics($userId, $startDateTime, $endDateTime);

// Prepare data for charts
$dates = [];
$clicks = [];
$uniqueClicks = [];
$topLinks = [];

foreach ($clickStats as $stat) {
    if (!isset($topLinks[$stat['slug']])) {
        $topLinks[$stat['slug']] = 0;
    }
    $topLinks[$stat['slug']] += $stat['clicks'];
    
    $dates[] = date('M j', strtotime($stat['date']));
    $clicks[] = $stat['clicks'];
    $uniqueClicks[] = $stat['unique_clicks'];
}

// Sort top links by clicks
arsort($topLinks);
$topLinks = array_slice($topLinks, 0, 5, true);

// Get country statistics
$stmt = $conn->prepare("
    SELECT 
        ca.country_code,
        COUNT(*) as clicks,
        COUNT(DISTINCT ca.ip_address) as unique_clicks
    FROM click_analytics ca
    JOIN links l ON ca.link_id = l.id
    WHERE l.user_id = ? AND ca.clicked_at BETWEEN ? AND ?
    GROUP BY ca.country_code
    ORDER BY clicks DESC
    LIMIT 10
");

// Bind parameters using variables
$stmt->bind_param("iss", $userId, $startDateTime, $endDateTime);
$stmt->execute();
$countryStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-2">
            Analytics Dashboard
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Track your link performance and audience insights
        </p>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-8" data-aos="fade-up">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow">
            <form method="get" action="admin.php" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="page" value="analytics">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                           id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                           id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Update Date Range
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Click Statistics Chart -->
        <div class="lg:col-span-2" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Click Statistics</h2>
                </div>
                <div class="p-6">
                    <div class="h-80">
                        <canvas id="clicksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-1 space-y-6">
            <!-- Top Links -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Top Links</h2>
                </div>
                <div class="p-4">
                    <?php if (empty($topLinks)): ?>
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No data available
                    </div>
                    <?php else: ?>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($topLinks as $slug => $totalClicks): ?>
                        <div class="py-3 flex justify-between items-center">
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[70%]">
                                <button onclick="copyToClipboard('<?= getFullUrl($slug) ?>')" 
                                       class="hover:text-primary-600 dark:hover:text-primary-400 flex items-center">
                                    <?= htmlspecialchars($slug) ?>
                                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200">
                                <?= number_format($totalClicks) ?> clicks
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Top Countries -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Top Countries</h2>
                </div>
                <div class="p-4">
                    <?php if (empty($countryStats)): ?>
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No data available
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-2">Country</th>
                                    <th class="px-4 py-2">Clicks</th>
                                    <th class="px-4 py-2">Unique</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($countryStats as $stat): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if ($stat['country_code']): ?>
                                            <div class="flex items-center">
                                                <img src="https://flagcdn.com/16x12/<?= strtolower($stat['country_code']) ?>.png"
                                                     alt="<?= $stat['country_code'] ?>"
                                                     class="mr-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?= $stat['country_code'] ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <?= number_format($stat['clicks']) ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <?= number_format($stat['unique_clicks']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show a toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-black/75 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = 'Link copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>

<?php
// Prepare chart data
$chartLabels = empty($dates) ? '' : "'" . implode("', '", $dates) . "'";
$chartClicks = empty($clicks) ? '' : implode(', ', $clicks);
$chartUniqueClicks = empty($uniqueClicks) ? '' : implode(', ', $uniqueClicks);

$pageScripts = <<<EOT
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#e5e7eb' : '#374151';
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
    
    var ctx = document.getElementById('clicksChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [{$chartLabels}],
            datasets: [{
                label: 'Total Clicks',
                data: [{$chartClicks}],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Unique Clicks',
                data: [{$chartUniqueClicks}],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: textColor
                    },
                    grid: {
                        color: gridColor
                    }
                },
                x: {
                    ticks: {
                        color: textColor
                    },
                    grid: {
                        color: gridColor
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: textColor
                    }
                }
            }
        }
    });
    
    // Update chart colors when theme changes
    document.addEventListener('themeChanged', function(e) {
        const isDark = e.detail.isDark;
        const chart = Chart.getChart(ctx);
        
        if (chart) {
            chart.options.scales.y.ticks.color = isDark ? '#e5e7eb' : '#374151';
            chart.options.scales.x.ticks.color = isDark ? '#e5e7eb' : '#374151';
            chart.options.scales.y.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            chart.options.scales.x.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            chart.options.plugins.legend.labels.color = isDark ? '#e5e7eb' : '#374151';
            chart.update();
        }
    });
});
</script>
EOT;
?> 