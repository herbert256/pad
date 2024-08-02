<?php

  $padSandbox  = padTagParm ( 'sandbox'  );
  $padIsolate  = padTagParm ( 'isolate'  );
  $padFunction = padTagParm ( 'function' );

  if ( $padFunction )
    return padFunction ( $padCode );
  else 
    return include pad . 'start/pad.php';
  
?>