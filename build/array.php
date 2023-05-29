<?php

  if ( strpos ( $padBuild, '@start@') === FALSE ) $padBuild = "@start@$padBuild";
  if ( strpos ( $padBuild, '@end@')   === FALSE ) $padBuild = "$padBuild@end@";

  $padBuild = str_replace ( '@start@', "{padBuildArray}",  $padBuild );
  $padBuild = str_replace ( '@end@',   '{/padBuildArray}', $padBuild );
    
?>