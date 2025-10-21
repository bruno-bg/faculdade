
<?php
function start_session(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
}

function login(PDO $pdo, string $email, string $password): bool {
  $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role_id,is_active FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  if (!$u || !$u['is_active']) return false;
  if (!password_verify($password, $u['password_hash'])) return false;

  start_session();
  $_SESSION['user'] = [
    'id' => (int)$u['id'],
    'name' => $u['name'],
    'email' => $u['email'],
    'role_id' => (int)$u['role_id']
  ];
  return true;
}

function require_login(): void {
  start_session();
  if (empty($_SESSION['user'])) {
    header('Location: login.php'); exit;
  }
}

function has_role(int $roleId): bool {
  start_session();
  return !empty($_SESSION['user']) && (int)$_SESSION['user']['role_id'] <= $roleId;
}
// 1=admin, 2=coordenadora, 3=voluntaria, 4=professora
