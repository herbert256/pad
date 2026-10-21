<?php

  // Fixture for a_request_value_never_decides_what_a_tag_is: a constant, pipe functions, two
  // sequences, a _data file, an application pipe function and PHP functions written as tags
  // and pipes - and $ucwords, a variable the page sets itself, which stays the field it is, as
  // $date does, given a default that keeps the request's value when one came. Fetched bare it
  // answers what the template means by each name.

  $ucwords = 'mine';
  $who     = $who ?? 'nobody';
  $date    = $date ?? '2000-01-01';

?>
