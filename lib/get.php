<?php  


  function pad_get_content_store () {

    $parm = pad_tag_parm ('content');

    if ( isset ( $GLOBALS ['pad_content_store'] [$parm] ) )
      return $GLOBALS ['pad_content_store'] [ $parm ];
    else
      return pad_get_content ( $parm );

  }


  function pad_get_data_store ( $type='' ) {

    $parm = pad_tag_parm ('data');

    if ( isset ( $GLOBALS ['pad_data_store'] [ $parm] ) )
      return $GLOBALS ['pad_data_store'] [ $parm ];
    else
      return pad_get_data ( $parm, [], $type );

  }


  function pad_get_flag_store () {

    $parm = pad_tag_parm ('flag');

    if ( isset ( $GLOBALS ['pad_flag_store'] [ $parm] ) )
      return $GLOBALS ['pad_flag_store'] [ $parm ];
    else
      return pad_get_flag ( $parm );

  }
 

  function pad_get_content ( $input, $parms=[]) {

    $data = pad_get_internal ( $input, $parms );

    return pad_make_string ( $data );

  }

  function pad_get_data ( $input, $parms=[], $data_type='' ) {

    $data = pad_get_internal ( $input, $parms );

    return pad_data ( $data, $data_type );

  }

  function pad_get_flag ( $input, $parms=[] ) {

    $data = pad_get_internal ( $input, $parms );

    return pad_make_flag ( $data );

  }


  function pad_get_internal ( $input, $parm=[] ) {

    $result = $input;

    while ( pad_get_check ( $result ) )
      $result = pad_get_go ( $result, $parm );

    return $result;

  }

  
 function pad_get_check ( $input, $curl=TRUE ) {

    pad_trace ( "get/check", $input);

    if ( $curl ) {
      if ( substr ( $input, 0, 7 ) == 'http://' )
        return TRUE;
      if ( substr ( $input, 0, 8 ) == 'https://' )
        return TRUE;
    }

    if ( file_exists ( PAD_APP . "data/$input" ) )
      return TRUE;

    $parts = pad_explode ($input, '://', 2);

    if ( count ($parts) <> 2 )
      return FALSE;

    $parm = $parts[1];

    $parts = pad_explode ($parts[0], ':');

    if ( count ($parts) > 2 )
      return FALSE;

    if ( count ($parts) == 2 ) {
      $kind = $parts [0];
      $name = $parts [1];
    } else {
      $kind = pad_get_type_eval ( $parts [0] );
      $name = $parts [0];
    }

    if ( ! file_exists ( PAD_HOME . "eval/$kind.php" ) ) 
      return FALSE;

    return TRUE;

  }

  function pad_get_go ( $input, $parm=[] ) {

    pad_trace ( "get/start", "$input");

    if ( substr ( $input, 0, 7 ) == 'http://' or substr ( $input, 0, 8 ) == 'https://' )
      return pad_curl ($input);
 
    if ( file_exists ( PAD_APP . "data/$input" ) )
      return pad_file_get_contents ( PAD_APP . "data/$input" );

    $parts = pad_explode ($input, '://', 2);

    $value = $parts[1];

    $parts = pad_explode ($parts[0], ':');

    if ( count ($parts) == 2 ) {
      $kind = $parts [0];
      $name = $parts [1];
    } else {
      $kind = pad_get_type_eval ( $parts [0] );
      $name = $parts [0];
    }

    $count = count ($parm);

    pad_trace ( "get/eval", "$kind:$name $value");

    $get = include PAD_HOME . "eval/$kind.php";

    pad_trace ( "get/end", pad_make_string ( $get ) );

    return $get;

  }

?>