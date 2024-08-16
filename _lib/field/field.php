<?php


  function padFieldCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 1, $lvl  ); } 
  function padFieldValue   ( $parm, $lvl=0 ) { return padField ( $parm, 2, $lvl  ); } 
 
  function padArrayCheck   ( $parm, $lvl=0 ) { return padField ( $parm, 3, $lvl  ); } 
  function padArrayValue   ( $parm, $lvl=0 ) { return padField ( $parm, 4, $lvl  ); } 
  
  function padOptCheck     ( $parm )         { return padField ( $parm, 5        ); } 
  function padOptValue     ( $parm )         { return padField ( $parm, 6        ); } 
  
  function padTagCheck     ( $parm         ) { return padField ( $parm, 7        ); } 
  function padTagValue     ( $parm, $lvl=0 ) { return padField ( $parm, 8, $lvl  ); } 

  function padFieldNull    ( $parm )         { return padField ( $parm, 9        ); } 

  
  function padField ( $field, $type, $lvl=0 ) {

    if ( $GLOBALS ['padInfo'] ) 
      include '/pad/info/events/field.php';

    if ( str_contains ( $field, '@' ) or str_contains ( $field, '.' ) )
    
      $value = padFieldAt ( $field, $lvl );

    else {

      if ( str_contains ($field, ':' ) )
        list ( $prefix, $field ) = explode (':', $field, 2);
      else 
        $prefix = '';

      if     ( $prefix                   ) $idx = padFieldGetLevel ($prefix);
      elseif ( in_array ( $type, [5,6] ) ) $idx = padFieldFirstParmTag ();
      elseif ( in_array ( $type, [7,8] ) ) $idx = padFieldFirstNonTag ($lvl);
      else                                 $idx = $GLOBALS ['pad'];
    
      list ( $field, $parm ) = padSplit ( ':', $field );

      if     ( $type ==  5 ) $value = padParm        ( $field, $idx, $type );
      elseif ( $type ==  6 ) $value = padParm        ( $field, $idx, $type );
      elseif ( $type ==  7 ) $value = padTag         ( $field, $idx, $type, $parm );
      elseif ( $type ==  8 ) $value = padTag         ( $field, $idx, $type, $parm );
      elseif ( $prefix     ) $value = padFieldPrefix ( $field, $idx, $type, $prefix );
      else                   $value = padFieldLevel  ( $field, $type );

    }  
   
    if     ($type ==  1) $return = ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type ==  2) $return = ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type ==  3) $return = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type ==  4) $return = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type ==  5) $return = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  6) $return = ( $value === INF                                                ) ? ''    : $value;
    elseif ($type ==  7) $return = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  8) $return = ( $value === INF                                                ) ? ''    : $value;
    elseif ($type ==  9) $return = ( $value === NULL                                               ) ? TRUE  : FALSE;

    if ( $GLOBALS ['padInfo'] )
      include '/pad/info/events/field.php';    

    return $return;

  }


  function padFieldAt ( $field, $lvl ) {

    if ( str_starts_with ($field, '@') and substr_count($field, '@') == 1 )  
      $field .= '@*';

    if ( str_contains ( $field, '.' ) and ! str_contains ( $field, '@' )  )  
      $field .= '@*';
    
    return padAt ( $field, $lvl );

  }


  function padRawValue ( $parm ) { 

    return str_replace ( '}', '&close;', padFieldValue ($parm) );

  }


  function padUrlValue ( $parm )  { 

    return "&$parm=" . urlencode ( padFieldValue ( $parm ) ); 

  }
  

?>