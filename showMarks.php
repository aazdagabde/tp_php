<?php
/* ------------------------- Sécurité (facultatif) ------------------------- */
session_start();           // supprime ces deux lignes si l’accès est public
// if (!isset($_SESSION['login'])) { header('Location: login.php'); exit(); }

/* ---------------------- Connexion base de données ----------------------- */
try {
    $pdo = new PDO("mysql:host=sql211.infinityfree.com;dbname=if0_39105974_showmarks", "if0_39105974", 'jabtcGHVgGa');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}

/* ---------- Récupération des notes si le formulaire est soumis ---------- */
$marks      = [];   // tableau qui contiendra les notes
$average    = null; // moyenne
$studentRow = null; // infos étudiant

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apogee = trim($_POST['Apogee'] ?? '');

    if ($apogee === '' || !ctype_digit($apogee)) {
        $error = "Numéro Apogée invalide.";
    } else {
        /* On récupère les notes + la matière + l’étudiant dans une seule requête */
        $stmt = $pdo->prepare(
            "SELECT s.id_apogee,
                    CONCAT(s.first_name, ' ', s.last_name) AS fullname,
                    sub.name           AS subject,
                    m.mark
             FROM students s
             JOIN marks    m   ON m.idStudent = s.id_apogee
             JOIN subjects sub ON sub.id      = m.idSubject
             WHERE s.id_apogee = :apo
             ORDER BY sub.name"
        );
        $stmt->execute([':apo' => $apogee]);
        $marks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($marks) {
            $studentRow = $marks[0]; // même étudiant sur toutes les lignes
            /* Calcul de la moyenne */
            $stmt = $pdo->prepare(
                "SELECT AVG(mark) AS avgMark
                 FROM marks
                 WHERE idStudent = :apo"
            );
            $stmt->execute([':apo' => $apogee]);
            $average = number_format($stmt->fetchColumn(), 2);
        } else {
            $error = "Aucune note trouvée pour ce numéro Apogée.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos notes • ITIRC Show Marks</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">

    <h1 class="text-center">Consulter vos notes</h1>

    <!-- ---------- Formulaire de recherche ---------- -->
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="Apogee">Numéro Apogée</label>
                    <input type="text" name="Apogee" id="Apogee"
                           value="<?= isset($apogee) ? htmlspecialchars($apogee) : '' ?>"
                           class="form-control" placeholder="Ex. 1003" required>
                </div>
                <button class="btn btn-primary btn-block">Afficher les notes</button>
            </form>
        </div>
    </div>

    <!-- ---------- Tableau des notes ---------- -->
    <?php if ($marks): ?>
        <hr>
        <h3>Notes de : <strong><?= $studentRow['fullname'] ?> (<?= $studentRow['id_apogee'] ?>)</strong></h3>

        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Matière</th>
                <th>Note</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($marks as $k => $row): ?>
                <tr>
                    <td><?= $k + 1 ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['mark']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="alert alert-info text-center">
            Moyenne&nbsp;: <?= $average ?>
        </h3>
    <?php endif; ?>

</div><!-- /.container -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script
    src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
