<?php

  if ( strpos ( $padBuildPage, '@start@') === FALSE ) $padBuildPage = "@start@$padBuildPage";
  if ( strpos ( $padBuildPage, '@end@')   === FALSE ) $padBuildPage = "$padBuildPage@end@";

  $padBuildPage = str_replace ( '@start@', "{padBuildArray}",  $padBuildPage );
  $padBuildPage = str_replace ( '@end@',   '{/padBuildArray}', $padBuildPage );
    
?>