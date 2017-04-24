<?php session_start(); ?>
<?php
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
        <?php
        if (empty($_SESSION["name"]) || !$_SESSION["name"]) {
          echo "<div class='profile'>";
          echo "<a href='index.php'>login</a></div>";
        } else {
          echo "<div class='profile'><a href=''#'>";
          echo "<img src=\"images/profiles/".$_SESSION["profileImg"]."\" alt=\"profile\" class=\"profilePicture\">";
          echo "</a><ul><li>";
          echo "<a href='$thisPage?logout=true'>logout</a>";
          echo "</li><li><a href='#'>settings</a></li></ul></div>";
          echo "<div class='status'><span>".$_SESSION["name"]."</span><br>
          <a href='$thisPage?logout=true'>logout</a></div>";
        }
        ?>
        <?php if (isset($_GET["logout"])) {
          logout();
        } ?>

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
        <label for="gender">geslacht</label><br>
        <label for="male">m</label><input id="male" required type="radio" name="gender" value="m" checked="true">
        <label for="female">v</label><input id="female" required type="radio" name="gender" value="v">
        <select required name="department_id">
          <option value="false">afdeling</option>
          <?php
          $afdelingen = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM afdelingen");
          foreach ($afdelingen as $key => $value) {
            echo "<option value='".$value["id"]."'>".$value["naam"]."</option>";
          }
          ?>
        </select>
        <select required name="pc_nummer">
          <option value="false">configuratie</option>
          <?php
          $configuraties = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT pc_nummer FROM configuraties");
          foreach ($configuraties as $key => $value) {
            echo "<option value='".$value["pc_nummer"]."'>".$value["pc_nummer"]."</option>";
          }
          ?>
        </select>
        <input type="number" min="100" max="999" name="intern_tel" value="" placeholder="intern telefoonnummer">
        <input required type="email" name="email" value="" placeholder="email">
        <input required type="text" name="username" value="" placeholder="gebruikersnaam">
        <input required type="password" name="password" value="" placeholder="wachtwoord">
        <label for="profileImg">profiel foto</label>
        <input type="file" accept="image/*; capture=camera" name="profileImg" size="40">
        <input type="submit" name="submit" value="cre&euml;er account">
      </form>
      <?php #form handler
      if (isset($_POST["submit"])) {
        $profile_path = "defaultProfile.svg";
        $toegangs_level = "user";
        if ($_POST["department_id"] == false) {
          $_POST["department_id"] = null;
        }
        insertDataToDb("83.82.240.2", "user", "pass", "project", "
        INSERT INTO gebruikers(voornaam, achternaam, afdelingen_id, intern_tel, email, configuraties_nummer, gebruikersnaam, wachtwoord, profile_path, toegangs_level)
        VALUES('".$_POST["name"]."','".$_POST["lastname"]."', ".$_POST["department_id"].", ".$_POST["intern_tel"].", '".$_POST["email"]."', '".$_POST["pc_nummer"]."',
         '".$_POST["username"]."', '".hash("sha256", $_POST["password"])."', '".$profile_path."', '".$toegangs_level."')");
      }
      ?>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
