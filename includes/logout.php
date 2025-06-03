<?php
session_set_cookie_params(86400); // יום אחד
session_start();
session_unset();
session_destroy();


// ניצור SESSION זמני רק להודעה
session_start();
$_SESSION['logout_message'] = "Hope to see you again soon! 👋";

header("Location: ../index.php");
exit();
?>