
<?php
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/lib/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $action = $_POST['action'] ?? 'create';
    
    if ($action === 'create') {
      // Criar novo livro
      $title = trim($_POST['title'] ?? '');
      if (empty($title)) {
        throw new Exception('Título do livro é obrigatório');
      }
      
      $stmt = $pdo->prepare("INSERT INTO books (title, author, category, qty, notes) VALUES (?,?,?,?,?)");
      $stmt->execute([
        $title,
        trim($_POST['author'] ?? ''),
        trim($_POST['category'] ?? ''),
        (int)($_POST['qty'] ?? 1),
        trim($_POST['notes'] ?? '')
      ]);
      
      header('Location: livros.php?success=1');
      exit;
      
    } elseif ($action === 'update') {
      // Atualizar livro existente
      $id = (int)($_POST['id'] ?? 0);
      $title = trim($_POST['title'] ?? '');
      
      if ($id <= 0) {
        throw new Exception('ID do livro inválido');
      }
      
      if (empty($title)) {
        throw new Exception('Título do livro é obrigatório');
      }
      
      $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, qty=?, notes=? WHERE id=?");
      $stmt->execute([
        $title,
        trim($_POST['author'] ?? ''),
        trim($_POST['category'] ?? ''),
        (int)($_POST['qty'] ?? 1),
        trim($_POST['notes'] ?? ''),
        $id
      ]);
      
      header('Location: livros.php?success=2');
      exit;
      
    } elseif ($action === 'delete') {
      // Excluir livro
      $id = (int)($_POST['id'] ?? 0);
      
      if ($id <= 0) {
        throw new Exception('ID do livro inválido');
      }
      
      // Verificar se há sessões vinculadas
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM reading_sessions WHERE book_id = ?");
      $stmt->execute([$id]);
      $count = $stmt->fetchColumn();
      
      if ($count > 0) {
        // Verificar se o usuário quer excluir em cascata
        $cascade = $_POST['cascade'] ?? false;
        
        if (!$cascade) {
          // Mostrar quantas sessões estão vinculadas
          $stmt = $pdo->prepare("SELECT COUNT(*) as total, GROUP_CONCAT(DISTINCT DATE_FORMAT(date, '%d/%m/%Y') ORDER BY date DESC SEPARATOR ', ') as datas FROM reading_sessions WHERE book_id = ?");
          $stmt->execute([$id]);
          $sessoes_info = $stmt->fetch();
          
          throw new Exception("Não é possível excluir este livro pois há {$sessoes_info['total']} sessão(ões) de leitura vinculadas a ele. Datas: {$sessoes_info['datas']}. Primeiro exclua as sessões ou entre em contato com o administrador.");
        } else {
          // Excluir sessões primeiro (cascata)
          $stmt = $pdo->prepare("DELETE FROM reading_sessions WHERE book_id = ?");
          $stmt->execute([$id]);
          $sessoes_excluidas = $stmt->rowCount();
          
          // Log da operação de cascata
          error_log("Exclusão em cascata: $sessoes_excluidas sessões excluídas do livro ID $id");
        }
      }
      
      // Executar exclusão com tratamento de erro específico para foreign key
      try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
          throw new Exception('Livro não encontrado ou já foi excluído.');
        }
      } catch (PDOException $e) {
        // Verificar se é erro de foreign key constraint
        if ($e->getCode() == 23000 && strpos($e->getMessage(), 'foreign key constraint') !== false) {
          throw new Exception('Não é possível excluir este livro pois há dados vinculados a ele. Verifique se não há sessões de leitura ou outros registros associados.');
        }
        // Re-lançar outros erros
        throw $e;
      }
      
      header('Location: livros.php?success=3');
      exit;
    }
    
  } catch (Exception $e) {
    $error_message = $e->getMessage();
    $error_encoded = urlencode($error_message);
    header('Location: livros.php?error=' . $error_encoded);
    exit;
  }
}
$books = $pdo->query("SELECT * FROM books ORDER BY title")->fetchAll();

