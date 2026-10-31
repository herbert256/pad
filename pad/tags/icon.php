<?php

  // {icon 'arrow-right'/} - an icon from PAD's own set, inline SVG drawn by the icon subsystem in
  // icons/: an icon is a file of icons/types/, the same icon as the icon: type writes it,
  // {icon:arrow-right/}. Lines in the colour of the text around it (stroke="currentColor"), on
  // a 24 by 24 grid. size= is the width and height in pixels (default 20), stroke= the line
  // width (default 2). Without label= the icon is decoration, hidden from a screen reader
  // beside the text it goes with; with label= it is an image of that name - an icon alone in
  // a button or a link.
  //
  //   {icon 'arrow-right', size=20, label='Next'/}
  //   <button>{icon 'trash'/} Delete</button>
  //
  // padIconNames () lists the names; an unknown name is reported with the names close to it.

  $padIconName = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'icons/icon.php';

?>
