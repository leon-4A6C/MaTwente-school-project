<?php session_start(); ?>
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
          <li>
            <a href="#">medewerkers</a>
            <ul>
              <li><a href="#">overzicht</a></li>
            </ul>
          </li>
          <li>
            <a href="#">configuraties</a>
            <ul>
              <li><a href="#">overzicht</a></li>
              <li><a href="#">toevoegen</a></li>
            </ul>
          </li>
          <li>
            <a href="#">apparaten</a>
            <ul>
              <li><a href="#">overzicht</a></li>
              <li><a href="#">toevoegen</a></li>
            </ul>
          </li>
          <li>
            <a href="#">meldingen</a>
            <ul>
              <li><a href="#">overzicht</a></li>
              <li><a href="#">toevoegen</a></li>
            </ul>
          </li>
        </ul>
      </nav>
      <img class="navArrow navArrowOpen" src="images/leftArrow.svg" alt="leftArrow">
    </header>
    <main>

    </main>
    <script src="javascript/nav.js" charset="utf-8"></script>
  </body>
</html>
