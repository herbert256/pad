<?php

  // toggle - a switch, on or off: a native checkbox with role="switch", drawn as a switch by
  // its style alone, so it needs no script. It posts a value either way - on= when it is on,
  // 1 unless given, and off= when it is off, 0 unless given, from a hidden field before it
  // that the checkbox's own value replaces when it is ticked - so the page's PHP finds the
  // field in every post, and accepted holds the switch to on when on= is 1, on, yes or true -
  // for another on= the rule is in:, rules='in:dark'. off='' posts nothing when off,
  // as a checkbox does. checked turns it on before a post; after one it is as it was posted.

  // value= is the switch's own value, as it is a checkbox's: on= by another name.

  $padInputOn  = (string) padInputTake ( 'on', padInputTake ( 'value', '1' ) );
  $padInputOff = padInputTake ( 'off', '0' );
  $padInputOff = ( $padInputOff === FALSE or $padInputOff === NULL ) ? '' : (string) $padInputOff;

  if ( $padInputOn === '' and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " posts nothing when it is on - on='1'" );

  // accepted holds the switch to on when on= is one of the words accepted takes: a switch
  // that posts on='dark' never passed it, turned on or off, and its form could not be sent.

  elseif ( $GLOBALS ['padCheckSyntax'] and ! in_array ( strtolower ( $padInputOn ), [ '1', 'on', 'yes', 'true' ], TRUE )
           and in_array ( 'accepted', array_map ( fn ( $rule ) => padValidateParts ( $rule ) [0], padValidateSplit ( (string) padInputPeek ( 'rules', '' ) ) ), TRUE ) )
    padError ( "the " . padInputWhat () . " posts '" . padMakeSafe ( $padInputOn, 20 ) . "' when it is on - accepted takes 1, on, yes or true; rules='in:"
               . padMakeSafe ( $padInputOn, 20 ) . "' holds it to on" );

  $padInputSource = padFormSource ();

  if ( $padInputSource !== NULL ) {
    [ $padInputFound, $padInputPosted ] = padFormFind ( $padInputSource, padInputName () );
    $padInputChecked = ( $padInputFound and $padInputPosted === $padInputOn );
    padInputTake ( 'checked' );
  } else
    $padInputChecked = padAttrsTrue ( padInputTake ( 'checked', FALSE ) );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputOn, $padInputOff, $padInputChecked ) {

    $name = padInputEscape ( padInputName () );
    $off  = ( $padInputOff !== '' ) ? "<input type=\"hidden\" name=\"$name\" value=\"" . padInputEscape ( $padInputOff ) . '">' : '';

    return padInputFrame ( 'toggle', $worded,
             '<div class="pi-row pi-toggle-row">' . $label . $off
           . "<input type=\"checkbox\" role=\"switch\" $head value=\"" . padInputEscape ( $padInputOn ) . '"' . ( $padInputChecked ? ' checked' : '' ) . "$rest>"
           . '</div>',
             $error );

  } );

?>
