<?php

  // vertical - Events in chronological order, with dates beside their cards.
  // Spans show both dates. width= defaults to 600; height follows the content.
  return padTimelineColumn ( $padTimelineEvents, $padTimelineGroups, $padTimelineTitle, max ( 200, (int) padTagParm ( 'width', 600 ) ) );

?>
