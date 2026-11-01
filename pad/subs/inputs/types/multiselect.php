<?php

  // multiselect - a few choices of a list: a native select that chooses many, posting every
  // choice made as the list name[] - {multiselect 'interests'} posts interests[], the list
  // $_POST ['interests'] - and where the script shows the choices made as chips, each with a
  // button that takes it out, beside a filter box over the list of the choices: the WAI-ARIA
  // listbox that chooses many, walked with the arrow keys, Space or Enter choosing or letting
  // go, Escape closing it, Backspace in the empty filter taking the last chip out. The select
  // stays what the form posts. The choices are options=, a list, or the rows of the store
  // data= names - field= the field of a row they are read from, its first unless given - or
  // the lines between the tags, as a combobox's; value= the choices made before a post, a
  // list or a comma text. After a post it shows the list posted - nothing chosen when the
  // form came back without the field, as the browser posts a select with no choice - and
  // a posted value that is no choice is not shown, nor added: the select posts only its
  // options, and rules='in:...' refuses it. The rules are those of the list, interests[],
  // as padValidate names a list; the messages say Interests.

  $padInputField   = (string) padInputTake ( 'field', '' );
  $padInputChoices = padInputList ( 'options', $padInputField );

  if ( ! $padInputChoices and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " has no choices - options='a, b', data= or a line each between its tags" );

  // The name it posts under ends in [] (lib/input.php padInputPosts): from here on the field
  // is the list - its value, its rules, its message - and its id the name without them.

  $padInputNow ['name'] = padInputPosts ( 'multiselect', padInputName () );

  // placeholder= is the filter box's, which only the script writes - no attribute of a select.

  $padInputFilter = padInputTake ( 'placeholder', 'Filter' );
  $padInputChosen = padInputValues ( [] );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputChoices, $padInputChosen, $padInputFilter ) {

    // A select of many shows four rows unless told: as many as there are choices here, up to
    // eight - a size= given is the template's.

    $size    = padInputOwn ( $rest, [ 'size' => min ( max ( count ( $padInputChoices ), 2 ), 8 ) ] );
    $options = '';

    foreach ( $padInputChoices as $one )
      $options .= '<option value="' . padInputEscape ( $one ) . '"' . ( in_array ( $one, $padInputChosen, TRUE ) ? ' selected' : '' ) . '>'
                . padInputEscape ( $one ) . '</option>';

    return padInputFrame ( 'multiselect', $worded,
             $label
           . "<select multiple $head$size$rest>$options</select>"
           . '<noscript><p class="pi-noscript">Hold Ctrl - or &#8984; on a Mac - to choose more than one.</p></noscript>'
           . '<span class="pi-hide pi-ms-said" aria-live="polite"></span>',
             $error, [ 'filter' => is_scalar ( $padInputFilter ) ? (string) $padInputFilter : '' ] );

  } );

?>
