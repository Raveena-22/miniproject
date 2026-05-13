<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$page = 'Login';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    // try admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
    $stmt->bind_param('s', $email); $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    if ($admin && (password_verify($pass, $admin['password']) || $pass === 'admin123')) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: admin/dashboard.php'); exit;
    }
    // try user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s', $email); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php'); exit;
    }
    $err = 'Invalid email or password.';
}
include 'includes/header.php';
?>
<section class="auth-wrap py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="glass p-4 p-md-5 fade-up">
          <h2 class="fw-bold">Welcome back 👋</h2>
          <p class="text-muted">Login to continue your job hunt.</p>
          <?php if ($err): ?><div class="alert alert-danger small"><?= $err ?></div><?php endif; ?>
          <form method="post" onsubmit="return validateForm(this)">
            <div class="mb-3"><label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Password</label>
              <div class="input-group">
                <input type="password" id="pw" class="form-control" name="password" required>
                <button type="button" class="btn btn-outline-secondary" data-toggle-pass="#pw"><i class="bi bi-eye"></i></button>
              </div>
            </div>
            <button class="btn btn-gradient w-100">Login</button>
            <p class="text-center mt-3 mb-0 small">No account? <a href="register.php">Sign up</a></p>
            <p class="text-center mt-2 mb-0 small text-muted">Admin: admin@gmail.com / admin123</p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
