
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare("INSERT INTO creches (name, district, contact, notes) VALUES (?,?,?,?)");
  $stmt->execute([
    trim($_POST['name'] ?? ''),
    trim($_POST['district'] ?? ''),
    trim($_POST['contact'] ?? ''),
    trim($_POST['notes'] ?? '')
  ]);
  header('Location: creches.php'); exit;
}
$creches = $pdo->query("SELECT * FROM creches ORDER BY name")->fetchAll();

include __DIR__ . '/../src/views/partials/header.php';
?>

<h1 class="h4 mb-4">Creches</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h5">Lista de Creches</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novaCreche"><i class="bi bi-plus-lg"></i> Nova Creche</button>
</div>

<table class="table table-striped align-middle">
  <thead class="table-light"><tr><th>Nome</th><th>Bairro</th><th>Contato</th><th>Observações</th></tr></thead>
  <tbody>
    <?php foreach($creches as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['district']) ?></td>
        <td><?= htmlspecialchars($c['contact']) ?></td>
        <td><?= htmlspecialchars($c['notes']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if(!$creches): ?><tr><td colspan="4" class="text-muted">Nenhuma creche cadastrada.</td></tr><?php endif; ?>
  </tbody>
</table>

<div class="modal fade" id="novaCreche" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Cadastrar Creche</h5></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nome</label><input name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Bairro</label><input name="district" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Contato</label><input name="contact" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Observações</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
