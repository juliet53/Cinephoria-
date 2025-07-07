<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Symfony\Component\Dotenv\Dotenv;

// Chargez .env.local
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

// Débogage : Vérifiez les variables
var_dump(getenv('BUCKETEER_AWS_REGION'));
var_dump(getenv('BUCKETEER_AWS_ACCESS_KEY_ID'));
var_dump(getenv('BUCKETEER_AWS_SECRET_ACCESS_KEY'));
var_dump(getenv('BUCKETEER_BUCKET_NAME'));

$s3Client = new S3Client([
    'version' => 'latest',
    'region' => getenv('BUCKETEER_AWS_REGION') ?: 'eu-west-1',
    'credentials' => [
        'key' => getenv('BUCKETEER_AWS_ACCESS_KEY_ID'),
        'secret' => getenv('BUCKETEER_AWS_SECRET_ACCESS_KEY'),
    ],
]);

try {
    $buckets = $s3Client->listBuckets();
    echo "Connexion réussie. Buckets : " . print_r($buckets['Buckets'], true);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}