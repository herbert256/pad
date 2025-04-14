<?php
  
<<<<<<< HEAD
  if ( $padSeqBuildName and $padSeqBuildName !== TRUE )  

    $padSeqBuild = include 'sequence/inits/build/given.php';
  
  elseif ( ! $padSeqBuild ) 

=======
  if ( ! $padSeqBuild ) 
>>>>>>> dcb568e63ed9df0fcf4359709751cf7b83af17ce
    $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

?>
