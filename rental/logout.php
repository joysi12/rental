<?php
session_start();
session_destroy();
echo '<script>alert("Anda telah logout."); location.href="login.php";</script>';
exit();
?>
