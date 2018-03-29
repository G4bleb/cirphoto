<?php


  require_once('constants.php');

  //----------------------------------------------------------------------------
  //--- dbConnect --------------------------------------------------------------
  //----------------------------------------------------------------------------
  // Create the connection to the database.
  // \return False on error and the database otherwise.
  function dbConnect()
  {
    try
    {
      $db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8',
        DB_USER, DB_PASSWORD);
    }
    catch (PDOException $exception)
    {
      error_log('Connection error: '.$exception->getMessage());
      return false;
    }
    return $db;
  }

  //----------------------------------------------------------------------------
  //--- dbRequestPhotos --------------------------------------------------------
  //----------------------------------------------------------------------------
  // Get all photos.
  // \param db The connected database.
  // \return The list of small photos.
  function dbRequestPhotos($db)
  {
    try
    {
      $request = 'select id, small as src from photos';
      $statement = $db->prepare($request);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
  }

  //----------------------------------------------------------------------------
  //--- dbRequestPhoto ---------------------------------------------------------
  //----------------------------------------------------------------------------
  // Get a specific photo.
  // \param db The connected database.
  // \param id The id of the photo.
  // \return The photo.
  function dbRequestPhoto($db, $id)
  {
    try
    {
      $request = 'select id, title, large as src from photos where id=:id';
      $statement = $db->prepare($request);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
  }





  //----------------------------------------------------------------------------
  //--- dbRequestComments --------------------------------------------------------
  //----------------------------------------------------------------------------
  // Function to get all twitts (if $login='') or the twitts of a user
  // (otherwise).
  // \param db The connected database.
  // \param login The login of the user (for specific request).
  // \return The list of twitts.
  function dbRequestComments($db, $id ,$login = '')
  {
    try
    {
      $request = 'select * from comments';
      $request .= ' where photoId=:id';
      if ($login != '')
        $request .= ' and userLogin=:login';
      $statement = $db->prepare($request);
      if ($login != '')
        $statement->bindParam(':login', $login, PDO::PARAM_STR, 20);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
  }

  //----------------------------------------------------------------------------
  //--- dbAddCComment -----------------------------------------------------------
  //----------------------------------------------------------------------------
  // Add a twitt.
  // \param db The connected database.
  // \param login The login of the user.
  // \param text The twitt to add.
  // \return True on success, false otherwise.
  function dbAddComment($db, $userLogin, $id, $comment)
  {
    try
    {
      $request = 'insert into comments(userLogin, photoId, comment) values(:userLogin, :id,:comment)';
      $statement = $db->prepare($request);
      $statement->bindParam(':userLogin', $userLogin, PDO::PARAM_STR, 20);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->bindParam(':comment', $comment, PDO::PARAM_STR, 256);
      $statement->execute();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return true;
  }

  //----------------------------------------------------------------------------
  //--- dbModifyComment ----------------------------------------------------------
  //----------------------------------------------------------------------------
  // Function to modify a twitt.
  // \param db The connected database.
  // \param id The id of the twitt to update.
  // \param login The login of the user.
  // \param text The new twitt.
  // \return True on success, false otherwise.
  function dbModifyComment($db, $id, $login, $text)
  {
    try
    {
      $request = 'update comments set comment=:text where id=:id and userLogin=:login ';
      $statement = $db->prepare($request);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->bindParam(':login', $login, PDO::PARAM_STR, 20);
      $statement->bindParam(':text', $text, PDO::PARAM_STR, 80);
      $statement->execute();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return true;
  }

  //----------------------------------------------------------------------------
  //--- dbDeleteComment ----------------------------------------------------------
  //----------------------------------------------------------------------------
  // Delete a twitt.
  // \param db The connected database.
  // \param id The id of the twitt.
  // \param login The login of the user.
  // \return True on success, false otherwise.
  function dbDeleteComment($db, $id, $login)
  {
    try
    {
      $request = 'delete from comments where id=:id and userLogin=:login';
      $statement = $db->prepare($request);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->bindParam(':login', $login, PDO::PARAM_STR, 20);
      $statement->execute();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return true;
  }

  //----------------------------------------------------------------------------
  //--- dbCheckUser ------------------------------------------------------------
  //----------------------------------------------------------------------------
  // Check login/password of a user.
  // \param db The connected database.
  // \param login The login to check.
  // \param password The password to check.
  // \return True on success, false otherwise.
  function dbCheckUser($db, $login, $password)
  {
    try
    {
      $request = 'select * from users where login=:login and
        password=sha1(:password)';
      $statement = $db->prepare($request);
      $statement->bindParam (':login', $login, PDO::PARAM_STR, 20);
      $statement->bindParam (':password', $password, PDO::PARAM_STR, 40);
      $statement->execute();
      $result = $statement->fetch();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    if (!$result)
      return false;
    return true;
  }

  //----------------------------------------------------------------------------
  //--- dbAddToken -------------------------------------------------------------
  //----------------------------------------------------------------------------
  // Add a token to the database.
  // \param db The connected database.
  // \param login The login assocciated with the token.
  // \param token The token to add.
  // \return True on success, false otherwise.
  function dbAddToken($db, $login, $token)
  {
    try
    {
      $request = '
        update users set token=:token where login=:login';
      $statement = $db->prepare($request);
      $statement->bindParam(':login', $login, PDO::PARAM_STR, 20);
      $statement->bindParam(':token', $token, PDO::PARAM_STR, 20);
      $statement->execute();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return true;
  }

  //----------------------------------------------------------------------------
  //--- dbVerifyToken ----------------------------------------------------------
  //----------------------------------------------------------------------------
  // Verify a user token.
  // \param db The connected database.
  // \param token The token to check.
  // \return Login on success, false otherwise.
  function dbVerifyToken($db, $token)
  {
    try
    {
      $request = 'select login from users where token=:token';
      $statement = $db->prepare($request);
      $statement->bindParam (':token', $token, PDO::PARAM_STR, 20);
      $statement->execute();
      $result = $statement->fetch();
    }
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    if (!$result)
      return false;
    return $result['login'];
  }
?>
