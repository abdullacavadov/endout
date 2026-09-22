<?php
declare(strict_types=1);

function birbank_config(): array
{
    $localFile = __DIR__ . '/payment_config.local.php';

    if (is_file($localFile)) {
        $local = require $localFile;

        if (is_array($local)) {
            $values = [
                'base_url' => (string) ($local['base_url'] ?? ''),
                'username' => (string) ($local['username'] ?? ''),
                'password' => (string) ($local['password'] ?? ''),
                'callback_url' => (string) ($local['callback_url'] ?? '')
            ];
        } else {
            $values = [];
        }
    } else {
        $values = [
            'base_url' => getenv('BIRBANK_BASE_URL') ?: '',
            'username' => getenv('BIRBANK_USERNAME') ?: '',
            'password' => getenv('BIRBANK_PASSWORD') ?: '',
            'callback_url' => getenv('BIRBANK_CALLBACK_URL') ?: ''
        ];
    }

    foreach ($values as $key => $value) {
        if ($value === '') {
            throw new RuntimeException(
                'BirBank konfiqurasiyası tamamlanmayıb: ' . $key
            );
        }
    }

    return [
        $values['base_url'],
        $values['username'],
        $values['password'],
        $values['callback_url']
    ];
}
