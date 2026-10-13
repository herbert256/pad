<?php

  // A heading left unclosed - <h3>Orders<h3> - on a page of more than a megabyte costs that
  // heading, not the page: its scan ran on to the end of the page, gave up past PCRE's
  // limit, and the page came out empty or the request ended.

  $rows = str_repeat ( '<p>lorem ipsum</p>', 60000 );

?>
