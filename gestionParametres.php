<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GestionCompta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="./seConnecter.php"><input type="button" value="se déconnecter"></a><a href="./gestionSuperieur.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="Validation des missions"></a><a href="./gestionComptaPaiement.php<?php if (isset($_GET['Sal_No'])){ echo "?Sal_No=".$_GET['Sal_No']; }?>"><input type="button" value="Paiement des frais"></a><input style="color: #555;" type="button" value="paramètres">
</header>
<h1> Paramétrage de l'application</h1>
<?php

    if (isset($_GET['Sal_No'])){
        $pdo = new PDO('mysql:host=localhost;dbname=epoka_missions', 'root', '');
        $cityNotInside = false;
        // pour changer les indemnités
        if (isset($_POST['Par_IndemniteKm']) && isset($_POST['Par_IndemniteJour'])){
            $query = $pdo->prepare("UPDATE parametrage SET Par_IndemniteJour = :Par_IndemniteJour , Par_IndemniteKm = :Par_IndemniteKm ");
            $query->bindParam(":Par_IndemniteJour", $_POST['Par_IndemniteJour']);
            $query->bindParam(":Par_IndemniteKm", $_POST['Par_IndemniteKm']);
            $query->execute();
        }
        // pour changer les distances
        if (isset($_POST['Dist_NoVille1']) && isset($_POST['Dist_NoVille2']) && isset($_POST['Dist_Km'])){
            $ville1 = $pdo->prepare("SELECT * FROM ville WHERE Vil_No=:Vil_No");
            $ville1->bindParam(':Vil_No', $_POST['Dist_NoVille1']);
            $ville1->execute();
            $ville2 = $pdo->prepare("SELECT * FROM ville WHERE Vil_No=:Vil_No");
            $ville2->bindParam(':Vil_No', $_POST['Dist_NoVille2']);
            $ville2->execute();
            if ($ville1->rowCount() > 0 && $ville2->rowCount() > 0){
                //si les villes existent
                $ville1 = $ville1->fetch(PDO::FETCH_ASSOC);
                $ville2 = $ville2->fetch(PDO::FETCH_ASSOC);
                //distance aller
                $distance = $pdo->prepare("SELECT * FROM distance WHERE Dist_NoVille1=:Dist_NoVille1 AND Dist_NoVille2=:Dist_NoVille2");
                $distance->bindParam(':Dist_NoVille1', $ville1['Vil_No']);
                $distance->bindParam(':Dist_NoVille2', $ville2['Vil_No']);
                $distance->execute();
                if ($distance->rowCount() > 0){
                    $distance = $pdo->prepare("UPDATE distance SET Dist_Km=:Dist_Km WHERE Dist_NoVille1=:Dist_NoVille1 AND Dist_NoVille2=:Dist_NoVille2");
                    $distance->bindParam(':Dist_NoVille1', $ville1['Vil_No']);
                    $distance->bindParam(':Dist_NoVille2', $ville2['Vil_No']);
                    $distance->bindParam(':Dist_Km', $_POST['Dist_Km']);
                    $distance->execute();
                } else {
                    $distance = $pdo->prepare("INSERT INTO distance (Dist_NoVille1, Dist_NoVille2, Dist_Km) VALUES (:Dist_NoVille1, :Dist_NoVille2, :Dist_Km)");
                    $distance->bindParam(':Dist_NoVille1', $ville1['Vil_No']);
                    $distance->bindParam(':Dist_NoVille2', $ville2['Vil_No']);
                    $distance->bindParam(':Dist_Km', $_POST['Dist_Km']);
                    $distance->execute();
                }
                //distance retour
                $distance = $pdo->prepare("SELECT * FROM distance WHERE Dist_NoVille2=:Dist_NoVille1 AND Dist_NoVille1=:Dist_NoVille2");
                $distance->bindParam(':Dist_NoVille1', $ville1['Vil_No']);
                $distance->bindParam(':Dist_NoVille2', $ville2['Vil_No']);
                $distance->execute();
                if ($distance->rowCount() > 0){
                    $distance = $pdo->prepare("UPDATE distance SET Dist_Km=:Dist_Km WHERE Dist_NoVille2=:Dist_NoVille1 AND Dist_NoVille1=:Dist_NoVille2");
                    $distance->bindParam(':Dist_NoVille1', $ville1['Vil_No']);
                    $distance->bindParam(':Dist_NoVille2', $ville2['Vil_No']);
                    $distance->bindParam(':Dist_Km', $_POST['Dist_Km']);
                    $distance->execute();
                }else {
                    $distance = $pdo->prepare("INSERT INTO distance (Dist_NoVille1, Dist_NoVille2, Dist_Km) VALUES (:Dist_NoVille2, :Dist_NoVille1, :Dist_Km)");
                    $distance->bindParam(':Dist_NoVille2', $ville1['Vil_No']);
                    $distance->bindParam(':Dist_NoVille1', $ville2['Vil_No']);
                    $distance->bindParam(':Dist_Km', $_POST['Dist_Km']);
                    $distance->execute();
                }
                //si on entre une mauvaise ville, on le signale à l'affichage.
            } else { $cityNotInside = true; }
        }
        $Sal_Personnel = $pdo->query("SELECT Sal_Personnel FROM salarie WHERE Sal_No=".$_GET['Sal_No']);
        $Sal_Personnel = $Sal_Personnel->fetch(PDO::FETCH_ASSOC);
        $Sal_Personnel = $Sal_Personnel['Sal_Personnel'];
        if ($Sal_Personnel == 1) {
            $parametrage = $pdo->query("SELECT * FROM parametrage");
            $parametrage = $parametrage->fetchAll(PDO::FETCH_ASSOC);
            $listVilles = $pdo->query("SELECT * FROM ville");
            $listVilles = $listVilles->fetchAll(PDO::FETCH_ASSOC);
            $listDistances = $pdo->query("SELECT * FROM distance");
            $listDistances = $listDistances->fetchAll(PDO::FETCH_ASSOC);
            $listVille1Concernees = array();
            $listVille2Concernees = array();
            foreach ($listDistances as $distance){
                $ville1 = $pdo->query("select * from ville where Vil_No=".$distance['Dist_NoVille1']);
                $ville1 = $ville1->fetch(PDO::FETCH_ASSOC);
                $ville2 = $pdo->query("select * from ville where Vil_No=".$distance['Dist_NoVille2']);
                $ville2 = $ville2->fetch(PDO::FETCH_ASSOC);
                $listVille1Concernees[] = $ville1;
                $listVille2Concernees[] = $ville2;
            }
            //paramétrage des forfaits
            echo '<h2> Montant des remboursements </h2> <br>';
            echo '<form action="./gestionParametres.php?Sal_No='.$_GET['Sal_No'].'" method="POST">';
            echo 'Remboursement au Km : <input type="number" name="Par_IndemniteKm" required="required" step="0.01" value="'.$parametrage[0]['Par_IndemniteKm'].'"> <br><br>';
            echo 'Remboursement à la journée : <input type="number" name="Par_IndemniteJour" required="required" step="0.01" value="'.$parametrage[0]['Par_IndemniteJour'].'"> <br><br>';
            echo '<input type="submit" value="Valider"> </form>';
            //paramétrage des listDistances
            echo '<h2> Distances entre villes </h2> <br>';
            echo '<form action="./gestionParametres.php?Sal_No='.$_GET['Sal_No'].'" method="POST">';
            echo 'De : <input type="text" name="Dist_NoVille1" list="listVilles" required="required"> <br><br>';
            echo 'à : <input type="text" name="Dist_NoVille2" list="listVilles" required="required"> <br><br>';
            echo 'Distance en Km : <input type="number" name="Dist_Km" required="required"> <br><br>';
            if ($cityNotInside){
                echo '<h2 style="background-color:red"> Ville(s) non existante(s) </h2>';
            }
            echo '<input type="submit" value="Valider"> </form>';
            echo '<datalist id="listVilles">';
            foreach ($listVilles as $ville) {
                echo '<option value="'.$ville['Vil_No'].'">'. $ville['Vil_Nom'].' ('.$ville['Vil_CP'].')</option>';
            }
            echo '</datalist>';
            echo '<h2> Distances entre villes déjà saisies </h2> <br>';
            echo '<div style="display: flex">';
            echo "<div><b> De </b> <br>";
            $cnt = 0;
            foreach ($listDistances as $mission) {
                echo $listVille1Concernees[$cnt]['Vil_Nom']."<br>";
                $cnt++;
            }
            echo "</div>";
            echo "<div><b> A </b> <br>";
            $cnt = 0;
            foreach ($listDistances as $mission) {
                echo $listVille2Concernees[$cnt]['Vil_Nom']."<br>";
                $cnt++;
            }
            echo "</div>";
            echo "<div><b> Km </b> <br>";
            $cnt = 0;
            foreach ($listDistances as $distance) {
                echo $distance['Dist_Km']."<br>";
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
</body>
</html>
