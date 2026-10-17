<?php

  // A remember-me login of a user whose id is a name with a space: the cookie reads only a
  // number or a plain token back, so the login says it cannot be remembered - it wrote a
  // cookie that never logged in again, without a word.

  function idCaseFind ( $id ) { return [ 'id' => $id, 'name' => 'Ann' ]; }

  $padAuthRemember = 'idCaseFind';

  padLogin ( [ 'id' => 'ann smith', 'name' => 'Ann' ], TRUE );

?>
