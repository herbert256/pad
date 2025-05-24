<?php

  if ( $parm or $type == 'gould' or $type == 'range'
    or file_exists ( "sequence/types/$type/fixed.php" ) 
   # or file_exists ( "sequence/types/$type/bool.php" ) 
  )
    return;

  #if ( file_exists ( "sequence/types/$type/generated.php" ) )
  #  unlink ( "sequence/types/$type/generated.php" );

  if ( file_exists ( "sequence/types/$type/generated.php" ) )
    return;

  file_put_contents  (APP . "_generate.txt", $type);

  $fixed = padCode ( "{sequence $type, rows=10000, try=10000}{\$sequence},{/sequence}" );
  $fixed = substr ($fixed, 0, -1);
  
  $code = "<?php const PAD$type=[$fixed]; ?>";  
  
  file_put_contents ( "sequence/types/$type/generated.php", $code );
 
?>