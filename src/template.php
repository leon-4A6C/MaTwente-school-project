<?php session_start(); ?>
<?php $_SESSION["user_type"] = "user"; ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Twente</title>
    <link rel="stylesheet" href="styles/main.css">
  </head>
  <body>
    <header>
      <div class="profileBar">
        <div class="profile">
          <a href="#">
            <?php
            if ($_SESSION["profileImg"]) {
              echo "<img src=\"images/profiles/".$_SESSION["profileImg"]."\" alt=\"profile\" class=\"profilePicture\">";
            } else {
              echo "<img src=\"images/profiles/defaultProfile.svg\" alt=\"profile\" class=\"profilePicture\">";
            }
            ?>
          </a>
          <ul>
            <li>
              <a href="#">logout</a>
            </li>
            <li>
              <a href="#">settings</a>
            </li>
          </ul>
        </div>
        <div class="status">
          <span>
            <?php if($_SESSION["name"]){echo $_SESSION["name"];} else {echo "John Doe";} ?></span><br>
          <a href="#">logout</a>
        </div>
      </div>
      <img src="images/icon.svg" alt="logo" class="logo logoGone">
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
    <main>

    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
