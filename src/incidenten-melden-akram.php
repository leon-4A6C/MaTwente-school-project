<?php session_start(); ?>
<?php
error_reporting(0);
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
    <main class="new-user incidenten-melden-akram">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <input required type="text" name="onderwerp" value="<?php echo $_POST[" onderwerp "]; ?>" placeholder="onderwerp"><br>
        <input required type="text" name="omschrijving" value="<?php echo $_POST[" omschrijving "]; ?>" placeholder="omschrijving"><br>
        <input required type="text" name="oorzaak" value="<?php echo $_POST[" oorzaak "]; ?>" placeholder="oorzaak"><br>
        <input required type="text" name="oplossing" value="<?php echo $_POST[" oplossing "]; ?>" placeholder="oplossing"><br>
        <input required type="text" name="terugkoppeling" value="<?php echo $_POST[" terugkoppeling "]; ?>" placeholder="terugkoppeling"><br>
        <input type="submit" name="submit" value="verstuur">
        <?php #form handler
          if (isset($_POST["submit"])) {

            // clean user input
            $onderwerp = check(htmlspecialchars(trim($_POST["onderwerp"])), false);
            $omschrijving = check(htmlspecialchars(trim($_POST["omschrijving"])), false);
            $oorzaak = check(htmlspecialchars(trim($_POST["oorzaak"])), false);
            $oplossing = check(htmlspecialchars(trim($_POST["oplossing"])), false);
            $terugkoppeling = check(htmlspecialchars(trim($_POST["terugkoppeling"])), false);
            dataToDb("83.82.240.2", "user", "pass", "project", "incidenten", "INSERT INTO incidenten(onderwerp, omschrijving, oorzaak, oplossing, terugkoppeling) VALUES($onderwerp, $omschrijving, $oorzaak, $oplossing, $terugkoppeling)");

          }

        ?>
      </form>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
