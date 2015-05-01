<?php

$dbopts = parse_url(getenv('DATABASE_URL'));

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:'.
            'host=localhost;'.
            'port=5432;'.
            'dbname=bc',
    'username' => 'bc',
    'password' => 'bc',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public',
        ]
    ]
];
