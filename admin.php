<?php 
session_start();

if (isset($_SESSION['login'])) {

 
     try {
        $conn = new PDO("mysql:host=sql211.infinityfree.com;dbname=if0_39105974_showmarks", "if0_39105974", 'jabtcGHVgGa');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
    }


    $sql = "SELECT m.id , m.idStudent , s.first_name , s.last_name ,p.name ,m.mark 
    FROM marks m 
    join students s ON s.id_apogee=m.idStudent 
    join subjects p ON p.id = m.idSubject";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Show all marks</h1>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Num Apogee</th>
                        <th>Nom & Prenom</th>
                        <th>Matiere</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $value){
                    $name = $value["first_name"] . $value["last_name"];
                       
                    echo "<tr><td>".$value['id']."</td><td>".$value['idStudent']."</td><td>".$name."</td><td>".$value['name']."</td><td>".$value['mark']."</td><td><a href='functions.php?id=38&action=delete' >";     
                    }}
?>
 </table>
                    </div>
    </div>
</div>