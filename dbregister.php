

<?php
// Establish a connection to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = new mysqli($servername, $username, $password, $dbname);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert user details into database
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$password = $_POST['password'];

// Server-side validation
if (!preg_match('/^\d{10}$/', $contact)) {
    echo '<script>alert("Contact number must be exactly 10 digits."); window.location.href="login.php";</script>';
    exit();
}

// Password: at least 8 chars, 1 upper, 1 lower, 1 digit, 1 special
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
    echo '<script>alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character."); window.location.href="login.php";</script>';
    exit();
}

$sql = "INSERT INTO users (firstName, lastName, email, contact,  password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $firstName, $lastName, $email, $contact, $password);

try {
    if ($stmt->execute()) {
        echo '<script>alert("Registered successfully!"); window.location.href="login.php";</script>';
        exit();
    } else {
        $error = $conn->error;
        if (strpos($error, 'Duplicate entry') !== false) {
            $email = explode("'", $error)[3];
            echo '<script>alert("Email already exists: ' . $email . '"); window.location.href="login.php";</script>';
            exit();
        }
        throw new Exception($error);
    }
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        $email = explode("'", $e->getMessage())[3];
        echo '<script>alert("Email already exists."); window.location.href="login.php";</script>';
        exit();
    }
    echo '<script>alert("Registration failed! Error: ' . $e->getMessage() . '"); window.location.href="login.php";</script>';
    exit();
}

$stmt->close();
$conn->close();
?>
