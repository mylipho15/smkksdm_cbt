<div class="login-container">
    <div class="login-header">
        <h1>📝 Exam CBT</h1>
        <p>Silakan login untuk melanjutkan</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="<?= BASE_URL ?>/login.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-login">Login</button>
    </form>
    
    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 13px; color: #666;">
        <p><strong>Demo Login:</strong></p>
        <p>Admin: admin / password</p>
        <p>Guru: guru1 / password</p>
        <p>Siswa: siswa1 / password</p>
    </div>
</div>
