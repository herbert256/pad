<?php


  function padValid ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( ! preg_match('/^[a-zA-Z][:#a-zA-Z0-9_]*$/',$name) ) return FALSE;

    return TRUE;  

  }


  function padValidFile ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, pad)     ) return TRUE;
    if ( str_starts_with($file, padApp)  ) return TRUE;
    if ( str_starts_with($file, padData) ) return TRUE;

    return FALSE;

  }

  
  function padValidPage ($page) {

    if ( ! preg_match ( '/^[a-zA-Z][a-zA-Z0-9_\/]*$/', $page ) )  return FALSE;
    if ( trim($page) == '' )                                      return FALSE;
    if ( strpos($page, '//') !== FALSE)                           return FALSE;
    if ( substr($page, -1) == '/')                                return FALSE;
    if ( strpos($page, '/_') !== FALSE)                           return FALSE;
    if ( substr($page, 0, 1) == '_')                              return FALSE;
 
    return TRUE;  

  }

  
  function padValidVar ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( substr($name, 0, 3) == 'pad' )                      return FALSE;
    if ( ! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$name) )  return FALSE;

    return TRUE;  

  }

 
  function padValidVar2 ($name) {

    if ( trim ( $name ) == '' ) 
      return FALSE;

    if ( str_contains ( $name, '@' ) )
      return padAtCheck ( $name );

    if ( ! preg_match ( '/^[a-zA-Z0-9\-\$][a-zA-Z0-9:#_]*$/',$name ) )
      return FALSE;

    return TRUE;  

  }


  function padAtValid ( $part ) {

    if ( trim($part) == '' )                                       return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z0-9_][a-zA-Z0-9_]*$/', $part ) ) return FALSE;

    return TRUE;  

  }


  function padValidType ($name) {

    if ( trim($name) == '' ) 
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z][a-zA-Z]*$/',$name) )
      return FALSE;

    return TRUE;  

  }
  
  
  function padValidTag ($name) {

    if ( trim($name) == '' ) 
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z][a-zA-Z0-9:_]*$/',$name) )
      return FALSE;

    return TRUE;  

  }

  
?>