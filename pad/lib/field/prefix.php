<?php


  function pField_prefix ( $field, $type ) {

    global $p, $pCurrent;

    list ( $prefix, $field ) = explode (':', $field, 2);

    if ( is_numeric($field) )
      return pField_tag_nr ($prefix, $field);

    if ( $prefix == 'PHP' or $prefix === 1 or $prefix === '1' )
      return pField_search ($GLOBALS, $field, $type);

    $lvl = pField_tag_lvl_base ( $prefix, FALSE );

    if ( $lvl == 1 ) 
      $return = pField_search ($GLOBALS, $field, $type);
    elseif ( $lvl ) 
      $return = pField_search ($pCurrent [$lvl], $field, $type);

    if ( $return === INF ) {
      $GLOBALS['pField_double_check'] = TRUE;
      $tag = "$prefix#$field";
      if     ( pField_check($tag) ) $return = pField_value($tag);
      elseif ( pArray_check($tag) ) $return = pArray_value($tag);   
      $GLOBALS['pField_double_check'] = FALSE;
    }

    return $return;
    
  }


  function pField_tag_nr ($tag, $nr) {

    $lvl = pField_tag_lvl ($tag, FALSE);
    $idx = intval ($nr) - 1 ;

    global $pPrmsVal;
    
    if ( isset ( $pPrmsVal[$lvl] [$idx] ) )
      return $pPrmsVal[$lvl] [$idx]; 
    else
      return INF;

  }


?>