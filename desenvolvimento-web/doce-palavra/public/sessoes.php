
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

// Carrega dados necessários
$creches = $pdo->query("SELECT id,name FROM creches ORDER BY name")->fetchAll();
$books   = $pdo->query("SELECT id,title FROM books ORDER BY title")->fetchAll();

// Verificar mensagens de sucesso/erro
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
  switch ($_GET['success']) {
    case '1':
      $success_message = 'Sessão registrada com sucesso!';
      break;
    case '2':
      $success_message = 'Sessão atualizada com sucesso!';
      break;
    case '3':
      $success_message = 'Sessão excluída com sucesso!';
      break;
  }
}

if (isset($_GET['error'])) {
  $error_message = urldecode($_GET['error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $action = $_POST['action'] ?? 'create';
    
    if ($action === 'create') {
      // Criar nova sessão
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
      
      header('Location: sessoes.php?success=1');
      exit;
      
    } elseif ($action === 'update') {
      // Atualizar sessão existente
      $id = (int)($_POST['id'] ?? 0);
      $date = $_POST['date'] ?? date('Y-m-d');
      $creche_id = (int)($_POST['creche_id'] ?? 0);
      $book_id   = !empty($_POST['book_id']) ? (int)$_POST['book_id'] : null;
      $audience  = (int)($_POST['audience_estimate'] ?? 0);
      $notes     = trim($_POST['notes'] ?? '');
      
      if ($id <= 0) {
        throw new Exception('ID da sessão inválido');
      }
      
      if ($creche_id <= 0) {
        throw new Exception('Selecione uma creche.');
      }
      
      $stmt = $pdo->prepare("UPDATE reading_sessions SET date=?, creche_id=?, book_id=?, audience_estimate=?, notes=? WHERE id=?");
      $stmt->execute([$date, $creche_id, $book_id, $audience, $notes, $id]);
      
      header('Location: sessoes.php?success=2');
      exit;
      
    } elseif ($action === 'delete') {
      // Excluir sessão
      $id = (int)($_POST['id'] ?? 0);
      
      if ($id <= 0) {
        throw new Exception('ID da sessão inválido');
      }
      
      $stmt = $pdo->prepare("DELETE FROM reading_sessions WHERE id = ?");
      $stmt->execute([$id]);
      
      header('Location: sessoes.php?success=3');
      exit;
    }
    
  } catch (Throwable $e) {
    $error_message = $e->getMessage();
    $error_encoded = urlencode($error_message);
    header('Location: sessoes.php?error=' . $error_encoded);
    exit;
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

<?php if ($success_message): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    <?= htmlspecialchars($success_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if ($error_message): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= htmlspecialchars($error_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 mb-2">
      <i class="bi bi-book-open text-primary me-2"></i>
      Sessões de Leitura
    </h1>
    <p class="text-muted mb-0">Registre e gerencie as sessões de leitura realizadas</p>
  </div>
  <button class="btn btn-primary btn-lg" onclick="novaSessao()" title="Registrar nova sessão">
    <i class="bi bi-plus-lg me-2"></i> Nova Sessão
  </button>
</div>

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

<?php if($recent): ?>
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-primary">
      <tr>
        <th><i class="bi bi-calendar me-1"></i> Data</th>
        <th><i class="bi bi-building me-1"></i> Creche</th>
        <th><i class="bi bi-book me-1"></i> Livro</th>
        <th class="text-center"><i class="bi bi-people me-1"></i> Crianças</th>
        <th class="text-center">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recent as $r): ?>
        <tr class="fade-in">
          <td>
            <strong><?= date('d/m/Y', strtotime($r['date'])) ?></strong>
          </td>
          <td><?= htmlspecialchars($r['creche']) ?></td>
          <td>
            <?php if($r['book']): ?>
              <span class="badge bg-light text-dark"><?= htmlspecialchars($r['book']) ?></span>
            <?php else: ?>
              <span class="text-muted">Sem livro</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <span class="badge bg-info fs-6"><?= (int)$r['audience_estimate'] ?></span>
          </td>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button class="btn btn-outline-primary btn-sm" title="Editar sessão" onclick="editSessao(<?= $r['id'] ?>, '<?= $r['date'] ?>', <?= $r['creche_id'] ?? 0 ?>, <?= $r['book_id'] ?? 'null' ?>, <?= (int)$r['audience_estimate'] ?>, '<?= htmlspecialchars($r['notes'] ?? '', ENT_QUOTES) ?>')">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" title="Excluir sessão" onclick="deleteSessao(<?= $r['id'] ?>, '<?= htmlspecialchars($r['creche'], ENT_QUOTES) ?>', '<?= $r['date'] ?>')">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="text-center py-5">
  <i class="bi bi-book-open text-muted" style="font-size: 4rem;"></i>
  <h3 class="text-muted mt-3">Nenhuma sessão registrada</h3>
  <p class="text-muted">Comece registrando a primeira sessão de leitura</p>
  <button class="btn btn-primary btn-lg" onclick="novaSessao()">
    <i class="bi bi-plus-lg me-2"></i> Registrar Primeira Sessão
  </button>
</div>
<?php endif; ?>

<!-- Modal para Nova/Editar Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1" aria-labelledby="modalSessaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content" id="formSessao">
      <input type="hidden" name="action" id="actionSessao" value="create">
      <input type="hidden" name="id" id="idSessao" value="">
      
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalSessaoLabel">
          <i class="bi bi-book-open me-2"></i><span id="tituloModalSessao">Registrar Nova Sessão</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      
      <div class="modal-body p-4">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="dataSessao" class="form-label">
              <i class="bi bi-calendar me-1"></i>Data da Sessão *
            </label>
            <input type="date" name="date" id="dataSessao" class="form-control form-control-lg" 
                   value="<?= date('Y-m-d') ?>" required autofocus>
            <div class="form-text">Data em que a sessão foi realizada</div>
          </div>
          
          <div class="col-md-6 mb-3">
            <label for="crecheSessao" class="form-label">
              <i class="bi bi-building me-1"></i>Creche *
            </label>
            <select name="creche_id" id="crecheSessao" class="form-select form-select-lg" required>
              <option value="">Selecione uma creche</option>
              <?php foreach($creches as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Creche onde a sessão foi realizada</div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="livroSessao" class="form-label">
              <i class="bi bi-book me-1"></i>Livro
            </label>
            <select name="book_id" id="livroSessao" class="form-select">
              <option value="">Selecione um livro (opcional)</option>
              <?php foreach($books as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title']) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Livro utilizado na sessão (opcional)</div>
          </div>
          
          <div class="col-md-6 mb-3">
            <label for="criancasSessao" class="form-label">
              <i class="bi bi-people me-1"></i>Número de Crianças
            </label>
            <input type="number" name="audience_estimate" id="criancasSessao" class="form-control" 
                   min="0" value="0">
            <div class="form-text">Quantidade de crianças presentes</div>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="observacoesSessao" class="form-label">
            <i class="bi bi-chat-text me-1"></i>Observações
          </label>
          <textarea name="notes" id="observacoesSessao" class="form-control" rows="3" 
                    placeholder="Descreva como foi a sessão, reações das crianças, atividades realizadas..."></textarea>
          <div class="form-text">Informações sobre como foi a sessão (opcional)</div>
        </div>
      </div>
      
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-info btn-lg" id="btnSubmitSessao">
          <i class="bi bi-check-lg me-1"></i><span id="textoBtnSessao">Registrar Sessão</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Função para abrir modal de nova sessão
function novaSessao() {
  document.getElementById('actionSessao').value = 'create';
  document.getElementById('idSessao').value = '';
  document.getElementById('tituloModalSessao').textContent = 'Registrar Nova Sessão';
  document.getElementById('textoBtnSessao').textContent = 'Registrar Sessão';
  document.getElementById('formSessao').reset();
  document.getElementById('dataSessao').value = '<?= date('Y-m-d') ?>';
  
  const modal = new bootstrap.Modal(document.getElementById('modalSessao'));
  modal.show();
}

// Função para editar sessão
function editSessao(id, date, creche_id, book_id, audience, notes) {
  document.getElementById('actionSessao').value = 'update';
  document.getElementById('idSessao').value = id;
  document.getElementById('tituloModalSessao').textContent = 'Editar Sessão';
  document.getElementById('textoBtnSessao').textContent = 'Salvar Alterações';
  
  document.getElementById('dataSessao').value = date;
  document.getElementById('crecheSessao').value = creche_id;
  document.getElementById('livroSessao').value = book_id || '';
  document.getElementById('criancasSessao').value = audience;
  document.getElementById('observacoesSessao').value = notes || '';
  
  const modal = new bootstrap.Modal(document.getElementById('modalSessao'));
  modal.show();
}

// Função para excluir sessão
function deleteSessao(id, creche, date) {
  console.log('deleteSessao chamada para ID:', id, 'Creche:', creche, 'Data:', date);
  
  const dataFormatada = new Date(date).toLocaleDateString('pt-BR');
  
  // Verificar se confirmDelete existe
  if (typeof confirmDelete === 'function') {
    console.log('confirmDelete encontrada');
    confirmDelete(`Tem certeza que deseja excluir a sessão da creche "${creche}" do dia ${dataFormatada}?`, function() {
      console.log('Callback de exclusão executado');
      const form = document.createElement('form');
      form.method = 'POST';
      form.innerHTML = `
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="${id}">
      `;
      document.body.appendChild(form);
      form.submit();
    });
  } else {
    console.log('confirmDelete não encontrada, usando SweetAlert2 diretamente');
    Swal.fire({
      title: 'Tem certeza?',
      text: `Tem certeza que deseja excluir a sessão da creche "${creche}" do dia ${dataFormatada}?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sim, excluir!',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Limpar formulário quando modal for fechado
  document.getElementById('modalSessao').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formSessao').reset();
  });
});
</script>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
