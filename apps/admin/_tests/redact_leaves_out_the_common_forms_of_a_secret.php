<?php

  // The secrets the console leaves out of the sources it shows - in the ways a secret is
  // written: JSON, YAML, XML, a define (), an array element, a property, an escaped quote
  // and the password of an address, besides the PHP variable, the array key and the .env
  // line it knew. Each line says whether hunter2 is still to be read.

  $redactSamples = [
    'json'     => '{ "password": "hunter2", "apiKey": "hunter2" }',
    'yaml'     => "db:\n  password: hunter2\n",
    'xml'      => '<password>hunter2</password>',
    'define'   => "define ( 'DB_PASSWORD', 'hunter2' );",
    'element'  => "\$config ['db_password'] = 'hunter2';",
    'property' => "\$this->password = 'hunter2';",
    'escaped'  => "\$padSqlPassword = 'it\\'s-hunter2';",
    'address'  => "\$dsn = 'mysql://root:hunter2@localhost/db';",
    'variable' => "\$padSqlPassword = 'hunter2';",
    'env'      => 'padSqlPassword = hunter2'
  ];

  $redactRows = [];

  foreach ( $redactSamples as $redactKind => $redactText )
    $redactRows [] = [ 'kind' => $redactKind, 'said' => str_contains ( adminRedact ( $redactText ), 'hunter2' ) ? 'shown' : 'left out' ];

?>
