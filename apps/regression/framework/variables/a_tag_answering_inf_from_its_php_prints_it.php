<?php

  // INF is a value in the answer of a tag's PHP as it is in {echo} and a field
  // (a_tag_answering_inf_prints_it_as_the_field_does): the call that runs the PHP turned INF
  // into NULL and the level took it for no answer, so an application tag, an {at} over a
  // value INF and a pipe function answering INF printed nothing - where a tag answering -INF
  // printed -INF.

  $s = [ 'm' => INF, 'n' => 1 ];

?>
