<?php

  // A posting form of any size gets its token - an admin list of a few thousand rows with a
  // checkbox each, a megabyte inside one form. The forms were found with one regular
  // expression over the form's content, which ran out of PCRE's backtrack limit from about
  // a megabyte on: the answer was NULL, and the page went out empty.

  $padCsrf = TRUE;

  $megabyte = str_repeat ( '<label><input type="checkbox" name="row[]" value="1"> a row</label>', 18000 );

?>
