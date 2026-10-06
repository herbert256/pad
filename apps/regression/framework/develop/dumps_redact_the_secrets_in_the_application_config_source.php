<?php

  // An error report shows the globals, and $padConfigApp holds the whole source of the
  // application's _config/config.php - where the database password and the application key
  // are written. Redacted by name alone it went out whole; the assignment of a secret name
  // inside a text is redacted as the global of that name is, the rest stays readable.

  $source = "<?php\n"
          . "  \$padSqlHost     = 'db.example';\n"
          . "  \$padSqlPassword = 'pw;\\'x';\n"
          . "  \$padAppKey      = \"base64:a2tr\";\n"
          . "  \$mail           = [ 'smtp_password' => 'mailpw', 'host' => 'mx' ];\n"
          . "  define ( 'API_TOKEN', 'tok' );\n";

  $redacted = json_encode ( padRedact ( [ 'padConfigApp' => $source ] ), JSON_UNESCAPED_SLASHES );

?>
