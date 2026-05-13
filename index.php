<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';
$page = 'Home';

$jobs = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC LIMIT 6");

// Safe counters to avoid fatal errors if tables are empty
$totalJobs = 0;
$totalUsers = 0;
$totalApps = 0;

if ($jobs) {
    $res = $conn->query("SELECT COUNT(*) c FROM jobs");
    if ($res) $totalJobs = $res->fetch_assoc()['c'];
}

$resUsers = $conn->query("SELECT COUNT(*) c FROM users");
if ($resUsers) $totalUsers = $resUsers->fetch_assoc()['c'];

$resApps = $conn->query("SELECT COUNT(*) c FROM applications");
if ($resApps) $totalApps = $resApps->fetch_assoc()['c'];


include 'includes/header.php';
?>
<div id="loader"><div class="spinner-border text-primary"></div></div>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7 fade-up">
        <span class="badge rounded-pill bg-light text-primary mb-3 px-3 py-2">🚀 Skill-based hiring, reimagined</span>
        <h1>Find a job that <br>matches your <span class="text-gradient typing"></span></h1>
        <p class="lead text-muted my-3">SkillBased recommends jobs that genuinely fit your skills — no more resume-job mismatch.</p>
        <form action="jobs.php" method="get" class="glass p-3 mt-4 d-flex flex-wrap gap-2">
          <input class="form-control form-control-lg flex-grow-1 border-0" name="q" placeholder="Job title, skill, or company">
          <input class="form-control form-control-lg border-0" style="max-width:220px" name="loc" placeholder="Location">
          <button class="btn btn-gradient btn-lg px-4"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="row mt-4 g-3 text-center">
          <div class="col-4"><div class="counter text-gradient" data-target="<?= $totalJobs ?>">0</div><small class="text-muted">Jobs</small></div>
          <div class="col-4"><div class="counter text-gradient" data-target="<?= $totalUsers ?>">0</div><small class="text-muted">Candidates</small></div>
          <div class="col-4"><div class="counter text-gradient" data-target="<?= $totalApps ?>">0</div><small class="text-muted">Applications</small></div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="glass p-4 fade-up">
          <h5 class="mb-3"><i class="bi bi-stars text-gradient"></i> Trending Skills</h5>
          <div>
            <?php foreach (['HTML','CSS','JavaScript','Bootstrap','PHP','MySQL','React','Python','Node.js','Java','Docker','AWS'] as $s): ?>
              <span class="skill-chip"><?= $s ?></span>
            <?php endforeach; ?>
          </div>
          <hr>
          <p class="small text-muted mb-2">Why SkillBased?</p>
          <ul class="small mb-0">
            <li>Match score on every job</li>
            <li>Resume + skill profile</li>
            <li>One-click apply</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured jobs -->
<section class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h2 class="fw-bold mb-1">Featured Jobs</h2>
      <p class="text-muted mb-0">Handpicked roles updated daily</p>
    </div>
    <a href="jobs.php" class="btn btn-outline-primary">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4">
    <?php while ($j = $jobs->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card job-card h-100 p-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($j['job_type']) ?></span>
            <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($j['location']) ?></small>
          </div>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($j['title']) ?></h5>
          <p class="text-muted small mb-2"><i class="bi bi-building"></i> <?= htmlspecialchars($j['company']) ?></p>
          <div class="mb-3">
            <?php foreach (array_slice(explode(',', $j['skills_required']),0,4) as $s): ?>
              <span class="skill-chip"><?= htmlspecialchars(trim($s)) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <strong class="text-gradient"><?= htmlspecialchars($j['salary']) ?></strong>
            <a href="apply_job.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-gradient">View</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Companies -->
<section class="container py-5">
  <h3 class="text-center fw-bold mb-4">Trusted by leading companies</h3>
  <div class="logo-strip d-flex flex-wrap justify-content-center gap-3">
    <?php foreach (['TechNova','CodeCrafters','InnoSoft','DataPeak','PixelPro','FinEdge'] as $c): ?>
      <div class="logo-pill"><?= $c ?></div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
