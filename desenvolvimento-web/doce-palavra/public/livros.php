
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare("INSERT INTO books (title, author, category, qty) VALUES (?,?,?,?)");
  $stmt->execute([
    trim($_POST['title'] ?? ''),
    trim($_POST['author'] ?? ''),
    trim($_POST['category'] ?? ''),
    (int)($_POST['qty'] ?? 1),
  ]);
  header('Location: livros.php'); exit;
}
$books = $pdo->query("SELECT * FROM books ORDER BY title")->fetchAll();

include __DIR__ . '/../src/views/partials/header.php';
?>

<h1 class="h4 mb-4">Livros</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h5">Lista de livros</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoLivro"><i class="bi bi-plus-lg"></i> Novo Livro</button>
</div>

<table class="table table-striped">
  <thead><tr><th>Título</th><th>Autor</th><th>Categoria</th><th>Qtd</th></tr></thead>
  <tbody>
    <?php foreach($books as $b): ?>
      <tr>
        <td><?= htmlspecialchars($b['title']) ?></td>
        <td><?= htmlspecialchars($b['author']) ?></td>
        <td><?= htmlspecialchars($b['category']) ?></td>
        <td><?= (int)$b['qty'] ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if(!$books): ?><tr><td colspan="4" class="text-muted">Nenhum livro cadastrado.</td></tr><?php endif; ?>
  </tbody>
</table>

<div class="modal fade" id="novoLivro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Novo Livro</h5></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Título</label><input name="title" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Autor</label><input name="author" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Categoria</label><input name="category" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Quantidade</label><input type="number" name="qty" class="form-control" min="1" value="1"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
