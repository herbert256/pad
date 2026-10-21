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
  // With $options ['queue'] - TRUE, or a queue's name - padMail queues the mail instead
  // (lib/queue.php): pad work renders and sends it, in the application, with the variables
  // handed over - the page's own are not there then. The template is looked up when the
  // mail is queued, and the worker looks from the directory of the page that queued it.
  //
  // padMail          builds, renders and sends one message - its parts, from the template
  //                  to the transport, are tags/_lib/mail.php, read when it builds one

  function padMail ( $to, $template, $subject, $vars = [], $options = [] ) {

    global $padCheckSyntax;

    // Queued: the mail is a job for a worker (lib/queue.php), rendered and sent there - the
    // request answers at once. The answer is the job's id.

    if ( ! empty ( $options ['queue'] ) ) {

      $queue = is_string ( $options ['queue'] ) ? $options ['queue'] : 'default';

      unset ( $options ['queue'] );

      // The template is found here, from the page's directory, as a mail sent at once finds
      // it, and the directory goes with the job: the worker - pad work, whose page is the
      // application's root - looks from there (padQueueMail). Left to the worker, a template
      // in shop/_mail/ queued from shop/send, or a misspelt name, was a job id here and a job
      // failing "there is no mail template named 'order'" there.

      if ( (string) $template !== '' ) {

        include_once PAD . 'tags/_lib/mail.php';

        if ( ! padMailTemplate ( (string) $template ) )
          return padError ( "there is no mail template named '" . padMakeSafe ( $template, 60 ) . "' in _mail/" );

      }

      return padQueue ( '@mail', [ 'to'      => $to,      'template' => (string) $template, 'subject' => $subject,
                                   'vars'    => $vars,    'options'  => $options,
                                   'dir'     => (string) ( $GLOBALS ['padDir'] ?? '' ) ], queue: $queue );

    }

    include_once PAD . 'tags/_lib/mail.php';

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

?>
