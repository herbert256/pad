<?php

  // Type handler for a widget: {widget:tabs} ... {/widget:tabs}, or a bare tag naming a file
  // of widgets/types/ when nothing else has the name, {tabs} ... {/tabs}. The widget is the
  // tag's name, and it runs as {widget 'tabs'} runs it, by widgets/widget.php - itself run as
  // a built-in tag's PHP half is run, types/_go/tag.php, on every walk the widget asks for.

  $padWidgetKind = $padTag [$pad];

  $padTagGo = PAD . 'widgets/widget';

  return include PAD . 'types/_go/tag.php';

?>
