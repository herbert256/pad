<?php

  // rangecompare - Each record has unique stable id, label and paired plannedstart/plannedend and actualstart/actualend. Either complete pair can be absent and is labelled missing. Strict explicit-offset instants share one axis; equal instants are visible as 1px markers. Maximum 500 rows. A complete textual list accompanies the SVG and gives exact dates in timezone. Caller order is preserved. Does not infer progress.
  //
  // ## Example
  //
  // The paired PHP page supplies the application data:
  //
  // ```php
  // <?php
  // $rangeRecords=[['id'=>'a','label'=>'Design','plannedstart'=>'2026-11-01T00:00:00Z','plannedend'=>'2026-11-03T00:00:00Z','actualstart'=>'2026-11-01T00:00:00Z','actualend'=>'2026-11-04T00:00:00Z'],['id'=>'b','label'=>'Review','plannedstart'=>'2026-11-04T00:00:00Z','plannedend'=>'2026-11-05T00:00:00Z']];
  // ```
  //
  // The PAD template renders it:
  //
  // ```pad
  // {timeline:rangecompare data='rangeRecords', timezone='UTC'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/timeline-rangecompare.php`.

include_once PAD.'subs/timelines/_lib/rangecompare.php';try{return padTimelineRangeCompare((array)$padTimelineRows,(string)padTagParm('timezone','UTC'),(string)padTagParm('title','Planned and actual'));}catch(Exception $e){padError($e->getMessage());return '';}
