<?php

  $padStrBox = padTagParm ( 'sandbox'  );
  $padStrRes = padTagParm ( 'reset'    );
  $padStrCln = padTagParm ( 'clean'    );
  $padStrFun = padTagParm ( 'function' );

  if ( $padStrFun )
    return padStrFun ( $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun );
  else
    return include PAD . 'start/pad.php';

?>