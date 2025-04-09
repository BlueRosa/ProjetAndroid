<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SeConnecter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <input type="button" value="se connecter"><input type="button" style="color: #555;" value="Validation des missions"><input type="button" style="color: #555;" value="Paiement des frais"><input type="button" style="color: #555;" value="paramètres">
</header>
<h1> Se connecter </h1>
<?php

$pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
if (isset($_POST['Sal_No'])&&isset($_POST['Sal_MotDePasse'])) {
    $type = $pdo->prepare("SELECT * FROM salarie WHERE Sal_No = :Sal_No AND Sal_MotDePasse = :Sal_MotDePasse ");
    $type->bindParam(':Sal_No', $_POST['Sal_No']);
    $type->bindParam(':Sal_MotDePasse', $_POST['Sal_MotDePasse']);
    $type->execute();
    $type = $type->fetch();
    if ($type!==false) {
        if ( $type['Sal_Responsable']){
            header('location:gestionSuperieur.php?Sal_No='.$type['Sal_No']);
        } else if ( $type['Sal_Personnel']){
            header('location:gestionComptaPaiement.php?Sal_No='.$type['Sal_No']);
        } else {
            echo '<h4 style="background-color: red"> Mauvais identifiant ou mot de passe !</h4> <form action="./seConnecter.php" method="POST">';
            echo ' Utilisateur : <input type="text" name="Sal_No" placeholder="Numéro de salairé"> ';
            echo ' Mot de passe : <input type="password" name="Sal_MotDePasse" placeholder="Mot de passe"> <br><br>';
            echo ' <input type="submit" value="Se connecter"></form> ';
        }
    } else{
        echo '<h4 style="background-color: red"> Mauvais identifiant ou mot de passe !</h4> <form action="./seConnecter.php" method="POST">';
        echo ' Utilisateur : <input type="text" name="Sal_No" placeholder="Numéro de salairé"> ';
        echo ' Mot de passe : <input type="password" name="Sal_MotDePasse" placeholder="Mot de passe"> <br><br>';
        echo ' <input type="submit" value="Se connecter"></form> ';
    }
} else {
    echo '<form action="./seConnecter.php" method="POST">';
    echo ' Utilisateur : <input type="text" name="Sal_No" placeholder="Numéro de salairé"> ';
    echo ' Mot de passe : <input type="password" name="Sal_MotDePasse" placeholder="Mot de passe"> <br><br>';
    echo ' <input type="submit" value="Se connecter"></form> ';
}
?>
</body>
</html>