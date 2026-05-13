<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();
$page = 'Profile';
$uid = (int)$_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['full_name']);
    $skills = sanitize($conn, $_POST['skills']);
    $resume = $_POST['existing_resume'] ?? null;
    $photo  = $_POST['existing_photo'] ?? null;

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
    $stmt = $conn->prepare("UPDATE users SET full_name=?, skills=?, resume=?, profile_photo=? WHERE id=?");
    $stmt->bind_param('ssssi', $name, $skills, $resume, $photo, $uid);
    $stmt->execute();
    $msg = 'Profile updated successfully.';
}

$user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
include 'includes/header.php';
?>
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="glass p-4 p-md-5">
        <h3 class="fw-bold mb-3">My Profile</h3>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="existing_resume" value="<?= htmlspecialchars($user['resume']) ?>">
          <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($user['profile_photo']) ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label>
              <input class="form-control" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Email</label>
              <input class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
            <div class="col-12"><label class="form-label">Skills (comma separated)</label>
              <input class="form-control" name="skills" value="<?= htmlspecialchars($user['skills']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Update Resume</label>
              <input type="file" class="form-control" name="resume" accept=".pdf,.doc,.docx">
              <?php if ($user['resume']): ?><a class="small" target="_blank" href="assets/uploads/resumes/<?= htmlspecialchars($user['resume']) ?>">View current</a><?php endif; ?>
            </div>
            <div class="col-md-6"><label class="form-label">Update Photo</label>
              <input type="file" class="form-control" name="photo" accept="image/*"></div>
          </div>
          <button class="btn btn-gradient mt-4">Save Changes</button>
          <a href="dashboard.php" class="btn btn-link">Back</a>
        </form>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
