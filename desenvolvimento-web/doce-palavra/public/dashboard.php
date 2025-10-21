
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

$cards = [
  'Sessões'   => (int)$pdo->query("SELECT COUNT(*) c FROM reading_sessions")->fetch()['c'],
  'Crianças'  => (int)$pdo->query("SELECT COALESCE(SUM(audience_estimate),0) s FROM reading_sessions")->fetch()['s'],
  'Livros'    => (int)$pdo->query("SELECT COUNT(*) c FROM books")->fetch()['c'],
  'Creches'   => (int)$pdo->query("SELECT COUNT(*) c FROM creches")->fetch()['c'],
];
include __DIR__ . '/../src/views/partials/header.php';
?>

<h1 class="h4 mb-4">Dashboard</h1>
<div class="row g-3">
  <?php foreach ($cards as $label => $value): ?>
    <div class="col-md-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <div class="text-muted text-uppercase small"><?= htmlspecialchars($label) ?></div>
          <div class="display-6"><?= (int)$value ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="mt-4 d-flex gap-2 justify-content-center">
  <a class="btn btn-primary" href="sessoes.php"><i class="bi bi-journal-plus"></i> Registrar Sessão</a>
  <a class="btn btn-outline-secondary" href="relatorios.php"><i class="bi bi-graph-up"></i> Ver Relatórios</a>
</div>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
