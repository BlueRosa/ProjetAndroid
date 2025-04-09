<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GestionCompta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="./seConnecter.php"><input type="button" value="se déconnecter"></a><input type="button" style="color: #555;" value="Validation des missions"><a href="./gestionComptaPaiement.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="Paiement des frais"></a><a href="./gestionParametres.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="paramètres"></a>
</header>
<h1> Validation des missions</h1>
<div style="display: flex">
    <?php

    $pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
    if (isset($_GET['Sal_No'])){
        $Sal_Responsable = $pdo->query("SELECT Sal_Responsable FROM salarie WHERE Sal_No=".$_GET['Sal_No']);
        $Sal_Responsable = $Sal_Responsable->fetch(PDO::FETCH_ASSOC);
        if (count($Sal_Responsable)==0){
            $Sal_Responsable = 0;
        } else{
            $Sal_Responsable = $Sal_Responsable['Sal_Responsable'];
        }
        if ($Sal_Responsable == 1) {
            if (isset($_GET['Mis_No'])){
                $query = $pdo->prepare("UPDATE mission SET Mis_Validee = true WHERE Mis_No = :Mis_No");
                $query->bindValue(':Mis_No', $_GET['Mis_No']);
                $query->execute();
            }
            $query = $pdo->prepare("SELECT * FROM mission ORDER BY Mis_No");
            $query->execute();
            $missions = $query->fetchAll(pdo::FETCH_ASSOC);
            $ville1List = $pdo->prepare("SELECT * FROM ville, agence, salarie, mission WHERE Vil_No = Ag_NoVille AND Ag_No = Sal_NoAgence AND Sal_No = Mis_NoSalarie ORDER BY Mis_No");
            $ville1List->execute();
            $ville1List = $ville1List->fetchAll(pdo::FETCH_ASSOC);
            $ville2List = $pdo->prepare("SELECT * FROM ville, mission WHERE Vil_No = Mis_NoVille ORDER BY Mis_No");
            $ville2List->execute();
            $ville2List = $ville2List->fetchAll(pdo::FETCH_ASSOC);
            $salarieList = $pdo->prepare("SELECT * FROM salarie, mission WHERE Sal_No = Mis_NoSalarie ORDER BY Mis_No");
            $salarieList->execute();
            $salarieList = $salarieList->fetchAll(pdo::FETCH_ASSOC);
            $distanceList = $pdo->prepare("SELECT * FROM distance, mission, salarie, agence, ville WHERE Sal_No = Mis_NoSalarie AND Ag_No = Sal_NoAgence AND Dist_NoVille1 = Ag_NoVille AND Dist_NoVille2 = Mis_NoVille GROUP BY Mis_No ORDER BY Mis_No");
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
            echo "<div><b> Status </b> <br>";
            $cnt = 0;
            foreach ($missions as $mission) {
                if ($mission['Mis_Remboursee']) {
                    echo "Validée et Remboursée <br>";
                } else if ($mission['Mis_Validee']) {
                    echo "Validée <br>";
                } else {
                    echo '<a href="./gestionSuperieur.php?Mis_No='.$mission['Mis_No'].'&Sal_No='.$_GET['Sal_No'].'"><input type="button" value="Valider"></a> <br>';
                }
                $cnt++;
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