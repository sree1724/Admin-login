<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
$conn = new mysqli("localhost", "root", "", "abhishek");
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT * FROM contact_form WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR subject LIKE '%$search%' ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; }
    th { background: #0072ff; color: white; }
    tr:nth-child(even) { background: #f4f4f4; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; }
    .logout { color: red; text-decoration: none; }
  </style>
</head>
<body>
  <div class="top-bar">
    <h2>📬 Contact Submissions</h2>
    <a class="logout" href="logout.php">Logout</a>
  </div>
  <form method="GET">
    <input type="text" name="search" placeholder="Search name/email/subject" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
  </form>

  <table>
    <tr>
      <th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Actions</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><a href="mailto:<?= $row['email'] ?>"><?= $row['email'] ?></a></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
          <td><?= $row['submitted_at'] ?></td>
          <td><a href="delete-entry.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this entry?')">🗑️</a></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7" style="text-align:center;">No entries found.</td></tr>
    <?php endif; ?>
  </table>
</body>
</html>
