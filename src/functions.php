<?php

// select data from db and returns an two dimensional array
function sqlSelect($servername, $username, $password, $dbname, $sql) {
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    return $conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $rows;
    while($row = $result->fetch_assoc()){
      $rows[] = $row;
    }
  } else {
    return false;
  }
  $conn->close();
  return $rows;
}

// multiple queries in database, returns three dimensional array
function sqlSelectMultiLine($servername, $username, $password, $dbname, $sql) {
  $mysqli = new mysqli($servername, $username, $password, $dbname);

  /* check connection */
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  /* execute multi query */
  if ($mysqli->multi_query($sql)) {
    $rows = [];
    $statementCount = 0;
    do {
      /* store first result set */
      if ($result = $mysqli->store_result()) {
        while ($row = $result->fetch_assoc()) {
          $rows[$statementCount][] = $row;
        }
        $result->free();
      }
      /* print divider */
      if ($mysqli->more_results()) {
        $statementCount++;
      }
    } while ($mysqli->next_result());
  }

  /* close connection */
  $mysqli->close();
  return $rows;
}

// example
// echo twoDimenTable(sqlSelect("83.82.240.2", "user", "pass", "project", "SELECT * FROM gebruikers"));

// returns a table from an two dimensional array
function twoDimenTable($array) {
  $table =  "<table border='1'><thead><tr>";
  foreach ($array[0] as $key => $value) {
    $table .=  "<th>$key</th>";
  }
  $table .=  "</tr></thead><tbody>";
  foreach ($array as $key => $value) {
    $table .=  "<tr>";
    foreach ($value as $key => $value) {
      $table .=  "<td>$value</td>";
    }
    $table .=  "</tr>";
  }
  $table .=  "</tbody></table>";
  return $table;
}

// returns a table from an two dimensional array with order links
function twoDimenTableWithSortLinks($array, $asc, $search) {
  $table =  "<table border='1'><thead><tr>";
  foreach ($array[0] as $key => $value) {
    $table .=  "<th><a href='$_SERVER[PHP_SELF]?sort=$key&asc=$asc&search=$search'>$key</a></th>";
  }
  $table .=  "</tr></thead><tbody>";
  foreach ($array as $key => $value) {
    $table .=  "<tr>";
    foreach ($value as $key => $value) {
      $table .=  "<td>$value</td>";
    }
    $table .=  "</tr>";
  }
  $table .=  "</tbody></table>";
  return $table;
}

// sends data to DB
function dataToDb($servername, $username, $password, $dbname, $tableName, $sql) {

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    return $conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }

  if ($conn->query($sql) === TRUE) {
    return true;
  } else {
    return "$sql : ".$conn->error;
  }
}


// example
// $dataArray = [
//   ["soort"=>"Desktop", "stuffing"=>"stuff"],
//   ["soort"=>"Latop", "stuffing"=>"stuffing"]
// ];
//
// echo generateSqlInsert($dataArray, "medewerkers");

// unneeded by this project
function generateSqlInsert($dataArray, $tableName) {
  $sql = "INSERT INTO $tableName(";
  $counter = 0;
  foreach ($dataArray[0] as $key => $value) {
    $sql .= $key;
    if ($counter != count($dataArray[0])-1) {
      $sql .= ", ";
    }
    $counter++;
  }
  $sql .= ") VALUES";

  foreach ($dataArray as $key => $value) {
    $sql .= "(";
    $counter = 0;
    foreach ($value as $key2 => $value) {
      $sql .= "'$value'";
      if ($counter != count($dataArray[0])-1) {
        $sql .= ", ";
      }
      $counter++;
    }
    $sql .= ")";
    if ($key != count($dataArray)-1) {
      $sql .= ",";
    } else {
      $sql .= ";";
    }
  }
  return $sql;
}

// unneeded by this project
function generateSqlSelectFilter($tableName, $inputArray) {
  $sql = "SELECT * FROM $tableName";
  if (count($inputArray) > 0) {
    $sql .= " WHERE ";
    foreach ($inputArray as $key => $value) {
      if (strpos($key, "filter") === 0) {
        $columnName = substr($key, 6);
        $sql .= "$columnName = '$value'";
      } else {
        $sql .= " $value ";
      }
    }
  }
  $sqlEnd = substr($sql, strlen($sql)-5, strlen($sql));
  if (strlen($sqlEnd)-3 == strpos($sqlEnd, "OR")) {
    $sql = substr($sql, 0, strlen($sql)-4);
  } elseif (strlen($sqlEnd)-4 == strpos($sqlEnd, "AND")) {
    $sql = substr($sql, 0, strlen($sql)-5);
  }
  $sql .= ";";
  return $sql;
}

// a logout function
function logout() {
  $_SESSION = array();
  session_destroy();
  echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\" />";
}

?>
