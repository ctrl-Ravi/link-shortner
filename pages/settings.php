<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

if (!isAdmin()) {
    include 'unauthorized.php';
    exit;
}

// Get default ad settings
$stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = 1");
$stmt->execute();
$defaultAdSettings = $stmt->get_result()->fetch_assoc();

// Initialize all ad settings with empty strings if not set
$defaultAdSettings = array_merge([
    'social_bar_script' => '',
    'popunder_script' => '',
    'native_banner_script' => '',
    'banner_300x250_script' => '',
    'banner_728x90_script' => '',
    'banner_320x50_script' => '',
    'direct_link_1' => '',
    'direct_link_2' => '',
    'direct_link_3' => '',
    'direct_link_4' => '',
    'direct_link_5' => '',
    'is_using_default' => 0
], $defaultAdSettings ?? []);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_ad_settings'])) {
        $settings = [
            'social_bar_script' => $_POST['social_bar_script'] ?? '',
            'popunder_script' => $_POST['popunder_script'] ?? '',
            'native_banner_script' => $_POST['native_banner_script'] ?? '',
            'banner_300x250_script' => $_POST['banner_300x250_script'] ?? '',
            'banner_728x90_script' => $_POST['banner_728x90_script'] ?? '',
            'banner_320x50_script' => $_POST['banner_320x50_script'] ?? '',
            'is_using_default' => 0
        ];
        
        // Add direct links to settings
        for ($i = 1; $i <= 5; $i++) {
            $directLink = trim($_POST["direct_link_$i"] ?? '');
            $settings["direct_link_$i"] = $directLink;
        }
        
        if (updateUserAdSettings(1, $settings)) {
            $alertType = 'success';
            $alertMessage = 'Default ad settings updated successfully!';
            
            // Update the defaultAdSettings array directly with POST data
            // This ensures we see the changes immediately without another DB query
            $defaultAdSettings = $settings;
        } else {
            $alertType = 'danger';
            $alertMessage = 'Error updating default ad settings.';
        }
    }
}

