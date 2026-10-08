<?php

  // The second page of a table sorted on its name, three rows a page.

  $_GET ['sort'] = 'name';
  $_GET ['page'] = '2';

  $people = [];

  foreach ( [ 'Hugo', 'Eva', 'Gina', 'Bas', 'Fien', 'Ada', 'Cas', 'Dex' ] as $index => $name )
    $people [] = [ 'id' => $index + 1, 'name' => $name ];

?>
