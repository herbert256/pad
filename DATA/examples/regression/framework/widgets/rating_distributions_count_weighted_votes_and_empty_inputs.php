<?php

  $source = "{widget:ratingdistribution score='stars', count='votes'}stars,votes\n5,3\n1,1{/widget:ratingdistribution}{widget:ratingdistribution}[]{/widget:ratingdistribution}";
  $html = padUnprotect ( padUnescape ( padCode ( $source ) ) );
  $answer = str_contains ( $html, '4 votes, average 4 / 5' ) && str_contains ( $html, '5 stars: 75%' )
         && str_contains ( $html, '0 votes, average 0 / 5' ) && substr_count ( $html, '<meter ' ) === 10
         ? 'weighted and empty distributions rendered' : 'FAIL';

?>
