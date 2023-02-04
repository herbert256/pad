<?php

  $padLinkApp  = padTagParm ( 'app',  $app );
  $padLinkPage = padTagParm ( 'page', 'index' );
  $padLinkText = padTagParm ( 'text', $padContent );

  return "<a href=\"$padHost$padScript?app=$padLinkApp&page=$padLinkPage\">$padLinkText</a>";

?>