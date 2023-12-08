<?php

// define variables and set to empty values
$first_nameErr = $last_nameErr = $emailErr = $passwordErr = $addressErr = $cityErr = $stateErr = $zipErr = "";
$first_name = $last_name = $email = $password = $address = $city = $state = $zip = "";

// Form Validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["firstname"])) {
        $first_nameErr = "FirstName is required";
    } else {
        $first_name = test_input($_POST["firstname"]);
    }

    if (empty($_POST["lastname"])) {
        $last_nameErr = "LastName is required";
    } else {
        $last_name = test_input($_POST["lastname"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $email = test_input($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    if (empty($_POST["city"])) {
        $cityErr = "City is required";
    } else {
        $city = test_input($_POST["city"]);
    }

    if (empty($_POST["state"])) {
        $stateErr = "State is required";
    } else {
        $state = test_input($_POST["state"]);
    }

    if (empty($_POST["zip"])) {
        $zipErr = "Zip is required";
    } else {
        $zip = test_input($_POST["zip"]);
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Create + Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myPHPForm";
try {
    // Connect to MySQL server
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    $databaseExists = $stmt->fetchColumn();

    if (!$databaseExists) {
        // Database does not exist, so create it
        $conn->exec("CREATE DATABASE $dbname");
       // echo "Database created successfully<br>";

        // Reconnect to the newly created database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        // Connect to the existing database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected to the database<br>";
    }

    // SQL to create table
    $sql1 = "CREATE TABLE IF NOT EXISTS MyRegistration (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        email VARCHAR(50),
        address VARCHAR(50),
        city VARCHAR(25),
        state VARCHAR(25),
        zip INT(5),
        password VARCHAR(25),
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    // Use exec() because no results are returned
    $conn->exec($sql1);
    //echo "Table MyRegistration created successfully";

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Insert data into the table
      $sql2 = "INSERT INTO MyRegistration (firstname, lastname, email, address, city, state, zip, password)
               VALUES (:firstname, :lastname, :email, :address, :city, :state, :zip, :password)";

      $stmt = $conn->prepare($sql2);

      $stmt->bindParam(':firstname', $first_name);
      $stmt->bindParam(':lastname', $last_name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':address', $address);
      $stmt->bindParam(':city', $city);
      $stmt->bindParam(':state', $state);
      $stmt->bindParam(':zip', $zip);
      $stmt->bindParam(':password', $password);

      $stmt->execute();

      //echo "New record created successfully";
  }


} catch (PDOException $e) {
   // echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form</title>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">My Registration Form </h2>
    </div>
    <div class="container mt-5 shadow p-3 mb-5 bg-body-tertiary rounded bg-primary-subtle">
        <form class="row g-3 " action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="col-md-6">
          <label for="inputEmail4" class="form-label fs-5">First Name</label>
          <input type="text" class="form-control" id="inputEmail4"  placeholder="FirstName" name="firstname">
          <span class="error"><?php echo $first_nameErr; ?></span>
        </div>
        <div class="col-md-6">
          <label for="inputPassword4" class="form-label fs-5">Last Name</label>
          <input type="text" class="form-control" id="inputPassword4" placeholder="LastName"  name="lastname">
          <span class="error"><?php echo $last_nameErr; ?></span>
        </div>
        <div class="col-md-6">
            <label for="inputEmail4" class="form-label fs-5">Email</label>
            <input type="email" class="form-control" id="inputEmail4"  placeholder="user@gmail.com" name="email">
            <span class="error"><?php echo $emailErr; ?></span>
          </div>
          <div class="col-md-6">
            <label for="inputPassword4" class="form-label fs-5">Password</label>
            <input type="password" class="form-control" id="inputPassword4" placeholder="Password" name="password">
            <span class="error"><?php echo $passwordErr; ?></span>
          </div>
        <div class="col-12">
          <label for="inputAddress" class="form-label fs-5">Address</label>
          <input type="text" class="form-control" id="inputAddress" placeholder="Adress" name="address">
          <span class="error"><?php echo $addressErr; ?></span>
        </div>
        <div class="col-md-6">
          <label for="inputCity" class="form-label fs-5">City</label> 
          <input type="text" class="form-control" id="inputCity" placeholder="City" name="city">
          <span class="error"><?php echo $cityErr; ?></span>
        </div>
        <div class="col-md-4">
          <label for="inputState" class="form-label fs-5">State</label><span class="error">*</span>
          <select id="inputState" class="form-select" name="state">
            <option></option>
            <option name="state" value="state1">state1</option>
            <option name="state" value="state2">state2</option>
            <option>...</option>
          </select>
          <span class="error"><?php echo $stateErr; ?></span>
        </div>
        <div class="col-md-2">
          <label for="inputZip" class="form-label" fs-5>Zip</label>
          <input type="text" class="form-control" id="inputZip" placeholder="Zip Code" name="zip">
          <span class="error"><?php echo $zipErr; ?></span>
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheck">
            <label class="form-check-label" for="gridCheck">
              Check me out
            </label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary fs-5 float-end">Submit</button>
        </div>
      </form>
    </div>
    
</body>
</html>