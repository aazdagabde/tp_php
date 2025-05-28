
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">


</head>



<?php 
if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new PDO ("mysql:host=sql211.infinityfree.com;dbname=if0_39105974_showmarks", "if0_39105974", 'jabtcGHVgGa');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT * FROM admin WHERE login = :username AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result){
        session_start();
       
        $_SESSION['login'] = $username;
      
      header("Location: index.php");
    }else{
        echo "<div class='alert alert-danger' style='text-align: center;'>Login failed</div>";
        }
}

?>



<body>
    <!-- create a centred login form -->
    <h1 style="text-align: center;">Authentification</h1>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                    <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        Password
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-default">Login</button>
                </form>
            </div>
          
        </div>
    </div>

  <p class="text-center" style="margin-top:12px;">
    <a href="showMarks.php" class="btn btn-link">Voir les notes (Show Marks)</a>
</p>
</body>
<footer class="container-fluid bg-light">
    <p class="text-center text-muted" style="margin:16px 0;">
        Réalisé par <strong>Aazdag Abdellah</strong> — ITIRC4
    </p>
</footer>

</html>