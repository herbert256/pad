<?php

  // Lets a sequence type run its own init.php while it is being registered as a play.
  //
  // Included by plays/add.php; does nothing unless the type has an init.php. Presents the
  // play's parameter as $pqParm - resolved through $pqStore, first term, when it names a
  // stored sequence - and restores parm/increment/from/to afterwards, so a type that
  // rewrites those for its own generation (even/init.php doubles from and to, for
  // instance) cannot disturb the main sequence it is filtering.

  if ( ! file_exists ( PT . "$pqSeq/init.php" ) )
    return;

  $pqParmSave = $pqParm;
  $pqIncSave  = $pqInc;
  $pqFromSave = $pqFrom;
  $pqToSave   = $pqTo;

  $pqParm = $padPrmValue;

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = reset ( $pqStore [$pqParm] );

  include PT . "$pqSeq/init.php";

  $pqParm = $pqParmSave;
  $pqInc  = $pqIncSave;
  $pqFrom = $pqFromSave;
  $pqTo   = $pqToSave;

?>