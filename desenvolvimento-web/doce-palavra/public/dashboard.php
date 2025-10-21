
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

<div class="row align-items-center mb-4">
  <div class="col-md-8">
    <h1 class="h2 mb-2">
      <i class="bi bi-house-heart text-primary me-2"></i>
      Bem-vinda ao Doce Palavra
    </h1>
    <p class="text-muted mb-0">Acompanhe o progresso do seu trabalho voluntário incentivando a leitura nas creches</p>
  </div>
  <div class="col-md-4 text-md-end">
    <div class="text-muted small">
      <i class="bi bi-calendar-check me-1"></i>
      <?= date('d/m/Y') ?>
    </div>
  </div>
</div>

<div class="row g-4 mb-5">
  <?php 
  $cardData = [
    'Sessões' => ['icon' => 'bi-people', 'color' => 'primary', 'desc' => 'Sessões de leitura realizadas'],
    'Crianças' => ['icon' => 'bi-heart', 'color' => 'success', 'desc' => 'Crianças atendidas'],
    'Livros' => ['icon' => 'bi-book', 'color' => 'info', 'desc' => 'Livros cadastrados'],
    'Creches' => ['icon' => 'bi-building', 'color' => 'warning', 'desc' => 'Creches parceiras']
  ];
  foreach ($cards as $label => $value): 
    $data = $cardData[$label];
  ?>
    <div class="col-lg-3 col-md-6">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <i class="bi <?= $data['icon'] ?> text-<?= $data['color'] ?>" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="display-4 fw-bold text-<?= $data['color'] ?> mb-2">
            <?= number_format((int)$value) ?>
          </h3>
          <h5 class="text-muted mb-2"><?= htmlspecialchars($label) ?></h5>
          <p class="text-muted small mb-0"><?= $data['desc'] ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="bi bi-lightning-charge me-2"></i>
          Ações Rápidas
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <a class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center" href="sessoes.php">
              <i class="bi bi-journal-plus me-3" style="font-size: 1.5rem;"></i>
              <div class="text-start">
                <div class="fw-bold">Registrar Sessão</div>
                <small class="opacity-75">Nova sessão de leitura</small>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center" href="creches.php">
              <i class="bi bi-building me-3" style="font-size: 1.5rem;"></i>
              <div class="text-start">
                <div class="fw-bold">Gerenciar Creches</div>
                <small class="opacity-75">Adicionar ou editar creches</small>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a class="btn btn-outline-success btn-lg w-100 d-flex align-items-center justify-content-center" href="livros.php">
              <i class="bi bi-book me-3" style="font-size: 1.5rem;"></i>
              <div class="text-start">
                <div class="fw-bold">Cadastrar Livros</div>
                <small class="opacity-75">Adicionar novos livros</small>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a class="btn btn-outline-info btn-lg w-100 d-flex align-items-center justify-content-center" href="relatorios.php">
              <i class="bi bi-graph-up me-3" style="font-size: 1.5rem;"></i>
              <div class="text-start">
                <div class="fw-bold">Ver Relatórios</div>
                <small class="opacity-75">Acompanhar progresso</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">
          <i class="bi bi-info-circle me-2"></i>
          Dicas
        </h5>
      </div>
      <div class="card-body p-4">
        <div class="d-flex align-items-start mb-3">
          <i class="bi bi-lightbulb text-warning me-2 mt-1"></i>
          <div>
            <strong>Dica:</strong> Registre sempre as sessões de leitura para acompanhar o impacto do seu trabalho.
          </div>
        </div>
        <div class="d-flex align-items-start mb-3">
          <i class="bi bi-heart text-danger me-2 mt-1"></i>
          <div>
            <strong>Importante:</strong> Cada criança atendida é uma vida transformada pela leitura.
          </div>
        </div>
        <div class="d-flex align-items-start">
          <i class="bi bi-book text-primary me-2 mt-1"></i>
          <div>
            <strong>Lembre-se:</strong> Cadastre os livros utilizados para ter um histórico completo.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
