<?php include('layouts/header.php'); ?>
<?php
$data_nascimento_input = $_POST['data_nascimento'] ?? '';
$erro = null;
$signo_encontrado = null;

if (!$data_nascimento_input) {
  $erro = "Nenhuma data foi informada.";
} else {
  try {
    $data_nascimento_dt = new DateTime($data_nascimento_input);
  } catch (Exception $e) {
    $erro = "Data inválida.";
  }
}

if (!$erro) {
  $signos = @simplexml_load_file("signos.xml");
  if (!$signos) $erro = "Não foi possível carregar o arquivo de signos.";
}

function parseDiaMesParaDateTimeBase(string $diaMes, int $anoBase = 2000): ?DateTime {
  [$dia, $mes] = explode('/', $diaMes);
  try {
    return new DateTime(sprintf('%04d-%02d-%02d', $anoBase, (int)$mes, (int)$dia));
  } catch (Exception $e) { 
    return null; 
  }
}

function dataNascimentoNoAnoBase(DateTime $nasc, DateTime $inicioBase): DateTime {
  $anoBase = (int)$inicioBase->format('Y');
  return DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $anoBase, (int)$nasc->format('m'), (int)$nasc->format('d')));
}

if (!$erro) {
  foreach ($signos->signo as $signo) {
    $ini  = parseDiaMesParaDateTimeBase((string)$signo->dataInicio, 2000);
    $fim  = parseDiaMesParaDateTimeBase((string)$signo->dataFim, 2000);
    
    if ((int)$fim->format('md') < (int)$ini->format('md')) {
      $fim->modify('+1 year');
    }
    
    $dnBase = dataNascimentoNoAnoBase($data_nascimento_dt, $ini);
    
    if ((int)$fim->format('Y') === (int)$ini->format('Y') + 1 && (int)$dnBase->format('md') < (int)$ini->format('md')) {
      $dnBase->modify('+1 year');
    }
    
    if ($dnBase >= $ini && $dnBase <= $fim) {
      $signo_encontrado = [
        'nome' => (string)$signo->signoNome,
        'descricao' => (string)$signo->descricao,
        'inicio' => (string)$signo->dataInicio,
        'fim' => (string)$signo->dataFim
      ];
      break;
    }
  }
}
?>

<main class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 120px);">
  <div class="row justify-content-center w-100">
    <div class="col-lg-6 col-md-7 col-sm-9">
      <?php if ($erro): ?>
        <div class="alert alert-danger text-center">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <?= htmlspecialchars($erro) ?>
        </div>
        <div class="text-center">
          <a href="index.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left me-2"></i>Voltar ao início
          </a>
        </div>
      <?php elseif ($signo_encontrado): ?>
        <div class="card shadow-lg border-0">
          <div class="card-body p-4 text-center">
            <div class="signo-header mb-3">
              <h1 class="h2 fw-bold text-primary mb-3">
                <?= htmlspecialchars($signo_encontrado['nome']); ?>
              </h1>
              <div class="signo-image mb-3">
                <img src="assets/img/<?= htmlspecialchars($signo_encontrado['nome']); ?>.png" 
                     alt="Signo <?= htmlspecialchars($signo_encontrado['nome']); ?>"
                     class="img-fluid rounded-circle shadow"
                     style="max-width: 150px; height: auto;">
              </div>
            </div>
            
            <div class="signo-info">
              <div class="periodo-badge mb-3">
                <span class="badge bg-info fs-6 px-3 py-2">
                  <i class="bi bi-calendar-event me-2"></i>
                  Período: <?= $signo_encontrado['inicio']; ?> a <?= $signo_encontrado['fim']; ?>
                </span>
              </div>
              
              <div class="descricao-signo">
                <p class="lead fs-6 text-muted mb-3">
                  <?= htmlspecialchars($signo_encontrado['descricao']); ?>
                </p>
              </div>
              
              <div class="data-nascimento-info mb-3">
                <small class="text-muted">
                  <i class="bi bi-calendar-check me-1"></i>
                  Data informada: <?= $data_nascimento_dt->format('d/m/Y'); ?>
                </small>
              </div>
            </div>
            
            <div class="actions mt-3">
              <a href="index.php" class="btn btn-primary btn-lg px-3 py-2">
                <i class="bi bi-arrow-left me-2"></i>Consultar outro signo
              </a>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="alert alert-warning text-center">
          <i class="bi bi-question-circle-fill me-2"></i>
          Não encontramos um signo correspondente para a data informada.
        </div>
        <div class="text-center">
          <a href="index.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left me-2"></i>Voltar ao início
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
