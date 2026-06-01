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
        throw new RuntimeException('File upload failed.');
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
    $fileField = null;

    foreach ($fields as $fieldName => $definition) {
        if ($definition['type'] === 'file') {
            $fileField = $fieldName;
            continue;
        }

        $value = trim((string)($post[$fieldName] ?? ''));
        if (($definition['required'] ?? false) && $value === '') {
            throw new InvalidArgumentException($definition['label'] . ' is required.');
        }
        $data[$fieldName] = $value === '' ? null : $value;
    }

    if ($fileField !== null && isset($files[$fileField])) {
        $uploadedPath = backend_upload_file($files[$fileField], $moduleName);
        if ($uploadedPath !== null) {
            $data[$fileField] = $uploadedPath;
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
 * Send a simple email using PHP mail().
 * This is a best-effort helper; for production use configure SMTP or PHPMailer.
 */
function send_email(string $to, string $subject, string $body): bool
{
    if (defined('SMTP_FROM_EMAIL')) {
        $from = SMTP_FROM_EMAIL;
    } else {
        $from = 'no-reply@localhost';
    }

    $headers = [];
    $headers[] = 'From: ' . $from;
    $headers[] = 'Content-Type: text/plain; charset=utf-8';

    // Suppress warnings
    return @mail($to, $subject, $body, implode("\r\n", $headers));
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
