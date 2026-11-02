<?php

  // roadmap - Events grouped into months, quarters or years, with milestone badges.
  // period= is month, quarter (default) or year. lane= groups cards inside a period.
  // milestone= names a boolean field; by default point events are milestones.
  $padTimelinePeriod = padTagParm ( 'period', 'quarter' );
  if ( ! in_array ( $padTimelinePeriod, [ 'month', 'quarter', 'year' ], TRUE ) ) {
    if ( $padCheckSyntax )
      padError ( 'the roadmap period is month, quarter or year' );
    return FALSE;
  }
  return padTimelineRoadmap ( $padTimelineEvents, $padTimelineRows, $padTimelineTitle, $padTimelinePeriod, $padTimelineLane, $padTimelineMilestone );

?>
