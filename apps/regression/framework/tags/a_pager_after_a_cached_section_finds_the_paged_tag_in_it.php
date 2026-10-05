<?php

  // An earlier request renders the named section and stores it; in this one the section is
  // a hit, and the paged tag inside it never runs.

  $items = [ [ 'n' => 1 ], [ 'n' => 2 ], [ 'n' => 3 ], [ 'n' => 4 ], [ 'n' => 5 ] ];

  if ( ! isset ( $warm ) ) {
    padFragmentForget ( 'fwPagedSection' );
    padCurl ( [ 'url' => $padHost . 'regression/framework/?tags/a_pager_after_a_cached_section_finds_the_paged_tag_in_it&padInclude&warm=1' ] );
  }

?>
