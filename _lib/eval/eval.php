<?php


  function padEval ( $eval, $value='' ) {

    if ( strlen ( trim ( $eval ) ) == 0 ) 
      return ''; 

    $result = [];

    padEvalParse     ( $result, $eval, $value );    
    padEvalAfter     ( $result );  
    padEvalOpenClose ( $result, $value ) ;
    
    $return = reset ( $result );

    return $return [0];

  }  
 

?>