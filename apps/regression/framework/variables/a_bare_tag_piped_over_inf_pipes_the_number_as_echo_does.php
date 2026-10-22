<?php

  // A bare tag answering INF meets its pipe as the number, as {echo} does (a_tag_answering_
  // inf_prints_it_as_the_field_does): {big | + 1} piped the text 'INF', "not a number".

  $big = INF;
  $neg = -INF;
  $brace = "{php:getcwd}";

?>
