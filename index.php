<?php
  include_once 'passport/PassportClient.php';

  // Информация для подключения к базе данных
  $dbServer = '';
  $dbLogin = '';
  $dbPassword = '';
  $dbName = '';

  // Данные сервиса
  $apiKey = "";
  $applicationId = '';
  $client = new inversoft\PassportClient($apiKey, ""); // Backend server

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["method"] == 'post') { // Обращаемся к серверу, записываем куки и в БД
    $request = [];

    $request["applicationId"] = $applicationId;
    $request["email"] = $_POST["name"];
    $request["password"] = $_POST["password"];
    $result = $client->login($request);

    if ($result->successResponse) {
      $response = $result->successResponse;
      $link = mysql_connect($dbServer, $dbLogin, $dbPassword) or die("Could not connect");
      mysql_select_db($dbName) or die("Could not select database");

      $email = $response->user->email;
      $token = $response->token;
      $name = $response->user->firstName;
      $sql = "INSERT INTO users ( email, token, name) VALUES ('$email', '$token', '$name')";
      setcookie('login', $token, time() + (86400 * 30), "/");
      $isLogged[2] = $name;

      $result = mysql_query($sql) or die("Query failed");;
      mysql_close($link);
    }
  } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["method"] == 'delete') { // Удаляем куки и запись из БД
    if (isset( $_COOKIE['login'] )) {
      $token = $_COOKIE['login'];
      $link = mysql_connect($dbServer, $dbLogin, $dbPassword) or die("Could not connect");
      mysql_select_db($dbName) or die("Could not select database");

      $query="DELETE FROM users WHERE `token` = '$token' LIMIT 1";
      $result = mysql_query($query) or die("Query failed");

      mysql_close($link);
      setcookie('login', null, -1, "/");
    }
  } else if (isset( $_COOKIE['login'] )) { // Выводим имя при залогиненности
      $token = $_COOKIE['login'];
      $link = mysql_connect($dbServer, $dbLogin, $dbPassword) or die("Could not connect");
      mysql_select_db($dbName) or die("Could not select database");

      $query="SELECT * FROM users WHERE `token` = '$token'";
      $result = mysql_query($query) or die("Query failed");

      $isLogged = mysql_fetch_row($result);

      mysql_close($link);
  }

 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Test Passport</title>
   </head>
   <body>
     <?php if ($isLogged): ?>
       <p>Hello, <?= $isLogged[2] ?>!</p>
       <form class="" action="index.php" method="post">
         <input type="hidden" name="method" value="delete">
         <input type="submit" name="submit" value="Logout">
       </form>
     <?php else: ?>
       <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["method"] == 'delete'): ?>
         <p>Logout successful!</p>
       <?php endif; ?>
       <form class="" action="index.php" method="post">
         <input type="hidden" name="method" value="post">
         <label for="name">E-Mail:</label><br>
         <input type="text" name="name" value="">
         <br>
         <label for="password">Password:</label><br>
         <input type="password" name="password" value=""><br>
         <input type="submit" name="submit" value="Submit">
       </form>
     <?php endif; ?>
   </body>
 </html>
