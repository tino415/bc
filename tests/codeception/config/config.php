<?php
/**
 * Application configuration shared by all test types
 */
return [
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'db' => [
            'dsn' => 'pgsql:'.
                'host=localhost;'.
                'port=5432;'.
                'dbname=bc-tests',
            'username' => 'bcuser',
            'password' => 'bc-user',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => [
                    'class' => 'yii\db\pgsql\Schema',
                    'defaultSchema' => 'public',
                ]
            ]
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
    'params' => [
        'adminEmail' => 'cernakmartin3@gmail.com',
        'anonymousUserId' => '2',
        'long_term_groups' => '5',
        'time_aware_recommendation' => true,
        'tag_appereance_weights' => [
            'none' => 1,
            'document_name_tag' => 1.1,
            'interpret_name_tag' => 1.2,
            'name_tag' => 1.3,
            'interpret_name' => 1.4,
            'document_name' => 1.5,
            'interpret_name_document_tag' => 1.6,
            'document_name_interpret_tag' => 1.7,
            'name' => 1.8,
        ],
        'min_tag_length' => '1',
        'search_session_timeout' => 30 * 60, // min * sec
        'last_fm_api_key' => '0bec43a6e9d33b47df0f02df11e84960',
        'last_fm_secret' => 'is ae2293cd5e165bd33a7d47a6ab811570',
        'stopwords' => require(__DIR__ . '/stopwords.php'),
    ]
];
