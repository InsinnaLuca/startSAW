<?php
session_start();
session_unset();
session_destroy();

echo "Logout effettuato";
exit;