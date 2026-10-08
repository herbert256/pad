<?php

  // {macro 'entry', label, type='text'}...{/macro}: a template function - {entry 'E-mail'}
  // or {entry label='Name', type='email'} from here on renders the body with the
  // parameters as its fields, {$label}, the defaults filling in. The body is kept as
  // written and renders at each use - lib/macro.php.

  $padMacroName  = padMacroName ( 'macro' );
  $padMacroParms = padMacroDeclare ();

  if ( $padMacroName !== '' )
    $padMacroStore [$padMacroName] = [ 'parms' => $padMacroParms, 'source' => $padSource [$pad] ];

  $padContent = '';

  return NULL;

?>
