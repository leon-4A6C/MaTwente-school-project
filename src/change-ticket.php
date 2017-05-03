<?php session_start(); ?>
<?php
error_reporting(E_ALL & ~E_NOTICE);
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
            echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\" />";
            die("U heeft geen toestemming om hier te komen");
          }
        }
      }
    }
  }
}
// load functions.php
include "functions.php";


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
        <form id="logout" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" style="display: none;">
          <input type="hidden" name="logout" value="true">
        </form>
        <?php
        if (!$_SESSION["user"]["toegangs_level"]) {
          echo "<div class='profile'>";
          echo "<a href='index.php'>login</a></div>";
        } else {
          echo "<div class='profile'><a href=''#'>";
          echo "<img src=\"images/profiles/".$_SESSION["user"]["profile_path"]."\" alt=\"profile\" class=\"profilePicture\">";
          echo "</a><ul><li>";
          echo "<a onclick='document.getElementById(\"logout\").submit();'>logout</a>";
          echo "</li><li>";
          echo "<form id='user-settings' action='user-settings.php' method='post' style='display:none'>";
          foreach ($_SESSION["user"] as $key => $value) {
            echo "<input type='hidden' name='$key' value=\"$value\">";
          }
          echo "</form>";
          echo "<a onclick='document.getElementById(\"user-settings\").submit();' href='#'>settings</a>";
          echo "</li></ul></div>";
          echo "<div class='status'><span>".$_SESSION["user"]["naam"]."</span><br>
          <a onclick='document.getElementById(\"logout\").submit();'>logout</a></div>";
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
    <main class="new-ticket">
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <label for="onderwerp">onderwerp</label><br>
        <input required type="text" name="onderwerp" value="<?php echo $_POST['onderwerp'] ?>" placeholder="onderwerp"><br>
        <label for="omschrijving">omschrijving</label><br>
        <textarea required name="omschrijving" rows="15" cols="40" placeholder="omschrijving"><?php echo $_POST['omschrijving'] ?></textarea><br>
        <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
        <?php
        if ($_SESSION["user"]["toegangs_level"] == "admin") {
          echo "<label for=\"verantwoordelijke_id\">verantwoordelijke </label>";
          echo "<select name='verantwoordelijke_id'>";
          echo "<option value=\"null\"></option>";
          $icters = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT id, voornaam, achternaam FROM gebruikers WHERE afdelingen_id = 6"); //selecteer alle icters
          foreach ($icters as $key => $value) {
            echo "<option value='$value[id]' ";
            if ($_POST['verantwoordelijke_id'] == $value["id"]) {
              echo "selected";
            }
            echo ">$value[voornaam] $value[achternaam]</option>";
          }
          echo "</select>";
          echo "<img title=\"de persoon die het probleem gaat oplossen of heeft opgelost\" class=\"question\" src=\"images/questionMark.svg\" alt=\"?\"><br>";

          echo "<label for=\"afhandel_tijd\">afhandel tijd </label>";
          echo "<input type=\"number\" name=\"afhandel_tijd\" placeholder=\"afhandel tijd\" value=\"$_POST[afhandel_tijd]\" min=\"0\">";
          echo "<img title=\"het aantal minuten dat het probleem gaat kosten/heeft gekost\" class=\"question\" src=\"images/questionMark.svg\" alt=\"?\"><br><br>";
          echo "<label for=\"oorzaak\">oorzaak</label><br>";
          echo "<textarea name=\"oorzaak\" rows=\"8\" cols=\"40\" placeholder=\"oorzaak\">$_POST[oorzaak]</textarea><br>";
          echo "<label for=\"oplossing\">oplossing</label><br>";
          echo "<textarea name=\"oplossing\" rows=\"8\" cols=\"40\" placeholder=\"oplossing\">$_POST[oplossing]</textarea><br>";
          echo "<label for=\"terugkoppeling\">terugkoppeling</label><br>";
          echo "<textarea name=\"terugkoppeling\" rows=\"8\" cols=\"40\" placeholder=\"terugkoppeling\">$_POST[terugkoppeling]</textarea><br>";
        }
        ?>
        <input type="submit" name="submit" value="meld">
      </form>
      <?php
      if (isset($_POST["submit"])) {
        $onderwerp = check(htmlspecialchars(trim($_POST["onderwerp"])), false);
        $omschrijving = check(htmlspecialchars(trim($_POST["omschrijving"])), false);
        $oorzaak = check(htmlspecialchars(trim($_POST["oorzaak"])), false);
        $oplossing = check(htmlspecialchars(trim($_POST["oplossing"])), false);
        $terugkoppeling = check(htmlspecialchars(trim($_POST["terugkoppeling"])), false);
        $afhandel_tijd = check($_POST['afhandel_tijd'], true);
        $verantwoordelijke_id = check($_POST["verantwoordelijke_id"], true);

        $sql = "UPDATE incidenten
        SET afhandel_tijd = $afhandel_tijd,
        onderwerp = $onderwerp,
        omschrijving = $omschrijving,
        verantwoordelijke_id = $verantwoordelijke_id,
        oorzaak = $oorzaak,
        oplossing = $oplossing,
        terugkoppeling = $terugkoppeling WHERE id = ". $_POST["id"];

        $status = dataToDb("83.82.240.2", "user", "pass", "project", "incidenten", $sql);
        echo $status;
        if ($status === true) {
          echo "<succes>het probleem is gemeld!</succes><meta http-equiv='refresh' content='2;url=incidenten-overzicht.php'>";
        } else {
          echo "<error>er is iets fout gegaan probeer het opnieuw</error>";
        }
      }
      ?>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
