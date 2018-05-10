<?php
/**
 * Created by PhpStorm.
 * User: bayusyafresalizdham
 * Date: 07/05/18
 * Time: 11.12
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';


function auth($email,$pass){
    $conn = new mysqli("localhost","root","","db_ml");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }

    $stmt = $conn->prepare(
        "SELECT token FROM user WHERE email = ? && password = ?");
    $stmt->bind_param( "ss", $email, $pass);
    $stmt->execute();
    $stmt->bind_result($token);
    $temptoken = "";
    while ($stmt->fetch()) {
        $temptoken = $token;
    }

    $stmt->close();
    return $temptoken;

}
$app = new \Slim\App;
$app->get('/category/{type}', function (Request $request, Response $response, array $args) {

    $conn = new mysqli("localhost","root","","db_ml");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }
    $query = "";
    if(isset($args['type'])){
        if($args['type'] == "expense"){
            $query = "select * from category where type = 0";
        }else if($args['type'] == "income"){
            $query = "select * from category where type = 1";
        }else if($args['type'] == "other"){
            $query = "select * from category where type = 2";
        }
    }
    $resul = $conn->query($query);
    while($row = $resul->fetch_assoc()){
        $data[] = $row;
    }
    echo json_encode($data,JSON_PRETTY_PRINT);
});


$app->post('/login', function (Request $request, Response $response, array $args) {

    $email = $request->getParsedBody()["email"];
    $pass = $request->getParsedBody()["password"];
    $data = auth($email,md5($pass));
    $message = $data != "" ? "Login Berhasil" : "Username atau password anda salah";
    $vdata = array('token'=>$data);
    $msg = array('status' => 1,
        'message'=>$message,
        'data' => $vdata);
    echo json_encode($msg,JSON_PRETTY_PRINT);
});

$app->get('/category', function (Request $request, Response $response, array $args) {

    $conn = new mysqli("localhost","root","","db_ml");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }
    $query = "select * from category";
    $resul = $conn->query($query);
    while($row = $resul->fetch_assoc()){
        $data[] = $row;
    }

    $msg = array('status' => 1,
        'message'=>'Category berhasil di ambil',
        'data' => $data);
    echo json_encode($msg,JSON_PRETTY_PRINT);
});

$app->post('/newmovie', function (Request $request, Response $response, array $args) {

    $conn = new mysqli("localhost","root","","moviedb");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }
    $query = "insert into movie values (?,?,?)";
    $stmt = $conn->prepare($query);
    $movieid = 0;
    $movietitle = $request->getParsedBody()["movietitle"];
    $moviegenre = $request->getParsedBody()["moviegenre"];
    $stmt->bind_param("iss",$movieid,$movietitle,$moviegenre);
    $stmt->execute();
});

$app->run();