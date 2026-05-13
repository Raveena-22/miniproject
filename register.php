<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$page = 'Register';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['full_name'] ?? '');
    $email = sanitize($conn, $_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $skills = sanitize($conn, $_POST['skills'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $err = 'Please fill all fields correctly (password min 6 chars).';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $err = 'Email already registered.';
        } else {
            // file uploads
            $resume = $photo = null;
            if (!empty($_FILES['resume']['name'])) {
                $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['pdf','doc','docx'])) {
                    $resume = 'resume_'.time().'.'.$ext;
                    move_uploaded_file($_FILES['resume']['tmp_name'], 'assets/uploads/resumes/'.$resume);
                }
            }
            if (!empty($_FILES['photo']['name'])) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $photo = 'photo_'.time().'.'.$ext;
                    move_uploaded_file($_FILES['photo']['tmp_name'], 'assets/uploads/profiles/'.$photo);
                }
            }
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (full_name,email,password,skills,resume,profile_photo) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $name, $email, $hash, $skills, $resume, $photo);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                header('Location: dashboard.php'); exit;
            } else $err = 'Registration failed.';
        }
    }
}
include 'includes/header.php';
?>
<section class="auth-wrap py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="glass p-4 p-md-5 fade-up">
          <h2 class="fw-bold mb-1">Create your <span class="text-gradient">SkillBased</span> account</h2>
          <p class="text-muted">Sign up to discover skill-matched jobs.</p>
          <?php if ($err): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
          <form method="post" enctype="multipart/form-data" onsubmit="return validateForm(this)">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Full Name</label>
                <input class="form-control" name="full_name" required></div>
              <div class="col-md-6"><label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required></div>
              <div class="col-md-6"><label class="form-label">Password</label>
                <div class="input-group">
                  <input type="password" id="pw" class="form-control" name="password" required minlength="6">
                  <button type="button" class="btn btn-outline-secondary" data-toggle-pass="#pw"><i class="bi bi-eye"></i></button>
                </div>
              </div>
              <div class="col-md-6"><label class="form-label">Skills (comma separated)</label>
                <input class="form-control" name="skills" placeholder="HTML,CSS,JavaScript" required></div>
              <div class="col-md-6"><label class="form-label">Resume (PDF/DOC)</label>
                <input type="file" class="form-control" name="resume" accept=".pdf,.doc,.docx"></div>
              <div class="col-md-6"><label class="form-label">Profile Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*"></div>
            </div>
            <button class="btn btn-gradient btn-lg w-100 mt-4">Create Account</button>
            <p class="text-center mt-3 mb-0 small">Already have an account? <a href="login.php">Login</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
