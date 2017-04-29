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
            if ($value == $_SESSION["user"]["toegangs_level"]) {
              $found = true;
            }
          }
          if (!$found) {
            die("U heeft geen toestemming om hier te komen");
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
        if (!$_SESSION["user"]["toegangs_level"]) {
          echo "<div class='profile'>";
          echo "<a href='index.php'>login</a></div>";
        } else {
          echo "<div class='profile'><a href=''#'>";
          echo "<img src=\"images/profiles/".$_SESSION["user"]["profile_path"]."\" alt=\"profile\" class=\"profilePicture\">";
          echo "</a><ul><li>";
          echo "<a href='$thisPage?logout=true'>logout</a>";
          echo "</li><li>";
          echo "<form id='user-settings' action='user-settings.php' method='post' style='display:none'>";
          foreach ($_SESSION["user"] as $key => $value) {
            echo "<input type='hidden' name='$key' value=\"$value\">";
          }
          echo "</form>";
          echo "<a onclick='document.getElementById(\"user-settings\").submit();' href='#'>settings</a>";
          echo "</li></ul></div>";
          echo "<div class='status'><span>".$_SESSION["user"]["naam"]."</span><br>
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
          if (!empty($_SESSION["user"]["toegangs_level"])) {
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
                          if ($valueType == $_SESSION["user"]["toegangs_level"]) {
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
      if (!empty($_SESSION["user"]["toegangs_level"])) {
        echo '<img class="navArrow navArrowOpen" src="images/leftArrow.svg" alt="leftArrow">';
      }
      ?>
    </header>
    <main class="new-user">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <input required type="text" name="voornaam" value="<?php echo $_POST["voornaam"]; ?>" placeholder="naam">
        <input required type="text" name="achternaam" value="<?php echo $_POST["achternaam"]; ?>" placeholder="achternaam">
        <label for="gender">geslacht</label><br>
        <label for="male">m </label><input id="male" required type="radio" name="geslacht" value="m" <?php if($_POST["geslacht"]=="m"){echo "checked";} ?>>&nbsp;&nbsp;&nbsp;
        <label for="female">v </label><input id="female" required type="radio" name="geslacht" value="v" <?php if($_POST["geslacht"]=="v"){echo "checked";} ?>>
        <select required name="afdelingen_id">
          <option value="false">afdeling</option>
          <?php
          $afdelingen = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM afdelingen");
          foreach ($afdelingen as $key => $value) {
            echo "<option ";
            if ($_POST["afdelingen_id"] == $value["id"]) {
              echo "selected";
            }
            echo " value='".$value["id"]."'>".$value["naam"]."</option>";
          }
          ?>
        </select>
        <select required name="configuraties_nummer">
          <option value="false">configuratie</option>
          <?php
          $configuraties = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT pc_nummer FROM configuraties");
          foreach ($configuraties as $key => $value) {
            echo "<option ";
            if ($_POST["configuraties_nummer"] == $value["pc_nummer"]) {
              echo "selected";
            }
            echo " value='".$value["pc_nummer"]."'>".$value["pc_nummer"]."</option>";
          }
          ?>
        </select>
        <input type="number" min="100" max="999" name="intern_tel" value="<?php echo $_POST['intern_tel']?>" placeholder="intern telefoonnummer">
        <input required type="email" name="email" value="<?php echo $_POST['email']?>" placeholder="email">
        <input required type="text" name="gebruikersnaam" value="<?php echo $_POST['gebruikersnaam']?>" placeholder="gebruikersnaam">
        <label for="wachtwoord">als je hem leeg laat word hij niet gewijzigd</label>
        <input type="password" name="wachtwoord" value="" placeholder="wachtwoord">
        <label for="profile_path_file">profiel foto</label>
        <input type="file" accept="image/*; capture=camera" name="profile_path_file" size="40">
        <?php
        if ($_SESSION["user"]["toegangs_level"] == "admin") {
          echo "<input type='text' name='toegangs_level' placeholder='toegangs level' value='".$_POST["toegangs_level"]."'>";
        }
        ?>
        <input required type="password" name="bevestegings_wachtwoord" value="" placeholder="bevestiging wachtwoord">
        <input type="hidden" name="id" value="<?php echo $_POST['id']?>">
        <input type="hidden" name="profile_path" value="<?php echo $_POST['profile_path']?>">
        <input type="submit" name="submit" value="wijzig account">
        <?php #form handler
        if (isset($_POST["submit"])) {

          $user_id_data = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM gebruikers WHERE id = ".$_POST["id"])[0];

          // clean user input
          $voornaam = ucfirst(trim($_POST["voornaam"]));
          $achternaam = trim($_POST["achternaam"]);
          $intern_tel = trim($_POST["intern_tel"]);
          $email = trim($_POST["email"]);
          $gebruikersnaam = trim($_POST["gebruikersnaam"]);
          $wachtwoord = trim($_POST["wachtwoord"]);
          $toegangs_level = trim($_POST["toegangs_level"]);
          $bevestegings_wachtwoord = hash("sha256",trim($_POST["bevestegings_wachtwoord"]));

          if (!$toegangs_level) {
            $toegangs_level = $_SESSION["user"]["toegangs_level"];
          } else {
            $toegangs_level = $_POST["toegangs_level"];
          }

          if ($_POST["afdelingen_id"] == 'false') {
            $afdelingen_id = "null";
          } else {
            $afdelingen_id = $_POST["afdelingen_id"];
          }
          if ($_POST["configuraties_nummer"] == 'false') {
            $configuraties_nummer = "null";
          }else {
            $configuraties_nummer = "'".$_POST["configuraties_nummer"]."'";
          }
          if (empty($intern_tel)) {
            $intern_tel = "null";
          }

          if (!$wachtwoord) {
            $wachtwoord = $user_id_data["wachtwoord"];
          } else {
            $wachtwoord = hash("sha256", $wachtwoord);
          }

          // check of het wachtwoord overeen komt met de persoon die ingelogd is.
          if ($bevestegings_wachtwoord == $_SESSION["user"]["wachtwoord"]) {
            //file upload
            $uploadfile = "images/profiles/" . basename($_FILES["profile_path_file"]["name"]);
            if ($_FILES["profile_path_file"]["size"] < 300000) {
              if (move_uploaded_file($_FILES["profile_path_file"]["tmp_name"], $uploadfile)) {
                $file_uploaded = true;
                $profile_path = $_FILES["profile_path_file"]["name"];
              }else {
                if ($_FILES["profile_path_file"]["error"] == UPLOAD_ERR_NO_FILE) {
                  $profile_path = $_POST["profile_path"];
                } else {
                  echo "<error>er ging iets fout met de afbeelding probeer het opnieuw of laat hem leeg voor een standaard afbeelding.</error>";
                }
              }
            } else {
              echo "<error>bestandsgrootte is te groot, kies een bestand tussen de 0 en 0.3MB</error>";
            }

            $sql = "UPDATE gebruikers
            SET voornaam = '".$voornaam."',
            achternaam = \"$achternaam\",
            geslacht = '".$_POST["geslacht"]."',
            afdelingen_id = ".$afdelingen_id.",
            configuraties_nummer = $configuraties_nummer,
            intern_tel = ".$intern_tel.",
            email = '".$email."',
            gebruikersnaam = '".$gebruikersnaam."',
            wachtwoord = '".$wachtwoord."',
            profile_path = '$profile_path',
            toegangs_level = '$toegangs_level' WHERE id = ".$_POST["id"];
            $insert = dataToDb("83.82.240.2", "user", "pass", "project", "gebruikers", $sql);
            if ($insert === true) {
              if ($_SESSION["user"]["toegangs_level"] == "admin") {
                echo "<succes>account succesvol gewijzigd</succes><meta http-equiv=\"refresh\" content=\"2; url=user-overview.php\" />";
              } else {
                echo "<succes>account succesvol gewijzigd <a href='index.php'>login</a></succes><meta http-equiv=\"refresh\" content=\"2; url=index.php\" />";
              }
            } else {
              echo "<error>gebruikersnaam al in gebruik</error>";
            }
          } else {
            echo "<error>verkeerd bevestegings wachtwoord ingevoerd</error>";
          }

        }
        ?>
      </form>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
