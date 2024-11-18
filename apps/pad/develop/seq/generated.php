<?php

  if ( $parm or $type == 'gould'
    or file_exists ( PAD . "seq/types/$type/fixed.php" ) 
    or file_exists ( PAD . "seq/types/$type/bool.php" ) )
      return;

  if ( file_exists ( PAD . "seq/types/$type/generated.php" ) )
    return;

  $name = ucfirst ($type);

  $fixed = padCode ( "{seq $type, rows=5000, try=5000}{\$seq},{/seq}" );
  $fixed = substr ($fixed, 0, -1);
  
  $code = "<?php const PAD$type=[$fixed]; ?>";  

  file_put_contents ( PAD . "seq/types/$type/generated.php", $code );

?>