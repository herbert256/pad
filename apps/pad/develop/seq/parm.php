<?php

  $build = padSeqBuild ( $type );
  $parm  = '';

  if ( $build == 'fixed' or $build == 'order' or $type == 'get' )
    return; 

  $a = padCode ( "{sequence $type=3, rows=15}{\$sequence}{/sequence}" );
  $b = padCode ( "{sequence $type=5, rows=15}{\$sequence}{/sequence}" );

  $e = FALSE;
  foreach ( [ 'loop', 'make', 'function', 'bool' ] as $check ) 
    if (   file_exists       ( "seq/types/$type/$check.php") ) {
      $c = file_get_contents ( "seq/types/$type/$check.php");
      if ( str_contains ( $c, "padSeqParm" ) ) 
        $e = TRUE;
    }

  if ( $e or $a <> $b ) 
    $parm = '=4';

  if ( $parm )
    file_put_contents ( "seq/types/$type/flags/parm", 1 );

?>