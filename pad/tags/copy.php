<?php

  // {copy 'npm install pad'/} - the copy widget, subs/widgets/types/copy.php, as a built-in tag:
  // PHP has a copy() function, which a bare name reaches before any widget (lib/type.php),
  // so {copy} stays a tag that runs the widget - the same one {widget:copy} and {widget
  // 'copy'} write.

  $padWidgetKind = 'copy';

  return include PAD . 'subs/widgets/widget.php';

?>
