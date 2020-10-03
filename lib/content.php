<?php  


  function pad_content ($input) {

    if     ( $input === NULL       )  return '';
    elseif ( is_array ( $input)    )  $data = $input;
    elseif ( is_object ( $input)   )  $data = pad_xxx_to_array ( $input );
    elseif ( is_resource ( $input) )  $data = pad_xxx_to_array ( $input );
    elseif ( $input === FALSE      )  return '';
    elseif ( $input === TRUE       )  return '1';
    else                              $data = (string) trim($input);    

    if ( is_array ( $data) ) 
      return pad_array_to_string (' ', $data);

    if ( isset ( $GLOBALS ['pad_content_store'] [$data] ) ) 
      return $GLOBALS ['pad_content_store'] [$data];

    $stream = pad_explode ($data, '://', 2);

    if ( count ($stream) == 2 and $stream[0] == 'sql' ) {

        $work = db ( $stream [1] );

        if ( is_array($work) )
          return pad_array_to_string (' ', $work);
        else
          return $work;

    }

    if ( count ($stream) == 2 and pad_valid_name($stream[0]) ) {
  
        $curl_return = pad_curl ($data, $curl);
  
        if ( $curl_return === TRUE)
          return $curl ['data'];  
        else
          return pad_error ("Curl failed: " . $curl ['result_code'] . ' ' . $data);
  
    }

    return $data;
    
  }


  function pad_array_to_string ($glue, $arr){            

    for ($i=0; $i<count($arr); $i++) {
        if (@is_array($arr[$i])) 
            $arr[$i] = pad_array_to_string ($glue, $arr[$i]);
    }            

    return implode($glue, $arr);

  }


?>