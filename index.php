<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh">
        
        <!-- Logos Section -->
        <div class="logos mb-4 text-center">
            <img src="images/INET.png" alt="Logo 1" class="logo">
            <img src="images/PHILSCA Logo.png" alt="Logo 2" class="logo">
            <img src="images/ICS.png" alt="Logo 3" class="logo">
        </div>

      <!-- Login Form -->
<form action="login.php" method="post" class="border p-4 rounded shadow login-form text-center">

<!-- Logo inside form -->
<div class="form-logo mb-3">
    <img src="images/LOGOhd_NoLetters.png" alt="PAEES Logo" class="form-logo-img">
</div>

<h1 class="mb-4">PAEES Login</h1>

<?php if (isset($_GET['error'])) { ?>
    <p class="text-danger"><?=htmlspecialchars($_GET['error'])?></p>
<?php } ?>
<div class="mb-3 text-start">
    <label for="username" class="form-label">Username</label>
    <input type="text" class="form-control" id="username" name="username" required>
</div>
<div class="mb-3 text-start">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password" required>
</div>
<button type="submit" class="btn btn-primary w-100">Login</button>
</form>

        <!-- Footer -->
        <footer class="mt-4 text-center">
            <p>&copy; Instutute of Computer Studies. All rights reserved.</p>
        </footer>

    </div>

</body>
</html>
