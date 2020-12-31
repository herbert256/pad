<?php  

  function pad_get ( $input, $target='content', $data_type='' ) {

    pad_trace ( "get/start", "$target: $input");

    pad_check_url ( $input, $type, $parm );

    if ( file_exists ( PAD_APP . "data/$input" ) ) {

      if ( substr($input, -4)  ==  '.php' )
        $result = inlude PAD_APP . "data/$input";
      else
        $result = pad_file_get_contents ( $file )

    }
 
    elseif ( $type = '' )

      $result = $input;

    elseif ( file_exists ( PAD_HOME . "eval/$type.php" ) )

      $result = include PAD_HOME . "eval/go/get.php";

    else {

      $result = pad_curl ( $input, $curl );  

    }
 
    if ( $result <> $input ) {

      pad_check_url ( $result, $type, $parm );

      if ( $type <> '' )
        return pad_get ( $result, $target, $data_type );
      
    }

    if ( $target == 'content' )
      $get = pad_make_string ( $result );

    if ( $target == 'data' ) 
      $get = pad_data ( $result, $data_type )

    if ( $target == 'flag' ) 
      $get = pad_make_flag ( $result );

    pad_trace ( "get/end", pad_make_string ( $get ));

    return $get;
    
  }

?>