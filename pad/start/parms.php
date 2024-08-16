<?php

  $padStrBox = padTagParm ( 'sandbox'  );
  $padStrRes = padTagParm ( 'reset'    );
  $padStrCln = padTagParm ( 'clean'    );
  $padStrFun = padTagParm ( 'function' );

  if ( $padStrFun )
    return padStr ( $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun );
  else 
    return include '/pad/start/pad.php';
  
?>