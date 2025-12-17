<?php
$errors = [];
$success = "";

// Run if form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm_password"]);

    // Step 2: validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors → go ahead
    if (empty($errors)) {

        $jsonFile = "users.json";

        // Step 9: file handling errors
        if (!file_exists($jsonFile)) {
            die("Error: users.json not found.");
        }

        $data = file_get_contents($jsonFile);
        $users = json_decode($data, true);

        if (!is_array($users)) {
            $errors[] = "JSON file corrupted.";
        }

        if (empty($errors)) {
            // Step 5: hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Step 6: create user entry
            $newUser = [
                "name"     => $name,
                "email"    => $email,
                "password" => $hashed
            ];

            $users[] = $newUser;

            // Step 7: save back to file
            if (file_put_contents($jsonFile, json_encode($users, JSON_PRETTY_PRINT))) {
                $success = "Registration successful!";
            } else {
                $errors[] = "Failed to write to JSON file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>

  <link rel="stylesheet" href="style.css">

</head>
<body>

<h2>User Registration</h2>

<?php
// Step 8: success message
if (!empty($success)) {
    echo "<div style='color: green;'>$success</div>";
}

// Show validation errors
foreach ($errors as $err) {
    echo "<div style='color: red;'>$err</div>";
}
?>

<!-- Step 1: HTML Form -->
<form action="" method="POST">

    <label>Name:</label><br>
    <input type="text" name="name"><br><br>

    <label>Email:</label><br>
    <input type="text" name="email"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password"><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>
