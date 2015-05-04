<?php

return [
    'adminEmail' => 'cernakmartin3@gmail.com',
    'anonymousUserId' => '2',
    'long_term_groups' => '5',
    'time_aware_recommendation' => false,
    'min_tag_length' => '1',
    
    // SHORT_TERM, SESSION, LONG_TERM, ALL_TERM
    'recommendation_model' => [
        'SHORT' => 1,
        'SESSION' => 1,
        'LONG_TERM' => 1,
        'DOCUMENT' => 1,
        'ACTUAL' => 1,
    ],
    'search_session_timeout' => 30 * 60, // min * sec
    'last_fm_api_key' => '0bec43a6e9d33b47df0f02df11e84960',
    'last_fm_secret' => 'is ae2293cd5e165bd33a7d47a6ab811570',
    'stopwords' => require(__DIR__ . '/stopwords.php'),
];
