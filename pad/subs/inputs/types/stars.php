<?php

  // stars - a rating to give: a row of max= stars, 5 unless given, each a native radio button
  // of the value 1 to max, in a fieldset whose legend is the label. The arrow keys move along
  // them as along any radio group, and the style alone fills the stars up to the one chosen
  // or pointed at. The script shows a button that takes the rating back, unless the field is
  // required - a radio group, once chosen, cannot be emptied otherwise. The first star has the
  // field's id, the others the id with their value, as the boxes of {input:field}'s group have.

  $padInputMax = padInputNumber ( 'max', 5, 2, 10, TRUE );

  // A rating that must be given has no button to take it back: required, as an attribute or
  // as a rule.

  $padInputClear = ( ! padAttrsTrue ( padInputPeek ( 'required', FALSE ) )
                   and ! in_array ( 'required', array_map ( fn ( $rule ) => strtolower ( trim ( $rule ) ), padValidateSplit ( (string) padInputPeek ( 'rules', '' ) ) ), TRUE ) );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputMax, $padInputClear ) {

    global $padInputNow;

    $name  = padInputEscape ( padInputName () );
    $value = $padInputNow ['value'];
    $text  = preg_match ( '/^<label[^>]*>(.*)<\/label>$/s', $label, $own ) ? $own [1] : padInputEscape ( $worded );
    $stars = '';

    // The label= is the legend: a radio group is named by its fieldset, not by a <label> of
    // one of its radios, and the aria-label a field without a label gets is the legend too.

    $rest  = preg_replace ( '/\s+aria-label="[^"]*"/', '', $rest );
    $class = padInputOwn ( $rest, [ 'class' => 'pi-star-input' ] );

    for ( $i = 1; $i <= $padInputMax; $i++ ) {
      $one    = ( $i == 1 ) ? $id : "$id-$i";
      $stars .= "<input type=\"radio\"$class name=\"$name\" id=\"" . padInputEscape ( $one ) . "\" value=\"$i\""
              . ( (string) $i === $value ? ' checked' : '' ) . "$rest>"
              . '<label class="pi-star" for="' . padInputEscape ( $one ) . "\"><span class=\"pi-hide\">$i " . ( $i == 1 ? 'star' : 'stars' )
              . " of $padInputMax</span></label>";
    }

    return padInputFrame ( 'stars', $worded,
             "<legend>$text</legend><span class=\"pi-stars\">$stars</span>"
           . '<button type="button" class="pi-button pi-stars-clear" hidden>Clear</button>',
             $error, [ 'max' => $padInputMax, 'clear' => $padInputClear ], 'fieldset' );

  } );

?>
