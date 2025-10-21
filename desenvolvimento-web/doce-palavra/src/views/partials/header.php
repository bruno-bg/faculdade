
<?php
require_once __DIR__ . '/../../../src/lib/auth.php';
require_login();
$current = basename($_SERVER['PHP_SELF']);
function active($page, $current) {
  return $page === $current ? 'btn-secondary active text-white' : 'btn-outline-secondary';
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Doce Palavra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-size: 1.05rem; }
    .navbar { background-color: #f8f9fa !important; }
    .navbar .btn { font-size: 1rem; padding: 0.5rem 1rem; }
    .navbar-brand { font-size: 1.25rem; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm sticky-top border-bottom">
  <div class="container d-flex flex-wrap gap-2 justify-content-center">
    <a class="navbar-brand fw-bold" href="dashboard.php">📖 Doce Palavra</a>
    <div class="d-flex flex-wrap gap-2 justify-content-center">
      <a class="btn btn-sm <?= active('dashboard.php', $current) ?>" href="dashboard.php"><i class="bi bi-house-door"></i> Início</a>
      <a class="btn btn-sm <?= active('creches.php', $current) ?>" href="creches.php"><i class="bi bi-building"></i> Creches</a>
      <a class="btn btn-sm <?= active('livros.php', $current) ?>" href="livros.php"><i class="bi bi-book"></i> Livros</a>
      <a class="btn btn-sm <?= active('sessoes.php', $current) ?>" href="sessoes.php"><i class="bi bi-people"></i> Sessões</a>
      <a class="btn btn-sm <?= active('relatorios.php', $current) ?>" href="relatorios.php"><i class="bi bi-bar-chart"></i> Relatórios</a>
      <a class="btn btn-danger btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </div>
</nav>
<div class="container py-4">
