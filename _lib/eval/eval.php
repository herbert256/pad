<?php


  function padEval ( $eval, $value='' ) {
      
    if ( file_exists ( "/pad/functions/single/$eval.php" ) )
      return include '/pad/eval/fast.php';
        
    global $padEval, $padEvalCnt;

    $padEvalCnt++;
  
    $padEval [$padEvalCnt] = [];

    padEvalParse  ( $padEval [$padEvalCnt], $eval, $value );    
    padEvalAfter  ( $padEval [$padEvalCnt] );  
    padEvalArray  ( $padEval [$padEvalCnt], $value );
    padEvalOpnCls ( $padEval [$padEvalCnt], $value );
    padEvalOpr    ( $padEval [$padEvalCnt], $value );

    $key = array_key_first ($padEval [$padEvalCnt]);

    if     ( count($padEval [$padEvalCnt]) < 1        ) padError ("No value back: $eval");
    elseif ( count($padEval [$padEvalCnt]) > 1        ) padError ("More then one value back: $eval");
    elseif ( $padEval [$padEvalCnt][$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    $padEvalCnt--;

    return $padEval [$padEvalCnt+1] [$key] [0];

  }  
 

?>