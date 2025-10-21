
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $action = $_POST['action'] ?? 'create';
    
    if ($action === 'create') {
      // Criar nova creche
      $name = trim($_POST['name'] ?? '');
      if (empty($name)) {
        throw new Exception('Nome da creche é obrigatório');
      }
      
      $stmt = $pdo->prepare("INSERT INTO creches (name, district, contact, notes) VALUES (?,?,?,?)");
      $stmt->execute([
        $name,
        trim($_POST['district'] ?? ''),
        trim($_POST['contact'] ?? ''),
        trim($_POST['notes'] ?? '')
      ]);
      
      header('Location: creches.php?success=1');
      exit;
      
    } elseif ($action === 'update') {
      // Atualizar creche existente
      $id = (int)($_POST['id'] ?? 0);
      $name = trim($_POST['name'] ?? '');
      
      if ($id <= 0) {
        throw new Exception('ID da creche inválido');
      }
      
      if (empty($name)) {
        throw new Exception('Nome da creche é obrigatório');
      }
      
      $stmt = $pdo->prepare("UPDATE creches SET name=?, district=?, contact=?, notes=? WHERE id=?");
      $stmt->execute([
        $name,
        trim($_POST['district'] ?? ''),
        trim($_POST['contact'] ?? ''),
        trim($_POST['notes'] ?? ''),
        $id
      ]);
      
      header('Location: creches.php?success=2');
      exit;
      
    } elseif ($action === 'delete') {
      // Excluir creche
      $id = (int)($_POST['id'] ?? 0);
      
      if ($id <= 0) {
        throw new Exception('ID da creche inválido');
      }
      
      // Verificar se há sessões vinculadas
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM reading_sessions WHERE creche_id = ?");
      $stmt->execute([$id]);
      $count = $stmt->fetchColumn();
      
      if ($count > 0) {
        // Verificar se o usuário quer excluir em cascata
        $cascade = $_POST['cascade'] ?? false;
        
        if (!$cascade) {
          // Mostrar quantas sessões estão vinculadas
          $stmt = $pdo->prepare("SELECT COUNT(*) as total, GROUP_CONCAT(DISTINCT DATE_FORMAT(date, '%d/%m/%Y') ORDER BY date DESC SEPARATOR ', ') as datas FROM reading_sessions WHERE creche_id = ?");
          $stmt->execute([$id]);
          $sessoes_info = $stmt->fetch();
          
          throw new Exception("Não é possível excluir esta creche pois há {$sessoes_info['total']} sessão(ões) de leitura vinculadas a ela. Datas: {$sessoes_info['datas']}. Primeiro exclua as sessões ou entre em contato com o administrador.");
        } else {
          // Excluir sessões primeiro (cascata)
          $stmt = $pdo->prepare("DELETE FROM reading_sessions WHERE creche_id = ?");
          $stmt->execute([$id]);
          $sessoes_excluidas = $stmt->rowCount();
          
          // Log da operação de cascata
          error_log("Exclusão em cascata: $sessoes_excluidas sessões excluídas da creche ID $id");
        }
      }
      
      // Executar exclusão com tratamento de erro específico para foreign key
      try {
        $stmt = $pdo->prepare("DELETE FROM creches WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
          throw new Exception('Creche não encontrada ou já foi excluída.');
        }
      } catch (PDOException $e) {
        // Verificar se é erro de foreign key constraint
        if ($e->getCode() == 23000 && strpos($e->getMessage(), 'foreign key constraint') !== false) {
          throw new Exception('Não é possível excluir esta creche pois há dados vinculados a ela. Verifique se não há sessões de leitura, turmas ou outros registros associados.');
        }
        // Re-lançar outros erros
        throw $e;
      }
      
      header('Location: creches.php?success=3');
      exit;
    }
    
  } catch (Exception $e) {
    $error_message = $e->getMessage();
    $error_encoded = urlencode($error_message);
    header('Location: creches.php?error=' . $error_encoded);
    exit;
  }
}
$creches = $pdo->query("SELECT * FROM creches ORDER BY name")->fetchAll();

