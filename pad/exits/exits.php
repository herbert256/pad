<?php
  
  $padOutput = $padResult [0];

  $padOutput = padUnescape ( $padOutput );

  include pad . 'exits/output.php';
 
  $padEtag = padMD5 ($padOutput);
 
  $padStop = ( $padEtag304 and ($padCacheClient??'') == $padEtag ) ? 304 : 200;

  if ( $padCache and $padCacheServerAge )
    include pad . 'cache/exits.php';

  if ( $padTraceActive )
    include pad . 'tail/types/trace/exit/config.php'; 

  padStop ($padStop);

?>