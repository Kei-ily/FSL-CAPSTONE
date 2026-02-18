<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "fsldb";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Helper to find smallest missing positive integer in a table column
    function get_smallest_missing($conn, $table, $col) {
        $next = 1;
        $res = mysqli_query($conn, "SELECT $col FROM $table ORDER BY $col ASC");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $val = intval($row[$col]);
                if ($val === $next) {
                    $next++;
                } elseif ($val > $next) {
                    break;
                }
            }
        }
        return $next;
    }

    // determine acc_no to use (fill gaps)
    $acc_no = get_smallest_missing($conn, 'account_record', 'acc_no');
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Only process student-specific fields if they were submitted
    if ($role === 'student') {
        $contact = mysqli_real_escape_string($conn, $_POST['contact'] ?? '');
        $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
        $grade = mysqli_real_escape_string($conn, $_POST['grade'] ?? '');
    }
    
    $profile_picture = null;
    if (!empty($_POST['profile_picture'])) {
        $profile_picture = $_POST['profile_picture'];
    }

    if ($role === 'student') {
        if ($profile_picture) {
            $sql = "INSERT INTO account_record (acc_no, name, grade, contact, address, email, password, role, profile_picture) 
                    VALUES ('$acc_no', '$name', '$grade', '$contact', '$address', '$email', '$password', '$role', '$profile_picture')";
        } else {
            $sql = "INSERT INTO account_record (acc_no, name, grade, contact, address, email, password, role) 
                    VALUES ('$acc_no', '$name', '$grade', '$contact', '$address', '$email', '$password', '$role')";
        }
    } else if ($role === 'teacher') {
        if ($profile_picture) {
            $sql = "INSERT INTO account_record (acc_no, name, email, password, role, profile_picture) 
                    VALUES ('$acc_no', '$name', '$email', '$password', '$role', '$profile_picture')";
        } else {
            $sql = "INSERT INTO account_record (acc_no, name, email, password, role) 
                    VALUES ('$acc_no', '$name', '$email', '$password', '$role')";
        }
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?success=Signup successful! You can now log in.");
        exit();
    } else {
        header("Location: signup.php?error=Error: " . mysqli_error($conn));
        exit();
    }
}

mysqli_close($conn);
?>
