<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GestionCompta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="./seConnecter.php"><input type="button" value="se déconnecter"></a><a href="./gestionSuperieur.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="Validation des missions"></a><input type="button" style="color: #555;" value="Paiement des frais"><a href="./gestionParametres.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="paramètres"></a>
</header>
<h1> Paiement des missions</h1>
<div style="display: flex">
    <?php

    $pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
    if (isset($_GET['Sal_No'])){
        $Sal_Personnel = $pdo->query("SELECT Sal_Personnel FROM salarie WHERE Sal_No=".$_GET['Sal_No']);
        $Sal_Personnel = $Sal_Personnel->fetch(PDO::FETCH_ASSOC);
        $Sal_Personnel = $Sal_Personnel['Sal_Personnel'];
        if ($Sal_Personnel == 1) {
            if (isset($_GET['Mis_No'])){
                $query = $pdo->prepare("UPDATE mission SET Mis_Remboursee = true WHERE Mis_No = :Mis_No");
                $query->bindValue(':Mis_No', $_GET['Mis_No']);
                $query->execute();
            }
            $query = $pdo->prepare("SELECT * FROM mission WHERE Mis_Validee = true ORDER BY Mis_No");
            $query->execute();
            $missions = $query->fetchAll(pdo::FETCH_ASSOC);
            $ville1List = $pdo->prepare("SELECT * FROM ville, agence, salarie, mission WHERE Vil_No = Ag_NoVille AND Ag_No = Sal_NoAgence AND Sal_No = Mis_NoSalarie AND Mis_Validee = true ORDER BY Mis_No");
            $ville1List->execute();
            $ville1List = $ville1List->fetchAll(pdo::FETCH_ASSOC);
            $ville2List = $pdo->prepare("SELECT * FROM ville, mission WHERE Vil_No = Mis_NoVille AND Mis_Validee = true ORDER BY Mis_No");
            $ville2List->execute();
            $ville2List = $ville2List->fetchAll(pdo::FETCH_ASSOC);
            $salarieList = $pdo->prepare("SELECT * FROM salarie, mission WHERE Sal_No = Mis_NoSalarie AND Mis_Validee = true ORDER BY Mis_No");
            $salarieList->execute();
            $salarieList = $salarieList->fetchAll(pdo::FETCH_ASSOC);
            $distanceList = $pdo->prepare("SELECT * FROM distance, mission, salarie, agence, ville WHERE Sal_No = Mis_NoSalarie AND Ag_No = Sal_NoAgence AND Dist_NoVille1 = Ag_NoVille AND Dist_NoVille2 = Mis_NoVille AND Mis_Validee = true GROUP BY Mis_No ORDER BY Mis_No");
            $distanceList->execute();
            $distanceList = $distanceList->fetchAll(pdo::FETCH_ASSOC);
            $parametrage = $pdo->prepare("SELECT * FROM parametrage");
            $parametrage->execute();
            $parametrage = $parametrage->fetchAll(pdo::FETCH_ASSOC);
            $fmt = new IntlDateFormatter(
                'fr_FR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'Europe/Paris',
                IntlDateFormatter::GREGORIAN
            );

            echo "<div><b> Nom du Salarié </b> <br>";
            $cnt = 0;
            foreach ($missions as $mission) {
                echo $salarieList[$cnt]['Sal_Nom']."<br>";
                $cnt++;
            }
            echo "</div>";
            echo "<div><b> Prénom du Salarié </b> <br>";
            $cnt = 0;
            foreach ($missions as $mission) {
                echo $salarieList[$cnt]['Sal_Prenom']."<br>";
                $cnt++;
            }
            echo "</div>";
            setlocale(LC_TIME, "fr_FR");
            echo "<div><b> Début de la mission </b> <br>";
            foreach ($missions as $mission) {
                echo $fmt->format(strtotime($mission['Mis_DateDebut']))."<br>";
            }
            echo "</div>";
            echo "<div><b> Fin de la mission </b> <br>";
            foreach ($missions as $mission) {
                echo $fmt->format(strtotime($mission['Mis_DateFin']))."<br>";
            }
            echo "</div>";
            echo "<div><b> Lieu de la mission </b> <br>";
            $cnt = 0;
            foreach ($missions as $mission) {
                echo $ville2List[$cnt]['Vil_Nom']." ( ". $ville2List[$cnt]['Vil_CP'] .")<br>";
                $cnt++;
            }
            echo "</div>";
            echo "<div><b> Paiement </b> <br>";
            $cnt = 0;
            foreach ($missions as $mission) {
                if ($distanceList[$cnt]['Mis_No'] == $mission['Mis_No']) {
                    echo $parametrage[0]['Par_IndemniteKm']*$distanceList[$cnt]['Dist_Km'] + $parametrage[0]['Par_IndemniteJour'] * (strtotime($mission['Mis_DateFin'])-strtotime($mission['Mis_DateDebut']))/86400 . " <br>";
                    $cnt++;
                } else {
                    echo "<br>";
                }
            }
            echo "</div>";
            echo "<div><b> Status </b> <br>";
            $cnt = 0;
            $cnt2 = 0;
            foreach ($missions as $mission) {
                if ($mission['Mis_Remboursee']) {
                    echo "Remboursee <br>";
                    $cnt++;
                } else{
                    if ($distanceList[$cnt]['Mis_No'] == $mission['Mis_No']){
                        echo '<a href="./gestionComptaPaiement.php?Mis_No='.$mission['Mis_No'].'&Sal_No='.$_GET['Sal_No'].'"> Rembourser </a>';
                        $cnt++;
                    } else {
                        echo "Distance entre ".$ville2List[$cnt2]['Vil_Nom']." et ".$ville1List[$cnt2]['Vil_Nom']." non définie <br>";
                    }
                }
                $cnt2++;
            }
            echo "</div>";

        } else {
            echo '<h2 style="background-color:red"> Accès non autorisé </h2>';
        }
    } else {
        echo '<h2 style="background-color:red"> Accès non autorisé </h2>';
    }


    ?>
</div>
</body>
</html>