<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function backend_modules(): array
{
    static $modules = null;

    if ($modules === null) {
        $modules = require __DIR__ . '/modules.php';
    }

    return $modules;
}

function backend_module(string $moduleName): array
{
    $modules = backend_modules();

    if (!isset($modules[$moduleName])) {
        throw new InvalidArgumentException('Unknown module: ' . $moduleName);
    }

    return $modules[$moduleName];
}

function backend_upload_file(array $file, string $folder): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds upload_max_filesize directive in php.ini).',
            UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds MAX_FILE_SIZE directive in HTML form).',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        ];
        $errorMsg = $uploadErrors[$file['error']] ?? 'Unknown file upload error code: ' . $file['error'];
        throw new RuntimeException('File upload failed: ' . $errorMsg);
    }

    $targetDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0775, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = $extension !== '' ? $extension : 'bin';
    $fileName = sprintf('%s_%s.%s', $folder, bin2hex(random_bytes(8)), $extension);
    $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Unable to move uploaded file.');
    }

    return 'uploads/' . $folder . '/' . $fileName;
}

function backend_fetch_all(string $moduleName): array
{
    $module = backend_module($moduleName);
    $statement = pdo()->query('SELECT * FROM ' . $module['table'] . ' ORDER BY ' . $module['primary_key'] . ' DESC');
    return $statement->fetchAll();
}

function backend_fetch_one(string $moduleName, int $id): ?array
{
    $module = backend_module($moduleName);
    $statement = pdo()->prepare('SELECT * FROM ' . $module['table'] . ' WHERE ' . $module['primary_key'] . ' = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $row = $statement->fetch();

    return $row ?: null;
}

function backend_save(string $moduleName, array $post, array $files = [], ?int $id = null): int
{
    $module = backend_module($moduleName);
    $fields = $module['fields'];
    $data = [];
    $fileFields = [];

    foreach ($fields as $fieldName => $definition) {
        if (($definition['type'] ?? '') === 'file') {
            $fileFields[] = $fieldName;
            continue;
        }

        $val = $post[$fieldName] ?? '';
        if (is_array($val)) {
            $value = implode(',', array_map('trim', $val));
        } else {
            $value = trim((string)$val);
        }
        if (($definition['required'] ?? false) && $value === '') {
            throw new InvalidArgumentException($definition['label'] . ' is required.');
        }
        $data[$fieldName] = $value === '' ? null : $value;
    }

    foreach ($fileFields as $fileField) {
        if (!empty($_POST['remove_' . $fileField])) {
            $data[$fileField] = null;
        }
        if (isset($files[$fileField])) {
            $uploadedPath = backend_upload_file($files[$fileField], $moduleName);
            if ($uploadedPath !== null) {
                $data[$fileField] = $uploadedPath;
            }
        }
    }

    if ($id === null) {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);
        $statement = pdo()->prepare(
            'INSERT INTO ' . $module['table'] . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')'
        );
        $statement->execute($data);

        return (int)pdo()->lastInsertId();
    }

    $assignments = [];
    foreach (array_keys($data) as $column) {
        $assignments[] = $column . ' = :' . $column;
    }

    $data['id'] = $id;
    $statement = pdo()->prepare(
        'UPDATE ' . $module['table'] . ' SET ' . implode(',', $assignments) . ' WHERE ' . $module['primary_key'] . ' = :id'
    );
    $statement->execute($data);

    return $id;
}

function backend_delete(string $moduleName, int $id): void
{
    $module = backend_module($moduleName);
    $statement = pdo()->prepare('DELETE FROM ' . $module['table'] . ' WHERE ' . $module['primary_key'] . ' = :id');
    $statement->execute(['id' => $id]);
}

/**
 * Send email via PHPMailer (SMTP) if configured, otherwise fall back to PHP mail().
 * Hostinger REQUIRES SMTP — PHP mail() is silently discarded on shared hosting.
 */
function send_email(string $to, string $subject, string $body): bool
{
    // Use PHPMailer + SMTP when credentials are defined (live server)
    if (defined('SMTP_HOST') && defined('SMTP_USER') && defined('SMTP_PASS') && SMTP_PASS !== 'YOUR_EMAIL_PASSWORD_HERE') {
        $autoloadPath = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            error_log('PHPMailer autoload not found at: ' . $autoloadPath);
            return false;
        }
        require_once $autoloadPath;

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'ssl';
            $mail->Port       = defined('SMTP_PORT')   ? SMTP_PORT   : 465;

            $fromName  = defined('SMTP_FROM_NAME')  ? SMTP_FROM_NAME  : 'Nishchala Yoga';
            $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : SMTP_USER;

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->isHTML(false); // Plain text email

            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log('PHPMailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    // Fallback: PHP mail() for local/dev environments
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $host = preg_replace('/^www\./', '', $host);
    $from = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'no-reply@' . $host;

    $headers  = 'From: Nishchala Yoga <' . $from . ">\r\n";
    $headers .= 'Reply-To: ' . $from . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8';

    return @mail($to, $subject, $body, $headers, '-f' . $from);
}

/**
 * Send SMS - placeholder. If Twilio constants are defined, attempt to use it.
 * Returns true on success (best-effort), false otherwise.
 */
function send_sms(string $to, string $message): bool
{
    if (empty($to) || empty($message)) {
        return false;
    }

    // Basic placeholder: if Twilio is configured, try to call REST API via curl
    if (defined('TWILIO_SID') && defined('TWILIO_AUTH_TOKEN') && defined('TWILIO_FROM')) {
        $sid = TWILIO_SID;
        $token = TWILIO_AUTH_TOKEN;
        $from = TWILIO_FROM;
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $data = http_build_query(['From' => $from, 'To' => $to, 'Body' => $message]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code >= 200 && $code < 300;
    }

    // No provider configured - return false but do not throw
    return false;
}
