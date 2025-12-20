<?php

  function pqTypes () {

    return array_diff ( scandir ( PAD . 'sequence/types' ), [ '.', '..' ] ) ;

  }

  function pqActions () {

    $array = array_diff ( scandir ( PAD . 'sequence/actions/types' ), [ '.', '..' ] ) ;

    foreach ( $array as &$str )
      $str = str_replace ( '.php', '', $str );

    return $array;

  }

?>