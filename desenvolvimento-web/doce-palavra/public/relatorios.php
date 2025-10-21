<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

// Buscar dados para os relatórios
$stats = [
    'total_sessoes' => $pdo->query("SELECT COUNT(*) FROM reading_sessions")->fetchColumn(),
    'total_criancas' => $pdo->query("SELECT COALESCE(SUM(audience_estimate), 0) FROM reading_sessions")->fetchColumn(),
    'total_livros' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'total_creches' => $pdo->query("SELECT COUNT(*) FROM creches")->fetchColumn(),
];

// Sessões por mês (últimos 6 meses)
$sessoes_mes = $pdo->query("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as mes,
        COUNT(*) as total,
        SUM(audience_estimate) as criancas
    FROM reading_sessions 
    WHERE date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY mes
")->fetchAll();

// Top 5 livros mais utilizados
$top_livros = $pdo->query("
    SELECT b.title, b.author, COUNT(rs.id) as vezes_utilizado
    FROM books b
    LEFT JOIN reading_sessions rs ON b.id = rs.book_id
    GROUP BY b.id, b.title, b.author
    ORDER BY vezes_utilizado DESC
    LIMIT 5
")->fetchAll();

// Sessões por creche
$sessoes_creche = $pdo->query("
    SELECT c.name, COUNT(rs.id) as total_sessoes, COALESCE(SUM(rs.audience_estimate), 0) as total_criancas
    FROM creches c
    LEFT JOIN reading_sessions rs ON c.id = rs.creche_id
    GROUP BY c.id, c.name
    ORDER BY total_sessoes DESC
")->fetchAll();

include __DIR__ . '/../src/views/partials/header.php';
?>

<div class="row align-items-center mb-4">
  <div class="col-md-8">
    <h1 class="h2 mb-2">
      <i class="bi bi-graph-up text-primary me-2"></i>
      Relatórios e Estatísticas
    </h1>
    <p class="text-muted mb-0">Acompanhe o impacto do seu trabalho voluntário com dados detalhados</p>
  </div>
  <div class="col-md-4 text-md-end">
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i class="bi bi-printer me-1"></i>Imprimir Relatório
    </button>
  </div>
</div>

<!-- Cards de Resumo -->
<div class="row g-4 mb-5">
  <div class="col-lg-3 col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
        </div>
        <h3 class="display-4 fw-bold text-primary mb-2"><?= number_format($stats['total_sessoes']) ?></h3>
        <h5 class="text-muted mb-2">Sessões Realizadas</h5>
        <p class="text-muted small mb-0">Total de sessões de leitura</p>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-heart text-success" style="font-size: 2.5rem;"></i>
        </div>
        <h3 class="display-4 fw-bold text-success mb-2"><?= number_format($stats['total_criancas']) ?></h3>
        <h5 class="text-muted mb-2">Crianças Atendidas</h5>
        <p class="text-muted small mb-0">Vidas tocadas pela leitura</p>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-book text-info" style="font-size: 2.5rem;"></i>
        </div>
        <h3 class="display-4 fw-bold text-info mb-2"><?= number_format($stats['total_livros']) ?></h3>
        <h5 class="text-muted mb-2">Livros Cadastrados</h5>
        <p class="text-muted small mb-0">Acervo disponível</p>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center p-4">
        <div class="mb-3">
          <i class="bi bi-building text-warning" style="font-size: 2.5rem;"></i>
        </div>
        <h3 class="display-4 fw-bold text-warning mb-2"><?= number_format($stats['total_creches']) ?></h3>
        <h5 class="text-muted mb-2">Creches Parceiras</h5>
        <p class="text-muted small mb-0">Instituições atendidas</p>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Gráfico de Sessões por Mês -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="bi bi-calendar-month me-2"></i>
          Sessões por Mês (Últimos 6 Meses)
        </h5>
      </div>
      <div class="card-body p-4">
        <?php if($sessoes_mes): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Mês</th>
                  <th class="text-center">Sessões</th>
                  <th class="text-center">Crianças Atendidas</th>
                  <th class="text-center">Média por Sessão</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($sessoes_mes as $mes): ?>
                  <tr>
                    <td>
                      <strong><?= date('M/Y', strtotime($mes['mes'] . '-01')) ?></strong>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-primary fs-6"><?= $mes['total'] ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-success fs-6"><?= number_format($mes['criancas']) ?></span>
                    </td>
                    <td class="text-center">
                      <span class="text-muted"><?= $mes['total'] > 0 ? number_format($mes['criancas'] / $mes['total'], 1) : '0' ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">Nenhuma sessão registrada</h5>
            <p class="text-muted">Comece registrando suas primeiras sessões de leitura</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Top Livros -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">
          <i class="bi bi-trophy me-2"></i>
          Livros Mais Utilizados
        </h5>
      </div>
      <div class="card-body p-4">
        <?php if($top_livros): ?>
          <?php foreach($top_livros as $index => $livro): ?>
            <div class="d-flex align-items-center mb-3">
              <div class="me-3">
                <span class="badge bg-<?= $index < 3 ? 'warning' : 'secondary' ?> rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                  <?= $index + 1 ?>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-truncate" title="<?= htmlspecialchars($livro['title']) ?>">
                  <?= htmlspecialchars($livro['title']) ?>
                </div>
                <div class="text-muted small">
                  <?= htmlspecialchars($livro['author']) ?>
                </div>
              </div>
              <div class="text-end">
                <span class="badge bg-primary"><?= $livro['vezes_utilizado'] ?>x</span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-3">
            <i class="bi bi-book text-muted" style="font-size: 2rem;"></i>
            <p class="text-muted mt-2 mb-0">Nenhum livro cadastrado</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Sessões por Creche -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">
          <i class="bi bi-building me-2"></i>
          Atividade por Creche
        </h5>
      </div>
      <div class="card-body p-4">
        <?php if($sessoes_creche): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Creche</th>
                  <th class="text-center">Sessões Realizadas</th>
                  <th class="text-center">Crianças Atendidas</th>
                  <th class="text-center">Média por Sessão</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($sessoes_creche as $creche): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($creche['name']) ?></strong>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-info fs-6"><?= $creche['total_sessoes'] ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-success fs-6"><?= number_format($creche['total_criancas']) ?></span>
                    </td>
                    <td class="text-center">
                      <span class="text-muted">
                        <?= $creche['total_sessoes'] > 0 ? number_format($creche['total_criancas'] / $creche['total_sessoes'], 1) : '0' ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">Nenhuma creche cadastrada</h5>
            <p class="text-muted">Cadastre as creches para começar a registrar as sessões</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  .btn, .navbar, .footer-fixed { display: none !important; }
  .main-content { box-shadow: none !important; }
  .card { break-inside: avoid; }
}
</style>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>