// Verificar mensagens de sucesso/erro
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
  switch ($_GET['success']) {
    case '1':
      $success_message = 'Creche cadastrada com sucesso!';
      break;
    case '2':
      $success_message = 'Creche atualizada com sucesso!';
      break;
    case '3':
      $success_message = 'Creche excluída com sucesso!';
      break;
  }
}

if (isset($_GET['error'])) {
  $error_message = urldecode($_GET['error']);
}

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
      <i class="bi bi-building text-primary me-2"></i>
      Creches Cadastradas
    </h1>
    <p class="text-muted mb-0">Gerencie as creches onde as sessões de leitura são realizadas</p>
  </div>
  <button class="btn btn-primary btn-lg" onclick="novaCreche()" title="Adicionar nova creche">
    <i class="bi bi-plus-lg me-2"></i> Nova Creche
  </button>
</div>

<?php if($creches): ?>
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-primary">
      <tr>
        <th><i class="bi bi-building me-1"></i> Nome da Creche</th>
        <th><i class="bi bi-geo-alt me-1"></i> Bairro</th>
        <th><i class="bi bi-telephone me-1"></i> Contato</th>
        <th><i class="bi bi-chat-text me-1"></i> Observações</th>
        <th class="text-center">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($creches as $c): ?>
        <tr class="fade-in">
          <td>
            <strong><?= htmlspecialchars($c['name']) ?></strong>
          </td>
          <td>
            <span class="badge bg-light text-dark">
              <?= htmlspecialchars($c['district']) ?: 'Não informado' ?>
            </span>
          </td>
          <td>
            <?php if($c['contact']): ?>
              <i class="bi bi-telephone-fill text-success me-1"></i>
              <?= htmlspecialchars($c['contact']) ?>
            <?php else: ?>
              <span class="text-muted">Não informado</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($c['notes']): ?>
              <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?= htmlspecialchars($c['notes']) ?>">
                <?= htmlspecialchars($c['notes']) ?>
              </span>
            <?php else: ?>
              <span class="text-muted">Sem observações</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button class="btn btn-outline-primary btn-sm" title="Editar creche" onclick="editCreche(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['district'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['contact'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['notes'], ENT_QUOTES) ?>')">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" title="Excluir creche" onclick="deleteCreche(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>')">
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
  <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
  <h3 class="text-muted mt-3">Nenhuma creche cadastrada</h3>
  <p class="text-muted">Comece adicionando a primeira creche ao sistema</p>
  <button class="btn btn-primary btn-lg" onclick="novaCreche()">
    <i class="bi bi-plus-lg me-2"></i> Cadastrar Primeira Creche
  </button>
</div>
<?php endif; ?>

<!-- Modal para Nova/Editar Creche -->
<div class="modal fade" id="modalCreche" tabindex="-1" aria-labelledby="modalCrecheLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content" id="formCreche">
      <input type="hidden" name="action" id="actionCreche" value="create">
      <input type="hidden" name="id" id="idCreche" value="">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalCrecheLabel">
          <i class="bi bi-building me-2"></i><span id="tituloModal">Cadastrar Nova Creche</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      
      <div class="modal-body p-4">
        <div class="row">
          <div class="col-md-12 mb-3">
            <label for="nomeCreche" class="form-label">
              <i class="bi bi-building me-1"></i>Nome da Creche *
            </label>
            <input type="text" name="name" id="nomeCreche" class="form-control form-control-lg" 
                   placeholder="Digite o nome da creche" required autofocus>
            <div class="form-text">Nome completo da instituição</div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="bairroCreche" class="form-label">
              <i class="bi bi-geo-alt me-1"></i>Bairro
            </label>
            <input type="text" name="district" id="bairroCreche" class="form-control" 
                   placeholder="Nome do bairro">
            <div class="form-text">Bairro onde a creche está localizada</div>
          </div>
          
          <div class="col-md-6 mb-3">
            <label for="contatoCreche" class="form-label">
              <i class="bi bi-telephone me-1"></i>Contato
            </label>
            <input type="text" name="contact" id="contatoCreche" class="form-control" 
                   placeholder="Telefone ou email">
            <div class="form-text">Telefone ou email para contato</div>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="observacoesCreche" class="form-label">
            <i class="bi bi-chat-text me-1"></i>Observações
          </label>
          <textarea name="notes" id="observacoesCreche" class="form-control" rows="3" 
                    placeholder="Informações adicionais sobre a creche..."></textarea>
          <div class="form-text">Informações complementares (opcional)</div>
        </div>
      </div>
      
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary btn-lg" id="btnSubmitCreche">
          <i class="bi bi-check-lg me-1"></i><span id="textoBtn">Cadastrar Creche</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Aguardar o carregamento completo da página
document.addEventListener('DOMContentLoaded', function() {
  // Limpar formulário quando modal for fechado
  document.getElementById('modalCreche').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formCreche').reset();
  });
});

