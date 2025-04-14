<?php
session_start();

$admin_user = "Abhi";
$admin_pass = "Abhi@123";

// Initialize attempts
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = 0;
}

$locked = false;
$remainingTime = 0;

if ($_SESSION['attempts'] >= 3) {
    $timePassed = time() - $_SESSION['lockout_time'];
    if ($timePassed < 10) {
        $locked = true;
        $remainingTime = 10 - $timePassed;
    } else {
        $_SESSION['attempts'] = 0;
        $_SESSION['lockout_time'] = 0;
        $locked = false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$locked) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['attempts'] = 0;
        header("Location: admin-contacts.php");
        exit;
    } else {
        $_SESSION['attempts']++;
        if ($_SESSION['attempts'] >= 3) {
            $_SESSION['lockout_time'] = time();
            $locked = true;
            $remainingTime = 10;
        } else {
            $_SESSION['error'] = "Invalid username or password! (" . (3 - $_SESSION['attempts']) . " attempts left)";
        }
    }
}

$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure Admin Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Share Tech Mono', monospace; }
    body {
      background: #0d0d0d;
      color: #00ffcc;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .matrix-bg {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      z-index: 0;
      overflow: hidden;
    }
    canvas { display: block; }
    .login-container {
      position: relative;
      z-index: 1;
      background: rgba(0, 0, 0, 0.8);
      padding: 40px;
      border: 2px solid #00ffcc;
      box-shadow: 0 0 20px #00ffcc;
      width: 360px;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #00ffcc;
      text-shadow: 0 0 10px #00ffcc;
      animation: typing 2s steps(20) 1;
      white-space: nowrap;
      overflow: hidden;
      border-right: 2px solid #00ffcc;
    }
    @keyframes typing {
      from { width: 0; }
      to { width: 100%; }
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 12px 0;
      background: #1a1a1a;
      color: #00ffcc;
      border: 1px solid #00ffcc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(45deg, #00ffcc, #00ccff);
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      box-shadow: 0 0 10px #00ffcc;
      transition: 0.3s ease;
    }
    button:hover {
      background: linear-gradient(45deg, #00ccff, #00ffcc);
      box-shadow: 0 0 20px #00ffcc;
    }
    .error {
      color: #ff4444;
      text-align: center;
      margin-bottom: 15px;
    }
    .lock-msg {
      color: orange;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="matrix-bg"><canvas id="matrix"></canvas></div>

  <div class="login-container">
    <h2>🛡️ Secure Login</h2>
    <?php if ($locked): ?>
      <p class="lock-msg">⏳ Locked for <span id="countdown"><?php echo $remainingTime; ?></span> seconds...</p>
    <?php else: ?>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">ACCESS</button>
      </form>
    <?php endif; ?>
  </div>

  <script>
    const canvas = document.getElementById('matrix');
    const ctx = canvas.getContext('2d');
    canvas.height = window.innerHeight;
    canvas.width = window.innerWidth;
    const letters = "01";
    const fontSize = 14;
    const columns = canvas.width / fontSize;
    const drops = [];
    for (let x = 0; x < columns; x++) drops[x] = 1;

    function draw() {
      ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "#00ffcc";
      ctx.font = fontSize + "px monospace";
      for (let i = 0; i < drops.length; i++) {
        const text = letters.charAt(Math.floor(Math.random() * letters.length));
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);
        if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
          drops[i] = 0;
        }
        drops[i]++;
      }
    }
    setInterval(draw, 33);

    // Countdown script
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
      let timeLeft = parseInt(countdownEl.innerText);
      const timer = setInterval(() => {
        timeLeft--;
        countdownEl.innerText = timeLeft;
        if (timeLeft <= 0) {
          clearInterval(timer);
          location.href = location.pathname; // reload without warning
        }
      }, 1000);
    }
  </script>
</body>
</html>
