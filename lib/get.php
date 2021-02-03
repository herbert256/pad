<?php  

  function pad_get_content_store ( $type='' ) {
    return pad_get_store ( 'content', $type );
  }

  function pad_get_data_store ( $type='' ) {
    return pad_get_store ( 'data', $type  );
  }

  function pad_get_flag_store ( $type='' ) {
    return pad_get_store ( 'flag', $type  );
  }


  function pad_get_store ( $store, $type ) {

    $parm = pad_tag_parm ($store);

    if ( isset ( $GLOBALS ["pad_$store"."_store"] [$parm] ) )
      return $GLOBALS ["pad_$store"."_store"] [ $parm ];

    $function = "pad_get_$store";

    return $function ( $parm, $type );

  }

  function pad_get_content ( $input, $type='', $parms=[]) {
    return pad_make_content ( pad_get_xxx ( 'content', $input, $parms ));
  }

  function pad_get_data ( $input, $type='', $parms=[] ) {
    return pad_make_data ( pad_get_xxx ( 'data', $input, $parms ), $type );
  }

  function pad_get_flag ( $input, $type='', $parms=[] ) {
    return pad_make_flag ( pad_get_xxx ( 'flag', $input, $parms ) );
  }


  function pad_get_xxx ( $type, $input, $parms ) {

    pad_trace ("get/$type", "start: " . pad_info ($input));
    $data = pad_get ( $input, $parms );
    pad_trace ("get/type", "end: " . pad_info ($data));

    return $data;

  }


  function pad_get ( $input, $parm=[] ) {

    pad_trace ( "get/start", $input);

    $result = $input;

    while ( pad_get_check ( $result ) )
      $result = pad_get_go ( $result, $parm );

    return $result;

  }

  
 function pad_get_check ( $input, $curl=TRUE ) {

    pad_trace ( "get/check", $input);

    if ( strlen(trim($input)) == 0)
      return FALSE;

    if ( is_array($input) or is_object($input) or is_resource($input) )
      return FALSE;

    if ( ! $input )
      return FALSE;

    if ( $curl ) {
      if ( substr ( $input, 0, 7 ) == 'http://' )
        return TRUE;
      if ( substr ( $input, 0, 8 ) == 'https://' )
        return TRUE;
    }

    if ( pad_valid_file_name ( PAD_APP . "data/$input" ) and pad_file_exists ( PAD_APP . "data/$input" ) )
      return TRUE;

    $parts = pad_explode ($input, ':', 3);

    if ( count ($parts) >= 2 and pad_check_type ( $parts [0], $parts [1] ) )
      return TRUE;

   if ( pad_get_type_eval ( $parts [0] ) )
     return TRUE;

    return FALSE;

  }

  function pad_get_go ( $input, $parm=[] ) {

    pad_trace ( "get/go", $input);

    if ( is_array($input) or is_object($input) or is_resource($input) )
      return FALSE;

    if ( substr ( $input, 0, 7 ) == 'http://' or substr ( $input, 0, 8 ) == 'https://' )
      return pad_curl ($input);
 
    if ( pad_file_exists ( PAD_APP . "data/$input" ) )
      return pad_file_get_contents ( PAD_APP . "data/$input" );

    $parts = pad_explode ($input, ':', 3);

    if ( count ($parts) >= 2 and pad_check_type ( $parts [0], $parts [1] ) ) {
      
      $kind  = $parts [0];
      $name  = $parts [1];
      $value = $parts [2] ?? '';
   
    } else {

      $parts = pad_explode ($input, ':', 2);
      $kind  = pad_get_type_eval ( $parts [0] );
      $name  = $parts [0];
      $value = $parts [1] ?? '';      
   
    }

    $count = count ($parm);

    $get = include PAD_HOME . "eval/$kind.php";

    pad_trace ( "get/end", "$kind:$name:$value --> " . pad_make_content ( $get ) );

    return $get;

  }

?>