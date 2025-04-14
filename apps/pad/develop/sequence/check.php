<?php

  $name = ucfirst ($type);

  if ($parm) $extra = "='\$p'";
  else       $extra = '';

  $code =   "<?php"
        . "\n"
        . "\n  function padSeqCheck$name ( \$f, \$n, \$p ) {"
        . "\n"
        . "\n    if ( file_exists ( 'sequence/types/$type/bool.php' ) )"
        . "\n      return padSeqBool$name ( \$n, \$p );"
        . "\n"
        . "\n    if ( file_exists ( 'sequence/types/$type/fixed.php' ) ) {"
        . "\n      \$fixed = include 'sequence/types/$type/fixed.php';"
        . "\n      return in_array ( \$n, \$fixed );"        
        . "\n    }"
        . "\n"
        . "\n    if ( file_exists ( 'sequence/types/$type/generated.php' ) ) "
        . "\n      return in_array ( \$n, PAD$type );"
        . "\n"
        . "\n    \$text = padCode ( \"{sequence $type$extra, from=\$f, stop=\$n, try=\$n}{\\\$sequence},{/sequence}\" );"
        . "\n    \$arr  = explode ( ',', \$text );"
        . "\n"
        . "\n    return in_array ( \$n, \$arr );"
        . "\n"  
        . "\n  }"
        . "\n"
        . "\n?>";  

  file_put_contents ( "sequence/types/$type/check.php", $code );
  
?>