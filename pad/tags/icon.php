<?php

  // {icon 'arrow-right'} - an icon from PAD's own set, inline SVG by lib/icon.php: lines in
  // the colour of the text around it (stroke="currentColor"), on a 24 by 24 grid. size= is
  // the width and height in pixels (default 20), stroke= the line width (default 2). Without
  // label= the icon is decoration, hidden from a screen reader beside the text it goes
  // with; with label= it is an image of that name - an icon alone in a button or a link.
  //
  //   {icon 'arrow-right', size=20, label='Next'}
  //   <button>{icon 'trash'} Delete</button>
  //
  // padIconNames () lists the names; an unknown name is reported with the names close to it.

  $padIconName = strtolower ( trim ( (string) $padParm ) );

  if ( $padIconName === '' ) {
    if ( $padCheckSyntax )
      padError ( "the icon has no name - {icon 'arrow-right'}" );
    return '';
  }

  $padIconSvg = padIcon ( $padIconName, (int) padTagParm ( 'size', 20 ), (string) padTagParm ( 'label' ), padTagParm ( 'stroke', 2 ) );

  if ( $padIconSvg === '' and $padCheckSyntax ) {
    $padIconLike = padIconSimilar ( $padIconName );
    padError ( "there is no icon named '" . padMakeSafe ( $padIconName, 30 ) . "'"
             . ( $padIconLike ? ' - did you mean ' . implode ( ', ', $padIconLike ) . '?' : ' - padIconNames () lists the ' . count ( PAD_ICONS ) . ' icons' ) );
  }

  return $padIconSvg;

?>
