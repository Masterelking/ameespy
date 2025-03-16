<html>
<head>
<title>Lecture d'un sujet</title>
</head>
<body>

<?php
if (!isset($_GET['id_sujet_a_lire'])) {
    echo 'Sujet non défini.';
} else {
?>

<table width="500" border="1">
<tr>
    <td>Auteur</td>
    <td>Messages</td>
</tr>

<?php
try {
    // connexion à la base de données avec PDO
    $pdo = new PDO('mysql:host=localhost;dbname=forum', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // préparation de la requête
    $sql = 'SELECT auteur, message, date_reponse FROM forum_reponses WHERE correspondance_sujet = :id_sujet_a_lire ORDER BY date_reponse ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_sujet_a_lire' => $_GET['id_sujet_a_lire']]);

    // afficher les résultats
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        sscanf($data['date_reponse'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);

        echo '<tr>';
        echo '<td>' . htmlentities(trim($data['auteur'])) . '<br />' . $jour . '-' . $mois . '-' . $annee . ' ' . $heure . ':' . $minute . '</td>';
        echo '<td>' . nl2br(htmlentities(trim($data['message']))) . '</td>';
        echo '</tr>';
    }
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

</table>
<br /><br />
<a href="./insert_reponse.php?numero_du_sujet=<?php echo $_GET['id_sujet_a_lire']; ?>">Répondre</a>

<?php
}
?>
<br /><br />
<a href="./index.php">Retour à l'accueil</a>

</body>
</html>
