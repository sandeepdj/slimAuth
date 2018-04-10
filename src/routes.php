<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;
use Tuupola\Base62;
// Routes
$app->post("/token", function ($request, $response, $args) use ($container){
    $con = $this->db;
    /* Here generate and return JWT to the client. */
    $valid_scopes = ["read", "write", "delete"];
    $fdata =(object)  $request->getParsedBody();

    $code = $fdata->code;
    $year = $fdata->year;

    $username = $fdata->username;
    $password = md5($fdata->password);
     
    $sql="select * from  clients where clcode='$code' AND acyr ='$year' ";
    $exe = mysqli_query($con,$sql) or die(mysqli_error($con));
    $res = mysqli_fetch_assoc($exe);
   

    $_SESSION['dbname'] =$res['dbnm'];
    $conn = $this->connect;
 
    $sqlau="select * from  hrmemployee where mobile='$username' AND lgpw ='$password' ";
    $exeau = mysqli_query($conn,$sqlau);
    $result = mysqli_fetch_assoc($exeau) ;

    $data['empname'] =   $_SESSION['dbname'];
    $data['gender'] = $result['gender'];
    $data['splzn'] = $result['splzn'];
    $data['education'] = $result['edu'];

    $requested_scopes = $request->getParsedBody() ?: [];
    $now = new DateTime();
    $future = new DateTime("+10 minutes");
    $server = $request->getServerParams();
    $jti = (new Base62)->encode(mt_rand(16));

    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "sub" => $server["PHP_AUTH_USER"],
        "scope" => $valid_scopes
    ];

    $secret = "123456789helo_secret";
    $token = JWT::encode($payload, $secret, "HS256");
    $data["token"] = $token;
    
    $data["expires"] = $future->getTimeStamp();
        return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/secure", function ($request, $response, $args) {
        $data = ["status" => 1, 'msg' => "This route is secure!"];
        return $response->withStatus(200)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/employees", function ($request, $response, $args) use ($container) {
   
    $conn = $this->connect;
    if($conn){
        $data['success'] = "Connected";
    }else{
        $data['Error'] = "Error";
    }

    $sql="select * from  hrmemployee limit 10";
    $exe = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($exe)){
        $data['employees'][] = $row ;
    }

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->get("/not-secure", function ($request, $response, $args) {
   $data = ["status" => 1, 'msg' => "No need of token to access me"];
   return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});



$app->post("/formData", function ($request, $response, $args) {
    $data = $request->getParsedBody();
     $result = ["status" => 1, 'msg' => $data];
   // Request with status response
    return $this->response->withJson($result, 200);
});

 


$app->get('/home', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
   // Render index view
    return $this->renderer->render($response, 'index.phtml', ["name" => "Welcome to Trinity Tuts demo Api"]);
});

 
