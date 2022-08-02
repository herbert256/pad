<?php

  $pad_parameters [$pad_lvl] ['tag_count']++;

  $pad_tag_result = include PAD . "level/type.php";

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( pad_tag_parm ('content') ) $pad_content         = include PAD . "options/content.php";    
  if ( pad_tag_parm ('else')    ) $pad_false           = include PAD . "options/else.php";    
  if ( pad_tag_parm ('data')    ) $pad_data [$pad_lvl] = include PAD . "options/data.php";    
  if ( pad_tag_parm ('flag')    ) $pad_tag_result      = include PAD . "options/flag.php";    

  $pad_parameters [$pad_lvl] ['true']            = pad_true_false ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['tag_result']      = pad_info ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['tag_result_data'] = $pad_tag_result;

  if     ( $pad_tag_result === NULL )   $pad_null = TRUE;
  elseif ( $pad_tag_result === INF )    $pad_null = TRUE;
  elseif ( $pad_tag_result === NAN )    $pad_null = TRUE;
  else                                  $pad_null = FALSE;

  if     ( is_array($pad_tag_result) and ! count($pad_tag_result) ) $pad_else = TRUE;
  elseif ( is_array($pad_tag_result) and   count($pad_tag_result) ) $pad_else = FALSE;
  elseif ( $pad_tag_result === FALSE                              ) $pad_else = TRUE;
  elseif ( $pad_tag_result === ''                                 ) $pad_else = TRUE;
  else                                                              $pad_else = FALSE;

  if ( $pad_null ) {

    $pad_base [$pad_lvl] = '';
    $pad_data [$pad_lvl] = [];

  } elseif ( $pad_else ) {
 
    $pad_base [$pad_lvl] = $pad_false;
 
    if ( count ($pad_data [$pad_lvl]) )
      $pad_data [$pad_lvl] = array_slice ($pad_data [$pad_lvl], 0, 1); 
    else
      $pad_data [$pad_lvl] = pad_default_data ();  

  } else {

    if ( is_array($pad_tag_result) or $pad_tag_result === TRUE  )     
      $pad_base [$pad_lvl] = $pad_content;
    elseif ( strpos($pad_tag_result , '@content@') === FALSE)
      $pad_base [$pad_lvl] = $pad_tag_result . $pad_content;
    else
      $pad_base [$pad_lvl] = str_replace('@content@', $pad_content, $pad_tag_result);

    if ( is_array ( $pad_tag_result ) )
      $pad_data [$pad_lvl] = $pad_tag_result;

    $pad_base [$pad_lvl] .= $pad_tag_content;

  }

  if ( $pad_parms_type == 'open' ) {
    if ( pad_tag_parm ('toContent') ) $pad_content_store [$pad_parm] = $pad_base [$pad_lvl];  
    if ( pad_tag_parm ('toData')    ) $pad_data_store    [$pad_parm] = $pad_data [$pad_lvl];  
    if ( pad_tag_parm ('toFlag')    ) $pad_flag_store    [$pad_parm] = $pad_parameters [$pad_lvl] ['true'];  
  }

  if     ( $pad_tag === 'content' ) $pad_content_store [$pad_parm] = $pad_base [$pad_lvl];
  elseif ( $pad_tag === 'data' )    $pad_data_store    [$pad_parm] = $pad_data [$pad_lvl];
  elseif ( $pad_tag === 'flag' )    $pad_flag_store    [$pad_parm] = $pad_parameters [$pad_lvl] ['true'];

  $pad_data [$pad_lvl] = pad_make_data ( $pad_data [$pad_lvl] );   

  $pad_parameters [$pad_lvl] ['content']    = $pad_content;
  $pad_parameters [$pad_lvl] ['false']      = $pad_false;
  $pad_parameters [$pad_lvl] ['true']       = $pad_true;
  $pad_parameters [$pad_lvl] ['null']       = $pad_else;
  $pad_parameters [$pad_lvl] ['else']       = pad_true_false ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['tag_result'] = pad_info ($pad_tag_result);
  $pad_parameters [$pad_lvl] ['default']    = pad_is_default_data ( $pad_data [$pad_lvl] );

  if ( $pad_parms_type == 'open' ) {
    if     ( pad_tag_parm ('toContent') ) return NULL;
    elseif ( pad_tag_parm ('toData') )    return NULL;
    elseif ( pad_tag_parm ('toFlag') )    return NULL;
  }

  elseif ( $pad_tag === 'content' )     return NULL;
  elseif ( $pad_tag === 'data' )        return NULL;
  elseif ( $pad_tag === 'flag' )        return NULL;

  return $pad_tag_result;
  
?>