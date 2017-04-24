<?php session_start(); ?>
<?php
$_SESSION["user_type"] = "user";
$_SESSION["profileImg"] = "defaultProfile.svg";
$_SESSION["name"] = "John Doe";
include "functions.php";
$thisPage = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Twente</title>
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
  </head>
  <body>
    <header>
      <div class="profileBar">
        <div class="profile">
          <a href="#">
            <?php echo "<img src=\"images/profiles/".$_SESSION["profileImg"]."\" alt=\"profile\" class=\"profilePicture\">";?>
          </a>
          <ul>
            <li>
              <?php echo "<a href='$thisPage?logout=true'>logout</a>"; ?>
              <?php if (isset($_GET["logout"])) {
                logout();
              } ?>
            </li>
            <li>
              <a href="#">settings</a>
            </li>
          </ul>
        </div>
        <div class="status">
          <span><?php echo $_SESSION["name"]; ?></span><br>
          <?php echo "<a href='$thisPage?logout=true'>logout</a>"; ?>
        </div>
      </div>
      <img src="images/icon.svg" alt="logo" class="logo logoGone" style="display: none">
      <nav class="navClosed">
        <ul>
          <?php
            $menuItemsFile = fopen("menuItems.json", "r") or die("unable to open menuItems.json");
            $menuItems = json_decode(fread($menuItemsFile, filesize("menuItems.json")), true);
            fclose($menuItemsFile);
            foreach ($menuItems as $key => $value) {
              echo "<li>";
              foreach ($value as $key => $value) {
                echo "<a href='#'>$key</a><ul>";
                foreach ($value as $key => $value) {
                  if ($_SESSION["user_type"] === $value["type"] || $_SESSION["user_type"] === "admin") {
                    echo "<li><a href='".$value["path"]."'>".$value["title"]."</a><li>";
                  }
                }
                echo "</ul>";
              }
              echo "</li>";
            }
          ?>
        </ul>
      </nav>
      <img class="navArrow navArrowOpen" src="images/leftArrow.svg" alt="leftArrow">
    </header>
    <main class="new-user">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input required type="text" name="name" value="" placeholder="naam">
        <input required type="text" name="lastname" value="" placeholder="achternaam">
        <label for="gender">geslacht</label>
        <label for="gender">m</label><input required type="radio" name="gender" value="m" checked="true">
        <label for="gender">v</label><input required type="radio" name="gender" value="v">
        <select required name="department_id">
          <option value="false">afdeling</option>
          <?php
          $afdelingen = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM afdelingen");
          foreach ($afdelingen as $key => $value) {
            echo "<option value='".$value["id"]."'>".$value["naam"]."</option>";
          }
          ?>
        </select>
        <input required type="email" name="email" value="" placeholder="email">
        <input required type="text" name="username" value="" placeholder="gebruikersnaam">
        <input required type="password" name="password" value="" placeholder="wachtwoord">
        <input type="submit" name="submit" value="cre&euml;er account">
      </form>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
