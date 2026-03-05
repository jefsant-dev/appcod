<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_response(405, ['ok' => false, 'message' => 'Method not allowed']);
}

try {
    $input = read_json_input();
    if ($input === []) {
        $input = $_POST;
    }

    $ua = str_limit($_SERVER['HTTP_USER_AGENT'] ?? null, 1000);
    $bot = detect_bot($ua);
    if ($bot['is_bot'] === 1) {
        json_response(400, ['ok' => false, 'message' => 'Envio bloqueado']);
    }

    $nome = str_limit($input['nome'] ?? null, 150);
    $email = str_limit($input['email'] ?? null, 190);
    $assunto = str_limit($input['assunto'] ?? null, 120);
    $mensagem = str_limit($input['mensagem'] ?? null, 5000);
    $visitorId = isset($input['visitor_id']) && $input['visitor_id'] !== ''
        ? (int) $input['visitor_id']
        : null;

    if ($nome === null || $email === null || $assunto === null || $mensagem === null) {
        json_response(422, ['ok' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(422, ['ok' => false, 'message' => 'E-mail inválido']);
    }

    if ($visitorId !== null) {
        $check = db()->prepare('SELECT id FROM visitors WHERE id = :id LIMIT 1');
        $check->execute([':id' => $visitorId]);
        if (!$check->fetch()) {
            $visitorId = null;
        }
    }

    $ipData = client_ip();
    $stmt = db()->prepare(
        'INSERT INTO contact_messages (
            visitor_id, nome, email, assunto, mensagem, ip_address, user_agent
        ) VALUES (
            :visitor_id, :nome, :email, :assunto, :mensagem, :ip_address, :user_agent
        )'
    );

    $stmt->execute([
        ':visitor_id' => $visitorId,
        ':nome' => $nome,
        ':email' => $email,
        ':assunto' => $assunto,
        ':mensagem' => $mensagem,
        ':ip_address' => $ipData['ip'],
        ':user_agent' => $ua,
    ]);
    $messageId = (int) db()->lastInsertId();

    $mailTo = 'contato@appcod.com.br';
    $mailSubject = 'Novo contato do site AppCod: ' . $assunto;
    $mailBody = implode("\n", [
        'Nova mensagem enviada pelo site AppCod',
        '',
        'Nome: ' . $nome,
        'E-mail: ' . $email,
        'Assunto: ' . $assunto,
        'Mensagem:',
        $mensagem,
        '',
        'Visitor ID: ' . ($visitorId !== null ? (string) $visitorId : 'N/A'),
        'IP: ' . ($ipData['ip'] ?? 'N/A'),
        'Data: ' . date('Y-m-d H:i:s'),
    ]);

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'From: AppCod Site <no-reply@appcod.com.br>';
    $headers[] = 'Reply-To: ' . $email;
    $headersText = implode("\r\n", $headers);

    $mailSent = @mail($mailTo, $mailSubject, $mailBody, $headersText);

    $updateStmt = db()->prepare(
        'UPDATE contact_messages
         SET email_sent = :email_sent, email_sent_at = :email_sent_at
         WHERE id = :id'
    );
    $updateStmt->execute([
        ':email_sent' => $mailSent ? 1 : 0,
        ':email_sent_at' => $mailSent ? date('Y-m-d H:i:s') : null,
        ':id' => $messageId,
    ]);

    json_response(201, ['ok' => true, 'message' => 'Mensagem enviada com sucesso']);
} catch (Throwable $e) {
    json_response(500, ['ok' => false, 'message' => 'Erro ao enviar mensagem']);
}
