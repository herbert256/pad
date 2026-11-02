<?php

  // {section:newsletter title='Stay in the loop', button='Subscribe'/} - a sign-up for the
  // newsletter: a heading, a line, and a form with one e-mail field and its button, posting
  // back to the page as {pad:form 'newsletter'} does - the session's token, the form's name, so the
  // page's PHP asks padPosted ( 'newsletter' ) and padValidate ( [ 'email' => 'required|email' ] )
  // as for any form; the field refills and shows the message when the post came back wrong.
  // Once the form came back right its place says thanks= ('Thanks - you are on the list.').
  // form= names the form ('newsletter'), label= the field ('E-mail address'), placeholder= the
  // example in it ('you@example.com'), note= the small print under it.
  //
  // Parts: eyebrow, title, text, note. Options: title (or the first value), eyebrow, text,
  // button, label, placeholder, form, thanks, note, level, tone (soft), align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'note' ] );

  $GLOBALS ['padSectionNow'] ['tone'] = 'soft';

  $padSecHead   = padSectionHead ();
  $padSecNote   = padSectionPart ( 'note' );
  $padSecForm   = padSectionFormName ( 'newsletter' );
  $padSecButton = padSectionOption ( 'button', 'Subscribe' );
  $padSecLabel  = padSectionOption ( 'label', 'E-mail address' );
  $padSecPlace  = padSectionOption ( 'placeholder', 'you@example.com' );
  $padSecThanks = padSectionOption ( 'thanks', 'Thanks - you are on the list.' );

  if ( padSectionCameBack ( $padSecForm, [ 'email' ] ) )
    $padSecBody = '<p class="ps-thanks" role="status">' . padSectionEscape ( $padSecThanks ) . '</p>';
  else
    $padSecBody = padSectionForm ( $padSecForm, 'ps-form ps-signup', fn () =>
                    padSectionInput ( $padSecForm, 'email', $padSecLabel, 'email',
                                      ' required autocomplete="email" placeholder="' . padSectionEscape ( $padSecPlace ) . '"' )
                    . '<button class="ps-button" type="submit">' . padSectionEscape ( $padSecButton ) . '</button>' );

  return padSection ( 'newsletter', "<div class=\"ps-newsletter\">$padSecHead<div class=\"ps-newsletter-form\">$padSecBody"
                                  . ( $padSecNote !== '' ? "<p class=\"ps-note\">$padSecNote</p>" : '' ) . '</div></div>' );

?>
