<?php


  function sequenceDir ( $dir )  {

    $out = [];

    foreach ( files ( $dir ) as $file ) {
      $key = str_replace( '.pad', '', str_replace( '.php', '', $file ) );
      $out [$key] = $key;
    }

    return array_values ( $out );

  }


  function types () {

    return files ( PAD . 'sequence/types' ) ;

  }


  function actions () {

    $array = files ( PAD . 'sequence/actions/types' ) ;

    foreach ( $array as &$str )
      $str = str_replace ( '.php', '', $str );

    return $array;

  }


?>