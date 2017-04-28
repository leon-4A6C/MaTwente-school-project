<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Ma Twente</title>
    <link rel="stylesheet" href="styles/main.css">
  </head>
  <body>
    <main class="login">
      <form name="form" action="" method="post">
        <input required type="text" name="gebruikersnaam" placeholder="gebruikersnaam" value="">
        <input required type="password" name="wachtwoord" placeholder="wachtwoord" value="">
        <input type="submit" name="submit" value="login">
        <a href="new-user.php">cre&euml;er account</a>
      </form>
      <?php
      if (isset($_POST["submit"])) {
        $user = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM gebruikers WHERE gebruikersnaam = '".$_POST["gebruikersnaam"]."'")[0];
        if ($user) {
          if ($user["password"] == hash("sha256", $_POST["password"])) {
            $_SESSION["user"] = $user;
            echo "<succes>succesvol ingelogd</succes><meta http-equiv='refresh' content='2;url=user-overview.php'> ";
          } else {
            echo "<error>verkeerde wachtwoord ingevuld</error>";
          }
        } else {
          echo "<error>verkeerde gebruikersnaam ingevuld</error>";
        }
      }
      ?>
    </main>
  </body>
</html>
