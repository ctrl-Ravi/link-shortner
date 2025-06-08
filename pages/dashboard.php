<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

$userId = getCurrentUserId();

// Get statistics
$totalLinks = 0;
$totalClicks = 0;
$activeLinks = 0;
$recentLinks = [];
$clickStats = [];

// Get total links
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM links WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$totalLinks = $stmt->get_result()->fetch_assoc()['total'];

// Get total clicks
$stmt = $conn->prepare("SELECT SUM(clicks) as total FROM links WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$totalClicks = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Get active links
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM links WHERE user_id = ? AND (expires_at IS NULL OR expires_at > NOW())");
$stmt->bind_param("i", $userId);
$stmt->execute();
$activeLinks = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Get recent links
$stmt = $conn->prepare("SELECT id, destination_url, slug, clicks, created_at FROM links WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $userId);
$stmt->execute();
$recentLinks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get click statistics for the last 7 days
$stmt = $conn->prepare("
    SELECT DATE(clicked_at) as date, COUNT(*) as clicks
    FROM click_analytics ca
    JOIN links l ON ca.link_id = l.id
    WHERE l.user_id = ? AND clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(clicked_at)
    ORDER BY date ASC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$clickStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Initialize arrays for the last 7 days
$dates = [];
$clicks = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('M j', strtotime($date));
    $clicks[] = 0; // Initialize with 0 clicks
}

// Fill in actual click data where available
foreach ($clickStats as $stat) {
    $daysAgo = (strtotime('today') - strtotime($stat['date'])) / (60 * 60 * 24);
    if ($daysAgo >= 0 && $daysAgo < 7) {
        $index = 6 - $daysAgo;
        $clicks[(int)$index] = (int)$stat['clicks'];
    }
}

// Get today's clicks
$todayClicks = 0;
if (!empty($clickStats)) {
    $lastStat = end($clickStats);
    if (date('Y-m-d') === date('Y-m-d', strtotime($lastStat['date']))) {
        $todayClicks = $lastStat['clicks'];
    }
}

// Prepare chart data
$chartLabels = empty($dates) ? '' : "'" . implode("', '", $dates) . "'";
$chartData = empty($clicks) ? '' : implode(', ', $clicks);

// Get username from session
$username = $_SESSION['username'] ?? 'User';
?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Welcome Section -->
    <div class="mb-8" data-aos="fade-up">
        <h1 class="text-3xl font-bold text-gradient mb-2">
            Welcome back, <?= htmlspecialchars($username) ?>!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Here's an overview of your monetization performance
        </p>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Links -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-primary-100 dark:bg-primary-900">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Links</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= number_format($totalLinks) ?></p>
                </div>
            </div>
        </div>

        <!-- Total Clicks -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clicks</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= number_format($totalClicks) ?></p>
                </div>
            </div>
        </div>

        <!-- Today's Clicks -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Clicks</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= number_format($todayClicks) ?></p>
                </div>
            </div>
        </div>

        <!-- Active Links -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow" data-aos="fade-up" data-aos-delay="400">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Links</h2>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?= number_format($activeLinks) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8" data-aos="fade-up">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="admin.php?page=links&action=new" class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center card-shadow hover:scale-105 transition-transform">
                <div class="p-2 rounded-lg bg-gradient text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-900 dark:text-white">Create New Link</span>
            </a>
            
            <a href="admin.php?page=analytics" class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center card-shadow hover:scale-105 transition-transform">
                <div class="p-2 rounded-lg bg-gradient text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-900 dark:text-white">View Analytics</span>
            </a>

            <a href="admin.php?page=ad-settings" class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center card-shadow hover:scale-105 transition-transform">
                <div class="p-2 rounded-lg bg-gradient text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-900 dark:text-white">Configure Ad Settings</span>
            </a>

            <a href="admin.php?page=links" class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center card-shadow hover:scale-105 transition-transform">
                <div class="p-2 rounded-lg bg-gradient text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-900 dark:text-white">View All Links</span>
            </a>
            
            <?php if (isAdmin()): ?>
            <a href="admin.php?page=users" class="bg-white dark:bg-gray-800 rounded-xl p-4 flex items-center card-shadow hover:scale-105 transition-transform">
                <div class="p-2 rounded-lg bg-gradient text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="ml-3 font-medium text-gray-900 dark:text-white">Manage Users</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Click Statistics Chart -->
    <div class="mb-8" data-aos="fade-up">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Click Statistics - Last 7 Days</h2>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 card-shadow">
            <div class="h-64">
                <canvas id="clicksChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Links -->
    <div data-aos="fade-up">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Links</h2>
        <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destination URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Short URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clicks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($recentLinks)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No links found. Create your first link to get started!
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentLinks as $link): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                    <div class="max-w-xs truncate" title="<?= htmlspecialchars($link['destination_url'] ?? '') ?>">
                                        <?= htmlspecialchars($link['destination_url'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="copyToClipboard('<?= getBaseUrl() . '/' . ($link['slug'] ?? '') ?>')" 
                                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 flex items-center">
                                        <?= $link['slug'] ?? '' ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                    <?= number_format($link['clicks'] ?? 0) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                    <?= date('M j, Y', strtotime($link['created_at'] ?? 'now')) ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-3">
                                        <a href="admin.php?page=links&action=edit&id=<?= $link['id'] ?? '' ?>" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button onclick="deleteLink(<?= $link['id'] ?? 0 ?>)" 
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show a toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-black/75 text-white px-4 py-2 rounded-lg shadow-lg';
        toast.textContent = 'Copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}

function deleteLink(id) {
    if (confirm('Are you sure you want to delete this link?')) {
        window.location.href = `admin.php?page=links&action=delete&id=${id}`;
    }
}
</script>

<?php
// Add chart initialization script
$pageScripts = <<<EOT
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('clicksChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [{$chartLabels}],
            datasets: [{
                label: 'Clicks',
                data: [{$chartData}],
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
EOT;
?> 