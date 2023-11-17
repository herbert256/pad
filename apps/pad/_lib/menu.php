<?php


  function refLink () {

    global $padPage;

    if ( str_starts_with ($padPage, 'reference' ) ) return TRUE;
    if ( $padPage == 'index'                      ) return FALSE;

    $types = padData ('references.json');

    foreach ( $types as $key => $value ) {
      if ( str_starts_with ( $padPage, $value ['ref'] . '/' ) )
        return TRUE;

    }

    return FALSE;

  }


  function parts ( ) {

    global $padPage, $manual; 

    if ( $padPage == 'index' ) {
      $parts ['home'] ['part'] = 'home';
      $parts ['home'] ['link'] = '';       
      $parts ['man']  ['part'] = 'manual';
      $parts ['man']  ['link'] = 'manual';  
      $parts ['ref']  ['part'] = 'reference';
      $parts ['ref']  ['link'] = 'reference';  
      $parts ['dev']  ['part'] = 'development';
      $parts ['dev']  ['link'] = 'development';
      return $parts;
    }

    $refLink = refLink();

    $parts ['home'] ['part'] = 'home';
    $parts ['home'] ['link'] = 'index';    

    if ( $padPage == 'manual/index') {

      if ( ! $manual ) {

        $parts ['man']  ['part'] = 'manual';
        $parts ['man']  ['link'] = '';  

        return $parts;

      }

      $parts ['man']  ['part'] = 'manual';
      $parts ['man']  ['link'] = 'manual';  

      $parts ['now']  ['part'] = $manual;
      $parts ['now']  ['link'] = '';  

      return $parts;

    } elseif ( $padPage == 'reference/index') {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = '';    

      return $parts;

    } elseif ( $refLink ) {

      $parts ['ref'] ['part'] = 'reference';
      $parts ['ref'] ['link'] = 'reference'; 

    }  

    if     ( strpos ($padPage, '/show/' )      ) $source = 'development/regression/show';
    elseif ( $padPage == 'reference/reference' ) $source = $GLOBALS['reference'];
    elseif ( $padPage == 'reference/show'      ) $source = $GLOBALS['item'];
    else                                         $source = $padPage;

    $source = str_replace ( '/index', '', $source ); 
    $source = str_replace ( '/docs', '', $source ); 
    $source = padExplode ( $source, '/' );

    $link = '';

    foreach ( $source as $key => $part ) {
          
      $link = ($link) ? "$link/$part" : $part;

      $parts [$key] ['part'] = $part;
 
      if ( $refLink and $key <> array_key_last ($source)) {

        $parts [$key] ['link'] = "reference/reference&reference=$link";

      } elseif ( $key == array_key_last ($source) ) {

        $parts [$key] ['link'] = '';

      } else {

        $parts [$key] ['link'] =  $link;

        if ( $part == 'regression')
          $parts [$key] ['link'] .= '&fromMenu=1';

      }

    }

    return $parts;

  }


?>