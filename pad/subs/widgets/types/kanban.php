<?php

  // {widget:kanban data='cards'/} - Cards grouped by status, movable with a select control or drag and drop.
  //

include_once PAD.'subs/widgets/_lib/kanban.php';
return padKanban(padWidgetRows('kanban')??[],padTagParm('columns',['todo'=>'To do','doing'=>'In progress','done'=>'Done']),padWidgetText('label','Board'));
?>
