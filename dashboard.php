<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();
$page = 'Dashboard';

$uid = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$completion = profile_completion($user);

// Applied jobs
$applied = $conn->query("SELECT j.*, a.match_score, a.applied_at FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.user_id=$uid ORDER BY a.applied_at DESC");

// Recommended jobs (compute match in PHP)
$jobs = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC");
$recs = [];
while ($j = $jobs->fetch_assoc()) {
    $j['match'] = skill_match($user['skills'], $j['skills_required']);
    $recs[] = $j;
}
usort($recs, fn($a,$b) => $b['match'] <=> $a['match']);
$recs = array_slice($recs, 0, 6);

include 'includes/header.php';
?>
<section class="container py-5">
  <div class="row g-4">
    <div class="col-lg-4">
      <div class="glass p-4 text-center">
        <?php if ($user['profile_photo']): ?>
          <img src="assets/uploads/profiles/<?= htmlspecialchars($user['profile_photo']) ?>" class="rounded-circle mb-3" width="100" height="100" style="object-fit:cover">
        <?php else: ?>
          <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width:100px;height:100px;background:var(--gradient);font-size:2rem;font-weight:700">
            <?= strtoupper(substr($user['full_name'],0,1)) ?>
          </div>
        <?php endif; ?>
        <h5 class="fw-bold mb-0"><?= htmlspecialchars($user['full_name']) ?></h5>
        <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
        <div class="text-start small mb-2">Profile completion <strong><?= $completion ?>%</strong></div>
        <div class="match-bar mb-3" data-pct="<?= $completion ?>"><div></div></div>
        <a href="profile.php" class="btn btn-sm btn-gradient w-100">Manage Profile</a>
        <?php if ($user['resume']): ?>
          <a href="assets/uploads/resumes/<?= htmlspecialchars($user['resume']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2"><i class="bi bi-file-earmark-pdf"></i> View Resume</a>
        <?php endif; ?>
        <hr>
        <div class="text-start">
          <small class="text-muted">Your Skills</small>
          <div class="mt-2">
            <?php foreach (array_filter(array_map('trim', explode(',', $user['skills']))) as $s): ?>
              <span class="skill-chip"><?= htmlspecialchars($s) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="glass p-4 mb-4">
        <h4 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?> 👋</h4>
        <p class="text-muted mb-0">Here are jobs that match your skills.</p>
      </div>

      <h5 class="fw-bold mb-3"><i class="bi bi-stars text-gradient"></i> Recommended for you</h5>
      <div class="row g-3 mb-4">
        <?php foreach ($recs as $r): ?>
          <div class="col-md-6">
            <div class="card job-card p-3 h-100">
              <div class="d-flex justify-content-between"><strong><?= htmlspecialchars($r['title']) ?></strong>
                <span class="badge bg-success-subtle text-success"><?= $r['match'] ?>% match</span></div>
              <small class="text-muted"><?= htmlspecialchars($r['company']) ?> • <?= htmlspecialchars($r['location']) ?></small>
              <div class="match-bar my-2" data-pct="<?= $r['match'] ?>"><div></div></div>
              <a href="apply_job.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-gradient mt-auto align-self-start">View Job</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <h5 class="fw-bold mb-3"><i class="bi bi-briefcase"></i> Applied Jobs</h5>
      <div class="glass p-3">
        <?php if ($applied->num_rows === 0): ?>
          <p class="text-muted mb-0">No applications yet. <a href="jobs.php">Browse jobs</a>.</p>
        <?php else: ?>
        <div class="table-responsive"><table class="table align-middle mb-0">
          <thead><tr><th>Job</th><th>Company</th><th>Match</th><th>Applied</th></tr></thead>
          <tbody>
          <?php while ($a = $applied->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($a['title']) ?></td>
              <td><?= htmlspecialchars($a['company']) ?></td>
              <td><span class="badge bg-primary-subtle text-primary"><?= $a['match_score'] ?>%</span></td>
              <td><small class="text-muted"><?= date('d M Y', strtotime($a['applied_at'])) ?></small></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
