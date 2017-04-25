<?php session_start(); ?>
<?php
$request_uri = substr($_SERVER["REQUEST_URI"], 1);
// load menuItems.json
$menuItemsFile = fopen("menuItems.json", "r") or die("unable to open menuItems.json");
$menuItems = json_decode(fread($menuItemsFile, filesize("menuItems.json")), true);
fclose($menuItemsFile);

// security if user doesn't have permission to visit this page the page doesn't load
foreach ($menuItems as $key => $value) {
  foreach ($value as $key => $value) {
    if ($key != "menuItem") {
      foreach ($value["menuItems"] as $key => $value) {
        if ($value["path"] == $request_uri) {
          $found = false;
          foreach ($value["type"] as $key => $value) {
            if ($value == $_SESSION["user_type"]) {
              $found = true;
            }
          }
          if (!$found) {
            die();
          }
        }
      }
    }
  }
}
// load functions.php
include "functions.php";
// the webpage url(needed for logout)
$thisPage = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$request_uri";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Twente</title>
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet"> 
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
          echo "</li><li><a href='user-settings.php'>settings</a></li></ul></div>";
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
          if (!empty($_SESSION["user_type"])) {
            foreach ($menuItems as $key => $value) {
              if ($value["menuItem"]) {
                echo "<li>";
                foreach ($value as $key => $value) {
                  if ($key != "menuItem") {
                    echo "<a href='#'>$key</a><ul>";
                    foreach ($value as $key => $value) {
                      foreach ($value as $key => $value) {
                        $found = false;
                        foreach ($value["type"] as $key => $valueType) {
                          if ($valueType == $_SESSION["user_type"]) {
                            $found = true;
                          }
                        }
                        if ($found == true) {
                          echo "<li><a href='".$value["path"]."'>".$value["title"]."</a><li>";
                        }
                      }
                    }
                    echo "</ul>";
                  }
                }
                echo "</li>";
              }
            }
          }
          ?>
        </ul>
      </nav>
      <?php
      if (!empty($_SESSION["name"])) {
        echo '<img class="navArrow navArrowOpen" src="images/leftArrow.svg" alt="leftArrow">';
      }
      ?>
    </header>
    <main class="new-user">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <input class="inputs" required type="text" name="name" value="" placeholder="naam">
        <input class="inputs" required type="text" name="lastname" value="" placeholder="achternaam">
        <label for="gender">geslacht</label><br>
        <label for="male">m </label><input id="male" required type="radio" name="gender" value="m" checked="true">&nbsp;&nbsp;&nbsp;
        <label for="female">v </label><input id="female" required type="radio" name="gender" value="v">
        <select class="inputs" name="department_id">
          <option value="false">afdeling</option>
          <?php
          $afdelingen = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM afdelingen");
          foreach ($afdelingen as $key => $value) {
            echo "<option value='".$value["id"]."'>".$value["naam"]."</option>";
          }
          ?>
        </select>
        <select class="inputs" name="pc_nummer">
          <option value="false">configuratie</option>
          <?php
          $configuraties = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT pc_nummer FROM configuraties");
          foreach ($configuraties as $key => $value) {
            echo "<option value='".$value["pc_nummer"]."'>".$value["pc_nummer"]."</option>";
          }
          ?>
        </select>
        <input class="inputs" type="number" min="100" max="999" name="intern_tel" value="" placeholder="intern telefoonnummer">
        <input class="inputs" required type="email" name="email" value="" placeholder="email">
        <input class="inputs" required type="text" name="username" value="" placeholder="gebruikersnaam">
        <input required type="password" name="password" value="" placeholder="wachtwoord">
        <label for="profileImg">profiel foto</label>
        <input type="file" accept="image/*; capture=camera" name="profileImg" size="40">
        <input type="submit" name="submit" value="cre&euml;er account">
        <?php #form handler
        if (isset($_POST["submit"])) {
          //file upload

          $uploadfile = "images/profiles/" . basename($_FILES["profileImg"]["name"]);
          if ($_FILES["profileImg"]["size"] < 300000) {
            if (move_uploaded_file($_FILES["profileImg"]["tmp_name"], $uploadfile)) {
              $file_uploaded = true;
              $profile_path = $_FILES["profileImg"]["name"];
            }else {
              if ($_FILES["profileImg"]["error"] == UPLOAD_ERR_NO_FILE) {
                $profile_path = "defaultProfile.svg";
              } else {
                echo "<meta http-equiv=\"refresh\" content=\"0; url=$thisPage?error=er ging iets fout met de afbeelding probeer het opnieuw of laat hem leeg voor een standaard afbeelding.&name=$_POST[name]&lastname=$_POST[lastname]&gender=$_POST[gender]&department_id=$_POST[department_id]&pc_nummer=$_POST[pc_nummer]&intern_tel=$_POST[intern_tel]&email=$_POST[email]&username=$_POST[username]\" />";
              }
            }
            $upload = true;
          } else {
            echo "<meta http-equiv=\"refresh\" content=\"0; url=$thisPage?error=bestandsgrootte is te groot, kies een bestand tussen de 0 en 0.3MB&name=$_POST[name]&lastname=$_POST[lastname]&gender=$_POST[gender]&department_id=$_POST[department_id]&pc_nummer=$_POST[pc_nummer]&intern_tel=$_POST[intern_tel]&email=$_POST[email]&username=$_POST[username]\" />";
          }
          if ($upload) {
            $toegangs_level = "user";
            $name = ucfirst($_POST["name"]);
            if ($_POST["department_id"] == false) {
              $_POST["department_id"] = null;
            }
            $sql = "INSERT INTO gebruikers(geslacht, voornaam, achternaam, afdelingen_id, intern_tel, email, configuraties_nummer, gebruikersnaam, wachtwoord, profile_path, toegangs_level)
            VALUES('".$_POST["gender"]."', \"".$name."\",\"".$_POST["lastname"]."\", ".$_POST["department_id"].", ".$_POST["intern_tel"].", '".$_POST["email"]."', '".$_POST["pc_nummer"]."', '".$_POST["username"]."', '".hash("sha256", $_POST["password"])."', '".$profile_path."', '".$toegangs_level."')";
            $insert = dataToDb("83.82.240.2", "user", "pass", "project", "gebruikers", $sql);
            if ($insert === true) {
              echo "<succes>account succesvol aangemaakt <a href='index.php'>login</a></succes><meta http-equiv=\"refresh\" content=\"2; url=index.php\" />";
            } else {
              echo "<meta http-equiv=\"refresh\" content=\"0; url=$thisPage?error=gebruikersnaam al in gebruik&name=$_POST[name]&lastname=$_POST[lastname]&gender=$_POST[gender]&department_id=$_POST[department_id]&pc_nummer=$_POST[pc_nummer]&intern_tel=$_POST[intern_tel]&email=$_POST[email]&username=$_POST[username]\" />";
            }
          }
        }
        ?>
      </form>
    </main>
    <script src="javascript/new-user.js" charset="utf-8"></script>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
