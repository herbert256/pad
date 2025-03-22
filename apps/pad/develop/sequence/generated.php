<?php

  if ( $parm or $type == 'gould'
    or file_exists ( "sequence/types/$type/fixed.php" ) 
    or file_exists ( "sequence/types/$type/bool.php" ) )
      return;

  if ( file_exists ( "sequence/types/$type/generated.php" ) )
    return;

  $name = ucfirst ($type);

  $fixed = padCode ( "{sequence $type, rows=5000, try=5000}{\$sequence},{/sequence}" );
  $fixed = substr ($fixed, 0, -1);
  
  $code = "<?php const PAD$type=[$fixed]; ?>";  

  file_put_contents ( "sequence/types/$type/generated.php", $code );

?>