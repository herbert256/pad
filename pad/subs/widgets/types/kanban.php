<?php
include_once PAD.'subs/widgets/_lib/kanban.php';
return padKanban(padWidgetRows('kanban')??[],padTagParm('columns',['todo'=>'To do','doing'=>'In progress','done'=>'Done']),padWidgetText('label','Board'));
?>
