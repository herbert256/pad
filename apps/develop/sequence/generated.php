<?php

  if ( $parm or $type == 'gould' or $type == 'range'
    or file_exists ( PT . "$type/fixed.php" )

  )
    return;

  if ( file_exists ( PT . "$type/generated.php" ) )
    return;

  padFilePut ( APP . "_generate.txt", $type );

  $fixed = padCode ( "{sequence $type, rows=10000, try=10000}{\$sequence},{/sequence}" );
  $fixed = substr ($fixed, 0, -1);

  $code = "<?php const PAD$type=[$fixed]; ?>";

  padFilePut ( PT . "$type/generated.php", $code );

?>