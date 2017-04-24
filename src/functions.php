<?php

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
    return "0 results";
  }
  $conn->close();
  return $rows;
}

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

function insertDataToDb($servername, $username, $password, $dbname, $tableName, $sql) {

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    return $conn->connect_error;
    die("Connection failed: " . $conn->connect_error);
  }

  if ($conn->query($sql) === TRUE) {
    return true;
  } else {
    return $conn->error;
  }
}


// example
// $dataArray = [
//   ["soort"=>"Desktop", "stuffing"=>"stuff"],
//   ["soort"=>"Latop", "stuffing"=>"stuffing"]
// ];
//
// echo generateSqlInsert($dataArray, "medewerkers");

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

function logout() {
  $_SESSION = array();
  session_destroy();
  echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\" />";
}

?>
