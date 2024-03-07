<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Load environment variables
(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env.test');

// Extract the database connection info from the environment variable
$databaseUrl = $_ENV['DATABASE_URL'];

// Use parse_url() to break down the URL into components
$parsedUrl = parse_url($databaseUrl);

// Rebuild the URL without the database name path
$host = $parsedUrl['host'];
$user = $parsedUrl['user'];
$pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
$port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';

// Notice how the scheme is preserved and no specific database is targeted
$connectionUrl = "{$parsedUrl['scheme']}://$user$pass@$host$port";

// Establish the connection using the modified URL
$connection = DriverManager::getConnection(['url' => $connectionUrl]);

// Proceed to check for the existence of the database and create it if necessary
$dbName = ltrim($parsedUrl['path'], '/');
$schemaManager = $connection->createSchemaManager();

if (!in_array($dbName, $schemaManager->listDatabases())) {
    $schemaManager->createDatabase($dbName);
    echo "Created database $dbName\n";
    $process = new Process(['php', 'bin/console', 'doctrine:migrations:migrate', '--env=test', '-n']);
    $process->run();
}

$connection->close();