<?php

  if ( file_exists ( APP . "tags/$padTag[$pad].php" ) or file_exists ( APP . "tags/$padTag[$pad].html" ) )
    $padTag_go = APP;
  else
    $padTag_go = PAD;

  $padTag_go .= "tags/". $padTag[$pad];

  $padTag_php = '';

  if ( file_exists("$padTag_go.html") )
    $padTagContent .= pGet_html ("$padTag_go.html");

  if ( file_exists("$padTag_go.php") ) {

    ob_start();

    $padTag_php = include "$padTag_go.php";

    if ( $padTag_php === 1 )
      $padTag_php = '' ;

    $padTagContent .= ob_get_clean() ;

  }

  if ( is_array($padTag_php) or is_object($padTag_php) or is_resource($padTag_php) )
    return $padTag_php;

  if ( $padTag_php !== TRUE and $padTag_php !== FALSE and $padTag_php !== NULL ) {
    if ( $padTag_php or $padTagContent )
      $padTag_php = $padTagContent . $padTag_php;
    $padTagContent = '';
  }

  return $padTag_php;

?>