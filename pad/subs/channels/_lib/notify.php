<?php

  // The channels subsystem: a notification rendered per channel and handed to it - for
  // padNotify (lib/notify.php). A channel is a file of subs/channels/types/, answering an
  // array:
  //
  //   settings  what it needs - name => [ 'env' => the padEnv name, 'to' => the recipient's
  //             field (alternatives split by |), 'default' => the value when neither has one,
  //             'address' => TRUE for an address a request goes to, 'pattern' => the regular
  //             expression a value must match and 'is' => what that is, in a message,
  //             'secret' => TRUE for a token that message never shows ]; a
  //             setting without a default is required. Looked for in the options first
  //             ([ 'slack' => [ 'url' => ... ] ]), then in the recipient, then in the
  //             environment. An address - a webhook, a homeserver, a gateway - is never read
  //             from the recipient, whatever its spec says: the row is data, a profile field a
  //             visitor fills or a column that happens to share the channel's name, and this
  //             server would post to wherever it points. The row names the person within the
  //             channel alone - an e-mail address, a phone, a chat, a topic, a room - checked
  //             against its pattern, a value that is none the data's reason not to send. An
  //             address must be absolute http or https: padCurl reads a value without a scheme
  //             as a page of the application or a _data/ file
  //   send      function ( $message, $settings, $recipient, $options ) - TRUE when it went, the
  //             reason as text when it did not, or [ 'ok' => ..., 'error' => ..., 'gone' => ... ]
  //
  // The message a channel gets: name, channel, title, text, html, data, url. Its template is
  // _notifications/<name>.pad, looked up from the page's directory up to the application and
  // then _common, as _mail/ is. A line @<channel>@ - or @title@ - alone on a line opens the
  // section of that channel; what stands before the first one is the plain text, for every
  // channel without a section of its own. Each part is rendered for the channel that uses it,
  // as a mail's template is (padCode, a pass of its own), with the data handed over as its
  // variables and $channel and $recipient beside them; the page's variables are left as they
  // were. A part without markup is text: the text as it rendered, the html with its line
  // breaks as <br>; a part with markup is the html, its text made as a mail's text part is
  // (padMailText). The title is the @title@ section, else the option title, else the first
  // line of the text.
  //
  // Queued ([ 'queue' => TRUE, or a queue's name ]) every channel is a job of its own,
  // '@notify' (lib/queue.php): a channel that fails is retried alone, and the mail that went
  // is not sent again. The worker renders the message, from the directory of the page that
  // queued it; the job keeps the recipient as padLogin keeps a user row, its secrets left out.

  function padNotifyGo ( $to, $name, $data, $channels, $options ) {

    padNotifyStore ( 'results', [] );

    if ( ! is_array ( $data ) )
      return padNotifyAuthor ( 'padNotify: the data is an array of named values - not ' . get_debug_type ( $data ) );

    if ( ! is_array ( $options ) )
      return padNotifyAuthor ( "padNotify: the options are an array - [ 'queue' => TRUE ] - not " . get_debug_type ( $options ) );

    $recipient = padNotifyRecipient ( $to );

    if ( $recipient === NULL )
      return padNotifyAuthor ( 'padNotify: the recipient is a user row - an array with id, email and the like - or an e-mail address, not '
                               . ( is_scalar ( $to ) ? "'" . padMakeSafe ( (string) $to, 40 ) . "'" : get_debug_type ( $to ) ) );

    $template = padNotifyTemplate ( $name );

    if ( $template === NULL )
      return padNotifyAuthor ( "padNotify: there is no notification named '" . padMakeSafe ( is_scalar ( $name ) ? (string) $name : get_debug_type ( $name ), 60 )
                               . "' - a template _notifications/<name>.pad" );

    if ( $channels === NULL )
      $channels = array_values ( array_diff ( array_keys ( $template ['sections'] ), [ '', 'title' ] ) );

    if ( is_string ( $channels ) )
      $channels = array_filter ( array_map ( 'trim', explode ( ',', $channels ) ), 'strlen' );

    if ( ! is_array ( $channels ) or ! $channels )
      return padNotifyAuthor ( "padNotify: name the channels - [ 'mail', 'database' ] - the template of '" . padMakeSafe ( $name, 60 ) . "' has no section of a channel" );

    // Every channel known and every setting there before anything goes - a misspelt channel
    // or a missing key is named now, not after the mail went.

    $plan = [];

    foreach ( array_values ( array_unique ( $channels ) ) as $channel ) {

      if ( ! is_string ( $channel ) or ! padKind ( 'channels', $channel ) )
        return padNotifyAuthor ( "padNotify: there is no channel named '" . padMakeSafe ( is_scalar ( $channel ) ? (string) $channel : get_debug_type ( $channel ), 30 )
                                 . "' - " . implode ( ', ', padKinds ( 'channels' ) ) );

      $kind     = padNotifyKind ( $channel );
      $settings = padNotifySettings ( $channel, $kind, $recipient, $options );

      if ( $settings === NULL )
        return FALSE;

      $plan [$channel] = [ $kind, $settings ];

      // The channel used goes on the xref record, which the reference application shows.

      if ( $GLOBALS ['padInfoXref'] ?? FALSE )
        padInfoXref ( 'channel', $channel );

    }

    if ( ! empty ( $options ['queue'] ) )
      return padNotifyQueue ( $recipient, $name, $data, $plan, $options );

    $all = TRUE;

    foreach ( $plan as $channel => [ $kind, $settings ] )
      $all = padNotifySend ( $channel, $kind, $settings, $recipient, $name, $template, $data, $options ) && $all;

    return $all;

  }

  // One job a channel. A queue's name, or TRUE for the default queue; the options go along
  // without the queue, the data as it was given - JSON, as every job's.

  function padNotifyQueue ( $recipient, $name, $data, $plan, $options ) {

    $queue = is_string ( $options ['queue'] ) ? $options ['queue'] : 'default';

    unset ( $options ['queue'] );

    // The job is a file under DATA/queue/, kept with the failed jobs after its tries: of the
    // recipient it holds what padLogin keeps of a user row (padAuthKeep), without the password
    // hash, the remember token and the second factor's secret.

    $recipient = padAuthKeep ( $recipient );

    $results = [];
    $all     = TRUE;

    foreach ( $plan as $channel => [ $kind, $settings ] ) {

      // A recipient without the address a channel needs gets no job that could only fail.

      if ( isset ( $settings ['@missing'] ) ) {
        $results [$channel] = [ 'ok' => FALSE, 'error' => $settings ['@missing'], 'gone' => [], 'job' => FALSE ];
        $all                = FALSE;
        continue;
      }

      $id = padQueue ( '@notify', [ 'to'      => $recipient, 'name' => (string) $name, 'vars' => $data, 'channel' => $channel,
                                    'options' => $options,   'dir'  => (string) ( $GLOBALS ['padDir'] ?? '' ) ], queue: $queue );

      $results [$channel] = [ 'ok' => $id !== FALSE, 'error' => $id === FALSE ? 'it could not be queued' : '', 'gone' => [], 'job' => $id ];
      $all                = $all && $id !== FALSE;

    }

    padNotifyStore ( 'results', $results );

    return $all;

  }

  // The worker's side: one channel of a queued notification, rendered and sent from the
  // directory of the page that queued it. FALSE fails the attempt - the queue retries it.

  function padNotifyWork ( $data ) {

    global $padDir;

    $was = $padDir ?? '';
    $dir = trim ( (string) ( $data ['dir'] ?? '' ), '/' );

    if ( $dir !== '' and ( str_contains ( "/$dir/", '/../' ) or ! is_dir ( APP . $dir ) ) )
      $dir = '';

    $padDir = $dir;

    try {

      $ok = padNotifyGo ( $data ['to'] ?? [], $data ['name'] ?? '', (array) ( $data ['vars'] ?? [] ),
                          [ (string) ( $data ['channel'] ?? '' ) ], (array) ( $data ['options'] ?? [] ) );

    } finally {

      $padDir = $was;

    }

    if ( ! $ok )
      throw new Exception ( padNotifyResults () [ $data ['channel'] ?? '' ] ['error'] ?? 'the notification was not sent' );

    return TRUE;

  }

  function padNotifySend ( $channel, $kind, $settings, $recipient, $name, $template, $data, $options ) {

    if ( isset ( $settings ['@missing'] ) )
      $answer = $settings ['@missing'];

    else try {
      $message = padNotifyMessage ( $channel, $name, $template, $data, $recipient, $options );
      $answer  = is_callable ( $kind ['send'] ?? NULL ) ? ( $kind ['send'] ) ( $message, $settings, $recipient, $options ) : 'the channel has no send';
    } catch ( Exception $e ) {
      $answer = $e->getMessage ();
    }

    $result = is_array ( $answer ) ? $answer + [ 'ok' => FALSE, 'error' => '', 'gone' => [] ]
            : [ 'ok' => $answer === TRUE, 'error' => $answer === TRUE ? '' : (string) $answer, 'gone' => [] ];

    if ( ! $result ['ok'] and $result ['error'] === '' )
      $result ['error'] = 'it was not delivered';

    $results            = padNotifyStore ( 'results' ) ?? [];
    $results [$channel] = $result;

    padNotifyStore ( 'results', $results );

    if ( ! $result ['ok'] )
      padLog ( 'the notification {name} was not sent on {channel}: {error}', 'warning',
               [ 'name' => (string) $name, 'channel' => $channel, 'error' => $result ['error'] ] );

    return $result ['ok'];

  }

  function padNotifyKind ( $channel ) {

    static $kinds = [];

    return $kinds [$channel] ??= ( function ( $padNotifyFile ) {
      $answer = include $padNotifyFile;
      return is_array ( $answer ) ? $answer : [];
    } ) ( PAD . "subs/channels/types/$channel.php" );

  }

  // A recipient as a row: an array as it is, an object read as one, an e-mail address as
  // [ 'email' => ... ]; anything else is no recipient.

  function padNotifyRecipient ( $to ) {

    if ( is_object ( $to ) )
      $to = padToArray ( $to );

    if ( is_array ( $to ) )
      return $to;

    if ( is_string ( $to ) and str_contains ( $to, '@' ) )
      return [ 'email' => $to ];

    return NULL;

  }

  // A channel's settings - from the options, the recipient or the environment - or NULL when
  // one the environment should give is missing or wrong (the author's), or the reason as text
  // when the recipient lacks one or holds what is none (the data's: a person without a phone
  // number gets no text message).

  function padNotifySettings ( $channel, $kind, $recipient, $options ) {

    $settings = [];
    $env      = [];
    $field    = [];
    $wrong    = [];
    $bad      = [];

    foreach ( (array) ( $kind ['settings'] ?? [] ) as $setting => $spec ) {

      $value = $options [$channel] [$setting] ?? NULL;
      $from  = ( $value === NULL ) ? '' : "the option [ '$channel' => [ '$setting' => ... ] ]";
      $row   = '';

      if ( $value === NULL and empty ( $spec ['address'] ) )
        foreach ( explode ( '|', (string) ( $spec ['to'] ?? '' ) ) as $to )
          if ( $value === NULL and $to !== '' and isset ( $recipient [$to] ) and $recipient [$to] !== '' and $recipient [$to] !== [] ) {
            $value = $recipient [$to];
            $row   = $to;
          }

      if ( $value === NULL and ( $spec ['env'] ?? '' ) !== '' ) {
        $read = padEnv ( $spec ['env'], NULL );
        if ( $read !== NULL and $read !== '' and $read !== FALSE ) {
          $value = $read;
          $from  = $spec ['env'];
        }
      }

      if ( $value === NULL and array_key_exists ( 'default', $spec ) )
        $value = $spec ['default'];

      if ( $value === NULL ) {
        if ( ( $spec ['env'] ?? '' ) !== '' )
          $env [$setting] = $spec ['env'];
        else
          $field [] = str_replace ( '|', ' or ', (string) ( $spec ['to'] ?? $setting ) );
      }

      elseif ( $row !== '' and ! padNotifyValid ( $spec, $value ) )
        $wrong [] = "the recipient's $row is no " . ( $spec ['is'] ?? $setting ) . ': ' . padNotifyShow ( $value );

      elseif ( $from !== '' and ! padNotifyValid ( $spec, $value ) )
        $bad [] = "the $setting of the $channel channel ($from) is no "
                  . ( empty ( $spec ['address'] ) ? ( $spec ['is'] ?? $setting ) . ( empty ( $spec ['secret'] ) ? ': ' . padNotifyShow ( $value ) : '' )
                                                  : 'absolute http or https address' );

      $settings [$setting] = $value;

    }

    if ( $env ) {
      $tos = array_filter ( array_map ( fn ( $setting ) => empty ( $kind ['settings'] [$setting] ['address'] ) ? ( $kind ['settings'] [$setting] ['to'] ?? '' ) : '',
                                        array_keys ( $env ) ) );
      padNotifyAuthor ( "padNotify: the $channel channel needs " . implode ( ' and ', $env ) . " - set it in the application's .env"
                        . ( $tos ? ', give the recipient a field ' . implode ( ', ', $tos ) : '' )
                        . ", or the option [ '$channel' => [ '" . array_key_first ( $env ) . "' => ... ] ]" );
      return NULL;
    }

    if ( $bad ) {
      padNotifyAuthor ( 'padNotify: ' . implode ( '; ', $bad ) );
      return NULL;
    }

    if ( $field or $wrong )
      return [ '@missing' => implode ( '; ', array_merge ( $field ? [ 'the recipient has no ' . implode ( ' and no ', $field ) ] : [], $wrong ) ) ];

    return $settings;

  }

  // Whether a setting's value is what its spec says: an address absolute http or https, with
  // a plain host - else a value matching the pattern. A setting with neither takes any value.
  // An address is never shown in a message: a webhook's is its credential.

  function padNotifyValid ( $spec, $value ) {

    if ( ! empty ( $spec ['address'] ) )
      return is_string ( $value ) and (bool) preg_match ( '~^https?://[^\s/?#\\\\]+(/[^\s\\\\]*)?$~iD', $value );

    if ( isset ( $spec ['pattern'] ) )
      return is_scalar ( $value ) and (bool) preg_match ( $spec ['pattern'], (string) $value );

    return TRUE;

  }

  function padNotifyShow ( $value ) {

    return "'" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : get_debug_type ( $value ), 40 ) . "'";

  }

  // The message of one channel: its own section, else the plain text, rendered for it. Markup
  // is an HTML tag - <p>, </b>, <br/> - not every < : Slack's <url|label> is text.

  function padNotifyMessage ( $channel, $name, $template, $data, $recipient, $options ) {

    include_once PAD . 'tags/_lib/mail.php';

    $vars   = [ 'channel' => $channel, 'recipient' => $recipient ] + $data;
    $own    = isset ( $template ['sections'] [$channel] );
    $body   = padNotifyRender ( $own ? $template ['sections'] [$channel] : $template ['sections'] [''], $vars );
    $markup = (bool) preg_match ( '/<\/?[a-z][a-z0-9]*(\s[^>]*)?\/?>/i', $body );
    $html   = $markup ? $body : nl2br ( $body, FALSE );
    $text   = $markup ? padMailText ( $body ) : html_entity_decode ( $body, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $title  = isset ( $template ['sections'] ['title'] ) ? padMailText ( padNotifyRender ( $template ['sections'] ['title'], $vars ) )
                                                          : trim ( (string) ( $options ['title'] ?? '' ) );

    if ( $title === '' )
      $title = mb_strimwidth ( trim ( strtok ( $text, "\n" ) ?: '' ), 0, 120, '...', 'UTF-8' );

    return [ 'name'     => (string) $name, 'channel' => $channel, 'title' => trim ( preg_replace ( '/\s+/', ' ', $title ) ),
             'text'     => $text,          'html'    => $html,    'data'  => $data,
             'rendered' => $body,          'markup'  => $markup,  'own'   => $own,
             'url'      => (string) ( $options ['url'] ?? ( is_scalar ( $data ['url'] ?? NULL ) ? $data ['url'] : '' ) ) ];

  }

  // A part rendered as a mail's template is: a pass of its own, its variables set and taken
  // away again, the stand-ins of PAD's characters written as the characters.

  function padNotifyRender ( $source, $vars ) {

    if ( trim ( $source ) === '' )
      return '';

    $saved = [];

    foreach ( $GLOBALS as $name => $value )
      if ( padValidStore ( $name ) )
        $saved [$name] = $value;

    foreach ( $vars as $name => $value )
      if ( is_string ( $name ) and padValidVar ( $name ) )
        $GLOBALS [$name] = $value;

    static $passes = 0;

    $scopeWas                   = $GLOBALS ['padOnceScope'] ?? NULL;
    $nestedWas                  = $GLOBALS ['padGuardNested'] ?? FALSE;
    $GLOBALS ['padOnceScope']   = 'notify' . ( ++$passes );
    $GLOBALS ['padGuardNested'] = TRUE;
    $GLOBALS ['padSectionsNext'] = TRUE;

    try {

      return trim ( padUnprotect ( padUnescape ( padCode ( $source ) ) ) );

    } finally {

      $GLOBALS ['padOnceScope']   = $scopeWas;
      $GLOBALS ['padGuardNested'] = $nestedWas;

      foreach ( array_keys ( $GLOBALS ) as $name )
        if ( padValidStore ( $name ) and ! array_key_exists ( $name, $saved ) )
          unset ( $GLOBALS [$name] );

      foreach ( $saved as $name => $value )
        $GLOBALS [$name] = $value;

    }

  }

  // The template of a notification and its sections, or NULL.

  function padNotifyTemplate ( $name ) {

    if ( ! is_string ( $name ) or ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_-]*(\/[a-zA-Z0-9][a-zA-Z0-9_-]*)*$/D', $name ) )
      return NULL;

    $dirs = [];

    foreach ( padDirs () as $value )
      $dirs [] = APP2 . $value . '_notifications/';

    $dirs [] = COMMON . '_notifications/';

    foreach ( $dirs as $dir )
      if ( is_file ( "$dir$name.pad" ) )
        return [ 'file' => "$dir$name.pad", 'sections' => padNotifySections ( padFileGet ( "$dir$name.pad" ) ) ];

    return NULL;

  }

  function padNotifySections ( $source ) {

    $known    = array_flip ( padKinds ( 'channels' ) ) + [ 'title' => TRUE ];
    $sections = [ '' => '' ];
    $at       = '';

    foreach ( preg_split ( '/\R/', (string) $source ) as $line )
      if ( preg_match ( '/^\s*@([a-z][a-z0-9]*)@\s*$/D', $line, $match ) and isset ( $known [ $match [1] ] ) )
        $sections [ $at = $match [1] ] = '';
      else
        $sections [$at] .= "$line\n";

    return array_map ( fn ( $text ) => trim ( $text, "\r\n" ), $sections );

  }

  // One request of a channel to its service: a body that is an array goes as JSON. The answer
  // as [ status, json, data, error ].

  function padNotifyHttp ( $method, $url, $body, $headers = [] ) {

    // Only an absolute address goes to padCurl, which reads a name without a scheme as a page
    // of this application or a _data/ file.

    if ( ! is_string ( $url ) or ! preg_match ( '~^https?://~i', $url ) )
      return [ 'status' => 999, 'json' => NULL, 'data' => '', 'error' => 'no address' ];

    $input = [ 'url' => $url, 'headers' => $headers, 'options' => [ 'USERAGENT' => 'PAD' ] ];

    if ( is_array ( $body ) ) {
      $body = json_encode ( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
      $input ['headers'] += [ 'Content-Type' => 'application/json' ];
    }

    if ( $method == 'PUT' )
      $input ['options'] += [ 'CUSTOMREQUEST' => 'PUT', 'POSTFIELDS' => (string) $body ];
    elseif ( $method == 'POST' )
      $input ['post'] = (string) $body;

    $out  = padCurl ( $input );
    $data = (string) ( $out ['data'] ?? '' );
    $json = json_decode ( $data, TRUE );

    return [ 'status' => (int) ( $out ['result'] ?? 0 ), 'json' => is_array ( $json ) ? $json : NULL,
             'data'   => $data, 'error' => (string) ( $out ['ERROR'] ?? '' ) ];

  }

  // TRUE for a 2xx answer, else what it said - its error or message field (Gotify's
  // errorDescription before its error, the title of a status), or the list of its errors
  // (Pushover's), else its status.

  function padNotifyAnswer ( $answer, $service ) {

    if ( $answer ['status'] >= 200 and $answer ['status'] <= 299 )
      return TRUE;

    if ( $answer ['error'] !== '' and $answer ['status'] == 999 )
      return "$service did not answer";

    $json = $answer ['json'] ?? [];
    $said = $json ['description'] ?? $json ['message'] ?? $json ['errorDescription'] ?? $json ['error'] ?? $json ['detail'] ?? '';
    $said = is_scalar ( $said ) ? (string) $said : '';

    if ( $said === '' and is_array ( $json ['errors'] ?? NULL ) )
      $said = implode ( '; ', array_filter ( $json ['errors'], 'is_string' ) );

    if ( $said === '' and $answer ['json'] === NULL )
      $said = $answer ['data'];

    return "$service answered HTTP " . $answer ['status'] . ( trim ( $said ) !== '' ? ': ' . padMakeSafe ( trim ( $said ), 160 ) : '' );

  }

  // A text cut to the most characters a service takes, ... the last three when it was cut.

  function padNotifyCut ( $text, $most ) {

    $text = (string) $text;

    return ( mb_strlen ( $text, 'UTF-8' ) > $most ) ? mb_substr ( $text, 0, $most - 3, 'UTF-8' ) . '...' : $text;

  }

  function padNotifyAuthor ( $message ) {

    $results = padNotifyStore ( 'results' ) ?? [];
    $results ['@error'] = [ 'ok' => FALSE, 'error' => $message, 'gone' => [] ];

    padNotifyStore ( 'results', $results );

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return FALSE;

  }

?>
