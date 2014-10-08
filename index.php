<?php

ini_set("display_errors",1);
require 'Slim/Slim.php';
//if($_POST)
//{
//    echo "asdfasdf";
//    exit;
//}
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/users', 'getUsers');
$app->get('/users/:id',    'getUser');
$app->get('/users/location/:id',    'getLocation');
$app->get('/users/search/:query', 'findByName');
$app->post('/users', 'addUser');
$app->put('/users/:id', 'updateUser');
$app->put('/users/location/:id', 'updateLocation');
$app->delete('/users/:id',    'deleteUser');


$app->run();

function getUsers() {
    $sql_query = "SELECT `name`,`id`,`email`,`date`,`ip`,`lastlocation` FROM restAPI ORDER BY name";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
}



function getUser($id) {
    $sql = "SELECT `name`,`email`,`date`,`ip`, `lastlocation` FROM restAPI WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $user = $stmt->fetchObject();  
        $dbCon = null;
        echo json_encode($user); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function getLocation($id) {
    $sql = "SELECT `lastlocation` FROM restAPI WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $user = $stmt->fetchObject();  
        $dbCon = null;
        echo json_encode($user); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


function addUser() {
    global $app;
    $req = $app->request(); // Getting parameter with names
    $paramName = $req->params('name'); // Getting parameter with names
    $paramEmail = $req->params('email'); // Getting parameter with names
    
    $sql = "INSERT INTO restAPI (`name`,`email`,`ip`) VALUES (:name, :email, :ip)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("email", $paramEmail);
        $stmt->bindParam("ip", $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
        $user->id = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode($user); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function updateUser($id) {
    global $app;
    $req = $app->request();
    $paramName = $req->params('name');
    $paramEmail = $req->params('email');
    
    $sql = "UPDATE restAPI SET name=:name, email=:email WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("email", $paramEmail);
        $stmt->bindParam("id", $id);
        $status->status = $stmt->execute();
        
        $dbCon = null;
        echo json_encode($status); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function updateLocation($id) {
    global $app;
    $req = $app->request();
    $paramLocation = $req->params('lastlocation');
    
    $sql = "UPDATE restAPI SET lastlocation=:lastlocation WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("lastlocation", $paramLocation);
        $stmt->bindParam("id", $id);
        $status->status = $stmt->execute();
        
        $dbCon = null;
        echo json_encode($status); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


function deleteUser($id) {
    $sql = "DELETE FROM restAPI WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id", $id);
        $status->status = $stmt->execute();
        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function findByName($query) {
    $sql = "SELECT * FROM restAPI WHERE UPPER(name) LIKE :query ORDER BY name";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"user": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function getConnection() {
    try {
        $db_username = "after_dark_dk";
        $db_password = "g6paxtd2";
        $conn = new PDO('mysql:host=mysql12.unoeuro.com;dbname=after_dark_dk_db', $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}

?>
