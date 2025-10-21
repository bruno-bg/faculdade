
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

// Carrega dados necessários
$creches = $pdo->query("SELECT id,name FROM creches ORDER BY name")->fetchAll();
$books   = $pdo->query("SELECT id,title FROM books ORDER BY title")->fetchAll();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $date = $_POST['date'] ?? date('Y-m-d');
    $creche_id = (int)($_POST['creche_id'] ?? 0);
    $book_id   = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
    $audience  = (int)($_POST['audience_estimate'] ?? 0);
    $notes     = trim($_POST['notes'] ?? '');
    $user_id   = (int)$_SESSION['user']['id'];

    if ($creche_id <= 0) {
      throw new Exception('Selecione uma creche.');
    }

    $stmt = $pdo->prepare("INSERT INTO reading_sessions (date,creche_id,book_id,audience_estimate,notes,created_by)
                           VALUES (?,?,?,?,?,?)");
    $stmt->execute([$date,$creche_id,$book_id,$audience,$notes,$user_id]);
    $success = true;
  } catch (Throwable $e) {
    $error = $e->getMessage();
  }
}

// Lista recentes
$recent = $pdo->query("
  SELECT s.id, s.date, c.name creche, s.audience_estimate, b.title book
  FROM reading_sessions s
  JOIN creches c ON c.id = s.creche_id
  LEFT JOIN books b ON b.id = s.book_id
  ORDER BY s.date DESC, s.id DESC
  LIMIT 20
")->fetchAll();

include __DIR__ . '/../src/views/partials/header.php';
?>

<h1 class="h4 mb-3">Registrar Sessão de Leitura</h1>

<?php if ($success): ?>
  <div class="alert alert-success">Sessão registrada com sucesso.</div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-danger">Erro: <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" class="row g-3">
  <div class="col-md-3">
    <label class="form-label">Data</label>
    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
  </div>
  <div class="col-md-5">
    <label class="form-label">Creche</label>
    <select name="creche_id" class="form-select" required <?= empty($creches) ? 'disabled' : '' ?>>
      <option value="">Selecione</option>
      <?php foreach($creches as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (empty($creches)): ?>
      <div class="form-text text-danger">Cadastre uma creche antes de registrar uma sessão.</div>
    <?php endif; ?>
  </div>
  <div class="col-md-4">
    <label class="form-label">Livro (opcional)</label>
    <select name="book_id" class="form-select" <?= empty($books) ? '' : '' ?>>
      <option value="">—</option>
      <?php foreach($books as $b): ?>
        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Crianças</label>
    <input type="number" name="audience_estimate" class="form-control" min="0" value="0">
  </div>
  <div class="col-12">
    <label class="form-label">Observações</label>
    <textarea name="notes" class="form-control" rows="2" placeholder="Atividade lúdica, reação das crianças, etc."></textarea>
  </div>
  <div class="col-12">
    <button class="btn btn-primary" <?= empty($creches) ? 'disabled' : '' ?>>
      Registrar Sessão
    </button>
    <?php if (empty($creches)): ?>
      <a class="btn btn-outline-secondary" href="creches.php">Ir para Creches</a>
    <?php endif; ?>
  </div>
</form>

<hr class="my-4">
<h2 class="h5">Sessões recentes</h2>

<table class="table table-hover">
  <thead><tr><th>Data</th><th>Creche</th><th>Livro</th><th>Crianças</th></tr></thead>
  <tbody>
    <?php foreach ($recent as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['date']) ?></td>
        <td><?= htmlspecialchars($r['creche']) ?></td>
        <td><?= htmlspecialchars($r['book'] ?? '—') ?></td>
        <td><?= (int)$r['audience_estimate'] ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if(!$recent): ?><tr><td colspan="4" class="text-muted">Sem registros.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
