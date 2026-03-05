<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_response(405, ['ok' => false, 'message' => 'Method not allowed']);
}

try {
    $input = read_json_input();
    $ua = str_limit($_SERVER['HTTP_USER_AGENT'] ?? null, 1000);
    $bot = detect_bot($ua);

    if ($bot['is_bot'] === 1) {
        json_response(200, [
            'ok' => true,
            'tracked' => false,
            'reason' => $bot['reason'],
        ]);
    }

    $ipData = client_ip();
    $referer = str_limit($input['referrer'] ?? ($_SERVER['HTTP_REFERER'] ?? null), 2048);
    $landingUrl = str_limit($input['landing_url'] ?? null, 2048);
    $queryString = str_limit($_SERVER['QUERY_STRING'] ?? null, 1024);
    $acceptLanguage = str_limit($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null, 255);

    $utmSource = str_limit($input['utm_source'] ?? null, 255);
    $utmMedium = str_limit($input['utm_medium'] ?? null, 255);
    $utmCampaign = str_limit($input['utm_campaign'] ?? null, 255);
    $utmTerm = str_limit($input['utm_term'] ?? null, 255);
    $utmContent = str_limit($input['utm_content'] ?? null, 255);

    $stmt = db()->prepare(
        'INSERT INTO visitors (
            ip_address, forwarded_for, user_agent, referer_url, landing_url, query_string,
            utm_source, utm_medium, utm_campaign, utm_term, utm_content, accept_language,
            is_bot, bot_reason
        ) VALUES (
            :ip_address, :forwarded_for, :user_agent, :referer_url, :landing_url, :query_string,
            :utm_source, :utm_medium, :utm_campaign, :utm_term, :utm_content, :accept_language,
            :is_bot, :bot_reason
        )'
    );

    $stmt->execute([
        ':ip_address' => $ipData['ip'],
        ':forwarded_for' => str_limit($ipData['forwarded_for'], 255),
        ':user_agent' => $ua,
        ':referer_url' => $referer,
        ':landing_url' => $landingUrl,
        ':query_string' => $queryString,
        ':utm_source' => $utmSource,
        ':utm_medium' => $utmMedium,
        ':utm_campaign' => $utmCampaign,
        ':utm_term' => $utmTerm,
        ':utm_content' => $utmContent,
        ':accept_language' => $acceptLanguage,
        ':is_bot' => 0,
        ':bot_reason' => null,
    ]);

    json_response(201, [
        'ok' => true,
        'tracked' => true,
        'visitor_id' => (int) db()->lastInsertId(),
    ]);
} catch (Throwable $e) {
    json_response(500, ['ok' => false, 'message' => 'Erro ao registrar visita']);
}
