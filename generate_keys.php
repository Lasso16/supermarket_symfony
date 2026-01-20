<?php
// generate_keys.php

// Detect OpenSSL config path for XAMPP
$opensslConfigPath = 'C:/xampp/apache/conf/openssl.cnf';
if (!file_exists($opensslConfigPath)) {
    $opensslConfigPath = 'C:/xampp/php/extras/ssl/openssl.cnf';
}
if (!file_exists($opensslConfigPath)) {
    // Fallback if not found, though it might fail
    echo "Warning: openssl.cnf not found in common XAMPP paths.\n";
} else {
    echo "Using OpenSSL config: $opensslConfigPath\n";
}

$config = array(
    "digest_alg" => "sha256",
    "private_key_bits" => 4096,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "config" => $opensslConfigPath,
);

// Create the private and public key
$res = openssl_pkey_new($config);

if (!$res) {
    die("Error generating key pair: " . openssl_error_string());
}

// Extract the private key
openssl_pkey_export($res, $privKey, 'efc91f35412172d45c22f65833e8dfe4f71fd7e79e2fc7f5531593026a52b354', $config); // Matching .env passphrase

// Extract the public key
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];

// Save keys
$jwtDir = __DIR__ . '/config/jwt';
if (!is_dir($jwtDir)) {
    mkdir($jwtDir, 0777, true);
}

file_put_contents($jwtDir . '/private.pem', $privKey);
file_put_contents($jwtDir . '/public.pem', $pubKey);

echo "Keys generated successfully in config/jwt/\n";