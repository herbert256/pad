<?php


  function padEval ( $eval, $value='' ) {
      
    if ( file_exists ( "/pad/functions/single/$eval.php" ) )
      return include '/pad/eval/fast.php';

    $result = [];

    padEvalParse ( $result, $eval );    
    padEvalAfter ( $result );  

    $pipe  = 0;
    $pipes = [];

    foreach ( $result as $key => $val )
      if ( $val [1] == 'pipe' )
        $pipe++;
      else
        $pipes [$pipe] [$key] = $val;

    foreach ( $pipes as $one )
      $value = padEvalResult ( $one, $value, $eval );

    return $value;

  }  
 

?>