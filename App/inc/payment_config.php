<?php
declare(strict_types=1);

/**
 * BirBank konfiqurasiyası repoda secret saxlamır.
 *
 * XAMPP/Apache üçün aşağıdakı environment dəyişənlərini təyin edin:
 * BIRBANK_BASE_URL
 * BIRBANK_USERNAME
 * BIRBANK_PASSWORD
 * BIRBANK_CALLBACK_URL
 */
function birbank_config(): array
{
    $values = [
        'base_url' => getenv('BIRBANK_BASE_URL') ?: '',
        'username' => getenv('BIRBANK_USERNAME') ?: '',
        'password' => getenv('BIRBANK_PASSWORD') ?: '',
        'callback_url' => getenv('BIRBANK_CALLBACK_URL') ?: ''
    ];

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
