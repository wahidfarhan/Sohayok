<?php
session_start();
// Shob session variables muche phelo
session_unset();
// Session-ti dhongsho koro
session_destroy();

// User-ke login page ba index page e niye jao
header("Location: index.php");
exit();
?>