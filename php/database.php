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
  //--- dbRequestComments ------------------------------------------------------
  //----------------------------------------------------------------------------
  // Get all comments of a specific photo.
  // \param db The connected database.
  // \param photoId The id of the photo.
  // \return The comments.
  function dbRequestComments($db, $photoId)
  {
    try
    {
      $request = 'select id, comment from comments where photoId=:photoId';
      $statement = $db->prepare($request);
      $statement->bindParam(':photoId', $photoId, PDO::PARAM_INT);
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
  //--- dbAddComment -----------------------------------------------------------
  //----------------------------------------------------------------------------
  // Add a comment to a specific photo.
  // \param db The connected database.
  // \param userLogin The login of the user.
  // \param photoId The id of the photo.
  // \param comment The comment to add.
  // \return True on success, false otherwise.
  function dbAddComment($db, $userLogin, $photoId, $comment)
  {
    try
    {
      $request = '
        insert into comments(userLogin, photoId, comment)
        values(:userLogin, :photoId, :comment)';
      $statement = $db->prepare($request);
      $statement->bindParam(':userLogin', $userLogin, PDO::PARAM_STR, 20);
      $statement->bindParam(':photoId', $photoId, PDO::PARAM_INT);
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
  //--- dbModifyComment --------------------------------------------------------
  //----------------------------------------------------------------------------
  // Modify a comment from a specific photo.
  // \param db The connected database.
  // \param userLogin The login of the user.
  // \param photoId The id of the photo.
  // \param comment The modified comment.
  // \return True on success, false otherwise.
  function dbModifyComment($db, $userLogin, $photoId, $comment)
  {
    try
    {
      $request = 'update comments set comment=:comment where photoId=:photoId and userLogin=:userLogin ';
      $statement = $db->prepare($request);
      $statement->bindParam(':comment', $comment, PDO::PARAM_STR, 256);
      $statement->bindParam(':userLogin', $userLogins, PDO::PARAM_STR, 20);
      $statement->bindParam(':photoId', $photoId, PDO::PARAM_INT);
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
  //--- dbDeleteComment --------------------------------------------------------
  //----------------------------------------------------------------------------
  // Delete a comment from a specific photo.
  // \param db The connected database.
  // \param userLogin The login of the user.
  // \param id The id of the comment.
  // \return True on success, false otherwise.
  function dbDeleteComment($db, $userLogin, $id)
  {
    try
    {
      $request = 'delete from comments where id=:id and
        userLogin=:userLogin';
      $statement = $db->prepare($request);
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->bindParam(':userLogin', $userLogin, PDO::PARAM_STR, 20);
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
  //--- dbCheckUserInjection ---------------------------------------------------
  //----------------------------------------------------------------------------
  // Check login/password of a user with possible SQL injection.
  // \param db The connected database.
  // \param login The login to check.
  // \param password The password to check.
  // \return True on success, false otherwise.
  function dbCheckUserInjection($db, $login, $password)
  {
    try
    {
      $statement = $db->query("select * from users where login='".$login."' and
        password=sha1('".$password."')");
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
