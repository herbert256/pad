<?php

  $padStrBox = padTagParm ( 'sandbox'  );
  $padStrRes = padTagParm ( 'reset'    );
  $padStrCln = padTagParm ( 'clean'    );
  $padStrFun = padTagParm ( 'function' );

  if ( $padStrFun )
    return padStr ( $padStrCod );
  else 
    return include pad . 'start/pad.php';
  
?>