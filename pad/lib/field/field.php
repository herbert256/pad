<?php


  function padFieldCheck ( $parm)          { return padField ( $parm, 1); } 
  function padFieldValue ( $parm)          { return padField ( $parm, 2); } 

  function padArrayCheck ( $parm)          { return padField ( $parm, 3); } 
  function padArrayValue ( $parm)          { return padField ( $parm, 4); } 
  
  function padParmCheck  ( $parm, $lvl=0 ) { return padField ( $parm, 5, $lvl ); } 
  function padParmValue  ( $parm, $lvl=0 ) { return padField ( $parm, 6, $lvl ); } 
  
  function padTagCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 7, $lvl ); } 
  function padTagValue   ( $parm, $lvl=0 ) { return padField ( $parm, 8, $lvl ); } 

  function padFieldNull  ( $parm)          { return padField ( $parm, 9); } 


  function padField ($field, $type, $lvl=0 ) {

    $field = ( substr ( $field, 0, 1 ) == '$' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '!' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '%' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '&' ) ? substr ( $field, 1 ) : $field;

    if ( strpos($field, ':' ) !== FALSE )
      list ( $tag, $field ) = explode (':', $field, 2);

    $idx = ( $tag ) ? padFieldGetLevel ($tag) : padFieldFirstNonParm ();

    if     ( $type == 5 ) $value = padParm        ( $field, $idx  );
    elseif ( $type == 6 ) $value = padParm        ( $field, $idx  );
    elseif ( $type == 7 ) $value = padTag         ( $field, $idx  );
    elseif ( $type == 8 ) $value = padTag         ( $field, $idx  );
    elseif ( $tag       ) $value = padFieldPrefix ( $field, $type, $tag, $idx );
    else                  $value = padFieldLevel  ( $field, $type );

    if ( $value === INF and $lvl and ! $tag ) {
      $idx = padFieldFirstNonParm () - 1;
      if     ( $type == 5 ) $value = padParm ( $field, 0, $idx );
      elseif ( $type == 6 ) $value = padParm ( $field, 1, $idx ); 
      elseif ( $type == 7 ) $value = padTag  ( $field, 0, $idx );
      elseif ( $type == 8 ) $value = padTag  ( $field, 1, $idx );
    }

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === NULL                                               ) ? TRUE  : FALSE;
    elseif ($type == 6) return ( $value === NULL                                               ) ? ''    : $value;
    elseif ($type == 7) return ( $value === NULL                                               ) ? TRUE  : FALSE;
    elseif ($type == 8) return ( $value === NULL                                               ) ? ''    : $value;
    elseif ($type == 9) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }


  function padFieldFirstNonParm  () {

    global $pad, $padType;

    for ($i=$pad; $i; $i--)
      if ( $padType[$i] and $padType[$i] <> 'parm' )
        return $i;

    return $pad - 1;

  }  


?>