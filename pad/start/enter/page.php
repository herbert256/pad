<?php

  $padPage    = $padParm;
  $padInclude = padTagParm ( 'include', TRUE );
 
  $padDir  = padDir ();  
  $padPath = padPath ();  

  $padBuild   = 'page';
  $padCode    = '';
  $padGlobals = FALSE;

  return include pad . "start/parms.php";
  
?>