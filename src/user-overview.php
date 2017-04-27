<?php session_start(); ?>
<?php
$_SESSION["user_type"] = "admin";
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
    <main class="user-overview">
      <?php
      if ($_POST["delete-id"]) {
        dataToDb("83.82.240.2", "user", "pass", "project", "gebruikers", "DELETE FROM gebruikers WHERE id = ".$_POST["delete-id"]);
      }
      $users_data = sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers INNER JOIN afdelingen ON afdelingen_id = afdelingen.id");
      if ($_SESSION["user_type"] == "admin") {
        foreach ($users_data as $key => $value) {
          $forms = "<form id='".$users_data[$key]["gebruikersnaam"]."-edit' action='user-settings.php' method='post' style='display:none'>";
          foreach ($value as $key1 => $value1) {
            $forms .= "<input type='hidden' name='edit-$key1' value=\"$value1\">";
          }
          $forms .= "</form>";
          $forms .= "<a href='#' class='edit-button' onclick='document.getElementById(\"".$users_data[$key]["gebruikersnaam"]."-edit\").submit();'><img src='../images/edit.svg' alt='edit'></a>
            <form id='".$users_data[$key]["gebruikersnaam"]."-delete' action='$_SERVER[PHP_SELF]' method='post' style='display:none'>
              <input type='hidden' name='delete-id' value='".$users_data[$key]["id"]."'>
            </form>
            <a class='edit-button' onclick='document.getElementById(\"".$users_data[$key]["gebruikersnaam"]."-delete\").submit();' href='#'><img src='../images/delete.svg' alt='delete'></a>
          ";
          $users_data[$key]["admin_tools"] = $forms;
        }
      } else {
        // get data out of table
        foreach ($users_data as $key => $value) {
          unset($users_data[$key]["id"]);
          unset($users_data[$key]["gebruikersnaam"]);
          unset($users_data[$key]["configuraties_nummer"]);
        }
      }
      // get data out of table
      foreach ($users_data as $key => $value) {
        unset($users_data[$key]["afdelingen_id"]);
      }
      echo twoDimenTable($users_data);
      ?>
    </main>

    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
