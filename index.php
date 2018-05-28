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
    $conn = new mysqli("localhost","root","","db_moneylover");
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
function wallet_user($token){
    $conn = new mysqli("localhost","root","","db_moneylover");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }
    $id = cektoken($token);
    $stmt = $conn->prepare(
        "select t.wallet_name,w.id_wallet_user from wallet_user w,wallet_type t where w.id_wallet_type = t.id_wallet_type and id_user = ?");
    $stmt->bind_param( "s", $id);
    $stmt->execute();
    $stmt->bind_result($wallet_name,$id_wallet_user);
    $twallet_name = array();
    while ($stmt->fetch()) {
        $data = array();
        $data["id_wallet"] =$id_wallet_user;
        $data["name_wallet"] =$wallet_name;
        $data["nominal_wallet"] =wallet_nominal($id_wallet_user);
        $twallet_name[] = $data;
    }

    $stmt->close();
    return $twallet_name;

}

function wallet_nominal($id){
    $conn = new mysqli("localhost","root","","db_moneylover");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }

    $stmt = $conn->prepare(
        "SELECT sum(nominal) as jum FROM `transaction` WHERE id_wallet_user = ?");
    $stmt->bind_param( "s", $id);
    $stmt->execute();
    $stmt->bind_result($jum);
    $temptoken = 0;
    while ($stmt->fetch()) {
        $temptoken += intval($jum);
    }

    $stmt->close();
    return $temptoken;

}
function cektoken($email){
    $conn = new mysqli("localhost","root","","db_moneylover");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }

    $stmt = $conn->prepare(
        "SELECT id_user FROM user WHERE token = ?");
    $stmt->bind_param( "s", $email);
    $stmt->execute();
    $stmt->bind_result($id_user);
    $temptoken = "";
    while ($stmt->fetch()) {
        $temptoken = $id_user;
    }

    $stmt->close();
    return $temptoken;

}
$app = new \Slim\App;
$app->get('/category/{type}', function (Request $request, Response $response, array $args) {
    if(empty($request->getHeader("token"))){
        $data = array();
        $msg = array('status' => 0,
            'message'=>'token tidak ada',
            'data' => $data);
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $conn = new mysqli("localhost","root","","db_moneylover");
            if($conn -> connect_error){
                die("connection failed".$conn->connect_error);
            }
            $query = "";
            if(isset($args['type'])){
                if($args['type'] == "expense"){
                    $query = "select * from category where category_type = 0";
                }else if($args['type'] == "income"){
                    $query = "select * from category where category_type = 1";
                }else if($args['type'] == "other"){
                    $query = "select * from category where category_type = 2";
                }
            }
            $resul = $conn->query($query);
            while($row = $resul->fetch_assoc()){
                $data[] = $row;
            }

            $msg = array('status' => 1,
                'message'=>'Category berhasil di ambil',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }

});


$app->post('/login', function (Request $request, Response $response, array $args) {

    $email = $request->getParsedBody()["email"];
    $pass = $request->getParsedBody()["password"];
    $data = auth($email,md5($pass));
    $message = $data != "" ? "Login Berhasil" : "Username atau password anda salah";
    $status = $data != "" ? 1 : 0;
    $vdata = array('token'=>$data);
    $msg = array('status' => $status,
        'message'=>$message,
        'data' => $vdata);
    echo json_encode($msg,JSON_PRETTY_PRINT);
});


$app->post('/register', function (Request $request, Response $response, array $args) {

    $conn = new mysqli("localhost","root","","db_moneylover");
    if($conn -> connect_error){
        die("connection failed".$conn->connect_error);
    }
    //(`id_transaction`, `nominal`, `note`, `with_name`, `id_wallet_user`, `date`, `id_category`)
    $query = "insert into `user` values (?,?,?,?)";
    $stmt = $conn->prepare($query);
    if($stmt){

        $id = 0;
        $email = $request->getParsedBody()["email"];
        $pass = $request->getParsedBody()["password"];
        if($email != "" || $pass != "" ){
          $stmt->bind_param("isss",$id,$email,md5($pass),md5($email));
          $stmt->execute();
          //INSERT INTO `wallet_user`(`id_wallet_user`, `id_wallet_type`, `id_user`) VALUES ([value-1],[value-2],[value-3])
          $iduser = "select * from user";
          $resul = $conn->query($iduser);
          $iduser1 = 0;
          while($row = $resul->fetch_assoc()){
              $iduser1 = $row['id_user'];
          }
          $idwallet = 0;
          $query1 = "insert into `wallet_user` values (?,?,?,?)";
          $stmt1 = $conn->prepare($query1);
          $nama = "";
          $type = 1;
          $stmt1->bind_param("iiis",$idwallet,$type,$iduser1,$nama);
          $stmt1->execute();
          $type = 2;
          $stmt1->bind_param("iiis",$idwallet,$type,$iduser1,$nama);
          $stmt1->execute();

          $msg = array('status' => 1,
              'message'=>'registrasi berhasil.');
          echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
          $msg = array('status' => 0,
              'message'=>'registrasi gagal, form tidak boleh kosong.');
          echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }else{

        $msg = array('status' => 0,
            'message'=>'registrasi gagal.');
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }
});

$app->get('/category', function (Request $request, Response $response, array $args) {
    if(empty($request->getHeader("token"))){
        $data = array();
        $msg = array('status' => 0,
            'message'=>'token tidak ada',
            'data' => $data);
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $conn = new mysqli("localhost","root","","db_moneylover");
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
        }
    }
});

$app->get('/wallet', function (Request $request, Response $response, array $args) {
  $conn = new mysqli("localhost","root","","db_moneylover");
  if($conn -> connect_error){
      die("connection failed".$conn->connect_error);
  }
    if(empty($request->getHeader("token"))){
        $data = array();
        $msg = array('status' => 0,
            'message'=>'token tidak ada',
            'data' => $data);
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $data = wallet_user($request->getHeader("token")[0]);
            $iduser= cektoken($request->getHeader("token")[0]);
            $query = "select * from wallet_user where id_wallet_type = 3 and id_user =".$iduser;
            $resul = $conn->query($query);
            $c = count($data);
            while($row = $resul->fetch_assoc()){
                $data[$c]['id_wallet'] = $row['id_wallet_user'];
                $data[$c]['name_wallet'] = $row['name_from_user'];
                $data[$c]['nominal_wallet'] = 0;
                $c++;
            }
            $msg = array('status' => 1,
                'message'=>'Category berhasil di ambil',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }
});
$app->post('/wallet', function (Request $request, Response $response, array $args) {
  $conn = new mysqli("localhost","root","","db_moneylover");
  if($conn -> connect_error){
      die("connection failed".$conn->connect_error);
  }
    if(empty($request->getHeader("token"))){
        $msg = array('status' => 0,
            'message'=>'token tidak ada');
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah');
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $iduser = cektoken($request->getHeader("token")[0]);
            $idwallet = 0;
            $query1 = "insert into `wallet_user` values (?,?,?,?)";
            $stmt1 = $conn->prepare($query1);
            $nama = $request->getParsedBody()["walletname"];;
            $type = 3;
            $stmt1->bind_param("iiis",$idwallet,$type,$iduser,$nama);
            $stmt1->execute();
            $msg = array('status' => 1,
                'message'=>'wallet berhasil ditambah');
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }
});

$app->post('/transaction', function (Request $request, Response $response, array $args) {

    if(empty($request->getHeader("token"))){
        $msg = array('status' => 0,
            'message'=>'token tidak ada');
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $msg = array('status' => 0,
                'message'=>'token anda salah');
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{

            $conn = new mysqli("localhost","root","","db_moneylover");
            if($conn -> connect_error){
                die("connection failed".$conn->connect_error);
            }
            //(`id_transaction`, `nominal`, `note`, `with_name`, `id_wallet_user`, `date`, `id_category`)
            $query = "insert into `transaction` values (?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            if($stmt){

                $id_transaction = 0;
                $nominal = $request->getParsedBody()["nominal"];
                $note = $request->getParsedBody()["note"];
                $with_name = $request->getParsedBody()["with_name"];
                $id_wallet_user = $request->getParsedBody()["id_wallet_user"];
                $date = date("Y/m/d");
                $id_category = $request->getParsedBody()["id_category"];
                $stmt->bind_param("iissisi",$id_transaction,$nominal,$note,$with_name,$id_wallet_user,$date,$id_category);
                $stmt->execute();

                $msg = array('status' => 1,
                    'message'=>'data berhasil di tambahkan');
                echo json_encode($msg,JSON_PRETTY_PRINT);
            }else{

                $msg = array('status' => 0,
                    'message'=>'data tidak berhasil di tambahkan');
                echo json_encode($msg,JSON_PRETTY_PRINT);
            }
        }
    }
});


$app->get('/transaction/{id_wallet}', function (Request $request, Response $response, array $args) {

    if(empty($request->getHeader("token"))){
        $data = array();
        $msg = array('status' => 0,
            'message'=>'token tidak ada',
            'data' => $data);
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $data = array();
            $conn = new mysqli("localhost","root","","db_moneylover");
            if($conn -> connect_error){
                die("connection failed".$conn->connect_error);
            }
            //SELECT `id_transaction`, `nominal`, `note`, `with_name`, `id_wallet_user`, `date`, `id_category` FROM `transaction` WHERE 1
            $query = "select * from `transaction` t,`category` c  where t.id_category = c.id_category and id_wallet_user = ".$args["id_wallet"];
            $resul = $conn->query($query);
            while($row = $resul->fetch_assoc()){
                $data[] = $row;
            }

            $msg = array('status' => 1,
                'message'=>'Transaction berhasil di ambil',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }
});
$app->get('/transaction/{id_wallet}/{tgl}', function (Request $request, Response $response, array $args) {

    if(empty($request->getHeader("token"))){
        $data = array();
        $msg = array('status' => 0,
            'message'=>'token tidak ada',
            'data' => $data);
        echo json_encode($msg,JSON_PRETTY_PRINT);
    }else{
        if(cektoken($request->getHeader("token")[0]) == ""){
            $data = array();
            $msg = array('status' => 0,
                'message'=>'token anda salah',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }else{
            $data = array();
            $conn = new mysqli("localhost","root","","db_moneylover");
            if($conn -> connect_error){
                die("connection failed".$conn->connect_error);
            }

            $nominal = $request->getParsedBody()["date"];
            $tgl = $args["tgl"];
            //SELECT `id_transaction`, `nominal`, `note`, `with_name`, `id_wallet_user`, `date`, `id_category` FROM `transaction` WHERE 1
            $query = "select * from `transaction` t,`category` c  where t.id_category = c.id_category and id_wallet_user = '".$args["id_wallet"]."' and t.date = '".$tgl."'";;
            $resul = $conn->query($query);
            while($row = $resul->fetch_assoc()){
                $data[] = $row;
            }

            $msg = array('status' => 1,
                'message'=>'Transaction berhasil di ambil',
                'data' => $data);
            echo json_encode($msg,JSON_PRETTY_PRINT);
        }
    }
});

$app->run();
