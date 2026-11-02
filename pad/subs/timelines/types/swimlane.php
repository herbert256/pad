<?php

  // swimlane - Named lanes sharing one time axis, with bars for spans and diamonds for milestones.
  // lane= names the lane field, else color= supplies the lanes. milestone= names a boolean field.
  // Every event has its own row to keep overlapping spans and labels readable.
  return padTimelineSwimlane ( $padTimelineEvents, $padTimelineRows, $padTimelineGroups, $padTimelineTitle,
                              $padTimelineLane, $padTimelineMilestone, max ( 360, (int) padTagParm ( 'width', 900 ) ) );

?>
