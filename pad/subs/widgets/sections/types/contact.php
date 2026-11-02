<?php

  // {section:contact title='Get in touch', mail='hello@example.com', phone='+31 20 123 4567',
  // address='Damrak 1, Amsterdam', form/} - how to reach you: the address, the mail address
  // and the phone number as links, the opening hours, and - with form - a message form beside
  // them that posts back to the page as {input:form 'contact'} does: its name, e-mail and message
  // fields, required, refilled and with their messages when the post came back wrong, thanks=
  // ('Thanks - we will get back to you.') in its place once it came back right. form='name'
  // names the form ('contact'), button= its button ('Send'). The details slot replaces the
  // list of details with markup of its own - a map, an opening-hours table.
  //
  // Parts: eyebrow, title, text, details. Options: title (or the first value), eyebrow, text,
  // address, mail, phone, hours, form, button, thanks, level, tone, align (left), id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'details' ] );

  $GLOBALS ['padSectionNow'] ['align'] = 'left';

  $padSecHead    = padSectionHead ();
  $padSecAddress = padSectionOption ( 'address' );
  $padSecMail    = padSectionOption ( 'mail' );
  $padSecPhone   = padSectionOption ( 'phone' );
  $padSecHours   = padSectionOption ( 'hours' );
  $padSecFormOn  = padTagParm ( 'form', FALSE );
  $padSecFormOn  = ( $padSecFormOn === TRUE or ( is_string ( $padSecFormOn ) and trim ( $padSecFormOn ) !== '' ) );
  $padSecForm    = padSectionFormName ( 'contact' );
  $padSecButton  = padSectionOption ( 'button', 'Send' );
  $padSecThanks  = padSectionOption ( 'thanks', 'Thanks - we will get back to you.' );

  $padSecDetails = '';

  if ( $padSecAddress !== '' )
    $padSecDetails .= '<dt>Address</dt><dd>' . padSectionEscape ( $padSecAddress ) . '</dd>';

  if ( $padSecMail !== '' and ( $padSecHref = padSectionLink ( 'mailto:' . $padSecMail ) ) !== '' )
    $padSecDetails .= "<dt>E-mail</dt><dd><a href=\"$padSecHref\">" . padSectionEscape ( $padSecMail ) . '</a></dd>';

  // A phone number links as tel: with its digits and a leading +, the way a phone dials it.

  if ( $padSecPhone !== '' )
    $padSecDetails .= '<dt>Phone</dt><dd><a href="tel:' . preg_replace ( '/(?!^\+)[^0-9]/', '', $padSecPhone ) . '">' . padSectionEscape ( $padSecPhone ) . '</a></dd>';

  if ( $padSecHours !== '' )
    $padSecDetails .= '<dt>Hours</dt><dd>' . padSectionEscape ( $padSecHours ) . '</dd>';

  $padSecDetails = ( $padSecDetails !== '' ) ? "<dl class=\"ps-contact-details\">$padSecDetails</dl>" : '';

  if ( array_key_exists ( 'details', $GLOBALS ['padSectionNow'] ['fills'] ) )
    $padSecDetails = '<div class="ps-contact-details">' . padSectionHold ( $GLOBALS ['padSectionNow'] ['fills'] ['details'] ) . '</div>';

  $padSecMessage = '';

  if ( $padSecFormOn and padSectionCameBack ( $padSecForm, [ 'name', 'email', 'message' ] ) )
    $padSecMessage = '<p class="ps-thanks" role="status">' . padSectionEscape ( $padSecThanks ) . '</p>';
  elseif ( $padSecFormOn )
    $padSecMessage = padSectionForm ( $padSecForm, 'ps-form ps-contact-form', fn () =>
                       padSectionInput ( $padSecForm, 'name', 'Name', 'text', ' required autocomplete="name"' )
                       . padSectionInput ( $padSecForm, 'email', 'E-mail address', 'email', ' required autocomplete="email"' )
                       . padSectionInput ( $padSecForm, 'message', 'Message', 'textarea', ' required' )
                       . '<div><button class="ps-button" type="submit">' . padSectionEscape ( $padSecButton ) . '</button></div>' );

  return padSection ( 'contact', $padSecHead . '<div class="ps-contact' . ( $padSecMessage !== '' ? ' ps-contact-with-form' : '' ) . "\">$padSecDetails$padSecMessage</div>" );

?>
