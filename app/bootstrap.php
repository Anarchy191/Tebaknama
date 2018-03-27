<?php

/**
 * Display errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Default timezone
 */
date_default_timezone_set('Asia/Jakarta');

/**
 * Create app
 */
$app = new \Slim\Slim();
$app->add(new \Slim\Middleware\SessionCookie(array('secret' => '_ini_rahasia_sekali_')));

/**
 * Initiate singletons
 */
$app->container->singleton('logger', function() {
  $logger = new \Monolog\Logger('tebaknama');
  $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
  $logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/app.log', \Monolog\Logger::DEBUG));
  return $logger;
});
$app->container->singleton('bot', function() {
  $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('HczossPz2J/WSkjqJlxvDtJ71LV1A7NFCwzIl8eyWs3IqoJMKQaxkxMvX8DSz08cbVLRX/f1gzhhDJ14cIIqBkD9XTrPhF8ecmdUqfw8p896UkoKmlqW7eRb9TKdCQ4IdU32wRp7RXhPRpn1c+mcLwdB04t89/1O/w1cDnyilFU=');
  $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'c41d451e0f42f191c338b27603879566']);
  return $bot;
});

// Make a database connection
use Illuminate\Database\Capsule\Manager as Capsule;
if (file_exists(APPDIR . 'config' . DS . 'database.php')) {
  $capsule = new Capsule;
  $capsule->addConnection(include APPDIR . 'config' . DS . 'database.php');
  $capsule->bootEloquent();
  $capsule->setAsGlobal();
  $app->db = $capsule;
} else {
  die('<pre>Rename `app/config/database.php.install` to `app/config/database.php` and configure your connection</pre>');
}

/**
 * Extract settings from db
 */
$settings = Settings::where('id', '=', 1)->first();

/**
 * Load all helpers
 */
foreach (glob(APPDIR . 'helpers' . DS . '*.php') as $filename) {
  require_once $filename;
}
