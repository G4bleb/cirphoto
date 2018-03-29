<?php
function var_dump_ret($mixed = null) {
ob_start();
var_dump($mixed);
$content = ob_get_contents();
ob_end_clean();
return $content;
} ?>

<?php
function authenticate($db){
  $login = $_SERVER['PHP_AUTH_USER'];
  $password = $_SERVER['PHP_AUTH_PW'];

  if (!dbCheckUser($db, $login, $password)) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
  }
  $token = base64_encode(openssl_random_pseudo_bytes(12));
  dbAddToken($db, $login, $token);

  header('Content-Type: text/plain; charset=utf-8');
  header('Cache-control: no-store, no-cache, must-revalidate');
  header('Pragma: no-cache');
  echo $token;
  exit;
}

$login = 'cw_cir2';
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
// error_log(var_dump_ret($_SERVER));
if (isset($_SERVER['PATH_INFO'])) {
  $request = substr($_SERVER['PATH_INFO'], 1);
}else{
  $request = '';
}

// $request = substr(str_replace('BASE_URL' , "" , $_SERVER['REQUEST_URI']),1);

$request = explode('/', $request);
$requestRessource = array_shift($request);

$data = $requestType.':'.$requestRessource;

// Check the id associated to the request.
$id = array_shift($request);
if ($id == '')
$id = NULL;



// Photos request.
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


if ($requestRessource == 'comments')
{
  // $message =  "hello mon bro c'est le commentaire qu a été demandé";
  // $logfile = 'logfile.log';
  // error_log($message."\n", 3, $logfile);
  if ($requestType == 'GET') {
    // $message =  "hello mon bro c'est ".$_GET['login'];
    // $logfile = 'logfile.log';
    // error_log($message."\n", 3, $logfile);
    // if (isset($_GET['login']))
    //   $data = dbRequestComments($db, intval($id), $_GET['login']);
    // else
      $data = dbRequestComments($db, intval($id));
  }

  if ($requestType == 'POST')
  dbAddComment($db, $login, intval($id), $_POST['comment']);
  // dbAddComment($db, $_POST['login'], intval($id), $_POST['comment']);


  if ($requestType == 'PUT'){
    error_log(var_dump_ret($_GET));
    parse_str(file_get_contents('php://input'), $_PUT);
    // dbModifyComment($db, intval($id), $_PUT['login'], $_PUT['comment']);
    dbModifyComment($db, intval($id), $login, $_PUT['comment']);
  }
  if ($requestType == 'DELETE')
    // dbDeleteComment($db, intval($id), $_GET['login']);
    dbDeleteComment($db, intval($id), $login);
}


// Send data to the client.

header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;

// function sendJsonData($data, $code){
//
// }
?>
