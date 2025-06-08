<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

$userId = getCurrentUserId();

// Get current ad settings
$stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$adSettings = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ad_settings'])) {
    $settings = [
        'social_bar_script' => $_POST['social_bar_script'] ?? '',
        'popunder_script' => $_POST['popunder_script'] ?? '',
        'native_banner_script' => $_POST['native_banner_script'] ?? '',
        'banner_468x60_script' => $_POST['banner_468x60_script'] ?? '',
        'banner_300x250_script' => $_POST['banner_300x250_script'] ?? '',
        'banner_160x300_script' => $_POST['banner_160x300_script'] ?? '',
        'banner_160x600_script' => $_POST['banner_160x600_script'] ?? '',
        'banner_320x50_script' => $_POST['banner_320x50_script'] ?? '',
        'banner_728x90_script' => $_POST['banner_728x90_script'] ?? '',
        'direct_link' => $_POST['direct_link'] ?? '',
        'is_using_default' => isset($_POST['use_default_ads']) ? 1 : 0
    ];
    
    if (updateUserAdSettings($userId, $settings)) {
        $alertType = 'success';
        $alertMessage = 'Ad settings updated successfully!';
        
        // Refresh settings
        $stmt->execute();
        $adSettings = $stmt->get_result()->fetch_assoc();
    } else {
        $alertType = 'danger';
        $alertMessage = 'Error updating ad settings.';
    }
}
?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-2">
            Ad Settings
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Configure your Adsterra ad scripts for monetization
        </p>
    </div>

    <!-- Alert Message -->
    <?php if (isset($alertMessage)): ?>
    <div class="mb-6 rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>" role="alert">
        <?= htmlspecialchars($alertMessage) ?>
        <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Ad Settings Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up">
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Adsterra Ad Settings</h2>
        </div>
        <div class="p-6">
            <form method="post" action="admin.php?page=ad-settings">
                <div class="mb-6">
                    <div class="flex items-center">
                        <label class="inline-flex relative items-center cursor-pointer mr-3">
                            <input type="checkbox" id="use_default_ads" name="use_default_ads" 
                                   class="sr-only peer" <?= $adSettings['is_using_default'] ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        </label>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Use Default Ads</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enable to use system default ad settings</p>
                </div>

                <div id="custom_ad_settings" class="<?= $adSettings['is_using_default'] ? 'hidden' : '' ?> space-y-6">
                    <!-- Direct Link -->
                    <div>
                        <label for="direct_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Direct Link</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                               id="direct_link" name="direct_link" value="<?= htmlspecialchars($adSettings['direct_link'] ?? '') ?>"
                               placeholder="https://www.adsterra.com/directlink">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your Adsterra direct link URL</p>
                    </div>

                    <!-- Social Bar -->
                    <div>
                        <label for="social_bar_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Social Bar Script</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                id="social_bar_script" name="social_bar_script" rows="4" 
                                placeholder="<script>..."><?= htmlspecialchars($adSettings['social_bar_script'] ?? '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra Social Bar script here</p>
                    </div>

                    <!-- Popunder -->
                    <div>
                        <label for="popunder_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Popunder Script</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                id="popunder_script" name="popunder_script" rows="4" 
                                placeholder="<script>..."><?= htmlspecialchars($adSettings['popunder_script'] ?? '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra Popunder script here</p>
                    </div>

                    <!-- Native Banner -->
                    <div>
                        <label for="native_banner_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Native Banner Script</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                id="native_banner_script" name="native_banner_script" rows="4" 
                                placeholder="<script>..."><?= htmlspecialchars($adSettings['native_banner_script'] ?? '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra Native Banner script here</p>
                    </div>

                    <!-- Banner Ads -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Banner Ad Scripts</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="banner_468x60_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 468x60 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_468x60_script" name="banner_468x60_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_468x60_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 468x60 Banner script here</p>
                            </div>

                            <div>
                                <label for="banner_300x250_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 300x250 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_300x250_script" name="banner_300x250_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_300x250_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 300x250 Banner script here</p>
                            </div>

                            <div>
                                <label for="banner_160x300_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 160x300 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_160x300_script" name="banner_160x300_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_160x300_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 160x300 Banner script here</p>
                            </div>

                            <div>
                                <label for="banner_160x600_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 160x600 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_160x600_script" name="banner_160x600_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_160x600_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 160x600 Banner script here</p>
                            </div>

                            <div>
                                <label for="banner_320x50_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 320x50 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_320x50_script" name="banner_320x50_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_320x50_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 320x50 Banner script here</p>
                            </div>

                            <div>
                                <label for="banner_728x90_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 728x90 Script</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white font-mono text-sm" 
                                        id="banner_728x90_script" name="banner_728x90_script" rows="4" 
                                        placeholder="<script>..."><?= htmlspecialchars($adSettings['banner_728x90_script'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paste your Adsterra 728x90 Banner script here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit" name="update_ad_settings" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add direct script to ensure immediate toggle functionality -->
<script>
// Execute immediately without waiting for DOMContentLoaded
(function() {
    const useDefaultAds = document.getElementById('use_default_ads');
    const customAdSettings = document.getElementById('custom_ad_settings');
    
    if (useDefaultAds && customAdSettings) {
        // Initial check to ensure the UI matches the checkbox state
        customAdSettings.classList.toggle('hidden', useDefaultAds.checked);
        
        // Add event listener for changes
        useDefaultAds.addEventListener('change', function() {
            customAdSettings.classList.toggle('hidden', this.checked);
        });
    }
})();
</script>

<?php
$pageScripts = '';
?> 