<?php


  function refLink () {

    global $padPage;

    if ($padPage == 'index' or $padPage == 'reference/index' )
      return FALSE;

    $types = padData ('references.json');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }


  function parts ( ) {
 
    global $padPage;
    
    if ( $padPage == 'index')

      return [];

    else {

      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = 'index';    

    } 

    if ( $padPage == 'reference/index') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } 

    if ( refLink() ) {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference';

    } 

    $work = str_replace ( '/index', '', $padPage ); 
    $work = padExplode ( str_replace ( '/index', '', $padPage ), '/' );
    $link = '';

    foreach ( $work as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
      $parts [$key] ['link'] = ( $key == array_key_last ($work) ) ? '' : $link;
   
    }

    return $parts;

  }


?>