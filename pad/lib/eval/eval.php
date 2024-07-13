<?php


  function padEval ( $eval, $value='' ) {

    if ( file_exists( "_functions/$eval.php" ) )
      return padEvalFast ( $eval, $value );

    if ( padTrace )
      include pad . 'info/events/eval/start.php';

    if ( strlen(trim($eval)) == 0 )
      return ''; 

    $result = [];

    padEvalParse ( $result, $eval, $value );    
 
    if ( padTrace )
      include pad . 'info/events/eval/parse.php';

    padEvalAfter ( $result );  
 
    if ( padTrace )
      include pad . 'info/events/eval/after.php';

    padEvalGo ( $result, array_key_first($result), array_key_last($result), $value) ;
 
    if ( padTrace )
      include pad . 'info/events/eval/go.php';

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    if ( padTrace )
      include pad . 'info/events/eval/end.php';

    return $result [$key] [0];

  }  
  

  function padEvalFast ( $eval, $value ) {

    if ( padXref ) include pad . 'info/types/xref/events/fast.php';
    if ( padXapp ) include pad . 'info/types/xapp/events/fast.php';

    if ( padTrace )
      return include pad . 'info/events/eval/fast.php';
    else
      return include pad . "functions/$eval.php";

  }  


?>