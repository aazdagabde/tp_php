<?php

session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=sql211.infinityfree.com;dbname=if0_39105974_showmarks", "if0_39105974", 'jabtcGHVgGa');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idStudent = $_POST['student']  ?? '';
    $idSubject = $_POST['subject']  ?? '';
    $mark      = $_POST['mark']     ?? '';

    if ($idStudent && $idSubject && $mark !== '') {
        if (!is_numeric($mark) || $mark < 0 || $mark > 20) {
            $message = "<div class='alert alert-warning'>La note doit être comprise entre 0 et 20.</div>";
        } else {
            try {
                $stmt = $conn->prepare(
                    "INSERT INTO marks (idSubject, idStudent, mark)
                     VALUES (:subject, :student, :mark)"
                );
                $stmt->execute([
                    ':subject' => $idSubject,
                    ':student' => $idStudent,
                    ':mark'    => $mark
                ]);
                $message = "<div class='alert alert-success'>Note ajoutée !</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Erreur : "
                         . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>Tous les champs sont obligatoires.</div>";
    }
}


$students = $conn->query(
    "SELECT id_apogee, first_name, last_name FROM students ORDER BY first_name"
)->fetchAll(PDO::FETCH_ASSOC);

$subjects = $conn->query(
    "SELECT id, name FROM subjects ORDER BY name"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une note • ITIRC Show Marks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <span class="navbar-brand">ITIRC Show Marks</span>
            <button type="button" class="navbar-toggle collapsed"
                    data-toggle="collapse" data-target="#menu"
                    aria-expanded="false">
                <span class="sr-only">Basculer la navigation</span>
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Afficher les notes</a></li>
                <li class="active"><a href="addMarks.php">Ajouter une note</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?= $message ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <form method="post" action="">
                <div class="form-group">
                    <label for="student">Étudiant</label>
                    <select name="student" id="student" class="form-control" required>
                        <option value="" disabled selected>-- Choisir --</option>
                        <?php foreach ($students as $st): ?>
                            <option value="<?= $st['id_apogee'] ?>">
                                <?= htmlspecialchars($st['first_name'] . ' ' . $st['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Matière</label>
                    <select name="subject" id="subject" class="form-control" required>
                        <option value="" disabled selected>-- Choisir --</option>
                        <?php foreach ($subjects as $sb): ?>
                            <option value="<?= $sb['id'] ?>">
                                <?= htmlspecialchars($sb['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mark">Note</label>
                    <input type="number" step="0.01" min="0" max="20"
                           name="mark" id="mark" class="form-control"
                           placeholder="ex. 15.5" required>
                </div>

              <button type="submit" class="btn btn-default">Submit</button>
              
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script
    src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