// Verificar mensagens de sucesso/erro
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
  switch ($_GET['success']) {
    case '1':
      $success_message = 'Livro cadastrado com sucesso!';
      break;
    case '2':
      $success_message = 'Livro atualizado com sucesso!';
      break;
    case '3':
      $success_message = 'Livro excluído com sucesso!';
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
      <i class="bi bi-book text-primary me-2"></i>
      Livros Cadastrados
    </h1>
    <p class="text-muted mb-0">Gerencie o acervo de livros utilizados nas sessões de leitura</p>
  </div>
  <button class="btn btn-primary btn-lg" onclick="novoLivro()" title="Adicionar novo livro">
    <i class="bi bi-plus-lg me-2"></i> Novo Livro
  </button>
</div>

<?php if($books): ?>
<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-primary">
      <tr>
        <th><i class="bi bi-book me-1"></i> Título</th>
        <th><i class="bi bi-person me-1"></i> Autor</th>
        <th><i class="bi bi-tag me-1"></i> Categoria</th>
        <th class="text-center"><i class="bi bi-hash me-1"></i> Quantidade</th>
        <th class="text-center">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($books as $b): ?>
        <tr class="fade-in">
          <td>
            <strong><?= htmlspecialchars($b['title']) ?></strong>
            <?php if($b['notes']): ?>
              <br><small class="text-muted"><?= htmlspecialchars($b['notes']) ?></small>
            <?php endif; ?>
          </td>
          <td>
            <?= htmlspecialchars($b['author']) ?: '<span class="text-muted">Não informado</span>' ?>
          </td>
          <td>
            <?php if($b['category']): ?>
              <span class="badge bg-light text-dark"><?= htmlspecialchars($b['category']) ?></span>
            <?php else: ?>
              <span class="text-muted">Sem categoria</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <span class="badge bg-info fs-6"><?= (int)$b['qty'] ?></span>
          </td>
          <td class="text-center">
            <div class="btn-group" role="group">
              <button class="btn btn-outline-primary btn-sm" title="Editar livro" onclick="editLivro(<?= $b['id'] ?>, '<?= htmlspecialchars($b['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($b['author'], ENT_QUOTES) ?>', '<?= htmlspecialchars($b['category'], ENT_QUOTES) ?>', <?= (int)$b['qty'] ?>, '<?= htmlspecialchars($b['notes'], ENT_QUOTES) ?>')">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-outline-danger btn-sm" title="Excluir livro" onclick="deleteLivro(<?= $b['id'] ?>, '<?= htmlspecialchars($b['title'], ENT_QUOTES) ?>')">
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
  <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
  <h3 class="text-muted mt-3">Nenhum livro cadastrado</h3>
  <p class="text-muted">Comece adicionando o primeiro livro ao acervo</p>
  <button class="btn btn-primary btn-lg" onclick="novoLivro()">
    <i class="bi bi-plus-lg me-2"></i> Cadastrar Primeiro Livro
  </button>
</div>
<?php endif; ?>

<!-- Modal para Novo/Editar Livro -->
<div class="modal fade" id="modalLivro" tabindex="-1" aria-labelledby="modalLivroLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content" id="formLivro">
      <input type="hidden" name="action" id="actionLivro" value="create">
      <input type="hidden" name="id" id="idLivro" value="">
      
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalLivroLabel">
          <i class="bi bi-book me-2"></i><span id="tituloModalLivro">Cadastrar Novo Livro</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      
      <div class="modal-body p-4">
        <div class="row">
          <div class="col-md-12 mb-3">
            <label for="tituloLivro" class="form-label">
              <i class="bi bi-book me-1"></i>Título do Livro *
            </label>
            <input type="text" name="title" id="tituloLivro" class="form-control form-control-lg" 
                   placeholder="Digite o título do livro" required autofocus>
            <div class="form-text">Título completo do livro</div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="autorLivro" class="form-label">
              <i class="bi bi-person me-1"></i>Autor
            </label>
            <input type="text" name="author" id="autorLivro" class="form-control" 
                   placeholder="Nome do autor">
            <div class="form-text">Autor do livro</div>
          </div>
          
          <div class="col-md-6 mb-3">
            <label for="categoriaLivro" class="form-label">
              <i class="bi bi-tag me-1"></i>Categoria
            </label>
            <input type="text" name="category" id="categoriaLivro" class="form-control" 
                   placeholder="Categoria do livro">
            <div class="form-text">Ex: Infantil, Educativo, etc.</div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="quantidadeLivro" class="form-label">
              <i class="bi bi-hash me-1"></i>Quantidade
            </label>
            <input type="number" name="qty" id="quantidadeLivro" class="form-control" 
                   min="1" value="1" required>
            <div class="form-text">Quantidade de exemplares</div>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="observacoesLivro" class="form-label">
            <i class="bi bi-chat-text me-1"></i>Observações
          </label>
          <textarea name="notes" id="observacoesLivro" class="form-control" rows="3" 
                    placeholder="Informações adicionais sobre o livro..."></textarea>
          <div class="form-text">Informações complementares (opcional)</div>
        </div>
      </div>
      
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-success btn-lg" id="btnSubmitLivro">
          <i class="bi bi-check-lg me-1"></i><span id="textoBtnLivro">Cadastrar Livro</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Função para abrir modal de novo livro
function novoLivro() {
  document.getElementById('actionLivro').value = 'create';
  document.getElementById('idLivro').value = '';
  document.getElementById('tituloModalLivro').textContent = 'Cadastrar Novo Livro';
  document.getElementById('textoBtnLivro').textContent = 'Cadastrar Livro';
  document.getElementById('formLivro').reset();
  
  const modal = new bootstrap.Modal(document.getElementById('modalLivro'));
  modal.show();
}

// Função para editar livro
function editLivro(id, title, author, category, qty, notes) {
  document.getElementById('actionLivro').value = 'update';
  document.getElementById('idLivro').value = id;
  document.getElementById('tituloModalLivro').textContent = 'Editar Livro';
  document.getElementById('textoBtnLivro').textContent = 'Salvar Alterações';
  
  document.getElementById('tituloLivro').value = title;
  document.getElementById('autorLivro').value = author;
  document.getElementById('categoriaLivro').value = category;
  document.getElementById('quantidadeLivro').value = qty;
  document.getElementById('observacoesLivro').value = notes;
  
  const modal = new bootstrap.Modal(document.getElementById('modalLivro'));
  modal.show();
}

// Função para excluir livro
function deleteLivro(id, title) {
  console.log('deleteLivro chamada para ID:', id, 'Título:', title);
  
  // Primeiro verificar se há sessões vinculadas
  fetch('check_book_sessions.php', {
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
        html: `O livro "${title}" possui <strong>${data.count}</strong> sessão(ões) de leitura vinculadas.<br><br>
               <strong>Opções:</strong><br>
               • <strong>Excluir apenas o livro</strong> (recomendado)<br>
               • <strong>Excluir livro e todas as sessões</strong> (cuidado!)`,
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Excluir apenas livro',
        denyButtonText: 'Excluir tudo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        denyButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          // Tentar excluir apenas o livro (vai dar erro se houver constraint)
          submitDeleteBook(id, false);
        } else if (result.isDenied) {
          // Excluir em cascata
          Swal.fire({
            title: 'Confirmação Final',
            text: `Tem CERTEZA que deseja excluir o livro "${title}" e TODAS as ${data.count} sessões vinculadas? Esta ação não pode ser desfeita!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir TUDO!',
            cancelButtonText: 'Cancelar'
          }).then((finalResult) => {
            if (finalResult.isConfirmed) {
              submitDeleteBook(id, true);
            }
          });
        }
      });
    } else {
      // Não há sessões - exclusão normal
      Swal.fire({
        title: 'Tem certeza?',
        text: `Tem certeza que deseja excluir o livro "${title}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          submitDeleteBook(id, false);
        }
      });
    }
  })
  .catch(error => {
    console.error('Erro ao verificar sessões:', error);
    // Fallback para exclusão normal
    Swal.fire({
      title: 'Tem certeza?',
      text: `Tem certeza que deseja excluir o livro "${title}"?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, excluir!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        submitDeleteBook(id, false);
      }
    });
  });
}

// Função auxiliar para submeter a exclusão do livro
function submitDeleteBook(id, cascade) {
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Limpar formulário quando modal for fechado
  document.getElementById('modalLivro').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formLivro').reset();
  });
});
</script>

<?php include __DIR__ . '/../src/views/partials/footer.php'; ?>
