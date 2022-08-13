<?php

  if ( file_exists ( APP . "tags/$pTag[$p].php" ) or file_exists ( APP . "tags/$pTag[$p].html" ) )
    $pTag_go = APP;
  else
    $pTag_go = PAD;

  $pTag_go .= "tags/". $pTag[$p];

  $pTag_php = '';

  if ( file_exists("$pTag_go.html") )
    $pTagContent .= pGet_html ("$pTag_go.html");

  if ( file_exists("$pTag_go.php") ) {

    ob_start();

    $pTag_php = include "$pTag_go.php";

    if ( $pTag_php === 1 )
      $pTag_php = '' ;

    $pTagContent .= ob_get_clean() ;

  }

  if ( is_array($pTag_php) or is_object($pTag_php) or is_resource($pTag_php) )
    return $pTag_php;

  if ( $pTag_php !== TRUE and $pTag_php !== FALSE and $pTag_php !== NULL ) {
    if ( $pTag_php or $pTagContent )
      $pTag_php = $pTagContent . $pTag_php;
    $pTagContent = '';
  }

  return $pTag_php;

?>