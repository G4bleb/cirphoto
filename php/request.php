<!--
 /*
 * @Author: Gabriel Lebis
 * @GitHub: github.com/g4bleb
 */
-->
<?php
//------------------------------------------------------------------------------
//--- authenticate -------------------------------------------------------------
//------------------------------------------------------------------------------
// Generates the token.
// \param db The connected database.
function authenticate($db){

  $login = $_SERVER['PHP_AUTH_USER'];
  $password = $_SERVER['PHP_AUTH_PW'];

  if (!dbCheckUser($db, $login, $password)) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
  }
  $token = base64_encode(openssl_random_pseudo_bytes(12));//For some reason there's a carriage return at the start of $token
  $token = substr($token, 1); //Remove it
  dbAddToken($db, $login, $token);

  header('Content-Type: text/plain; charset=utf-8');
  header('Cache-control: no-store, no-cache, must-revalidate');
  header('Pragma: no-cache');
  echo $token;
  exit;
}
//------------------------------------------------------------------------------
//--- verifyToken -------------------------------------------------------------
//------------------------------------------------------------------------------
// Checks the token.
// \param db The connected database.
function verifyToken($db){
  $headers = getallheaders();

  $token = $headers['Authorization'];

  if (preg_match('/Bearer (.*)/', $token, $tab))$token = $tab[1];
  if(!($login = dbVerifyToken($db, $token))){
    header('HTTP/1.1 401 Unauthorized');
    exit;
  }

  return $login;
}
?>


<?php
require_once('database.php');
header('Content-Type: text/plain; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
// Database connection.
$db = dbConnect();
if (!$db)
{
  header ('HTTP/1.1 503 Service Unavailable');
  exit();
}

// Check the request.
$requestType = $_SERVER['REQUEST_METHOD'];

if (isset($_SERVER['PATH_INFO'])) { //If there's something to extract from the path
  $request = substr($_SERVER['PATH_INFO'], 1);
}else{
  $request = '';
}

$request = explode('/', $request);
$requestRessource = array_shift($request);
$data = $requestType.':'.$requestRessource;

// Check the id associated to the request.
$id = array_shift($request);
if ($id == '')
$id = NULL;

if ($requestRessource == 'authenticate') {
  authenticate($db);
}

if ($requestRessource == 'checkToken')
{
  if (verifyToken($db)) header('HTTP/1.1 200 OK');
}


//Photos request
if ($requestRessource == 'photos' || $requestRessource == 'checkToken'){
  if (isset($id)){
    $data = dbRequestPhoto($db, intval($id));
    if (!$data) {
      header('HTTP/1.1 400 Bad Request');
      exit;
    }
  }else{
    $data = dbRequestPhotos($db);
    if (!$data) {
      header('HTTP/1.1 400 Bad Request');
      exit();
    }
  }
}


//Comments request
if ($requestRessource == 'comments')
{
  //Requesting comments for review
  if ($requestType == 'GET') {
    $data = dbRequestComments($db, intval($id));
    dbRequestComments($db, intval($id));
  }
  //Requesting comment sending
  if ($requestType == 'POST'){
    dbAddComment($db, verifyToken($db), $_POST['id'], $_POST['comment']);
  }
  //Requesting comment deletion
  if ($requestType == 'DELETE')
  dbDeleteComment($db, verifyToken($db), intval($id));
}

// Send data to the client.

header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;
?>
