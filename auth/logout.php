<?php
require_once '../config/security.php';
initSecureSession();
secureLogout('../index.php');
?>
