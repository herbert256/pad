<?php

  include '/pad/config/config.php';

  if ( $padSyntax ) 
    include "/pad/config/syntax.php";
  
  if ( $padInfo ) 
    include "/pad/config/info/$padInfo.php";

  if ( file_exists ( '/app/_config/config.php' ) ) 
    include '/app/_config/config.php';

  include "/pad/config/output/$padOutputType.php";
  
  if ( file_exists ( '/app/_config/config.php' ) ) 
    include '/app/_config/config.php';
  
  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) ) 
    include_once '/pad/inits/configSet.php';

?>