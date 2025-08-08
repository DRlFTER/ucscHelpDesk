<!DOCTYPE html>
<html>
<head>
  <title>Login/Register</title>
  <style>
    .hidden { display: none; }
  </style>
</head>
<body>

<div id="loginForm">
  <h2>Login</h2>
  <form method="POST" action="../../controllers/authController.php?action=login">
    Username: <input type="text" name="username" autocomplete="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
  </form>
  <button onclick="showRegister()">Register</button>
</div>

<div id="registerForm" class="hidden">
  <h2>Register</h2>
  <form method="POST" action="../../controllers/authController.php?action=register">
    Name: <input type="text" name="name" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Role:
    <select name="role">
      <option value="student">Student</option>
      <option value="staff">Staff</option>
      <option value="admin">Admin</option>
      <option value="counselor">Counselor</option>
    </select><br>
    <input type="submit" value="Register">
  </form>
  <button onclick="showLogin()">Already have an account</button>
</div>

<script>
  function showRegister() {
    document.getElementById('loginForm').classList.add('hidden');
    document.getElementById('registerForm').classList.remove('hidden');
  }

  function showLogin() {
    document.getElementById('registerForm').classList.add('hidden');
    document.getElementById('loginForm').classList.remove('hidden');
  }
</script>

</body>
</html>
