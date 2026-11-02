<?php

  // Asked with ?back, the page sends the visitor back to itself: padRedirect() with no
  // page goes to the name the page was asked by, in the clean form.

  if ( isset ( $back ) )
    padRedirect ();

  // Asked with ?paged, the page shows a list in pages of two with its {pad:pager} (pagerroute).

  $pagedList = isset ( $paged );

  // Asked with ?crumbs, it shows its {widget:breadcrumb/} (breadcrumbroute).

  $crumbed = isset ( $crumbs );

?>
