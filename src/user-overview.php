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
    <main class="user-overview">
      <form id="user-overview-search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <input type="hidden" name="sort" value="<?php echo $_GET[sort] ?>">
        <input type="hidden" name="asc" value="<?php echo $_GET[asc] ?>">
        <input type="text" name="search" value="<?php echo $_GET[search] ?>" placeholder="zoek op naam, email, enz.">
        <input type="image" name="submit" src="images/search.svg" alt="zoek" width="20em" style="margin-bottom:-0.5em">
      </form>

      <?php
      if (!empty($_GET["search"])){
        $sql = "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE gebruikers.id LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE gebruikersnaam LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE geslacht LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE voornaam LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE achternaam LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE intern_tel LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE email LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE afdelingen.naam LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE afdelingen.id LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE configuraties_nummer LIKE \"%".$_GET["search"]."%\";";
        $sql .= "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id WHERE toegangs_level LIKE \"%".$_GET["search"]."%\";";
      }
      if ($_POST["delete-id"] && $_POST["yes"]) {
        dataToDb("83.82.240.2", "user", "pass", "project", "gebruikers", "DELETE FROM gebruikers WHERE id = ".$_POST["delete-id"]);
      } elseif ($_POST["delete-id"] && !$_POST["no"]) {
        echo "<error>weet je het zeker?<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
              <input type=\"hidden\" name=\"delete-id\" value=\"".$_POST['delete-id']."\">
              <input type=\"submit\" name=\"yes\" value=\"ja\">
              <input type=\"submit\" name=\"no\" value=\"nee\">
            </form></error>";
      }
      if (empty($sql)) {
        $sql = "SELECT gebruikers.id, gebruikersnaam, geslacht, voornaam, achternaam, intern_tel, email, afdelingen.naam AS 'afdeling', afdelingen_id, configuraties_nummer, toegangs_level  FROM gebruikers LEFT JOIN afdelingen ON afdelingen_id = afdelingen.id;";
      }
      if($_GET["sort"]) {
        $sqlReplace = " ORDER BY " . $_GET["sort"];
        if ($_GET["asc"] == true) {
          $sqlReplace .= " ASC;";
        } else {
          $sqlReplace .= " DESC;";
        }
        $sql = str_replace(";", $sqlReplace, $sql);
      }

      $users_data = sqlSelectMultiLine("83.82.240.2", "user", "pass", "project", $sql);
      $users_data_keys = [];
      foreach ($users_data as $key => $value) {
        foreach ($value[0] as $key1 => $value1) {
          $users_data_keys[] = $key1;
        }
        break;
      }
      if ($_SESSION["user"]["toegangs_level"] == "admin") {
        foreach ($users_data as $key => $value) {
          foreach ($value as $key1 => $value1) {
            $forms = "<form id='".$value1['gebruikersnaam']."-edit' action='user-settings.php' method='post' style='display:none'>";
            foreach ($value1 as $key2 => $value2) {
              $forms .= "<input type='hidden' name='$key2' value=\"$value2\">";
            }
            $forms .= "</form>";
            $forms .= "<a href='#' class='edit-button' onclick='document.getElementById(\"".$users_data[$key][$key1]["gebruikersnaam"]."-edit\").submit();'><img src='images/edit.svg' alt='edit'></a>
              <form id='".$users_data[$key][$key1]["gebruikersnaam"]."-delete' action='$_SERVER[PHP_SELF]' method='post' style='display:none'>
                <input type='hidden' name='delete-id' value='".$users_data[$key][$key1]["id"]."'>
              </form>
              <a class='edit-button' onclick='document.getElementById(\"".$users_data[$key][$key1]["gebruikersnaam"]."-delete\").submit();' href='#'><img src='images/delete.svg' alt='delete'></a>
            ";
            $users_data[$key][$key1]["admin_tools"] = $forms;
          }
        }
      } else {
        // get data out of table
        foreach ($users_data as $key => $value) {
          foreach ($value as $key1 => $value) {
            unset($users_data[$key][$key1]["id"]);
            unset($users_data[$key][$key1]["gebruikersnaam"]);
            unset($users_data[$key][$key1]["configuraties_nummer"]);
          }
        }
      }
      // get data out of table
      foreach ($users_data as $key => $value) {
        foreach ($value as $key1 => $value) {
          unset($users_data[$key][$key1]["afdelingen_id"]);
        }
      }
      if ($_GET["asc"] == true) {
        foreach ($users_data as $key => $value){
          if (count($users_data)>1) {
            echo "<h1>$users_data_keys[$key]</h1>";
          }
          echo twoDimenTableWithSortLinks($value, false, $_GET["search"]);
        }
      } else {
        foreach ($users_data as $key => $value) {
          if (count($users_data)>1) {
            echo "<h1>$users_data_keys[$key]</h1>";
          }
          echo twoDimenTableWithSortLinks($value, true, $_GET["search"]);
        }
      }
      ?>
    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
