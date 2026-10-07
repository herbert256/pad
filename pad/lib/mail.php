<?php

  // Template emails: a mail is a PAD template rendered to an HTML part and a text part and
  // handed to a transport.
  //
  //   {mail to=$email, template='order', subject='Order confirmation'}
  //   {mail to=$email, subject='Welcome'}<p>Hello {$name}</p>{/mail}
  //   padMail ( $email, 'order', 'Order confirmation', [ 'order' => $order ] );
  //
  // A template lives in _mail/, looked up like _include/ from the page's directory up to
  // the application and then _common: _mail/order.pad (or .html) is the HTML part,
  // _mail/order.txt the text part - when there is none, the text is made from the HTML -
  // and _mail/order.php runs first, as a page's PHP does. _inits.pad and _exits.pad in the
  // same _mail/ are the email's layout around the HTML part with its @page@, _inits.txt and
  // _exits.txt around the text. Both parts see the page's variables, the ones handed over
  // and what the mail's PHP sets, and leave the page's as they were; they render like
  // {code}, through a nested pass.
  //
  // $padMailTransport says where the message goes: 'file', the default, writes it as an
  // .eml under DATA/mail/<app>/ instead of sending it - development and the suites need no
  // mail server; 'mail' hands it to PHP's mail(); any other value is the name of a function
  // of the application's _lib that gets the message - to, cc, bcc, from, replyTo, subject,
  // html, text, headers, raw - and answers whether it went: the place for SMTP through a
  // library. $padMailFrom is the sender, noreply@ the host when empty; $padMailKeep the
  // number of messages the file transport keeps. $padMailLast holds the last message of the
  // request, with the file it was written to.
  //
  // padMail          builds, renders and sends one message
  // padMailTemplate  the _mail/ directory and the files of a template
  // padMailRender    a template's text rendered with the variables of the mail
  // padMailPhp       a mail's PHP, run where the page's variables are
  // padMailText      the text part made from the HTML part
  // padMailAddress   one header's addresses checked and written: a list, Name <addr>
  // padMailSplit     an address list split on the commas between its addresses
  // padMailHeader    a header value refused when it could start a header of its own
  // padMailMessage   the MIME message: multipart/alternative, quoted-printable UTF-8
  // padMailKeep      the file transport's outbox trimmed to its newest messages
  // padMailSend      the transport

  function padMail ( $to, $template, $subject, $vars = [], $options = [] ) {

    global $padCheckSyntax;

    $html = $options ['html'] ?? NULL;
    $text = $options ['text'] ?? NULL;
    $tpl  = NULL;

    if ( (string) $template !== '' ) {

      $tpl = padMailTemplate ( (string) $template );

      if ( ! $tpl )
        return padError ( "there is no mail template named '" . padMakeSafe ( $template, 60 ) . "' in _mail/" );

    } elseif ( $html === NULL and $text === NULL )

      return padError ( "a mail needs a template or a body - {mail template='order'}, or the body between {mail} and {/mail}" );

    if ( trim ( (string) $subject ) === '' )
      return padError ( "a mail needs a subject" );

    // The mail sees the page's variables, the ones handed over and what its own PHP sets -
    // and leaves the page's as they were: the application's globals are taken before and
    // put back after, what the mail added removed.

    $saved = [];

    foreach ( $GLOBALS as $name => $value )
      if ( padValidStore ( $name ) )
        $saved [$name] = $value;

    foreach ( $vars as $name => $value )
      if ( padValidVar ( $name ) )
        $GLOBALS [$name] = $value;

    try {

      if ( $tpl ) {

        if ( $tpl ['php'] )
          padMailPhp ( $tpl ['php'] );

        $html = $tpl ['html'] ? padMailRender ( $tpl ['dir'], $tpl ['html'], 'pad' ) : NULL;
        $text = $tpl ['text'] ? padMailRender ( $tpl ['dir'], $tpl ['text'], 'txt' ) : NULL;

      }

    } finally {

      foreach ( array_keys ( $GLOBALS ) as $name )
        if ( padValidStore ( $name ) and ! array_key_exists ( $name, $saved ) )
          unset ( $GLOBALS [$name] );

      foreach ( $saved as $name => $value )
        $GLOBALS [$name] = $value;

    }

    if ( $text === NULL and $html !== NULL )
      $text = padMailText ( $html );

    $message = [
      'to'      => padMailAddress ( $to, 'to' ),
      'cc'      => padMailAddress ( $options ['cc']      ?? '', 'cc' ),
      'bcc'     => padMailAddress ( $options ['bcc']     ?? '', 'bcc' ),
      'replyTo' => padMailAddress ( $options ['replyTo'] ?? '', 'replyTo' ),
      'from'    => padMailAddress ( ( $options ['from'] ?? '' ) !== '' ? $options ['from'] : padMailFrom (), 'from' ),
      'subject' => padMailHeader  ( trim ( (string) $subject ), 'subject' ),
      'html'    => $html,
      'text'    => (string) $text
    ];

    if ( $message ['to'] === '' )
      return padError ( "a mail needs an address to go to" );

    padMailMessage ( $message );

    return padMailSend ( $message );

  }

  function padMailFrom () {

    global $padMailFrom, $padHost;

    if ( ( $padMailFrom ?? '' ) !== '' )
      return $padMailFrom;

    // The host as an address takes it: an IP literal in brackets - an IPv6 one tagged
    // IPv6:, as RFC 5321 writes it, without which it is no address - a name without a
    // dot - localhost - completed, which an address must be.

    $host = trim ( parse_url ( $padHost, PHP_URL_HOST ) ?: 'localhost', '[]' );

    if ( filter_var ( $host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) )
      return "noreply@[IPv6:$host]";

    if ( filter_var ( $host, FILTER_VALIDATE_IP ) )
      return "noreply@[$host]";

    if ( ! str_contains ( $host, '.' ) )
      $host .= '.localdomain';

    return "noreply@$host";

  }

  // A template is found where an _include/ snippet would be: the page's directory, its
  // parents up to the application, then _common. The name is a page-style name - letters,
  // digits, - and _, directories with / - so it never leaves _mail/.

  function padMailTemplate ( $name ) {

    if ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_-]*(\/[a-zA-Z0-9][a-zA-Z0-9_-]*)*$/D', $name ) )
      return FALSE;

    $dirs = [];

    foreach ( padDirs () as $value )
      $dirs [] = APP2 . $value . '_mail/';

    $dirs [] = COMMON . '_mail/';

    foreach ( $dirs as $dir ) {

      $base = $dir . $name;

      $html = file_exists ( "$base.pad" ) ? "$base.pad" : ( file_exists ( "$base.html" ) ? "$base.html" : '' );
      $text = file_exists ( "$base.txt" ) ? "$base.txt" : '';
      $php  = file_exists ( "$base.php" ) ? "$base.php" : '';

      if ( $html or $text or $php )
        return [ 'dir' => $dir, 'html' => $html, 'text' => $text, 'php' => $php ];

    }

    return FALSE;

  }

  // The part's template inside the layout of its _mail/ directory - _inits/_exits with the
  // part's extension, @page@ where the part goes - rendered like {code} and written out as
  // the page would be: the stand-ins of PAD's syntax characters become the characters.

  function padMailRender ( $dir, $file, $ext ) {

    $source = padFileGet ( $file );
    $inits  = padFileGet ( "{$dir}_inits.$ext" );
    $exits  = padFileGet ( "{$dir}_exits.$ext" );

    if ( $inits !== '' or $exits !== '' ) {
      if ( str_contains ( $inits . $exits, '@page@' ) )
        $source = str_replace ( '@page@', $source, $inits . $exits );
      else
        $source = $inits . $source . $exits;
    }

    $out = trim ( padUnprotect ( padUnescape ( padCode ( $source ) ) ) );

    // A field's value arrives escaped for HTML - the sanitize step of every field - which
    // the text part, read as it is, must not show: Zo&euml; is Zoë there.

    return ( $ext == 'txt' ) ? html_entity_decode ( $out, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) : $out;

  }

  // A mail's PHP runs as a page's does, before its templates, with the page's variables at
  // hand: every global is bound in this scope, and what the file creates is kept as a
  // global for the templates - the way a nested pass keeps what it made.

  function padMailPhp ( $padMailFile ) {

    foreach ( array_keys ( $GLOBALS ) as $padMailKey )
      if ( $padMailKey !== 'padMailFile' and $padMailKey !== 'GLOBALS' )
        global $$padMailKey;

    include $padMailFile;

    foreach ( get_defined_vars () as $padMailKey => $padMailVal )
      if ( padValidStore ( $padMailKey ) and ! isset ( $GLOBALS [$padMailKey] ) )
        $GLOBALS [$padMailKey] = $padMailVal;

  }

  // The text part when the template has none: the HTML read as a mail client without HTML
  // would show it - block ends become line breaks, a list item a dash, a link its text with
  // the address behind it, the rest of the markup gone and the entities decoded.

  function padMailText ( $html ) {

    $text = preg_replace ( '/<(head|style|script|title)\b.*?<\/\1>/is', '', $html );

    $text = preg_replace_callback ( '/<a\b[^>]*href\s*=\s*(["\'])(.*?)\1[^>]*>(.*?)<\/a>/is', function ( $m ) {
      $label = trim ( strip_tags ( $m [3] ) );
      return ( $label === '' or $label === $m [2] or str_starts_with ( $m [2], '#' ) ) ? $m [3] : "$label ($m[2])";
    }, $text );

    $text = preg_replace ( '/<br\s*\/?>/i', "\n", $text );
    $text = preg_replace ( '/<li\b[^>]*>/i', "\n- ", $text );
    $text = preg_replace ( '/<\/(p|div|h[1-6]|ul|ol|table|tr|blockquote|pre)>/i', "\n\n", $text );
    $text = preg_replace ( '/<\/(td|th)>/i', ' ', $text );

    $text = html_entity_decode ( strip_tags ( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    $lines = [];

    foreach ( explode ( "\n", str_replace ( "\r", '', $text ) ) as $line )
      $lines [] = trim ( preg_replace ( '/[ \t]+/', ' ', $line ) );

    return trim ( preg_replace ( "/\n{3,}/", "\n\n", implode ( "\n", $lines ) ) );

  }

  // An address list - an array, or names separated by commas - each an address or
  // Name <address>, checked, the name encoded when it is not plain ASCII. A line break in
  // any of it could start a header of its own, Bcc: to the world, and is refused whatever
  // the strict switch says.

  function padMailAddress ( $list, $what ) {

    if ( is_array ( $list ) )
      $list = implode ( ',', $list );

    $list = padMailHeader ( (string) $list, $what );

    $out = [];

    foreach ( padMailSplit ( $list ) as $one ) {

      $one = trim ( $one );

      if ( $one === '' )
        continue;

      $open = strrpos ( $one, '<' );

      if ( str_ends_with ( $one, '>' ) and $open !== FALSE and $open < strlen ( $one ) - 2
           and ! str_contains ( substr ( $one, $open + 1, -1 ), '>' ) ) {
        $name = trim ( substr ( $one, 0, $open ), " \t\"" );
        $addr = trim ( substr ( $one, $open + 1, -1 ) );
      } else {
        $name = '';
        $addr = $one;
      }

      if ( filter_var ( $addr, FILTER_VALIDATE_EMAIL ) === FALSE ) {
        padError ( "'" . padMakeSafe ( $addr, 80 ) . "' is no mail address, in $what" );
        continue;
      }

      if ( $name === '' )
        $out [] = $addr;
      elseif ( preg_match ( '/^[\x20-\x7e]*$/', $name ) )
        $out [] = '"' . addcslashes ( $name, '"\\' ) . "\" <$addr>";
      else
        $out [] = mb_encode_mimeheader ( $name, 'UTF-8', 'B', "\r\n" ) . " <$addr>";

    }

    return implode ( ', ', $out );

  }

  // The addresses of a list, split on the commas between them by walking the text: a comma
  // inside a quoted name - "Doe, John" <john@...> - is the name's, a quote with no partner
  // after it is a character of the name, as it was before quoted names were read, and in
  // quotes a backslash escapes the character after it. One regular expression did the split:
  // a stray quote - "Ann "Ace Lee" <ann@...> - cut the address and stopped the mail, and a
  // quoted name of ten thousand characters ran out of PCRE's JIT stack, losing every address.

  function padMailSplit ( $list ) {

    $parts  = [];
    $from   = 0;
    $length = strlen ( $list );

    for ( $at = 0; $at < $length; $at++ )
      if ( $list [$at] == ',' ) {
        $parts [] = substr ( $list, $from, $at - $from );
        $from     = $at + 1;
      } elseif ( $list [$at] == '"' and ( $close = padMailQuoteEnd ( $list, $at + 1 ) ) !== FALSE )
        $at = $close;

    $parts [] = substr ( $list, $from );

    return $parts;

  }

  // The quote that closes the one before $at, FALSE when none does.

  function padMailQuoteEnd ( $list, $at ) {

    $length = strlen ( $list );

    while ( ( $at += strcspn ( $list, '"\\', $at ) ) < $length ) {

      if ( $list [$at] == '"' )
        return $at;

      $at += 2;

      if ( $at >= $length )
        return FALSE;

    }

    return FALSE;

  }

  function padMailHeader ( $value, $what ) {

    if ( preg_match ( '/[\r\n\0]/', $value ) ) {
      padError ( "a mail's $what may not hold a line break" );
      return preg_replace ( '/[\r\n\0]+/', ' ', $value );
    }

    return $value;

  }

  // The message as it travels: headers, then a multipart/alternative body with the text
  // part first - the one a client shows when it shows the last part it understands is the
  // HTML. ['headers'] leaves out To and Subject, which PHP's mail() takes apart; ['raw']
  // is the whole message, as the file transport writes it.

  function padMailMessage ( &$message ) {

    $host     = substr ( strrchr ( $message ['from'], '@' ), 1 );
    $host     = trim ( preg_replace ( '/[^a-zA-Z0-9.-]/', '', $host ) ) ?: 'localhost';
    $boundary = 'pad-' . padRandomString ( 24 );
    $eol      = "\r\n";

    $headers = [];

    $headers [] = 'From: ' . $message ['from'];

    if ( $message ['cc']      !== '' ) $headers [] = 'Cc: '       . $message ['cc'];
    if ( $message ['bcc']     !== '' ) $headers [] = 'Bcc: '      . $message ['bcc'];
    if ( $message ['replyTo'] !== '' ) $headers [] = 'Reply-To: ' . $message ['replyTo'];

    $headers [] = 'Date: '       . date ( 'r' );
    $headers [] = 'Message-ID: <' . padRandomString ( 24 ) . "@$host>";
    $headers [] = 'MIME-Version: 1.0';

    $parts = [ [ 'text/plain', $message ['text'] ] ];

    if ( $message ['html'] !== NULL )
      $parts [] = [ 'text/html', $message ['html'] ];

    if ( count ( $parts ) == 1 ) {

      $headers [] = 'Content-Type: text/plain; charset=UTF-8';
      $headers [] = 'Content-Transfer-Encoding: quoted-printable';

      $body = quoted_printable_encode ( str_replace ( [ "\r\n", "\n" ], [ "\n", $eol ], $message ['text'] ) );

    } else {

      $headers [] = "Content-Type: multipart/alternative; boundary=\"$boundary\"";

      $body = '';

      foreach ( $parts as [ $type, $content ] )
        $body .= "--$boundary$eol"
               . "Content-Type: $type; charset=UTF-8$eol"
               . "Content-Transfer-Encoding: quoted-printable$eol$eol"
               . quoted_printable_encode ( str_replace ( [ "\r\n", "\n" ], [ "\n", $eol ], (string) $content ) ) . $eol;

      $body .= "--$boundary--$eol";

    }

    $subject = preg_match ( '/^[\x20-\x7e]*$/', $message ['subject'] )
             ? $message ['subject']
             : mb_encode_mimeheader ( $message ['subject'], 'UTF-8', 'B', $eol );

    $message ['subjectHeader'] = $subject;
    $message ['headers']       = implode ( $eol, $headers );
    $message ['body']          = $body;
    $message ['raw']           = 'To: ' . $message ['to'] . $eol . "Subject: $subject$eol" . $message ['headers'] . $eol . $eol . $body;

  }

  // The file transport's outbox keeps the newest $padMailKeep messages of an application,
  // as the error reports do: a development server that sends on every request would
  // otherwise fill the disk. The names start with the time, so their order is the age.

  function padMailKeep ( $dir ) {

    global $padMailKeep;

    $keep  = (int) ( $padMailKeep ?? 100 );
    $files = glob ( $dir . '*.eml' ) ?: [];

    if ( $keep < 1 or count ( $files ) <= $keep )
      return;

    sort ( $files );

    foreach ( array_slice ( $files, 0, count ( $files ) - $keep ) as $old )
      @unlink ( $old );

  }

  function padMailSend ( $message ) {

    global $padApp, $padMailLast, $padMailTransport;

    $transport = $padMailTransport ?? 'file';

    if ( $transport === 'file' ) {

      $file = 'mail/' . $padApp . '/' . date ( 'Ymd-His' ) . '-' . padRandomString ( 8 ) . '.eml';

      padFilePut ( $file, $message ['raw'] );

      $message ['file'] = DATA . $file;
      $sent             = TRUE;

      padMailKeep ( DATA . 'mail/' . $padApp . '/' );

    } elseif ( $transport === 'mail' ) {

      $sent = mail ( $message ['to'], $message ['subjectHeader'], $message ['body'], $message ['headers'] );

    } elseif ( is_string ( $transport ) and function_exists ( $transport ) ) {

      $sent = (bool) $transport ( $message );

    } else {

      return padError ( "there is no mail transport named '" . padMakeSafe ( (string) $transport, 40 ) . "' - 'file', 'mail' or a function of the application" );

    }

    $padMailLast = $message + [ 'sent' => $sent ];

    if ( ! $sent )
      return padError ( "the mail to " . $message ['to'] . " was not sent - the $transport transport refused it" );

    return TRUE;

  }

?>
