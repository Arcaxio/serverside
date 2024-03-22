<?php
session_start();
session_destroy();
header("Location: ../index.php");  // Redirect back to index.php
exit;
?>