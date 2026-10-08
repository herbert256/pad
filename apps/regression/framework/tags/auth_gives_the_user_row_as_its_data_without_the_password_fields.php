<?php

  // padLogin keeps the row - less its password and token fields - and {auth} hands it to
  // its content as the data of one occurrence.

  padLogin ( [ 'id' => 7, 'name' => 'Ann', 'role' => 'editor', 'password' => padHash ( 'secret' ), 'remember_token' => 'x' ] );

  $kept = json_encode ( array_keys ( padUser () ) );

?>
