
CREATE DATABASE IF NOT EXISTS doce_palavra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doce_palavra;

CREATE TABLE IF NOT EXISTS roles (
  id TINYINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(30) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id TINYINT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS creches (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  district VARCHAR(120),
  contact VARCHAR(120),
  notes TEXT
);

CREATE TABLE IF NOT EXISTS turmas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  creche_id INT NOT NULL,
  name VARCHAR(80) NOT NULL,
  age_range VARCHAR(40),
  FOREIGN KEY (creche_id) REFERENCES creches(id)
);

CREATE TABLE IF NOT EXISTS books (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(120),
  category VARCHAR(80),
  qty INT NOT NULL DEFAULT 1,
  notes TEXT
);

CREATE TABLE IF NOT EXISTS reading_sessions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  date DATE NOT NULL,
  creche_id INT NOT NULL,
  turma_id INT,
  book_id INT,
  audience_estimate INT DEFAULT 0,
  notes TEXT,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creche_id) REFERENCES creches(id),
  FOREIGN KEY (turma_id) REFERENCES turmas(id),
  FOREIGN KEY (book_id) REFERENCES books(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS session_participants (
  session_id INT NOT NULL,
  user_id INT NOT NULL,
  role_in_session ENUM('contacao','apoio','professora') DEFAULT 'apoio',
  PRIMARY KEY (session_id, user_id),
  FOREIGN KEY (session_id) REFERENCES reading_sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT IGNORE INTO roles (id, name) VALUES (1,'admin'), (2,'coordenadora'), (3,'voluntaria'), (4,'professora');

INSERT IGNORE INTO users (id, name, email, password_hash, role_id, is_active)
VALUES (1, 'Admin', 'admin@docepalavra.org', '$2y$10$H3kM2b6Oe0zvH2kR9lFQ7u5HC8wZg3oV7cN0nZq7Jd6oYt1oE2w1e', 1, 1);

-- admin@docepalavra.org
-- Admin@123