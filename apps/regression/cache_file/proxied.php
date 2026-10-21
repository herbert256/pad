<?php

  // A page that greets the visitor it knows - from a cookie of its own or a Basic login - and
  // a guest otherwise; shared caches may keep it (_config/config.php).

  $proxiedWho = $_COOKIE ['viewer'] ?? $_SERVER ['PHP_AUTH_USER'] ?? 'guest';

?>
