<?php

  // A database tag's SQL is refused only for a value spliced into it: tested against every
  // value the request had printed anywhere, a page showing its sort order above the query
  // that sorts by it, or a guestbook entry reading from staff above a count of the staff,
  // answered a 500 for every visitor.

  $sort  = 'name desc';
  $entry = 'from staff';

?>
