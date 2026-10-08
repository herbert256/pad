<?php

  // Video embeds - the {video} tag. A YouTube or Vimeo video is shown as its poster with a
  // play button, and the player's iframe comes only when the button is pressed: the page
  // loads nothing of the video site before then - no player, no cookies, no tracking - and
  // a page with ten videos stays as light as one with ten pictures. The player that comes is
  // youtube-nocookie.com's or Vimeo's with dnt=1.
  //
  //   {video 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'}
  //   {video 'https://vimeo.com/76979871', title='The new Vimeo player', poster='photos/still.jpg'}
  //   {video 'clips/sunrise.webm', title='Sunrise', poster='photos/sunrise.jpg'}
  //
  // padVideo        the HTML of a video, '' when the address is no video it knows
  // padVideoParse   a YouTube or Vimeo address as [ site, id, start, short ], NULL when not
  // padVideoStart   a start time - 90, 1m30s, 1:30 - in seconds
  // padVideoEmbed   the lazy embed: poster, button, the address to open without script
  // padVideoLocal   a file of www/<application>/ as a <video> element
  // padVideoStyle   the rules and the script, written once per request
  //
  // Without JavaScript the play button is no use, so it is hidden (@media (scripting:none))
  // and a link to the video on its own site stands in its place, inside <noscript> - which
  // keeps the page valid where the video is inside a link itself, a card that links to its
  // page: the noscript text is no element to a browser that runs scripts. With script, a
  // click on the button puts the iframe in the poster's place and moves the focus into it.
  //
  // The poster of a YouTube video is its thumbnail on i.ytimg.com - an image, no script and
  // no cookie, though the address is asked of Google's server; poster= names a picture of
  // www/<application>/ instead, and a Vimeo video, whose thumbnail has no address of its
  // own, gets a quiet drawn poster unless poster= gives one. The title is required: it is
  // the button's text and the iframe's title, what a screen reader says.
  //
  // A file of www/<application>/ - .mp4, .webm, .ogv, .mov - is a <video controls
  // preload="none"> with its poster: nothing is loaded until it is played.

  function padVideo ( $source, $title, $opts ) {

    global $padCheckSyntax;

    $source = trim ( (string) $source );
    $video  = padVideoParse ( $source );

    if ( $video !== NULL )
      return padVideoEmbed ( $video, $source, $title, $opts );

    if ( ! preg_match ( '#^[a-z][a-z0-9+.-]*:#i', $source ) and preg_match ( '/\.(mp4|m4v|webm|ogv|mov)$/i', $source ) )
      return padVideoLocal ( $source, $title, $opts );

    if ( $padCheckSyntax )
      padError ( "the video tag knows no video at '" . padMakeSafe ( $source, 80 ) . "' - a YouTube or Vimeo address, or a .mp4, .webm, .ogv or .mov file of www/<application>/" );

    return '';

  }

  // youtube.com/watch?v=, youtu.be/, /shorts/, /embed/, /live/ and youtube-nocookie.com/
  // embed/ with an id of eleven; vimeo.com/<id>, vimeo.com/channels/x/<id>, .../video/<id>
  // and player.vimeo.com/video/<id>. A t= or start= in the address - or a #t= - is the
  // start time.

  function padVideoParse ( $url ) {

    $parts = parse_url ( $url );

    if ( ! $parts or ! in_array ( strtolower ( $parts ['scheme'] ?? '' ), [ 'http', 'https' ], TRUE ) )
      return NULL;

    $host = preg_replace ( '/^(www\.|m\.)/', '', strtolower ( $parts ['host'] ?? '' ) );
    $path = $parts ['path'] ?? '';

    parse_str ( $parts ['query']    ?? '', $query );
    parse_str ( $parts ['fragment'] ?? '', $mark  );

    $start = padVideoStart ( $query ['t'] ?? $query ['start'] ?? $mark ['t'] ?? '' );

    if ( in_array ( $host, [ 'youtube.com', 'youtube-nocookie.com', 'music.youtube.com' ], TRUE ) ) {

      if ( $path == '/watch' and is_string ( $query ['v'] ?? NULL ) and preg_match ( '/^[A-Za-z0-9_-]{11}$/D', $query ['v'] ) )
        return [ 'youtube', $query ['v'], $start, FALSE ];

      if ( preg_match ( '#^/(embed|shorts|live|v)/([A-Za-z0-9_-]{11})/?$#D', $path, $m ) )
        return [ 'youtube', $m [2], $start, $m [1] == 'shorts' ];

    }

    if ( $host == 'youtu.be' and preg_match ( '#^/([A-Za-z0-9_-]{11})/?$#D', $path, $m ) )
      return [ 'youtube', $m [1], $start, FALSE ];

    if ( $host == 'vimeo.com' and preg_match ( '#^/(?:(?:channels|groups)/[\w-]+/(?:videos/)?|video/)?(\d{4,12})/?$#D', $path, $m ) )
      return [ 'vimeo', $m [1], $start, FALSE ];

    if ( $host == 'player.vimeo.com' and preg_match ( '#^/video/(\d{4,12})/?$#D', $path, $m ) )
      return [ 'vimeo', $m [1], $start, FALSE ];

    return NULL;

  }

  function padVideoStart ( $value ) {

    $value = trim ( (string) ( is_array ( $value ) ? '' : $value ) );

    if ( ctype_digit ( $value ) )
      return (int) $value;

    if ( preg_match ( '/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/D', $value, $m ) and $value !== '' )
      return 3600 * (int) ( $m [1] ?? 0 ) + 60 * (int) ( $m [2] ?? 0 ) + (int) ( $m [3] ?? 0 );

    if ( preg_match ( '/^(?:(\d+):)?(\d{1,2}):(\d{2})$/D', $value, $m ) )
      return 3600 * (int) $m [1] + 60 * (int) $m [2] + (int) $m [3];

    return 0;

  }

  // The poster with its button. The iframe's address waits in data-pad-video; the
  // address of the video on its own site is the noscript link.

  function padVideoEmbed ( $video, $source, $title, $opts ) {

    list ( $site, $id, $start, $short ) = $video;

    $start = (int) ( $opts ['start'] ?? 0 ) ?: $start;

    if ( $site == 'youtube' ) {
      $embed = "https://www.youtube-nocookie.com/embed/$id?autoplay=1&rel=0" . ( $start ? "&start=$start" : '' );
      $watch = "https://www.youtube.com/watch?v=$id" . ( $start ? "&t={$start}s" : '' );
      $name  = 'YouTube';
      $thumb = "https://i.ytimg.com/vi/$id/hqdefault.jpg";
    } else {
      $embed = "https://player.vimeo.com/video/$id?autoplay=1&dnt=1" . ( $start ? "#t={$start}s" : '' );
      $watch = "https://vimeo.com/$id" . ( $start ? "#t={$start}s" : '' );
      $name  = 'Vimeo';
      $thumb = '';
    }

    $ratio  = padVideoRatio ( $opts ['ratio'] ?? '', $short ? '9:16' : '16:9' );
    $poster = padVideoPoster ( $opts ['poster'] ?? '', $thumb );
    $label  = padVideoAttr ( $title );

    $html = '<div class="pad-video pad-video-' . $site . '"' . padVideoRatioStyle ( $ratio ) . '>'
          . ( $poster !== '' ? '<img class="pad-video-poster" src="' . padVideoAttr ( $poster ) . '" alt="" loading="lazy" decoding="async">'
                             : padVideoDrawn () )
          . '<button type="button" class="pad-video-play" data-pad-video="' . padVideoAttr ( $embed ) . '"'
          . ' data-pad-title="' . $label . '">'
          . '<svg viewBox="0 0 68 48" width="68" height="48" aria-hidden="true" focusable="false">'
          . '<rect class="pad-video-disc" width="68" height="48" rx="14"/><path class="pad-video-arrow" d="M27 14v20l18-10z"/></svg>'
          . '<span class="pad-video-title"><span class="pad-video-hidden">Play </span>' . $label . '</span>'
          . '<span class="pad-video-site">Plays from ' . $name . '</span></button>'
          . '<noscript><a class="pad-video-link" href="' . padVideoAttr ( $watch ) . '">'
          . 'Watch “' . $label . '” on ' . $name . '</a></noscript>'
          . '</div>';

    return padVideoStyle ( TRUE ) . $html;

  }

  // A local file: the element with the browser's own controls, nothing loaded before play.

  function padVideoLocal ( $source, $title, $opts ) {

    $url = padAsset ( $source );

    if ( $url === '' )
      return '';

    $types  = [ 'mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'webm' => 'video/webm', 'ogv' => 'video/ogg', 'mov' => 'video/quicktime' ];
    $type   = $types [ strtolower ( pathinfo ( $source, PATHINFO_EXTENSION ) ) ];
    $poster = padVideoPoster ( $opts ['poster'] ?? '', '' );
    $ratio  = padVideoRatio ( $opts ['ratio'] ?? '', '16:9' );
    $label  = padVideoAttr ( $title );

    return padVideoStyle ( FALSE )
         . '<video class="pad-video pad-video-file" controls preload="none" playsinline'
         . padVideoRatioStyle ( $ratio )
         . ( $poster !== '' ? ' poster="' . padVideoAttr ( $poster ) . '"' : '' )
         . ' aria-label="' . $label . '">'
         . '<source src="' . padVideoAttr ( $url ) . "\" type=\"$type\">"
         . '<a href="' . padVideoAttr ( $url ) . '">Download “' . $label . '”</a>'
         . '</video>';

  }

  // ratio='4:3' (or 4/3); a short is upright.

  function padVideoRatio ( $ratio, $default ) {

    global $padCheckSyntax;

    $ratio = trim ( (string) $ratio );

    if ( $ratio === '' )
      $ratio = $default;

    if ( ! preg_match ( '#^(\d{1,4})\s*[:/]\s*(\d{1,4})$#D', $ratio, $m ) or ! (int) $m [1] or ! (int) $m [2] ) {
      if ( $padCheckSyntax )
        padError ( "the video tag's ratio is '" . padMakeSafe ( $ratio, 20 ) . "' - write it as 16:9" );
      return '16/9';
    }

    return (int) $m [1] . '/' . (int) $m [2];

  }

  // 16:9 is the rule's own; another proportion is said on the element, and an upright
  // one - a short - is kept to the width of a phone instead of the column's.

  function padVideoRatioStyle ( $ratio ) {

    if ( $ratio == '16/9' )
      return '';

    list ( $w, $h ) = explode ( '/', $ratio );

    return " style=\"aspect-ratio:$ratio" . ( $h > $w ? ';max-width:340px' : '' ) . '"';

  }

  // poster= is a picture of www/<application>/, versioned like an {asset}; without it the
  // site's thumbnail, when the site has one at a known address.

  function padVideoPoster ( $poster, $thumb ) {

    $poster = trim ( (string) $poster );

    if ( $poster === '' )
      return $thumb;

    return padAsset ( $poster );

  }

  // The poster of a video without one: a quiet field with a film frame, in the tag's colours.

  function padVideoDrawn () {

    return '<svg class="pad-video-poster pad-video-drawn" viewBox="0 0 160 90" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">'
         . '<rect width="160" height="90"/>'
         . '<path d="M0 64 Q40 44 80 60 T160 52 V90 H0Z"/><path d="M0 74 Q50 58 96 72 T160 66 V90 H0Z"/>'
         . '<circle cx="122" cy="26" r="9"/></svg>';

  }

  function padVideoAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The rules and - for an embed - the script, the first time a request asks: the colours are custom
  // properties with light-dark() steps on .pad-video. The script listens on the document,
  // so a video a fragment or a live region swaps in later plays as well; it carries the
  // request's nonce when $padCsp asks for one.

  function padVideoStyle ( $script ) {

    global $padCsp;

    static $written = [ 'style' => FALSE, 'script' => FALSE ];

    $html = '';

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . padVideoAttr ( padNonce () ) . '"' : '';

    $roles = [ 'surface' => [ '#e9e7e2', '#22221f' ], 'hill'   => [ '#d6d3cb', '#2f2f2b' ],
               'hill-2'  => [ '#c6c2b8', '#3a3a35' ], 'sun'    => [ '#f2c46b', '#a9853a' ],
               'button'  => [ 'rgba(18,18,18,.78)', 'rgba(0,0,0,.72)' ], 'button-hover' => [ '#e0322b', '#e0322b' ],
               'arrow'   => [ '#ffffff', '#ffffff' ], 'text' => [ '#ffffff', '#ffffff' ],
               'focus'   => [ '#2a78d6', '#7fb2f0' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-video-$role:$day;";
      $both  .= "--pad-video-$role:light-dark($day,$night);";
    }

    $css = ":where(.pad-video){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-video){{$both}}}"
         . '.pad-video{position:relative;display:block;aspect-ratio:16/9;width:100%;max-width:100%;overflow:hidden;border-radius:10px;background:var(--pad-video-surface);color:var(--pad-video-text)}'
         . 'video.pad-video{height:auto;background:#000}'
         . '.pad-video-poster,.pad-video iframe{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border:0;display:block}'
         . '.pad-video-drawn rect{fill:var(--pad-video-surface)}.pad-video-drawn path{fill:var(--pad-video-hill)}'
         . '.pad-video-drawn path+path{fill:var(--pad-video-hill-2)}.pad-video-drawn circle{fill:var(--pad-video-sun)}'
         . '.pad-video-play,.pad-video-link{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;'
         . 'width:100%;height:100%;margin:0;padding:12px;border:0;background:linear-gradient(transparent 55%,rgba(0,0,0,.55));color:var(--pad-video-text);'
         . 'font:600 15px/1.3 system-ui,sans-serif;text-align:center;text-decoration:none;cursor:pointer}'
         . '.pad-video-link{justify-content:flex-end;padding-bottom:18px;text-decoration:underline;text-shadow:0 1px 3px rgba(0,0,0,.7)}'
         . '.pad-video-play svg{flex:none;filter:drop-shadow(0 2px 6px rgba(0,0,0,.35))}'
         . '.pad-video-disc{fill:var(--pad-video-button);transition:fill .15s}.pad-video-arrow{fill:var(--pad-video-arrow)}'
         . '.pad-video-play:hover .pad-video-disc,.pad-video-play:focus-visible .pad-video-disc{fill:var(--pad-video-button-hover)}'
         . '.pad-video-play:focus-visible,.pad-video-link:focus-visible{outline:3px solid var(--pad-video-focus);outline-offset:-3px}'
         . '.pad-video-title{position:absolute;left:14px;right:14px;bottom:30px;text-shadow:0 1px 3px rgba(0,0,0,.6)}'
         . '.pad-video-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap}'
         . '.pad-video-site{position:absolute;left:14px;right:14px;bottom:12px;font-size:12px;font-weight:500;opacity:.85}'
         . '@media (scripting:none){.pad-video-play{display:none}}'
         . '@media print{.pad-video-play svg{display:none}}';

    $code = <<<'SCRIPT'
(function () {
  if (window.padVideoReady) return;
  window.padVideoReady = true;
  document.addEventListener('click', function (event) {
    var button = event.target.closest && event.target.closest('.pad-video-play');
    if (!button) return;
    event.preventDefault();
    var frame = document.createElement('iframe');
    frame.src = button.getAttribute('data-pad-video');
    frame.title = button.getAttribute('data-pad-title');
    frame.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
    frame.allowFullscreen = true;
    frame.referrerPolicy = 'strict-origin-when-cross-origin';
    var box = button.parentNode;
    box.querySelectorAll('.pad-video-poster').forEach(function (poster) { poster.remove(); });
    box.replaceChild(frame, button);
    frame.focus();
  });
})();
SCRIPT;

    if ( ! $written ['style'] )
      $html .= "<style$nonce>$css</style>";

    if ( $script and ! $written ['script'] )
      $html .= "<script$nonce>$code</script>";

    $written ['style']  = TRUE;
    $written ['script'] = $written ['script'] || $script;

    return $html;

  }

?>
