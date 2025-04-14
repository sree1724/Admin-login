<?php
// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "abhishek";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Sanitize and receive data
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$subject = $conn->real_escape_string($_POST['subject']);
$message = $conn->real_escape_string($_POST['message']);

// Store in DB
$sql = "INSERT INTO contact_form (name, email, subject, message) 
        VALUES ('$name', '$email', '$subject', '$message')";

if ($conn->query($sql) === TRUE) {
  // Send email notification to you
  $to = "sreea8309@gmail.com";
  $mailSubject = "New Contact Form Submission";
  $body = "You have a new message:\n\nName: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
  $headers = "From: no-reply@yourwebsite.com";

  mail($to, $mailSubject, $body, $headers);

  echo "<script>alert('Thank you! Your message has been sent.'); window.location.href='index.html';</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
