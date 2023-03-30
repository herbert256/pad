<?php

  $padLinkApp  = padTagParm ( 'app',  $padApp );
  $padLinkPage = padTagParm ( 'page', 'index' );
  $padLinkText = padTagParm ( 'text', $padContent );

  return "<a href=\"$padHost$padScript?padApp=$padLinkApp&padPage=$padLinkPage\">$padLinkText</a>";

?>