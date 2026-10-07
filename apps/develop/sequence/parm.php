<?php

  // Whether the type takes a parameter is the sequence application's own question, pqParm in
  // its _lib/: a copy of it stood in develop's _lib/ and had already drifted from it.

  include_once APPS . 'sequence/_lib/parm.php';

  $parm = pqParm ( $type );

  if ( $parm )
    file_put_contents ( PT . "$type/flags/parm", 1, LOCK_EX );

?>
