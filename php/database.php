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
  function dbRequestComments($db, $login = '')
  {
    try
    {
      $request = 'select * from comments';
      if ($login != '')
        $request .= ' where login=:login';
      $statement = $db->prepare($request);
      if ($login != '')
        $statement->bindParam(':login', $login, PDO::PARAM_STR, 20);
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
  function dbAddComment($db, $login, $text)
  {
    try
    {
      $request = 'insert into comments(login, text) values(:login, :text)';
      $statement = $db->prepare($request);
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
      $request = 'update comments set text=:text where id=:id and login=:login ';
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
      $request = 'delete from comments where id=:id and login=:login';
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
?>
