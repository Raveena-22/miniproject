<?php
// AJAX endpoint - returns job result HTML fragment
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$q = sanitize($conn, $_GET['q'] ?? '');
$jobs = $conn->query("SELECT * FROM jobs WHERE title LIKE '%$q%' OR company LIKE '%$q%' OR skills_required LIKE '%$q%' ORDER BY posted_at DESC");

$user_skills = '';
if (is_logged_in()) {
    $uid = (int)$_SESSION['user_id'];
    $u = $conn->query("SELECT skills FROM users WHERE id=$uid")->fetch_assoc();
    $user_skills = $u['skills'] ?? '';
}

if ($jobs->num_rows === 0) {
    echo '<div class="col-12 text-center text-muted py-5">No jobs found.</div>'; exit;
}
while ($j = $jobs->fetch_assoc()):
    $m = $user_skills ? skill_match($user_skills, $j['skills_required']) : null;
?>
<div class="col-md-6 col-lg-4">
  <div class="card job-card p-4 h-100">
    <div class="d-flex justify-content-between mb-2">
      <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($j['job_type']) ?></span>
      <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($j['location']) ?></small>
    </div>
    <h5 class="fw-bold mb-1"><?= htmlspecialchars($j['title']) ?></h5>
    <p class="text-muted small mb-2"><?= htmlspecialchars($j['company']) ?></p>
    <div class="mb-2">
      <?php foreach (array_slice(explode(',', $j['skills_required']),0,5) as $s): ?>
        <span class="skill-chip"><?= htmlspecialchars(trim($s)) ?></span>
      <?php endforeach; ?>
    </div>
    <?php if ($m !== null): ?>
      <div class="match-bar my-2" data-pct="<?= $m ?>"><div style="width: <?= $m ?>%"></div></div>
    <?php endif; ?>
    <div class="mt-auto d-flex justify-content-between align-items-center">
      <strong class="text-gradient"><?= htmlspecialchars($j['salary']) ?></strong>
      <a href="apply_job.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-gradient">View</a>
    </div>
  </div>
</div>
<?php endwhile; ?>
