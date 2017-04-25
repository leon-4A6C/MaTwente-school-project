<?php session_start(); ?>
<?php
$_SESSION["user_type"] = "user";
$_SESSION["profileImg"] = "defaultProfile.svg";
$_SESSION["name"] = "John Doe";
$_SESSION["username"] = "VDelen";
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
        <?php // TODO: insert mysql user data ?>
        <?php // TODO: if no user info session data then get user info from login ?>
        <input required type="text" name="name" value="" placeholder="naam">
        <input required type="text" name="lastname" value="" placeholder="achternaam">
        <label for="gender">geslacht</label><br>
        <label for="male">m </label><input id="male" required type="radio" name="gender" value="m" checked="true">&nbsp;&nbsp;&nbsp;
        <label for="female">v </label><input id="female" required type="radio" name="gender" value="v">
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
        <label for="password">als je hem leeg laat word hij niet gewijzigd</label>
        <input required type="password" name="password" value="" placeholder="wachtwoord">
        <label for="profileImg">profiel foto</label>
        <input type="file" accept="image/*; capture=camera" name="profileImg" size="40">
        <?php
        if ($_SESSION["user_type"] == "admin") {
          echo "<input type='text' name='toegangs_level' placeholder='toegangs level'>";
        }
        ?>
        <input required type="password" name="comfirmPass" value="" placeholder="bevestiging wachtwoord">
        <input type="submit" name="submit" value="wijzig account">
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
                echo "<error>er ging iets fout met de afbeelding probeer het opnieuw of laat hem leeg voor een standaard afbeelding.</error>";
              }
            }
          } else {
            echo "<error>bestandsgrootte is te groot, kies een bestand tussen de 0 en 0.3MB</error>";
          }
          if (!$_POST["toegangs_level"]) {
            $toegangs_level = "user";
          } else {
            $toegangs_level = $_POST["toegangs_level"];
          }
          // TODO: check eerst of het wachtwoord wel klopt in de DB
          $oud_wachtwoord = $_POST["confirmPass"];
          if (!$_POST["password"]) {
            $nieuw_wachtwoord = $oud_wachtwoord;
          }
          $name = ucfirst($_POST["name"]);
          if ($_POST["department_id"] == false) {
            $_POST["department_id"] = null;
          }
          $sql = "UPDATE gebruikers
          SET voornaam = '".$_POST["name"]."',
          achternaam = '".$_POST["lastname"]."',
          geslacht = '".$_POST["gender"]."',
          afdelingen_id = ".$_POST["department_id"].",
          configuraties_nummer = '".$_POST["pc_nummer"]."',
          intern_tel = ".$_POST["intern_tel"].",
          email = '".$_POST["email"]."',
          gebruikersnaam = '".$_POST["username"]."',
          wachtwoord = '".hash("sha256", $_POST["password"])."',
          profile_path = '$profile_path',
          toegangs_level = '$toegangs_level' WHERE id = $id";
          $insert = dataToDb("83.82.240.2", "user", "pass", "project", "gebruikers", $sql);
          if ($insert === true) {
            if ($_SESSION["user_type"] == admin) {
              echo "<succes>account succesvol gewijzigd <a href='configuraties.php'>login</a></succes><meta http-equiv=\"refresh\" content=\"2; url=index.php\" />";
            } else {
              echo "<succes>account succesvol gewijzigd <a href='index.php'>login</a></succes><meta http-equiv=\"refresh\" content=\"2; url=index.php\" />";
            }
          } else {
            echo "<error>gebruikersnaam al in gebruik</error>";
          }
        }
        ?>
      </form>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
