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
    return pad_make_content ( pad_get_xxx ( $input, $parms ));
  }

  function pad_get_data ( $input, $type='', $parms=[] ) {
    return pad_make_data ( pad_get_xxx ( $input, $parms ), $type );
  }

  function pad_get_flag ( $input, $type='', $parms=[] ) {
    return pad_make_flag ( pad_get_xxx ( $input, $parms ) );
  }

  function pad_get_xxx ( $input, $parms ) {
    return pad_get ( $input, $parms );
  }


  function pad_get ( $input, $parm=[] ) {

    $result = $input;

    while ( pad_get_check ( $result ) )
      $result = pad_get_go ( $result, $parm );

    return $result;

  }

  
 function pad_get_check ( $input, $curl=TRUE ) {

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

    if ( pad_file_valid_name ( APP . "data/$input" ) and pad_file_exists ( APP . "data/$input" ) )
      return TRUE;

    $parts = pad_explode ($input, ':', 3);

    if ( count ($parts) >= 2 and pad_check_type ( $parts [0], $parts [1] ) )
      return TRUE;

   if ( pad_get_type_eval ( $parts [0] ) )
     return TRUE;

    return FALSE;

  }


  function pad_get_go ( $input, $parm=[] ) {

    if ( is_array($input) or is_object($input) or is_resource($input) )
      return FALSE;

    if ( substr ( $input, 0, 7 ) == 'http://' or substr ( $input, 0, 8 ) == 'https://' )
      return pad_curl_data ($input);
 
    if ( pad_file_exists ( APP . "data/$input" ) )
      return pad_file_get_contents ( APP . "data/$input" );

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

    if ( pad_file_exists ( PAD . "eval/single/$kind.php" ) )
      $get = include PAD . "eval/single/$kind.php";
    else
      $get = include PAD . "eval/parms/$kind.php";

    return $get;

  }


?>