<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Include header
include 'views/partials/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Edit User</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['message']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?controller=admin&action=editUser&id=<?php echo htmlspecialchars($editUser['id']); ?>">
                        <div class="form-group mb-3">
                            <label for="name">Name*</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($editUser['name']); ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" id="surname" name="surname" value="<?php echo htmlspecialchars($editUser['surname'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email">Email*</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($editUser['email']); ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($editUser['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role">
                                <option value="client" <?php echo ($editUser['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                                <option value="admin" <?php echo ($editUser['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <hr>
                        <h4>Change Password</h4>
                        <p class="text-muted">Leave blank to keep the current password</p>
                        
                        <div class="form-group mb-3">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="index.php?controller=admin&action=dashboard" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'views/partials/footer.php';
?> 