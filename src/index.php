<?php session_start(); ?>
<?php
error_reporting(E_ALL & ~E_NOTICE);
include "functions.php";
if ($_SESSION["user"]) {
  echo "<meta http-equiv='refresh' content='0;url=user-overview.php'>";
  die();
}
?>
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
        <input required type="text" name="gebruikersnaam" placeholder="gebruikersnaam" value="<?php echo $_POST['gebruikersnaam'] ?>">
        <input required type="password" name="wachtwoord" placeholder="wachtwoord" value="">
        <input type="submit" name="submit" value="login">
        <a href="new-user.php">cre&euml;er account</a>
      </form>
      <?php
      if (isset($_POST["submit"])) {
        $user = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM gebruikers WHERE gebruikersnaam = '".$_POST["gebruikersnaam"]."'")[0];
        if ($user) {
          if (password_verify($_POST["wachtwoord"], $user["wachtwoord"])) {
            $_SESSION["user"] = $user;
            $_SESSION["user"]["naam"] = $_SESSION["user"]["voornaam"] . " " . $_SESSION["user"]["achternaam"];
            echo "<succes>succesvol ingelogd</succes><meta http-equiv='refresh' content='1;url=user-overview.php'>";
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
