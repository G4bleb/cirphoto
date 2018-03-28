<?php
// $requestType = $_SERVER['REQUEST_METHOD'];
// $request = substr($_SERVER['PATH_INFO'], 1);
// $request = explode('/', $request);
// $requestRessource = array_shift($request);
//
// $data = $requestType.':'.$requestRessource;
//
// header('Content-Type: text/plain; charset=utf-8');
// header('Cache-control: no-store, no-cache, must-revalidate');
// header('Pragma: no-cache');
//
// $db = dbConnect();
//
// if (!$db){
//   header ('HTTP/1.1 503 Service Unavailable');
//   exit();
// }
//
// echo json_encode($data);
// exit();
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
$request = substr($_SERVER['PATH_INFO'], 1);
// $request = str_replace('BASE_URL' , "" , $_SERVER['REQUEST_URI']);
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
  if ($requestType == 'GET') {
    if (isset($_GET['login']))
      $data = dbRequestComments($db, $_GET['id'], $_GET['login']);
    else
      $data = dbRequestComments($db, $_GET['id']);
  }

  if ($requestType == 'POST')
    dbAddComment($db, $_POST['login'], $_GET['id'], $_POST['text']);

  if ($requestType == 'PUT'){
    parse_str(file_get_contents('php://input'), $_PUT);
    dbModifyComment($db, intval($id), $_PUT['login'], $_PUT['text']);
  }
  if ($requestType == 'DELETE')
    dbDeleteComment($db, intval($id), $_GET['login']);
}


// Send data to the client.

header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;

// function sendJsonData($data, $code){
//
// }
?>
