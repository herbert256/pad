<?php

  if ( $GLOBALS ['padStrFun'] )
    foreach ( $GLOBALS as $padStrKey => $padStrVal )
      global $$padStrKey;

  include pad . 'start/backup.php';

  if ( $padStrIso )
    include pad . 'start/reset.php';
 
  include pad . 'inits/level.php'; 

  if ( $padStrBld  == 'code' ) {
    $padBase [$pad] = $padStrCod;    
    include pad . 'occurrence/start.php'; 
  } else 
    include pad . 'build/build.php'; 

  include pad . 'start/level.php'; 
  include pad . 'start/restore.php';
  
  return $padPad [$pad+1];

?>