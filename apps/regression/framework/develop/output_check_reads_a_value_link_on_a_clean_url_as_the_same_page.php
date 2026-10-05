<?php

  // On a page reached by a clean URL, ?sort=price keeps the path and is a value of the page
  // the path names, as the router reads it; a bare ?name still names a page. The link check
  // took sort for the page and called the link broken. Without a path both are pages.

  $cleanKeep = $GLOBALS ['padRoutePath'];

  $GLOBALS ['padRoutePath'] = 'develop/output_check_reads_a_value_link_on_a_clean_url_as_the_same_page';

  $cleanValue = padOutputCheckLink ( '?sort=price&x=1' ) ?: 'fine';
  $cleanBare  = padOutputCheckLink ( '?no/such/page'   ) ?: 'fine';

  $GLOBALS ['padRoutePath'] = '';

  $plainValue = padOutputCheckLink ( '?sort=price' ) ?: 'fine';

  $GLOBALS ['padRoutePath'] = $cleanKeep;

?>
