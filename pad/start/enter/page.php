<?php

  $padPage    = $padParm;
  $padInclude = padTagParm ( 'include', TRUE );
 
  $padDir  = padDir ();  
  $padPath = padPath ();  

  $padBuild = 'page';
  $padCode  = '';

  return include pad . "start/parms.php";
  
?>