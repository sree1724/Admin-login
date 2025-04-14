<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-login.php");
  exit;
}
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $conn = new mysqli("localhost", "root", "", "abhishek");
  $conn->query("DELETE FROM contact_form WHERE id = $id");
}
header("Location: admin-contacts.php");
?>
