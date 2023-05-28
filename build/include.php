<?php

  include 'call.php';

  $padIncludeHtml = padGetHtml ( padApp . "$padPage.html" );

  if ( count ( $padBuildArray ) ) {
    if ( strpos ( $padIncludeHtml, '@start@') === FALSE )  $padIncludeHtml = "@start@$padIncludeHtml";
    if ( strpos ( $padIncludeHtml, '@end@')   === FALSE )  $padIncludeHtml = "$padIncludeHtml@end@";
  }

  $padBase [$pad] .= $padIncludeHtml;

?>