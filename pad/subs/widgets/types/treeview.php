<?php

  // {widget:treeview data='nodes'/} - A hierarchical navigation list with expandable branches and keyboard navigation.
  //

include_once PAD.'subs/widgets/_lib/treeview.php';
return padTreeview(padWidgetRows('treeview')??[],padWidgetText('label','Navigation'));
?>
