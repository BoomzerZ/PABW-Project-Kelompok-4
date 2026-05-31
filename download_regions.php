<?php

$baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';
$publicDir = __DIR__ . '/public/data';

if (!is_dir($publicDir)) {
    mkdir($publicDir, 0777, true);
}
if (!is_dir($publicDir . '/regencies')) {
    mkdir($publicDir . '/regencies', 0777, true);
}

echo "Downloading provinces...\n";
$provincesJson = file_get_contents("$baseUrl/provinces.json");
file_put_contents("$publicDir/provinces.json", $provincesJson);

$provinces = json_decode($provincesJson, true);

foreach ($provinces as $prov) {
    echo "Downloading regencies for " . $prov['name'] . "...\n";
    $regenciesJson = file_get_contents("$baseUrl/regencies/{$prov['id']}.json");
    file_put_contents("$publicDir/regencies/{$prov['id']}.json", $regenciesJson);
}

echo "Done!\n";
