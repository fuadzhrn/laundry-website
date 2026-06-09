<?php
session_start();
session_unset();
session_destroy();

// Redirect ke halaman login dengan notifikasi
header('Location: login.php?logout=1');
exit;
