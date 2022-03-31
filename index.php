<?php

require 'connect.php';

$pdo = new PDO(DSN, USER, PASS);
//DSN: Data Source Name

// Je rajoute bing a la main et je vérifie si Bing est déjà dans ma database, si oui la methode exec ne s'execute pas. 
// je peux refresh la page et avoir une seule ligne Bing.

$bing = "SELECT * FROM friend WHERE lastname = 'bing'";
$query = "INSERT INTO friend (firstname, lastname) VALUES ('Chandler', 'Bing')";
if (empty($bing)) {
    $statebing = $pdo->exec($query);
}

$query = "SELECT * FROM friend";
$statement = $pdo->query($query);

$friends = $statement->fetchAll(PDO::FETCH_ASSOC);
/**
 * si la methode du formulaire est POST on récupère les data après nettoyage, 
 * puis cherck des erreurs si form vide ou trop long.
 * ensuite si l'array errors est vide on peut lancer les methodes PDO sur la bdd
 * ici les variables sont préparés par PDO 
 */
if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $data = array_map('trim', $_POST);
    $data = array_map('htmlentities', $_POST);

    $errors = [];

    if (empty($data['firstname']) || empty($data['lastname'])) {
        $errors[] = "both name are mandatory and must be less than 45 characteres";
    }
    if (mb_strlen($data['firstname']) > 45  || mb_strlen($data['lastname']) > 45) {
        $errors[] = "Name must be less than 45 characters";
    }

    if (empty($errors)) {
        $query = "INSERT INTO friend (firstname, lastname) VALUES (:firstname, :lastname)";
        $statement = $pdo->prepare($query);

        $statement->bindValue(':firstname', $data['firstname'], PDO::PARAM_STR);
        $statement->bindValue(':lastname', $data['lastname'], PDO::PARAM_STR);

        $statement->execute();
        header("Location: /");
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php if (!empty($errors)) : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <ul> <?php foreach ($friends as $friend) : ?>
            <li><?= $friend['firstname'] . ' ' . $friend['lastname'] ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <form action="" method="POST">
        <label for="firstname"></label>
        <input id="firstname" name="firstname" type="text">
        <label for="lastname"></label>
        <input id="lastname" name="lastname" type="text">
        <button>Add a Friend</button>
    </form>

</body>

</html>