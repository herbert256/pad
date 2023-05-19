<?php


  function refLink () {

    global $padPage;

    if ( $padPage == 'reference/reference' )
      return TRUE;

    if ($padPage == 'index' or $padPage == 'reference/index' )
      return FALSE;

    $types = padData ('references');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }


  function parts ( ) {
 
    global $padPage;

    $source  = ( $padPage == 'reference/reference' ) ? $GLOBALS['reference'] : $padPage;
    $refLink = refLink();
    
    if ( $padPage == 'index')
      return [];

    $parts ['home'] ['part'] = 'home';
    $parts ['home'] ['link'] = 'index';    

    if ( $padPage == 'reference/index') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } 

    if ( $refLink ) {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference/index'; 

    }  

    $work = str_replace ( '/index', '', $source ); 
    $work = padExplode ( str_replace ( '/index', '', $source ), '/' );
    $link = '';

    foreach ( $work as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
 
      if ( $refLink and $key <> array_key_last ($work))
        $parts [$key] ['link'] = "reference/reference&reference=$link";
      else
        $parts [$key] ['link'] = ( $key == array_key_last ($work) ) ? '' : $link;
   
    }

    return $parts;

  }


?>