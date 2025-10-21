
<?php
require_once __DIR__ . '/../../lib/auth.php';
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
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <style>
    /* Layout fixo */
    body { 
      font-size: 1.1rem; 
      line-height: 1.6;
      padding-top: 80px;
      padding-bottom: 80px;
      background-color: #f8f9fa;
    }
    
    /* Menu superior fixo */
    .navbar { 
      background-color: #ffffff !important; 
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: fixed !important;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1030;
    }
    
    /* Rodapé fixo */
    .footer-fixed {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 1030;
      background-color: #ffffff;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
    
    /* Botões maiores e mais acessíveis */
    .navbar .btn { 
      font-size: 1.1rem; 
      padding: 0.75rem 1.25rem; 
      margin: 0.25rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      min-width: 120px;
    }
    
    .navbar .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .navbar-brand { 
      font-size: 1.4rem; 
      font-weight: 700;
      color: #2c3e50 !important;
    }
    
    /* Conteúdo principal */
    .main-content {
      min-height: calc(100vh - 160px);
      background-color: #ffffff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      margin: 20px auto;
      padding: 30px;
    }
    
    /* Cards com animação */
    .card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    /* Botões principais */
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 8px;
      padding: 12px 24px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    /* Tabelas mais legíveis */
    .table {
      font-size: 1.1rem;
    }
    
    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
      border: none;
      padding: 15px;
    }
    
    .table td {
      padding: 15px;
      border-color: #e9ecef;
    }
    
    /* Formulários mais acessíveis */
    .form-control {
      font-size: 1.1rem;
      padding: 12px 16px;
      border-radius: 8px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 8px;
    }
    
    /* Animações suaves */
    .fade-in {
      animation: fadeIn 0.6s ease-in;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsividade melhorada */
    @media (max-width: 768px) {
      body { padding-top: 100px; }
      .navbar .btn { font-size: 1rem; padding: 0.6rem 1rem; min-width: 100px; }
      .main-content { margin: 10px; padding: 20px; }
    }
  </style>
</head>
<body>
<!-- Skip links para navegação por teclado -->
<a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
<a href="#navigation" class="skip-link">Pular para a navegação</a>

<nav class="navbar navbar-expand-lg" id="navigation">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
      <i class="bi bi-book-heart text-primary me-2"></i>
      Doce Palavra
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="navbar-nav ms-auto d-flex flex-wrap gap-2">
        <a class="btn <?= active('dashboard.php', $current) ?>" href="dashboard.php" title="Página inicial">
          <i class="bi bi-house-door me-1"></i> Início
        </a>
        <a class="btn <?= active('creches.php', $current) ?>" href="creches.php" title="Gerenciar creches">
          <i class="bi bi-building me-1"></i> Creches
        </a>
        <a class="btn <?= active('livros.php', $current) ?>" href="livros.php" title="Gerenciar livros">
          <i class="bi bi-book me-1"></i> Livros
        </a>
        <a class="btn <?= active('sessoes.php', $current) ?>" href="sessoes.php" title="Gerenciar sessões de leitura">
          <i class="bi bi-people me-1"></i> Sessões
        </a>
        <a class="btn <?= active('relatorios.php', $current) ?>" href="relatorios.php" title="Ver relatórios">
          <i class="bi bi-bar-chart me-1"></i> Relatórios
        </a>
        <a class="btn btn-outline-danger" href="logout.php" title="Sair do sistema">
          <i class="bi bi-box-arrow-right me-1"></i> Sair
        </a>
      </div>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="main-content fade-in" id="main-content">
