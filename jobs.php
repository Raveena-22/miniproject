<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$page = 'Jobs';

$q = sanitize($conn, $_GET['q'] ?? '');
$loc = sanitize($conn, $_GET['loc'] ?? '');
$type = sanitize($conn, $_GET['type'] ?? '');

$where = "1=1";
if ($q)   $where .= " AND (title LIKE '%$q%' OR company LIKE '%$q%' OR skills_required LIKE '%$q%')";
if ($loc) $where .= " AND location LIKE '%$loc%'";
if ($type)$where .= " AND job_type='$type'";

$jobs = $conn->query("SELECT * FROM jobs WHERE $where ORDER BY posted_at DESC");

$user_skills = '';
if (is_logged_in()) {
    $uid = (int)$_SESSION['user_id'];
    $u = $conn->query("SELECT skills FROM users WHERE id=$uid")->fetch_assoc();
    $user_skills = $u['skills'] ?? '';
}
include 'includes/header.php';
?>
<div id="loader"><div class="spinner-border text-primary"></div></div>
<section class="container py-5">
  <div class="glass p-4 mb-4">
    <form class="row g-2" method="get">
      <div class="col-md-4"><input id="jobSearch" class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search title/company/skill"></div>
      <div class="col-md-3"><input class="form-control" name="loc" value="<?= htmlspecialchars($loc) ?>" placeholder="Location"></div>
      <div class="col-md-3"><select class="form-select" name="type">
        <option value="">All types</option>
        <?php foreach (['Full-time','Part-time','Internship','Contract','Remote'] as $t): ?>
          <option <?= $type===$t?'selected':'' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select></div>
      <div class="col-md-2"><button class="btn btn-gradient w-100">Filter</button></div>
    </form>
  </div>

  <div id="jobResults" class="row g-4">
    <?php if ($jobs->num_rows === 0): ?>
      <div class="col-12 text-center text-muted py-5">No jobs found.</div>
    <?php endif; ?>
    <?php while ($j = $jobs->fetch_assoc()): $m = $user_skills ? skill_match($user_skills, $j['skills_required']) : null; ?>
      <div class="col-md-6 col-lg-4">
        <div class="card job-card p-4 h-100">
          <div class="d-flex justify-content-between mb-2">
            <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($j['job_type']) ?></span>
            <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($j['location']) ?></small>
          </div>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($j['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="bi bi-building"></i> <?= htmlspecialchars($j['company']) ?></p>
          <div class="mb-2">
            <?php foreach (array_slice(explode(',', $j['skills_required']),0,5) as $s): ?>
              <span class="skill-chip"><?= htmlspecialchars(trim($s)) ?></span>
            <?php endforeach; ?>
          </div>
          <?php if ($m !== null): ?>
            <small class="text-muted">Skill match: <strong><?= $m ?>%</strong></small>
            <div class="match-bar my-2" data-pct="<?= $m ?>"><div></div></div>
          <?php endif; ?>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <strong class="text-gradient"><?= htmlspecialchars($j['salary']) ?></strong>
            <a href="apply_job.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-gradient">View / Apply</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
