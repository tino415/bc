<?php

// Heroku
//return [
//  'class' => 'yii\db\Connection',
//  'dsn' => 'pgsql:'.
//          'host=localhost;'.
//          'port=5432;'.
//          'dbname=bc',
//  'username' => 'bc',
//  'password' => 'bc',
//  'charset' => 'utf8',
//  'schemaMap' => [
//      'pgsql' => [
//          'class' => 'yii\db\pgsql\Schema',
//          'defaultSchema' => 'public',
//      ]
//  ]
//];

$dbopts = parse_url(getenv('DATABASE_URL'));
extract($dbopts);
$dbname = ltrim($path,'/');
exit($user);

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:'.
            "host=$host;".
            "port=$port;".
            "dbname=$dbname",
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public',
        ]
    ]
];
