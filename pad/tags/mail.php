<?php

  // {mail to=$email, template='order', subject='Order confirmation'}: renders the _mail/
  // template to an HTML and a text part and sends it - lib/mail.php. As a pair without a
  // template, {mail to=$email, subject='Welcome'}<p>Hello {$name}</p>{/mail}, the content is
  // the HTML part, rendered where it stands - inside a loop with the fields of the row - and
  // the text part is made from it. from, cc, bcc and replyTo are the other headers. The tag
  // itself shows nothing. Like {tidy} the pair runs twice: first to ask for the end walk,
  // then on the rendered content.

  $padMailTo   = padTagParm ( 'to',       '' );
  $padMailTpl  = padTagParm ( 'template', '' );
  $padMailSubj = padTagParm ( 'subject',  '' );

  $padMailOpts = [ 'from'    => padTagParm ( 'from',    '' ),
                   'cc'      => padTagParm ( 'cc',      '' ),
                   'bcc'     => padTagParm ( 'bcc',     '' ),
                   'replyTo' => padTagParm ( 'replyTo', '' ) ];

  if ( $padPair [$pad] and (string) $padMailTpl === '' ) {

    if ( $padWalk [$pad] == 'start' ) {
      $padWalk [$pad] = 'end';
      return TRUE;
    }

    $padMailOpts ['html'] = trim ( padUnprotect ( padUnescape ( $padContent ) ) );

    padMail ( $padMailTo, '', $padMailSubj, [], $padMailOpts );

    $padContent = '';

    return TRUE;

  }

  padMail ( $padMailTo, $padMailTpl, $padMailSubj, [], $padMailOpts );

  return '';

?>