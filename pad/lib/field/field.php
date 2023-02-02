<?php


  function padFieldCheck ( $parm ) { return padField ( $parm, 1 ); } 
  function padFieldValue ( $parm ) { return padField ( $parm, 2 ); } 

  function padArrayCheck ( $parm ) { return padField ( $parm, 3 ); } 
  function padArrayValue ( $parm ) { return padField ( $parm, 4 ); } 
  
  function padParmCheck  ( $parm ) { return padField ( $parm, 5 ); } 
  function padParmValue  ( $parm ) { return padField ( $parm, 6 ); } 
  
  function padTagCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 7, $lvl) ; } 
  function padTagValue   ( $parm, $lvl=0 ) { return padField ( $parm, 8, $lvl ); } 

  function padFieldNull  ( $parm ) { return padField ( $parm, 9 ); } 


  function padField ( $field, $type, $lvl=0 ) {

    $field = ( substr ( $field, 0, 1 ) == '$' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '!' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '#' ) ? substr ( $field, 1 ) : $field;
    $field = ( substr ( $field, 0, 1 ) == '&' ) ? substr ( $field, 1 ) : $field;

    if ( strpos($field, ':' ) !== FALSE ) {
      list ( $prefix, $field ) = explode (':', $field, 2);
      $idx = padFieldGetLevel ($prefix);
    } else {
      $prefix = '';
      if ( in_array ( $type, [5,6] ) )
        $idx = $GLOBALS ['pad'];
      elseif ( in_array ( $type, [7,8] ) )
        if ($lvl)
          $idx = padFieldFirstNonTag (1);
        else
          $idx = padFieldFirstNonTag ();
      else
        $idx = $GLOBALS ['pad'];
    }

    if ( strpos($field, ':') !== FALSE  )
      list ( $field, $parm ) = explode (':', $field, 2);
    else
      $parm  = '';

    $result = padFieldGo ( $type, $prefix, $field, $parm, $idx );

    if ( $GLOBALS['padTrace'] )
      include PAD . 'pad/trace/field.php';

    return $result;

  }


  function padFieldGo ( $type, $prefix, $field, $parm, $idx ) {

    if     ( $type == 5 ) $value = padParm        ( $field, $idx, $type );
    elseif ( $type == 6 ) $value = padParm        ( $field, $idx, $type );
    elseif ( $type == 7 ) $value = padTag         ( $field, $idx, $type, $parm);
    elseif ( $type == 8 ) $value = padTag         ( $field, $idx, $type, $parm );
    elseif ( $prefix    ) $value = padFieldPrefix ( $field, $type, $prefix, $idx );
    else                  $value = padFieldLevel  ( $field, $type );

    if ( $value === INF and ! $prefix and in_array ( $type, [5,6] ) ) {
      $idx = $idx - 1;
      $value = padParm ( $field, $idx, $type );
    }

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type == 6) return ( $value === INF                                                ) ? ''    : $value;
    elseif ($type == 7) return ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type == 8) return ( $value === INF                                                ) ? ''    : $value;
    elseif ($type == 9) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }


?>