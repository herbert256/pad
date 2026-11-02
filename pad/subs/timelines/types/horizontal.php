<?php

  // horizontal - Events and spans on a time axis, as accessible inline SVG.
  // date= names a moment; from=/to= name a span; label= and text= name its text.
  // color= groups the events. More than 150 events use the vertical layout.
  if ( count ( $padTimelineEvents ) > 150 )
    return padTimelineColumn ( $padTimelineEvents, $padTimelineGroups, $padTimelineTitle, max ( 200, (int) padTagParm ( 'width', 600 ) ) );
  return padTimeline ( $padTimelineEvents, $padTimelineGroups, $padTimelineTitle,
                       max ( 200, (int) padTagParm ( 'width', 720 ) ), (int) padTagParm ( 'height', 0 ) );

?>
