<?php
    
  if ( $pqCheck == 'make' and ! file_exists ( "sequence/types/$pqSeq/make.php" ) )
    return;

  $pqBuild = $pqCheck;

?>