// Get domain rules
$stmt = $conn->prepare("SELECT * FROM domain_rules WHERE user_id = 1 ORDER BY rule_type, domain");
$stmt->execute();
$domainRules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Find the highest direct link number that has a value
$maxDirectLink = 0;
for ($i = 1; $i <= 5; $i++) {
    if (!empty($defaultAdSettings["direct_link_$i"])) {
        $maxDirectLink = $i;
    }
}
// If no direct links exist, show at least one input
$maxDirectLink = max(1, $maxDirectLink);
?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-2">
            System Settings
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Configure default ad settings and domain rules for the entire system
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ad Settings Form -->
        <div class="lg:col-span-2" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Default Ad Settings</h2>
                </div>
                <div class="p-6">
                    <form method="post" action="admin.php?page=settings">
                        <?php if (isset($alertMessage)): ?>
                        <div class="mb-6 rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>">
                            <?= htmlspecialchars($alertMessage) ?>
                            <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
                        </div>
                        <?php endif; ?>

                        <!-- Direct Links Section -->
                        <div class="mb-8">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 sm:mb-0">Direct Links</h3>
                                <button type="button" id="addDirectLinkBtn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add More Direct Links
                                </button>
                            </div>
                            <div id="directLinksContainer" class="space-y-4">
                                <?php
                                // Display existing direct links
                                for ($i = 1; $i <= $maxDirectLink; $i++): ?>
                                    <div class="direct-link-group">
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <div class="flex-grow">
                                                <label for="direct_link_<?= $i ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Direct Link <?= $i ?></label>
                                                <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                                       id="direct_link_<?= $i ?>" name="direct_link_<?= $i ?>" 
                                                       value="<?= htmlspecialchars($defaultAdSettings["direct_link_$i"] ?? '') ?>"
                                                       placeholder="https://www.adsterra.com/directlink<?= $i ?>">
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your Adsterra direct link URL #<?= $i ?></p>
                                            </div>
                                            <?php if ($i > 1): ?>
                                            <div class="flex items-end mb-1">
                                                <button type="button" class="remove-direct-link p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20" data-link-num="<?= $i ?>">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Ad Scripts Section -->
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Ad Scripts</h3>
                        
                        <!-- Social Bar Script -->
                        <div class="mb-6">
                            <label for="social_bar_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Social Bar Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="social_bar_script" name="social_bar_script" rows="3"
                                    placeholder="Paste your Adsterra Social Bar script here"><?= htmlspecialchars($defaultAdSettings['social_bar_script'] ?? '') ?></textarea>
                        </div>

                        <!-- Popunder Script -->
                        <div class="mb-6">
                            <label for="popunder_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Popunder Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="popunder_script" name="popunder_script" rows="3"
                                    placeholder="Paste your Adsterra Popunder script here"><?= htmlspecialchars($defaultAdSettings['popunder_script'] ?? '') ?></textarea>
                        </div>

                        <!-- Native Banner Script -->
                        <div class="mb-6">
                            <label for="native_banner_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Native Banner Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="native_banner_script" name="native_banner_script" rows="3"
                                    placeholder="Paste your Adsterra Native Banner script here"><?= htmlspecialchars($defaultAdSettings['native_banner_script'] ?? '') ?></textarea>
                        </div>

                        <!-- Banner Scripts -->
                        <div class="mb-6">
                            <label for="banner_300x250_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 300x250 Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="banner_300x250_script" name="banner_300x250_script" rows="3"
                                    placeholder="Paste your Adsterra 300x250 Banner script here"><?= htmlspecialchars($defaultAdSettings['banner_300x250_script'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-6">
                            <label for="banner_728x90_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 728x90 Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="banner_728x90_script" name="banner_728x90_script" rows="3"
                                    placeholder="Paste your Adsterra 728x90 Banner script here"><?= htmlspecialchars($defaultAdSettings['banner_728x90_script'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-8">
                            <label for="banner_320x50_script" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner 320x50 Script</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="banner_320x50_script" name="banner_320x50_script" rows="3"
                                    placeholder="Paste your Adsterra 320x50 Banner script here"><?= htmlspecialchars($defaultAdSettings['banner_320x50_script'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" name="update_ad_settings" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Domain Rules -->
        <div class="lg:col-span-1" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Domain Rules</h2>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#addDomainModal" class="p-1 rounded-full text-primary-600 dark:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($domainRules)): ?>
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No domain rules set
                    </div>
                    <?php else: ?>
                        <?php foreach ($domainRules as $rule): ?>
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $rule['rule_type'] === 'whitelist' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' ?> mr-2">
                                        <?= ucfirst($rule['rule_type']) ?>
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[150px]" title="<?= htmlspecialchars($rule['domain']) ?>">
                                        <?= htmlspecialchars($rule['domain']) ?>
                                    </span>
                                </div>
                                <button type="button" onclick="deleteDomainRule(<?= $rule['id'] ?>)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Domain Rule Modal -->
<div class="modal fade" id="addDomainModal" tabindex="-1" aria-labelledby="addDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDomainModalLabel">Add Domain Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDomainForm">
                    <div class="mb-3">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain" required
                               placeholder="example.com">
                        <div class="form-text">Enter domain without http:// or https://</div>
                    </div>
                    <div class="mb-3">
                        <label for="rule_type" class="form-label">Rule Type</label>
                        <select class="form-select" id="rule_type" name="rule_type" required>
                            <option value="whitelist">Whitelist</option>
                            <option value="blacklist">Blacklist</option>
                        </select>
                        <div class="form-text">
                            <ul class="mb-0">
                                <li>Whitelist: Only these domains can use the link service</li>
                                <li>Blacklist: These domains cannot use the link service</li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDomainRule">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
// Direct Links Management
document.addEventListener('DOMContentLoaded', function() {
    const directLinksContainer = document.getElementById('directLinksContainer');
    const addDirectLinkBtn = document.getElementById('addDirectLinkBtn');
    let currentMaxLink = <?= $maxDirectLink ?>;
    const MAX_DIRECT_LINKS = 5;
    
    // Add new direct link input
    addDirectLinkBtn.addEventListener('click', function() {
        if (currentMaxLink >= MAX_DIRECT_LINKS) {
            alert('Maximum of 5 direct links allowed.');
            return;
        }
        
        currentMaxLink++;
        
        const newLinkGroup = document.createElement('div');
        newLinkGroup.className = 'direct-link-group';
        newLinkGroup.innerHTML = `
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-grow">
                    <label for="direct_link_${currentMaxLink}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Direct Link ${currentMaxLink}</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                           id="direct_link_${currentMaxLink}" name="direct_link_${currentMaxLink}" 
                           placeholder="https://www.adsterra.com/directlink${currentMaxLink}">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your Adsterra direct link URL #${currentMaxLink}</p>
                </div>
                <div class="flex items-end mb-1">
                    <button type="button" class="remove-direct-link p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20" data-link-num="${currentMaxLink}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        directLinksContainer.appendChild(newLinkGroup);
        
        // Add event listener to new remove button
        const newRemoveBtn = newLinkGroup.querySelector('.remove-direct-link');
        newRemoveBtn.addEventListener('click', removeLinkHandler);
    });
    
    // Remove direct link handler
    function removeLinkHandler() {
        const linkNum = this.getAttribute('data-link-num');
        const linkGroup = this.closest('.direct-link-group');
        linkGroup.remove();
        
        // Clear the value of the removed link in the form
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `direct_link_${linkNum}`;
        hiddenInput.value = '';
        directLinksContainer.appendChild(hiddenInput);
    }
    
    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-direct-link').forEach(btn => {
        btn.addEventListener('click', removeLinkHandler);
    });
    
    // Domain Rules Management
    const saveDomainRuleBtn = document.getElementById('saveDomainRule');
    if (saveDomainRuleBtn) {
        saveDomainRuleBtn.addEventListener('click', function() {
            const form = document.getElementById('addDomainForm');
            const domain = document.getElementById('domain').value.trim();
            const ruleType = document.getElementById('rule_type').value;
            
            if (!domain) {
                alert('Please enter a domain');
                return;
            }
            
            // Send AJAX request to add domain rule
            fetch('admin.php?action=add_domain_rule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `domain=${encodeURIComponent(domain)}&rule_type=${encodeURIComponent(ruleType)}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show the new rule
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to add domain rule');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the domain rule');
            });
        });
    }
});

// Delete domain rule
function deleteDomainRule(ruleId) {
    if (confirm('Are you sure you want to delete this domain rule?')) {
        fetch(`admin.php?action=delete_domain_rule&id=${ruleId}`, {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to update the rules list
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete domain rule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the domain rule');
        });
    }
}
</script>