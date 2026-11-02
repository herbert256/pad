<?php

  // The shared body of icon:kind tags, such as {icon:arrow-right}. The caller sets
  // $padIconName; registered kinds are files in subs/icons/types/.
  //
  // size= is the width and height in pixels (default 20), stroke= the line width (default 2).
  // Without label= the icon is decoration, hidden from a screen reader beside the text it
  // goes with; with label= it is an image of that name - an icon alone in a button or a link.
  // No name, or a name that is no icon, is an error under the strict check, the names close
  // to it named.

  include_once PAD . 'subs/icons/_lib/icon.php';

  if ( $padIconName === '' ) {
    if ( $padCheckSyntax )
      padError ( "the icon has no name - {icon:arrow-right/}" );
    return '';
  }

  $padIconSvg = padIcon ( $padIconName, (int) padWidgetNumber ( 'icon', 'size', 20 ), padParmText ( padTagParm ( 'label' ) ), padWidgetNumber ( 'icon', 'stroke', 2 ) );

  if ( $padIconSvg === '' and $padCheckSyntax ) {
    $padIconLike = padIconSimilar ( $padIconName );
    padError ( "there is no icon named '" . padMakeSafe ( $padIconName, 30 ) . "'"
             . ( $padIconLike ? ' - did you mean ' . implode ( ', ', $padIconLike ) . '?' : ' - padIconNames () lists the ' . count ( padIconNames () ) . ' icons' ) );
  }

  // The icon drawn goes on the xref record, which the reference application shows: the pages
  // that draw each icon.

  if ( $padIconSvg !== '' and ( $padInfoXref ?? FALSE ) )
    padInfoXref ( 'icon', $padIconName );

  return $padIconSvg;

?>
