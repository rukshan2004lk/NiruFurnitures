<?php
/**
 * NiRu-Furnitures — Admin Settings (admin/settings.php)
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $val) {
        $key = sanitize($key);
        $val = sanitize($val);
        // Update or insert
        $exists = dbFetchOne("SELECT setting_key FROM settings WHERE setting_key = ?", 's', $key);
        if ($exists) {
            dbExecute("UPDATE settings SET setting_value = ? WHERE setting_key = ?", 'ss', $val, $key);
        } else {
            dbInsert("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", 'ss', $key, $val);
        }
    }
    setFlash('Settings updated successfully.', 'success');
    redirect('settings.php');
}

$pageTitle = 'Settings — Admin';
$extraJs = [SITE_URL . '/assets/js/admin.js'];

include '../includes/admin-navbar.php';
?>

    <?php include '../includes/admin-sidebar.php'; ?>

    <main class="admin-main">
        <?= renderFlash() ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 fw-bold mb-0">Store Settings</h1>
        </div>

        <form method="POST" action="settings.php" class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-4">General Settings</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Store Name</label>
                        <input type="text" class="form-control" name="settings[site_name]" value="<?= e(getSetting('site_name', 'NiRu-Furnitures')) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Contact Email</label>
                        <input type="email" class="form-control" name="settings[site_email]" value="<?= e(getSetting('site_email', 'info@nirufurnitures.com')) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Contact Phone</label>
                        <input type="text" class="form-control" name="settings[site_phone]" value="<?= e(getSetting('site_phone', '+94 77 000 0000')) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Store Address</label>
                        <textarea class="form-control" name="settings[site_address]" rows="3" required><?= e(getSetting('site_address', '42 Furniture Street, Colombo 03, Sri Lanka')) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-4">E-commerce Settings</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Currency Symbol</label>
                        <input type="text" class="form-control" name="settings[currency_symbol]" value="<?= e(getSetting('currency_symbol', 'Rs. ')) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Base Shipping Cost</label>
                        <input type="number" class="form-control" name="settings[shipping_cost]" value="<?= e(getSetting('shipping_cost', '500')) ?>" min="0" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium">Free Shipping Threshold</label>
                        <input type="number" class="form-control" name="settings[free_shipping_above]" value="<?= e(getSetting('free_shipping_above', '15000')) ?>" min="0" required>
                        <div class="form-text">Cart total above this gets free shipping.</div>
                    </div>
                    
                    <h5 class="fw-bold mb-3 mt-4">Social Links</h5>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Facebook URL</label>
                        <input type="url" class="form-control" name="settings[facebook_url]" value="<?= e(getSetting('facebook_url', '')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Instagram URL</label>
                        <input type="url" class="form-control" name="settings[instagram_url]" value="<?= e(getSetting('instagram_url', '')) ?>">
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-save me-2"></i>Save All Settings
                </button>
            </div>
        </form>

<?php include '../includes/admin-footer.php'; ?>
