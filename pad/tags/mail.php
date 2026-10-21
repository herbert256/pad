<?php

  // {mail to=$email, template='order', subject='Order confirmation'}: renders the _mail/
  // template to an HTML and a text part and sends it - lib/mail.php. As a pair without a
  // template, {mail to=$email, subject='Welcome'}<p>Hello {$name}</p>{/mail}, the content is
  // the HTML part, rendered where it stands - inside a loop with the fields of the row - and
  // the text part is made from it. from, cc, bcc and replyTo are the other headers. The tag
  // itself shows nothing. Like {tidy} the pair runs twice: first to ask for the end walk,
  // then on the rendered content.

  // A header is plain text, no HTML: a field inside a quoted header - subject='Welcome
  // {$name}', to='{$email}' - arrives escaped by its sanitize step and with the stand-ins of
  // its , = | @ ' " \, and both went into the mail - the subject "Welcome Zo&euml;
  // O&#039;Neil", and to='{$email}' refused as 'annexample.com'. padParmText gives the text
  // the field holds (lib/encoding.php), as the drawing tags take theirs; a line break an
  // entity decodes to is refused by padMailHeader as one written outright. A list of
  // addresses - to=$list - stays the list it is.

  $padMailText = fn ( $value ) => is_string ( $value ) ? padParmText ( $value ) : $value;

  $padMailTo   = $padMailText ( padTagParm ( 'to',       '' ) );
  $padMailTpl  = padTagParm ( 'template', '' );
  $padMailSubj = $padMailText ( padTagParm ( 'subject',  '' ) );

  $padMailOpts = [ 'from'    => $padMailText ( padTagParm ( 'from',    '' ) ),
                   'cc'      => $padMailText ( padTagParm ( 'cc',      '' ) ),
                   'bcc'     => $padMailText ( padTagParm ( 'bcc',     '' ) ),
                   'replyTo' => $padMailText ( padTagParm ( 'replyTo', '' ) ) ];

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
