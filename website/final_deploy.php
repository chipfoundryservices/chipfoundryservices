<?php
$pdo = new PDO("mysql:host=localhost;dbname=chipfoundry;charset=utf8mb4", "root", "fOm7eS:DyRW0", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
]);

$content = file_get_contents('/home/m/Documents/cv0/math/etch_profile_modeling.md');
$keywords = 'etch profile, plasma etching, level set, ARDE, RIE, monte carlo, surface evolution';

$stmt = $pdo->prepare("DELETE FROM qa_responses WHERE id = 1726");
$stmt->execute();

$stmt = $pdo->prepare("INSERT INTO qa_responses (id, keywords, response) VALUES (?, ?, ?)");
$stmt->execute([1726, $keywords, $content]);

echo "Deployed successfully. Bytes: " . strlen($content);
?>
