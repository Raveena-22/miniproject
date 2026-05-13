<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$page = 'Apply';

$id = (int)($_GET['id'] ?? 0);
$job = $conn->query("SELECT * FROM jobs WHERE id=$id")->fetch_assoc();
if (!$job) { header('Location: jobs.php'); exit; }

$applied = false; $match = null;
if (is_logged_in()) {
    $uid = (int)$_SESSION['user_id'];
    $user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
    $match = skill_match($user['skills'], $job['skills_required']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $check = $conn->query("SELECT id FROM applications WHERE user_id=$uid AND job_id=$id");
        if ($check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO applications (user_id, job_id, match_score) VALUES (?,?,?)");
            $stmt->bind_param('iii', $uid, $id, $match);
            $stmt->execute();
        }
        $applied = true;
    } else {
        $check = $conn->query("SELECT id FROM applications WHERE user_id=$uid AND job_id=$id");
        $applied = $check->num_rows > 0;
    }
}
include 'includes/header.php';
?>
<section class="container py-5">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="glass p-4 p-md-5">
        <span class="badge bg-primary-subtle text-primary mb-2"><?= htmlspecialchars($job['job_type']) ?></span>
        <h2 class="fw-bold"><?= htmlspecialchars($job['title']) ?></h2>
        <p class="text-muted"><i class="bi bi-building"></i> <?= htmlspecialchars($job['company']) ?> &nbsp; • &nbsp;
           <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?> &nbsp; • &nbsp;
           <i class="bi bi-cash-stack"></i> <?= htmlspecialchars($job['salary']) ?></p>
        <hr>
        <h5>Job Description</h5>
        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        <h6 class="mt-4">Required Skills</h6>
        <div>
          <?php foreach (explode(',', $job['skills_required']) as $s): ?>
            <span class="skill-chip"><?= htmlspecialchars(trim($s)) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="glass p-4 sticky-top" style="top:90px">
        <?php if (is_logged_in()): ?>
          <h6>Your Skill Match</h6>
          <div class="display-6 fw-bold text-gradient"><?= $match ?>%</div>
          <div class="match-bar mb-3" data-pct="<?= $match ?>"><div></div></div>
          <?php if ($applied): ?>
            <div class="alert alert-success small mb-0"><i class="bi bi-check-circle"></i> Application submitted! 🎉</div>
          <?php else: ?>
            <form method="post"><button class="btn btn-gradient w-100"><i class="bi bi-send"></i> Apply Now</button></form>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-muted small">Login to see your skill match and apply.</p>
          <a href="login.php" class="btn btn-gradient w-100">Login to Apply</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($applied && $_SERVER['REQUEST_METHOD']==='POST'): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  toast('🎉 Application submitted successfully!');
});
</script>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
