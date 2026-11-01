<?php

  // segmented - one of a few choices shown side by side as one bar, the chosen one filled: a
  // native radio group in a fieldset whose legend is the label, drawn as segments by its
  // style alone, so it needs no script - Tab comes to the group once, the arrow keys move
  // along it and choose, as in any radio group. The choices are options=, a list, or the rows
  // of the store data= names - field= the field of a row they are read from, its first unless
  // given - or the lines between the tags, as a combobox's; value= the choice made before a
  // post. After a post it shows the choice posted - none when the form came back without one,
  // and a posted value that is no choice is not shown: rules='required|in:...' holds the
  // field to the list. The first choice has the field's id, the others the id with their
  // place, size-2, as the boxes of {input}'s group have.

  $padInputField   = (string) padInputTake ( 'field', '' );
  $padInputChoices = padInputList ( 'options', $padInputField );

  if ( ! $padInputChoices and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " has no choices - options='a, b', data= or a line each between its tags" );

  // A form that came back without the field had nothing chosen in it - a radio group with no
  // choice is left out of the post, its padLists[] says it was there - so value= does not
  // come back in its place (_lib/input.php padInputChoiceValue).

  $padInputNow ['value'] = padInputChoiceValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputChoices ) {

    global $padInputNow;

    $name  = padInputEscape ( padInputName () );
    $value = $padInputNow ['value'];
    $text  = preg_match ( '/^<label[^>]*>(.*)<\/label>$/s', $label, $own ) ? $own [1] : padInputEscape ( $worded );
    $bar   = '';

    // The label= is the legend: a radio group is named by its fieldset, not by a <label> of
    // one of its radios, and the aria-label a field without a label gets is the legend too.

    $rest  = preg_replace ( '/\s+aria-label="[^"]*"/', '', $rest );
    $class = padInputOwn ( $rest, [ 'class' => 'pi-seg-input' ] );

    foreach ( $padInputChoices as $at => $one ) {
      $box  = ( $at == 0 ) ? $id : "$id-" . ( $at + 1 );
      $bar .= "<input type=\"radio\"$class name=\"$name\" id=\"" . padInputEscape ( $box ) . '" value="' . padInputEscape ( $one ) . '"'
            . ( $one === $value ? ' checked' : '' ) . "$rest>"
            . '<label class="pi-seg" for="' . padInputEscape ( $box ) . '">' . padInputEscape ( $one ) . '</label>';
    }

    return padInputFrame ( 'segmented', $worded,
             "<legend>$text</legend><span class=\"pi-segs\">$bar</span>"
           . padInputSentField ( padInputName (), isset ( $padInputNow ['attrs'] ['disabled'] ) ),
             $error, [], 'fieldset' );

  } );

?>
