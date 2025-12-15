-- Schema MySQL dla portal-modelingowy.pl - Zenbox
-- Baza danych: krzyszton_port1
-- Uruchom ten skrypt w phpMyAdmin lub przez MySQL CLI

-- Upewnij się, że jesteś w odpowiedniej bazie danych
USE krzyszton_port1;

-- Tabela użytkowników (rozszerzenie dla NextAuth lub własnego systemu auth)
CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255),
  name VARCHAR(255) NOT NULL,
  pronouns VARCHAR(50),
  location VARCHAR(255),
  experience_level ENUM('początkujący', 'średniozaawansowany', 'zaawansowany', 'profesjonalista'),
  bio TEXT,
  specialties JSON, -- Array jako JSON: ["portret", "moda", "studio"]
  avatar_url VARCHAR(500),
  email_verified BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela sesji (Looks)
CREATE TABLE IF NOT EXISTS looks (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  author_id VARCHAR(36) NOT NULL,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  location VARCHAR(255),
  image_url VARCHAR(500) NOT NULL,
  image_alt TEXT NOT NULL,
  tags JSON, -- Array jako JSON: ["portret", "moda", "studio"]
  is_public BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_author (author_id),
  INDEX idx_date (date),
  INDEX idx_public (is_public),
  FULLTEXT INDEX idx_title_fulltext (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela współpracowników
CREATE TABLE IF NOT EXISTS collaborators (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  look_id VARCHAR(36) NOT NULL,
  user_id VARCHAR(36),
  name VARCHAR(255) NOT NULL, -- Nazwa współpracownika (może być użytkownikiem lub zewnętrzną osobą)
  role ENUM('model', 'modelka', 'fotograf', 'wizażysta', 'wizażystka', 'fryzjer', 'fryzjerka', 'stylista', 'stylistka', 'retuszer', 'retuszerka', 'inny') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (look_id) REFERENCES looks(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_look (look_id),
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela sesji autentykacji (dla NextAuth)
CREATE TABLE IF NOT EXISTS sessions (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  user_id VARCHAR(36) NOT NULL,
  session_token VARCHAR(255) UNIQUE NOT NULL,
  expires TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_session_token (session_token),
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela kont weryfikacyjnych (dla NextAuth)
CREATE TABLE IF NOT EXISTS accounts (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  user_id VARCHAR(36) NOT NULL,
  type VARCHAR(50) NOT NULL,
  provider VARCHAR(50) NOT NULL,
  provider_account_id VARCHAR(255) NOT NULL,
  refresh_token TEXT,
  access_token TEXT,
  expires_at INT,
  token_type VARCHAR(50),
  scope TEXT,
  id_token TEXT,
  session_state VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_provider_account (provider, provider_account_id),
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela weryfikacji e-mail
CREATE TABLE IF NOT EXISTS verification_tokens (
  identifier VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires TIMESTAMP NOT NULL,
  PRIMARY KEY (identifier, token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

