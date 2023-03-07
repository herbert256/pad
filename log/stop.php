<?php

  if ( $padLogShort )
    $padLogRsl = padLogShort ( $padLogRsl );

  padFilePutContents ( "log/$app/$page/" . padFileTimestamp() . '.json', $padLogRsl );  

  
  function padLogShort ($array) {

    $array = padLogRoot    ( $array );
    $array = padLogEval    ( $array );
    $array = padLogFields  ( $array );
    $array = padLogTags    ( $array );
    $array = padLogOccur   ( $array );
    $array = padArrayClean ( $array );

    return $array;

  }


  function padLogRoot ($array) {

    foreach ( $array [0] as $key => $value )
      $return [$key] = $value;

    return $return;
   
  }


  function padLogEval ($array) {

    if ( ! is_array($array) )
      return $array;

    foreach ( $array as $key => $value )

      if ( $key == 'eval' and is_array($value) ) {

        foreach ( $value as $key2 => $value2 )
          $array ["Eval: $key2"] = $value2;

        unset ( $array [$key] );

      } elseif ( is_array ( $value ) )

        $array [$key] = padLogEval ($value);

    return $array;

  }


  function padLogFields ($array) {

    if ( ! is_array($array) )
      return $array;

    foreach ( $array as $key => $value )

      if ( $key == 'fields' and is_array($value) ) {
        
        foreach ( $value as $key2 => $value2 )
          $array ["Var: $key2"] = $value2;
        
        unset ( $array [$key] );

      } elseif ( is_array ( $value ) )

        $array [$key] = padLogFields ($value);

    return $array;

  }


  function padLogOccur ($array) {

    if ( ! is_array($array) )
      return $array;

    foreach ( $array as $key => $value )

      if ( $key == 'occurrences' and is_array($value) ) {

        if ( count ( $value ) == 1 ) {

          $first = array_key_first ( $value );

          foreach ( $value [$first] as $key2 => $value2 )
            $array [$key2] = padLogOccur ($value2);

        } else {

          foreach ( $value as $key2 => $value2 )
            $array [$key2] = padLogOccur ($value2);

        }
  
        unset ( $array [$key] );
      
      } elseif ( is_array ( $value ) )

        $array [$key] = padLogOccur ($value);

    return $array;

  }


  function padLogTags ($array) {

    if ( ! is_array($array) )
      return $array;

    foreach ( $array as $key => $value )

      if ( $key == 'tags' and is_array($value) and isset($value['tag']) ) {

        foreach ( $value as $key2 => $value2 ) {

          $index = 'Tag: ' . $value2 ['tag'];
          if ( isset ( $array [$index] ) )
            $index .= '-' .  $value2 ['cnt'];

          if ( $value2 ['base'] and $value2 ['base'] == $value2 ['true'] ) 
            $value2 ['base'] = '*TRUE*';
             
          if ( $value2 ['base'] and $value2 ['base'] == $value2 ['false'] ) 
            $value2 ['base'] = '*FALSE*';

          if ( $value2 ['name'] == $value2 ['tag'] ) 
            unset ( $value2 ['name'] );

          if ( $value2 ['walk'] == 'start' ) 
            unset ( $value2 ['walk'] );

          if ( $value2 ['p-type'] == 'none' ) 
            unset ( $value2 ['p-type'] );

          foreach ( $value2 ['opt'] as $key3 => $value3 )
            $value2 ["Opt: $key3"] = $value3;
          foreach ( $value2 ['prm'] as $key3 => $value3 )
            $value2 ["Prm: $key3"] = $value3;
          foreach ( $value2 ['set'] as $key3 => $value3 )
            $value2 ["Set: $key3"] = $value3;

          unset ( $value2 ['opt'] );
          unset ( $value2 ['prm'] );
          unset ( $value2 ['set'] );
          unset ( $value2 ['tag'] );
          unset ( $value2 ['cnt'] );
          unset ( $value2 ['pad'] );

          $array [$index] = padLogTags ($value2);

        }
        
        unset ( $array [$key] );

      } elseif ( is_array ( $value ) )

        $array [$key] = padLogTags ($value);

    return $array;

  }

?>