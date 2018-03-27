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
/**
* @Author: Thibault Napoléon <Imothep>
* @Company: ISEN Yncréa Ouest
* @Email: thibault.napoleon@isen-ouest.yncrea.fr
* @Created Date: 29-Jan-2018 - 16:48:46
* @Last Modified: 29-Jan-2018 - 21:46:02
*/

require_once('database.php');
header('Content-Type: text/plain; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
// Databse connexion.
$db = dbConnect();
if (!$db)
{
  header ('HTTP/1.1 503 Service Unavailable');
  exit();
}

// Check the request.
$requestType = $_SERVER['REQUEST_METHOD'];
$request = substr($_SERVER['PATH_INFO'], 1);
$request = explode('/', $request);
$requestRessource = array_shift($request);

$data = $requestType.':'.$requestRessource;

// Check the id associated to the request.
$id = array_shift($request);
if ($id == '')
$id = NULL;

// Photos request.
if (isset($_GET['id'])){
  $data = dbRequestPhoto($db, $_GET['id']);
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
  //
  // if ($requestType == 'POST')
  //   dbAddTwitt($db, $_POST['login'], $_POST['text']);
  //
  // if ($requestType == 'PUT'){
  //   parse_str(file_get_contents('php://input'), $_PUT);
  //   dbModifyTwitt($db, intval($id), $_PUT['login'], $_PUT['text']);
  // }
  // if ($requestType == 'DELETE')
  //   dbDeleteTwitt($db, intval($id), $_GET['login']);
}

// Send data to the client.

header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;

// function sendJsonData($data, $code){
//
// }
?>
