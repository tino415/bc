<?php

return [
    'adminEmail' => 'cernakmartin3@gmail.com',
    'anonymousUserId' => '2',
    'long_term_groups' => '5',
    'time_aware_recommendation' => false,
    'tag_appereance_weights' => [
        'none' => 0.5,
        'document_name_tag' => 0.7,
        'interpret_name_tag' => 0.9,
        'name_tag' => 1.1,
        'interpret_name' => 1.3,
        'document_name' => 1.5,
        'interpret_name_document_tag' => 1.7,
        'document_name_interpret_tag' => 1.9,
        'name' => 2.1,
    ],
    'min_tag_length' => '1',
    'search_session_timeout' => 30 * 60, // min * sec
    'last_fm_api_key' => '0bec43a6e9d33b47df0f02df11e84960',
    'last_fm_secret' => 'is ae2293cd5e165bd33a7d47a6ab811570',
    'stopwords' => require(__DIR__ . '/stopwords.php'),
];
