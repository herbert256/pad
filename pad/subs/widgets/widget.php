<?php

  // A widget uses {widget:tabs}...{/widget:tabs} or another registered kind.
  // The caller sets $padWidgetKind; $padParm is the widget's own first parameter,
  // such as the name in {widget:modal 'terms'}.
  //
  // A widget is a file of subs/widgets/types/, and the files there are all the widgets there are
  // (padWidgetTypes, lib/widget.php). The file is the widget's tag code, run on every walk
  // the widget asks for as a built-in tag's code is - most render their content first and
  // build their markup on the end walk. The functions of a widget stand in subs/widgets/_lib/
  // <widget>.php, included the first time it runs, as types/pad.php includes a built-in
  // tag's tags/_lib/ file; its style and script stand in www/assets/widgets/<widget>.css and
  // .js, written once per page (lib/widget.php: padWidgetStyle, padWidgetScript).

  include_once PAD . 'subs/widgets/_lib/widget.php';

  if ( ! padWidgetType ( $padWidgetKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no widget named '" . padMakeSafe ( $padWidgetKind, 20 ) . "' - " . implode ( ', ', padWidgetTypes () ) );
    return '';
  }

  // The widget used goes on the xref record, which the reference application shows: the
  // pages that use each widget - once, on its first walk.

  if ( ( $padInfoXref ?? FALSE ) and $padWalk [$pad] == 'start' )
    padInfoXref ( 'widget', $padWidgetKind );

  if ( padFileExists ( PAD . "subs/widgets/_lib/$padWidgetKind.php" ) )
    include_once PAD . "subs/widgets/_lib/$padWidgetKind.php";

  return include PAD . "subs/widgets/types/$padWidgetKind.php";

?>
