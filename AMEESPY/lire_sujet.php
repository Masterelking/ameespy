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
    // on se connecte à notre base de données
    $pdo = new PDO('mysql:host=localhost;dbname=forum', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // on prépare notre requête
    $sql = 'SELECT auteur, message, date_reponse FROM forum_reponses WHERE correspondance_sujet = :id_sujet_a_lire ORDER BY date_reponse ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_sujet_a_lire' => $_GET['id_sujet_a_lire']]);

    // on va scanner tous les tuples un par un
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // on décompose la date
        sscanf($data['date_reponse'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);

        // on affiche les résultats
        echo '<tr>';
        echo '<td>';

        // on affiche le nom de l'auteur de sujet ainsi que la date de la réponse
        echo htmlentities(trim($data['auteur']));
        echo '<br />';
        echo $jour, '-', $mois, '-', $annee, ' ', $heure, ':', $minute;

        echo '</td><td>';

        // on affiche le message
        echo nl2br(htmlentities(trim($data['message'])));
        echo '</td></tr>';
    }

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

</table>
<br /><br />
<!-- on insère un lien qui nous permettra de rajouter des réponses à ce sujet -->
<a href="./insert_reponse.php?numero_du_sujet=<?php echo $_GET['id_sujet_a_lire']; ?>">Répondre</a>

<?php
}
?>
<br /><br />
<!-- on insère un lien qui nous permettra de retourner à l'accueil du forum -->
<a href="./index.php">Retour à l'accueil</a>

</body>
</html>
