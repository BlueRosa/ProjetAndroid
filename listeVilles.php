<?php
$pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
$query = $pdo->prepare("SELECT * FROM ville");
$query->execute();
$villes = $query->fetchAll( pdo::FETCH_ASSOC );
echo json_encode($villes);