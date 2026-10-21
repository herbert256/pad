<?php

  // Handles the reverse option: turns the tag's data set back to front. A list stays a list,
  // numbered from 0; rows keyed by number keep their keys, as keyed by name they always did -
  // a set keyed by id came back keyed 0, 1, 2 and {&key} named no row.

  $padData [$pad] = array_reverse ( $padData [$pad], ! array_is_list ( $padData [$pad] ) );

?>
