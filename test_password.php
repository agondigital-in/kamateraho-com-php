<?php
$password = 'password';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($password, $hash)) {
    echo "Password verification successful!";
} else {
    echo "Password verification failed.";
}

echo "<br><br>Hash: " . $hash;
echo "<br>Password: " . $password;
?>