// Função para abrir modal de nova creche
function novaCreche() {
  document.getElementById('actionCreche').value = 'create';
  document.getElementById('idCreche').value = '';
  document.getElementById('tituloModal').textContent = 'Cadastrar Nova Creche';
  document.getElementById('textoBtn').textContent = 'Cadastrar Creche';
  document.getElementById('formCreche').reset();
  
  const modal = new bootstrap.Modal(document.getElementById('modalCreche'));
  modal.show();
}

// Função para editar creche
function editCreche(id, name, district, contact, notes) {
  document.getElementById('actionCreche').value = 'update';
  document.getElementById('idCreche').value = id;
  document.getElementById('tituloModal').textContent = 'Editar Creche';
  document.getElementById('textoBtn').textContent = 'Salvar Alterações';
  
  document.getElementById('nomeCreche').value = name;
  document.getElementById('bairroCreche').value = district;
  document.getElementById('contatoCreche').value = contact;
  document.getElementById('observacoesCreche').value = notes;
  
  const modal = new bootstrap.Modal(document.getElementById('modalCreche'));
  modal.show();
}

// Função para excluir creche
function deleteCreche(id, name) {
  console.log('deleteCreche chamada para ID:', id, 'Nome:', name);
  
  // Primeiro verificar se há sessões vinculadas
  fetch('check_creche_sessions.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.hasSessions) {
      // Há sessões vinculadas - mostrar opção de cascata
      Swal.fire({
        title: 'Atenção!',
        html: `A creche "${name}" possui <strong>${data.count}</strong> sessão(ões) de leitura vinculadas.<br><br>
               <strong>Opções:</strong><br>
               • <strong>Excluir apenas a creche</strong> (recomendado)<br>
               • <strong>Excluir creche e todas as sessões</strong> (cuidado!)`,
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Excluir apenas creche',
        denyButtonText: 'Excluir tudo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        denyButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          // Tentar excluir apenas a creche (vai dar erro se houver constraint)
          submitDelete(id, false);
        } else if (result.isDenied) {
          // Excluir em cascata
          Swal.fire({
            title: 'Confirmação Final',
            text: `Tem CERTEZA que deseja excluir a creche "${name}" e TODAS as ${data.count} sessões vinculadas? Esta ação não pode ser desfeita!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir TUDO!',
            cancelButtonText: 'Cancelar'
          }).then((finalResult) => {
            if (finalResult.isConfirmed) {
              submitDelete(id, true);
            }
          });
        }
      });
    } else {
      // Não há sessões - exclusão normal
      Swal.fire({
        title: 'Tem certeza?',
        text: `Tem certeza que deseja excluir a creche "${name}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          submitDelete(id, false);
        }
      });
    }
  })
  .catch(error => {
    console.error('Erro ao verificar sessões:', error);
    // Fallback para exclusão normal
    Swal.fire({
      title: 'Tem certeza?',
      text: `Tem certeza que deseja excluir a creche "${name}"?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, excluir!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        submitDelete(id, false);
      }
    });
  });
}

// Função auxiliar para submeter a exclusão
function submitDelete(id, cascade) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.innerHTML = `
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="${id}">
    ${cascade ? '<input type="hidden" name="cascade" value="1">' : ''}
  `;
  document.body.appendChild(form);
  form.submit();
}
</script>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
