<?php
try {
    // on se connecte à notre base de données
    $pdo = new PDO('mysql:host=localhost;dbname=forum', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // on teste si le formulaire a été soumis
    if (isset($_POST['go']) && $_POST['go'] == 'Poster') {
        // on teste la déclaration de nos variables
        if (!isset($_POST['auteur']) || !isset($_POST['titre']) || !isset($_POST['message'])) {
            $erreur = 'Les variables nécessaires au script ne sont pas définies.';
        } else {
            // on teste si les variables ne sont pas vides
            if (empty($_POST['auteur']) || empty($_POST['titre']) || empty($_POST['message'])) {
                $erreur = 'Au moins un des champs est vide.';
            } else {
                // on calcule la date actuelle
                $date = date("Y-m-d H:i:s");

                // préparation de la requête d'insertion (pour la table forum_sujets)
                $sql = 'INSERT INTO forum_sujets (auteur, titre, date_poste) VALUES (:auteur, :titre, :date)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':auteur' => $_POST['auteur'],
                    ':titre' => $_POST['titre'],
                    ':date' => $date
                ]);

                // on récupère l'id qui vient de s'insérer dans la table forum_sujets
                $id_sujet = $pdo->lastInsertId();

                // lancement de la requête d'insertion (pour la table forum_reponses)
                $sql = 'INSERT INTO forum_reponses (auteur, message, date_poste, sujet_id) VALUES (:auteur, :message, :date, :sujet_id)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':auteur' => $_POST['auteur'],
                    ':message' => $_POST['message'],
                    ':date' => $date,
                    ':sujet_id' => $id_sujet
                ]);

                // on redirige vers la page d'accueil
                header('Location: index.php');
                exit;
            }
        }
    }
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<html>
<head>
<title>Insertion d'un nouveau sujet</title>
</head>

<body>

<!-- on fait pointer le formulaire vers la page traitant les données -->
<form action="insert_sujet.php" method="post">
<table>
<tr><td>
<b>Auteur :</b>
</td><td>
<input type="text" name="auteur" maxlength="30" size="50" value="<?php if (isset($_POST['auteur'])) echo htmlentities(trim($_POST['auteur'])); ?>">
</td></tr><tr><td>
<b>Titre :</b>
</td><td>
<input type="text" name="titre" maxlength="50" size="50" value="<?php if (isset($_POST['titre'])) echo htmlentities(trim($_POST['titre'])); ?>">
</td></tr><tr><td>
<b>Message :</b>
</td><td>
<textarea name="message" cols="50" rows="10"><?php if (isset($_POST['message'])) echo htmlentities(trim($_POST['message'])); ?></textarea>
</td></tr><tr><td><td align="right">
<input type="submit" name="go" value="Poster">
</td></tr></table>
</form>
<?php
// on affiche les erreurs éventuelles
if (isset($erreur)) echo '<br /><br />' . $erreur;
?>
</body>
</html>
