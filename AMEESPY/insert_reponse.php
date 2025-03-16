<?php
try {
    // on se connecte à notre base de données
    $pdo = new PDO('mysql:host=localhost;dbname=forum', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // on teste si le formulaire a été soumis
    if (isset($_POST['go']) && $_POST['go'] == 'Poster') {
        // on teste le contenu de la variable $auteur
        if (!isset($_POST['auteur']) || !isset($_POST['message']) || !isset($_GET['numero_du_sujet'])) {
            $erreur = 'Les variables nécessaires au script ne sont pas définies.';
        } else {
            if (empty($_POST['auteur']) || empty($_POST['message']) || empty($_GET['numero_du_sujet'])) {
                $erreur = 'Au moins un des champs est vide.';
            } else {
                // on récupère la date de l'instant présent
                $date = date("Y-m-d H:i:s");

                // préparation de la requête d'insertion (table forum_reponses)
                $sql = 'INSERT INTO forum_reponses (auteur, message, date_poste, sujet_id) VALUES (:auteur, :message, :date, :sujet_id)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':auteur'   => $_POST['auteur'],
                    ':message'  => $_POST['message'],
                    ':date'     => $date,
                    ':sujet_id' => $_GET['numero_du_sujet']
                ]);

                // préparation de la requête de modification de la date de la dernière réponse postée (dans la table forum_sujets)
                $sql = 'UPDATE forum_sujets SET date_derniere_reponse = :date WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':date' => $date,
                    ':id'   => $_GET['numero_du_sujet']
                ]);

                // on redirige vers la page de lecture du sujet en cours
                header('Location: lire_sujet.php?id_sujet_a_lire=' . $_GET['numero_du_sujet']);
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
<title>Insertion d'une nouvelle réponse</title>
</head>

<body>

<!-- on fait pointer le formulaire vers la page traitant les données -->
<form action="insert_reponse.php?numero_du_sujet=<?php echo $_GET['numero_du_sujet']; ?>" method="post">
<table>
<tr><td>
<b>Auteur :</b>
</td><td>
<input type="text" name="auteur" maxlength="30" size="50" value="<?php if (isset($_POST['auteur'])) echo htmlentities(trim($_POST['auteur'])); ?>">
</td></tr><tr><td>
<b>Message :</b>
</td><td>
<textarea name="message" cols="50" rows="10"><?php if (isset($_POST['message'])) echo htmlentities(trim($_POST['message'])); ?></textarea>
</td></tr><tr><td><td align="right">
<input type="submit" name="go" value="Poster">
</td></tr></table>
</form>
<?php
if (isset($erreur)) echo '<br /><br />' . $erreur;
?>
</body>
</html>
