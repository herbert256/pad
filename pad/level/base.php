<?php

  // Settles the level's template text once the tag has run: nothing at all when the tag
  // returned null, the @else@ branch collected in $padFalse when it matched nothing, and
  // otherwise $padContent - the text between the tags as the handler left it.

  if     ( $padNull [$pad] ) $padBase [$pad] = '';
  elseif ( $padElse [$pad] ) $padBase [$pad] = $padFalse;
  else                       $padBase [$pad] = $padContent;

?>