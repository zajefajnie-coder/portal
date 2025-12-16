<?php
/**
 * Instalator bazy danych dla portal-modelingowy.pl
 * Uruchom ten plik raz przez przeglądarkę, aby utworzyć tabele w bazie danych
 */

// Konfiguracja bazy danych (dostosuj do swoich danych)
define('DB_HOST', 'localhost');
define('DB_USER', 'krzyszton_port1');
define('DB_PASS', 'Alicja2025##');
define('DB_NAME', 'krzyszton_port1');

// Sprawdź czy instalacja już została wykonana
$installed_file = __DIR__ . '/.installed';

$step = $_GET['step'] ?? 'check';
$error = '';
$success = '';

// Funkcja do połączenia z bazą danych
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            throw new Exception("Błąd połączenia: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        throw new Exception("Nie można połączyć się z bazą danych: " . $e->getMessage());
    }
}

// Funkcja do wykonania zapytania SQL
function executeQuery($conn, $sql) {
    if (!$conn->query($sql)) {
        throw new Exception("Błąd SQL: " . $conn->error . "<br>Zapytanie: " . htmlspecialchars($sql));
    }
}

// Sprawdź czy tabele już istnieją
function checkTables($conn) {
    $tables = ['users', 'looks', 'collaborators', 'sessions', 'accounts', 'verification_tokens'];
    $existing = [];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $existing[] = $table;
        }
    }
    
    return $existing;
}

// Instalacja bazy danych
if ($step === 'install' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = getConnection();
        
        // Sprawdź czy tabele już istnieją
        $existing = checkTables($conn);
        if (!empty($existing)) {
            throw new Exception("Tabele już istnieją: " . implode(', ', $existing) . ". Usuń je najpierw lub użyj opcji reset.");
        }
        
        // Utwórz tabele
        executeQuery($conn, "
        CREATE TABLE IF NOT EXISTS users (
          id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
          email VARCHAR(255) UNIQUE NOT NULL,
          password_hash VARCHAR(255),
          name VARCHAR(255) NOT NULL,
          pronouns VARCHAR(50),
          location VARCHAR(255),
          experience_level ENUM('początkujący', 'średniozaawansowany', 'zaawansowany', 'profesjonalista'),
          bio TEXT,
          specialties JSON,
          avatar_url VARCHAR(500),
          email_verified BOOLEAN DEFAULT FALSE,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX idx_email (email),
          INDEX idx_location (location)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        executeQuery($conn, "
        CREATE TABLE IF NOT EXISTS looks (
          id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
          author_id VARCHAR(36) NOT NULL,
          title VARCHAR(255) NOT NULL,
          date DATE NOT NULL,
          location VARCHAR(255),
          image_url VARCHAR(500) NOT NULL,
          image_alt TEXT NOT NULL,
          tags JSON,
          is_public BOOLEAN DEFAULT TRUE,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
          INDEX idx_author (author_id),
          INDEX idx_date (date),
          INDEX idx_public (is_public),
          FULLTEXT INDEX idx_title_fulltext (title)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        executeQuery($conn, "
        CREATE TABLE IF NOT EXISTS collaborators (
          id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
          look_id VARCHAR(36) NOT NULL,
          user_id VARCHAR(36),
          name VARCHAR(255) NOT NULL,
          role ENUM('model', 'modelka', 'fotograf', 'wizażysta', 'wizażystka', 'fryzjer', 'fryzjerka', 'stylista', 'stylistka', 'retuszer', 'retuszerka', 'inny') NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (look_id) REFERENCES looks(id) ON DELETE CASCADE,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
          INDEX idx_look (look_id),
          INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        executeQuery($conn, "
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
        ");
        
        executeQuery($conn, "
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
        ");
        
        executeQuery($conn, "
        CREATE TABLE IF NOT EXISTS verification_tokens (
          identifier VARCHAR(255) NOT NULL,
          token VARCHAR(255) NOT NULL,
          expires TIMESTAMP NOT NULL,
          PRIMARY KEY (identifier, token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        // Oznacz jako zainstalowane
        file_put_contents($installed_file, date('Y-m-d H:i:s'));
        
        $success = "Instalacja zakończona pomyślnie! Wszystkie tabele zostały utworzone.";
        $step = 'success';
        
        $conn->close();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Reset bazy danych (usuń wszystkie tabele)
if ($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = getConnection();
        
        // Usuń tabele w odpowiedniej kolejności (ze względu na foreign keys)
        $tables = ['verification_tokens', 'accounts', 'sessions', 'collaborators', 'looks', 'users'];
        
        foreach ($tables as $table) {
            $conn->query("DROP TABLE IF EXISTS $table");
        }
        
        // Usuń plik .installed
        if (file_exists($installed_file)) {
            unlink($installed_file);
        }
        
        $success = "Baza danych została zresetowana. Wszystkie tabele zostały usunięte.";
        $step = 'check';
        
        $conn->close();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Sprawdź status
try {
    $conn = getConnection();
    $existing = checkTables($conn);
    $conn->close();
} catch (Exception $e) {
    $error = $e->getMessage();
    $existing = [];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalator - portal-modelingowy.pl</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .info-box {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-box h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .info-box p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .status {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .table-list {
            list-style: none;
            margin: 10px 0;
        }
        
        .table-list li {
            padding: 5px 0;
            color: #666;
        }
        
        .table-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s;
            margin-right: 10px;
            margin-top: 10px;
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        form {
            margin-top: 20px;
        }
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .warning strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Instalator bazy danych</h1>
        <p class="subtitle">portal-modelingowy.pl</p>
        
        <?php if ($error): ?>
            <div class="status error">
                <strong>Błąd:</strong><br>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="status success">
                <strong>Sukces:</strong><br>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step === 'check' || $step === 'success'): ?>
            <div class="info-box">
                <h3>Status instalacji</h3>
                <?php if (empty($existing)): ?>
                    <p>Baza danych jest pusta. Kliknij przycisk poniżej, aby rozpocząć instalację.</p>
                <?php else: ?>
                    <p>Znalezione tabele w bazie danych:</p>
                    <ul class="table-list">
                        <?php foreach ($existing as $table): ?>
                            <li><?php echo htmlspecialchars($table); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($existing) === 6): ?>
                        <p style="margin-top: 10px; color: #28a745;"><strong>✓ Instalacja zakończona pomyślnie!</strong></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <?php if (empty($existing)): ?>
                <form method="POST" action="?step=install">
                    <button type="submit" class="btn btn-success">Rozpocznij instalację</button>
                </form>
            <?php else: ?>
                <form method="POST" action="?step=reset" onsubmit="return confirm('Czy na pewno chcesz usunąć wszystkie tabele? Ta operacja jest nieodwracalna!');">
                    <button type="submit" class="btn btn-danger">Reset bazy danych</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="info-box" style="margin-top: 30px;">
            <h3>Informacje o bazie danych</h3>
            <p>
                <strong>Host:</strong> <?php echo htmlspecialchars(DB_HOST); ?><br>
                <strong>Baza danych:</strong> <?php echo htmlspecialchars(DB_NAME); ?><br>
                <strong>Użytkownik:</strong> <?php echo htmlspecialchars(DB_USER); ?>
            </p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Ważne:</strong>
            Po zakończeniu instalacji usuń ten plik (install.php) ze względów bezpieczeństwa!
        </div>
    </div>
</body>
</html>

