<?php

  // A secret's value runs on past a ; - token=ab;cd is one value - but a plain value ended
  // there no more either, and swallowed the secret after it: a=1;token=abc, a PDO DSN's
  // ...;user=app;password=hunter2 and Server=db;Password=hunter2; showed the password in
  // {debug}, the dumps, the toolbar and the JSON channel.

  $shown = [];

  foreach ( [ 'pgsql:host=localhost;dbname=app;user=app;password=hunter2', 'Server=db;Database=app;Password=hunter2;',
              'a=1;token=abc', '/x/?a=1;password=hunter2', '/x/?reset&token=ab;cd&b=2' ] as $text )
    $shown [] = padRedactText ( $text, [] );

  $result = implode ( ' | ', $shown );

?>
