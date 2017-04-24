<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>user</title>
    <link rel="stylesheet" href="/styles/login.sass">
  </head>
  <body class="login">
    <form name="form" action="" method="post">
      <input required type="text" name="username" placeholder="username" value="">
      <input required type="password" name="password" placeholder="password" value="">
      <input type="submit" name="Lsubmit" value="login">
      <input type="submit" name="Csubmit" value="create account">
    </form>
    <?php
      $pass = hash("sha256", $_POST['password']);
      if ($_POST['username'] != "") {
        $username = $_POST['username'];
        $servername = "localhost";
        $DBusername = "leonidh203_userTest";
        $DBpassword = "Y9z1aetZge";
        $DBname = "leonidh203_loginTest";

        // Create connection
        $conn = new mysqli($servername, $DBusername, $DBpassword, $DBname);

        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error . "<br>");
        }
        //echo "Connected successfully<br>";
        if (isset($_POST['Csubmit'])) {
          // Create user
          $sql = "INSERT INTO users (username, pwd) VALUES('". $username ."', '". $pass ."');";
          if ($conn->query($sql) === TRUE) {
            echo "user created successfully<br>";
          } else {
            echo "username already in use";
          }
        }
        if (isset($_POST['Lsubmit'])) {
          // login
          //echo "login button<br>";
          $sql = "SELECT * FROM users WHERE username = '".$username."'";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
              //echo "id: " . $row["id"]. "<br>username: " . $row["username"]. "<br>password " . $row["pwd"]. "<br>";
              if ($row["pwd"] === $pass) {
                echo "login successfull!";
              } else {
                echo "wrong password";
              }
            }
          } else {
            echo "$username not found";
          }
        }
        $conn->close();
      }
    ?>
  </body>
</html>
