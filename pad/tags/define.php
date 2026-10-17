<?php

  // {define 'card'}...{/define}: a custom tag written in the template - {card title='x'}
  // from here on renders the body as _tags/card.pad would: {#title}, {parms}, @content@,
  // slots. The body is kept as written and renders at each use - tags/_lib/macro.php, the
  // code it shares with {macro}, read here.

  include_once PAD . 'tags/_lib/macro.php';

  $padDefineName = padMacroName ( 'define' );

  if ( $padDefineName !== '' )
    $padDefineStore [$padDefineName] = $padSource [$pad];

  $padContent = '';

  return NULL;

?>
