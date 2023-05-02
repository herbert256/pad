<?php

  $padLinkPage = padTagParm ( 'page', 'index' );
  $padLinkText = padTagParm ( 'text', $padContent );

  return "<a href=\"$padHost$padScript?$padLinkPage\">$padLinkText</a>";

?>