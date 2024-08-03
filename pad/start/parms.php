<?php

  $padStrBox  = padTagParm ( 'sandbox'  );
  $padStrIso  = padTagParm ( 'isolate'  );
  $padStrFun = padTagParm ( 'function' );

  if ( $padStrFun )
    return padStr ( $padStrCod );
  else 
    return include pad . 'start/pad.php';
  
?>