<?php

  $build = padSeqBuild ( $type );
  $parm  = '';

  if ( $build == 'fixed' or $build == 'order' or $type == 'get' )
    return; 

  $a = padCode ( "{seq $type=3, rows=15}{\$seq}{/seq}" );
  $b = padCode ( "{seq $type=5, rows=15}{\$seq}{/seq}" );

  $e = FALSE;
  foreach ( [ 'loop', 'make', 'function', 'bool' ] as $check ) 
    if (   file_exists       ( PAD . "seq/types/$type/$check.php") ) {
      $c = file_get_contents ( PAD . "seq/types/$type/$check.php");
      if ( str_contains ( $c, "padSeqParm" ) ) 
        $e = TRUE;
    }

  if ( $e or $a <> $b ) 
    $parm = '=4';

  if ( $parm )
    file_put_contents ( PAD . "seq/types/$type/flags/parm", 1 );

?>