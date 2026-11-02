<?php

  // The embed subsystem: a video, a sound, a map, a pen or a document of another site in a
  // page - without the page asking anything of that site before the visitor does.
  //
  //   {embed:auto 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'/}
  //   {embed:vimeo '76979871', title='The new Vimeo player'/}
  //   {embed:openstreetmap '52.3731,4.8922', zoom=15, title='Dam Square'/}
  //   {embed:audio 'sounds/bell.mp3', title='The bell'/}
  //
  // A provider is a file of subs/embeds/types/: the files there are the providers there are
  // (padEmbedProviders), the {embed:auto} tag picks the one that knows the address it is given,
  // the embed: type names one - {embed:youtube 'id'}, or bare, {embed:youtube 'id'}, when nothing
  // else has the name - and {pad:video} is the video form of the same providers, its markup kept
  // as it was (subs/embeds/_lib/video.php). A provider's file answers an array:
  //
  //   name      the provider as the visitor reads it - YouTube
  //   kind      video, audio, map, code or document: the drawn poster, the icon and the
  //             words of the button - "Play", "Plays from YouTube", "Watch ... on YouTube"
  //   frame     the origins its frame is loaded from, for the frame-src of a Content
  //             Security Policy (padEmbedSources) - '' where they are the page's own or any
  //   generic   TRUE for a provider that knows its addresses by their shape alone - a
  //             PeerTube instance, a file by its extension - tried after those that know
  //             theirs by their host
  //   options   the options it reads, beside the ones every embed has
  //   match     fn ( $source, $named ) - the address read: an array of its parts, or NULL
  //             when it is none of the provider's; $named when the provider was named, so a
  //             bare id is the provider's to read as well
  //   embed     fn ( $parts, $opts ) - what is written: [ mode, src, link, thumb, ratio,
  //             allow, sandbox, type, name ], the mode frame (a facade, the frame loaded on
  //             a click), page (a frame of the application's own, loaded lazily), video or
  //             audio (an element that loads nothing before it is played); sandbox the
  //             frame's sandbox tokens where the provider's own embed code has them - the
  //             frame then may do only what they allow; name the site the click loads from
  //             where that is not the provider - a PeerTube instance, the host of a PDF - and
  //             src '' when there is nothing to write
  //
  // A frame of another site is a facade first: a poster - drawn for its kind, or poster= a
  // picture of www/<application>/, or with thumbnail the provider's own picture - the title,
  // the provider's name and a button; pressing it puts the frame in its place, by a script
  // written once per page with the CSP nonce, and moves the focus into it. Without script
  // the button is hidden (@media (scripting:none)) and a link to the address on its own
  // site stands in its place, inside <noscript>. Nothing of the provider is loaded before
  // the click - with thumbnail, its picture only.
  //
  // The colours are custom properties with light-dark() defaults on .pad-embed; the rules
  // and the script go out once per page (padWidgetStyle, padWidgetScript), a proportion
  // other than 16:9 as a class with its rule, so no style attribute needs 'unsafe-inline'.
  //
  // padEmbedProviders, padEmbedProvider and padEmbedSources - the providers, one provider's
  // file, the origins a Content Security Policy allows for their frames - are in
  // lib/embed.php, loaded on every request: an application's _config/config.php writes its
  // $padCsp with them.

  // The provider that knows an address, and the parts it read from it: [ name, parts ], or
  // NULL. The providers that know their host come first, the generic ones after them; of
  // $among only those.

  function padEmbedFind ( $source, $among = NULL ) {

    $source = trim ( (string) $source );

    foreach ( [ FALSE, TRUE ] as $generic )
      foreach ( $among ?? padEmbedProviders () as $name ) {
        $provider = padEmbedProvider ( $name );
        if ( $provider and $provider ['generic'] === $generic and ( $parts = $provider ['match'] ( $source, FALSE ) ) !== NULL )
          return [ $name, $parts ];
      }

    return NULL;

  }

  // An address of the web: its parts as parse_url gives them, the host without www. or m.,
  // the query and the fragment read as parameters - or NULL for what is no http(s) address.

  function padEmbedUrl ( $url ) {

    $parts = parse_url ( (string) $url );

    if ( ! $parts or ! in_array ( strtolower ( $parts ['scheme'] ?? '' ), [ 'http', 'https' ], TRUE ) or ! isset ( $parts ['host'] ) )
      return NULL;

    parse_str ( $parts ['query']    ?? '', $query );
    parse_str ( $parts ['fragment'] ?? '', $mark  );

    return [ 'host'  => preg_replace ( '/^(www\.|m\.)/', '', strtolower ( $parts ['host'] ) ),
             'path'  => $parts ['path'] ?? '',
             'query' => $query,
             'mark'  => $mark,
             'url'   => (string) $url ];

  }

  // A file of www/<application>/ or of the web by its extension: [ url, extension ], the
  // application's file versioned as an {pad:asset} - or NULL when the name has none of them.

  function padEmbedFile ( $source, $extensions ) {

    $source = trim ( (string) $source );
    $path   = parse_url ( $source, PHP_URL_PATH ) ?? '';
    $ext    = strtolower ( pathinfo ( (string) $path, PATHINFO_EXTENSION ) );

    if ( ! in_array ( $ext, $extensions, TRUE ) )
      return NULL;

    if ( preg_match ( '#^[a-z][a-z0-9+.-]*:#i', $source ) )
      return padEmbedUrl ( $source ) ? [ 'url' => $source, 'ext' => $ext, 'local' => FALSE ] : NULL;

    return [ 'url' => $source, 'ext' => $ext, 'local' => TRUE ];

  }

  // The host names the page is shown under, for a player that must be told them - Twitch's
  // parent=: the names of $padHosts, the names this server answers to, when it lists them;
  // else the host of $padHostBase or of the request. Offline (pad pack) and in a static copy
  // (pad export) the page may be anywhere, so only $padHosts says - without it there is no
  // name, and no player made up for one. $more adds names of the tag's own - a page shown in
  // a frame of another site; a name that is none is an error under the strict check.

  function padEmbedHosts ( $more, $tag ) {

    global $padHosts, $padHostBase, $padHost, $padExportRun, $padCheckSyntax;

    $names = [];
    $name  = '/^(?=.{1,253}$)[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*$/D';

    if ( count ( $padHosts ?? [] ) )
      $names = array_map ( fn ( $host ) => preg_replace ( '/:[0-9]+$/', '', strtolower ( (string) $host ) ), $padHosts );
    elseif ( ! ( $padExportRun ?? FALSE ) and ! padWasm () )
      $names = [ strtolower ( (string) parse_url ( ( $padHostBase ?? '' ) !== '' ? $padHostBase : (string) ( $padHost ?? '' ), PHP_URL_HOST ) ) ];

    $names = array_filter ( $names, fn ( $one ) => preg_match ( $name, $one ) );

    foreach ( preg_split ( '/[\s,]+/', strtolower ( is_scalar ( $more ) ? (string) $more : '' ), -1, PREG_SPLIT_NO_EMPTY ) as $one )
      if ( preg_match ( $name, $one ) )
        $names [] = $one;
      elseif ( $padCheckSyntax )
        padError ( "the $tag embed has no host name '" . padMakeSafe ( $one, 40 ) . "' in domains= - write example.com, a name a comma" );

    return array_values ( array_unique ( $names ) );

  }

  // A moment - 90, 1m30s, 1:30 - in seconds; 0 for anything else.

  function padEmbedStart ( $value ) {

    $value = trim ( (string) ( is_array ( $value ) ? '' : $value ) );

    if ( ctype_digit ( $value ) )
      return (int) $value;

    if ( preg_match ( '/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/D', $value, $m ) and $value !== '' )
      return 3600 * (int) ( $m [1] ?? 0 ) + 60 * (int) ( $m [2] ?? 0 ) + (int) ( $m [3] ?? 0 );

    if ( preg_match ( '/^(?:(\d+):)?(\d{1,2}):(\d{2})$/D', $value, $m ) )
      return 3600 * (int) $m [1] + 60 * (int) $m [2] + (int) $m [3];

    return 0;

  }

  // The embed, written: the provider and the parts its match read, the title, and the
  // options - poster, ratio, thumbnail and the provider's own.

  function padEmbed ( $name, $parts, $title, $opts ) {

    $provider = padEmbedProvider ( $name );
    $embed    = $provider ['embed'] ( $parts, $opts ) + [ 'mode' => 'frame', 'link' => '', 'thumb' => '', 'ratio' => '16:9', 'allow' => '', 'sandbox' => '',
                                                         'type' => '', 'name' => $provider ['name'] ];

    if ( ( $embed ['mode'] == 'link' ? $embed ['link'] : $embed ['src'] ) === '' )
      return '';

    $ratio = padEmbedRatio ( $opts ['ratio'] ?? '', $embed ['ratio'] );
    $label = padWidgetAttr ( $title );
    $class = "pad-embed pad-embed-$name" . padEmbedRatioClass ( $ratio, $provider ['kind'] == 'video' );

    if ( $embed ['mode'] == 'video' or $embed ['mode'] == 'audio' )
      return padEmbedMedia ( $embed, $class, $label, $opts );

    if ( $embed ['mode'] == 'page' )
      return padEmbedStyle () . "<div class=\"$class\">"
           . '<iframe src="' . padWidgetAttr ( $embed ['src'] ) . "\" title=\"$label\" loading=\"lazy\"></iframe></div>"
           . '<a class="pad-embed-open" href="' . padWidgetAttr ( $embed ['link'] ?: $embed ['src'] ) . '">' . padEmbedWords ( $provider ['kind'], $embed ['name'] ) [2] . " “{$label}”</a>";

    return padEmbedFacade ( $provider, $embed, $class, $label, $opts );

  }

  // A frame of another site: the poster with its button, the frame's address waiting in
  // data-pad-embed; the address on the provider's own site is the noscript link. The mode
  // link - a frame that cannot work where the page is shown, a Twitch player without the
  // host names it must be told - is the poster with that link alone, script or not.

  function padEmbedFacade ( $provider, $embed, $class, $label, $opts ) {

    list ( $verb, $site, $open ) = padEmbedWords ( $provider ['kind'], $embed ['name'] );

    $poster = trim ( (string) ( $opts ['poster'] ?? '' ) );
    $poster = ( $poster !== '' ) ? padAsset ( $poster ) : ( ( $opts ['thumbnail'] ?? FALSE ) ? $embed ['thumb'] : '' );
    $poster = ( $poster !== '' ) ? '<img class="pad-embed-poster" src="' . padWidgetAttr ( $poster ) . '" alt="" loading="lazy" decoding="async">'
                                 : padEmbedDrawn ( $provider ['kind'] );
    $link   = '<a class="pad-embed-link" href="' . padWidgetAttr ( $embed ['link'] ) . "\">$open “{$label}” on " . padWidgetAttr ( $embed ['name'] ) . '</a>';

    if ( $embed ['mode'] == 'link' )
      return padEmbedStyle () . "<div class=\"$class\">$poster$link</div>";

    return padEmbedStyle ( TRUE )
         . "<div class=\"$class\">"
         . $poster
         . '<button type="button" class="pad-embed-load" data-pad-embed="' . padWidgetAttr ( $embed ['src'] ) . '"'
         . " data-pad-title=\"$label\"" . ( $embed ['allow'] !== '' ? ' data-pad-allow="' . padWidgetAttr ( $embed ['allow'] ) . '"' : '' )
         . ( $embed ['sandbox'] !== '' ? ' data-pad-sandbox="' . padWidgetAttr ( $embed ['sandbox'] ) . '"' : '' ) . '>'
         . padEmbedIcon ( $provider ['kind'] )
         . "<span class=\"pad-embed-title\"><span class=\"pad-embed-hidden\">$verb </span>$label</span>"
         . '<span class="pad-embed-site">' . $site . '</span></button>'
         . ( $embed ['link'] !== '' ? "<noscript>$link</noscript>" : '' )
         . '</div>';

  }

  // A file - of the application, or of the web - as the element of its kind, with the
  // browser's own controls and nothing loaded before it is played.

  function padEmbedMedia ( $embed, $class, $label, $opts ) {

    $url    = $embed ['src'];
    $poster = trim ( (string) ( $opts ['poster'] ?? '' ) );
    $poster = ( $poster !== '' and $embed ['mode'] == 'video' ) ? padAsset ( $poster ) : '';
    $type   = $embed ['type'] !== '' ? ' type="' . padWidgetAttr ( $embed ['type'] ) . '"' : '';
    $source = '<source src="' . padWidgetAttr ( $url ) . "\"$type>"
            . '<a href="' . padWidgetAttr ( $url ) . "\">Download “{$label}”</a>";

    // A sound has nothing to see: its title stands under the player, and names it.

    if ( $embed ['mode'] == 'audio' ) {
      $id = padWidgetId ( 'embed', [ $url, $label ] );
      return padEmbedStyle ()
           . "<figure class=\"pad-embed-sound\"><audio class=\"pad-embed-audio\" controls preload=\"none\" aria-labelledby=\"$id\">$source</audio>"
           . "<figcaption id=\"$id\">$label</figcaption></figure>";
    }

    return padEmbedStyle ()
         . "<video class=\"$class\" controls preload=\"none\" playsinline"
         . ( $poster !== '' ? ' poster="' . padWidgetAttr ( $poster ) . '"' : '' )
         . " aria-label=\"$label\">$source</video>";

  }

  // What the button and the link say, by the kind: the hidden verb before the title, the
  // line under it, and the words of the link that stands in for the button without script.

  function padEmbedWords ( $kind, $name ) {

    $name = padWidgetAttr ( $name );

    return match ( $kind ) {
      'audio'    => [ 'Play', "Plays from $name",  'Listen to' ],
      'map'      => [ 'Show', "Loads from $name",  'View' ],
      'code'     => [ 'Run',  "Loads from $name",  'Open' ],
      'document' => [ 'Show', "Loads from $name",  'Open' ],
      default    => [ 'Play', "Plays from $name",  'Watch' ],
    };

  }

  // ratio='4:3' (or 4/3) - a proportion of two whole numbers; '16:9' unless the provider
  // says otherwise.

  function padEmbedRatio ( $ratio, $default ) {

    global $padCheckSyntax;

    $ratio = trim ( (string) $ratio );

    if ( $ratio === '' )
      $ratio = $default;

    if ( ! preg_match ( '#^(\d{1,4})\s*[:/]\s*(\d{1,4})$#D', $ratio, $m ) or ! (int) $m [1] or ! (int) $m [2] ) {
      if ( $padCheckSyntax )
        padError ( "the embed's ratio is '" . padMakeSafe ( $ratio, 20 ) . "' - write it as 16:9" );
      return [ 16, 9 ];
    }

    return [ (int) $m [1], (int) $m [2] ];

  }

  // A proportion other than 16:9 is a class of its own, its rule written once per page; an
  // upright video - a short - is kept to the width of a phone instead of the column's.

  function padEmbedRatioClass ( $ratio, $video ) {

    list ( $w, $h ) = $ratio;

    if ( $w * 9 == $h * 16 )
      return '';

    $class = "pad-embed-r{$w}x{$h}";

    $GLOBALS ['padEmbedRatios'] [$class] = ".pad-embed.$class{aspect-ratio:$w/$h}";

    return " $class" . ( ( $video and $h > $w ) ? ' pad-embed-upright' : '' );

  }

  // The poster of a frame without a picture: a quiet field drawn for its kind - hills and a
  // sun for a video, the bars of a sound, the streets of a map, the lines of a pen, a page.

  function padEmbedDrawn ( $kind ) {

    $drawn = match ( $kind ) {
      'audio'    => '<rect width="160" height="90"/><path d="M38 52v-14M50 60v-30M62 56v-22M74 66v-42M86 58v-26M98 62v-34M110 54v-18M122 50v-10"/>',
      'map'      => '<rect width="160" height="90"/><path d="M0 30 Q60 22 160 40M0 70 Q70 58 160 76M40 0 Q52 50 30 90M112 0 Q100 46 124 90"/><circle cx="118" cy="24" r="7"/>',
      'code'     => '<rect width="160" height="90"/><path d="M60 30 L44 45 L60 60M100 30 L116 45 L100 60M86 26 L74 64"/>',
      'document' => '<rect width="160" height="90"/><path d="M62 14 H92 L104 26 V76 H62 Z"/><path d="M70 36 H96M70 46 H96M70 56 H90"/>',
      default    => '<rect width="160" height="90"/><path d="M0 64 Q40 44 80 60 T160 52 V90 H0Z"/><path d="M0 74 Q50 58 96 72 T160 66 V90 H0Z"/><circle cx="122" cy="26" r="9"/>',
    };

    return '<svg class="pad-embed-poster pad-embed-drawn pad-embed-drawn-' . $kind . '" viewBox="0 0 160 90" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">'
         . $drawn . '</svg>';

  }

  // The icon of the button: a play arrow for what plays, an arrow into the frame else.

  function padEmbedIcon ( $kind ) {

    $sign = in_array ( $kind, [ 'video', 'audio' ], TRUE )
          ? '<path class="pad-embed-arrow" d="M27 14v20l18-10z"/>'
          : '<path class="pad-embed-arrow" d="M24 24h20M36 16l8 8-8 8" fill="none" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>';

    return '<svg viewBox="0 0 68 48" width="68" height="48" aria-hidden="true" focusable="false">'
         . '<rect class="pad-embed-disc" width="68" height="48" rx="14"/>' . $sign . '</svg>';

  }

  // The rules and - for a facade - the script, once per page: the script listens on the
  // document, so an embed a fragment or a live region brings later loads as well. Then the
  // rules of the proportions the page used, each once.

  function padEmbedStyle ( $script = FALSE ) {

    $html = padWidgetStyle ( 'embed', [
              'surface' => [ '#e9e7e2', '#22221f' ], 'hill'   => [ '#d6d3cb', '#2f2f2b' ],
              'hill-2'  => [ '#c6c2b8', '#3a3a35' ], 'accent' => [ '#f2c46b', '#a9853a' ],
              'button'  => [ 'rgba(18,18,18,.78)', 'rgba(0,0,0,.72)' ], 'button-hover' => [ '#2a78d6', '#3987e5' ],
              'arrow'   => [ '#ffffff', '#ffffff' ], 'text' => [ '#ffffff', '#ffffff' ],
              'caption' => [ '#52514e', '#c3c2b7' ], 'focus' => [ '#2a78d6', '#7fb2f0' ] ],
                 '.pad-embed{position:relative;display:block;aspect-ratio:16/9;width:100%;max-width:100%;overflow:hidden;border-radius:10px;background:var(--pad-embed-surface);color:var(--pad-embed-text)}'
               . 'video.pad-embed{height:auto;background:#000}.pad-embed.pad-embed-upright{max-width:340px}'
               . '.pad-embed-poster,.pad-embed iframe{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border:0;display:block}'
               . '.pad-embed-drawn rect{fill:var(--pad-embed-surface)}.pad-embed-drawn path{fill:var(--pad-embed-hill)}'
               . '.pad-embed-drawn path+path{fill:var(--pad-embed-hill-2)}.pad-embed-drawn circle{fill:var(--pad-embed-accent)}'
               . '.pad-embed-drawn-audio path,.pad-embed-drawn-map path,.pad-embed-drawn-code path{fill:none;stroke:var(--pad-embed-hill-2);stroke-width:6;stroke-linecap:round;stroke-linejoin:round}'
               . '.pad-embed-drawn-document path{fill:var(--pad-embed-hill);stroke:none}.pad-embed-drawn-document path+path{fill:none;stroke:var(--pad-embed-hill-2);stroke-width:3;stroke-linecap:round}'
               . '.pad-embed-load,.pad-embed-link{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;'
               . 'box-sizing:border-box;width:100%;height:100%;margin:0;padding:12px;border:0;background:linear-gradient(transparent 55%,rgba(0,0,0,.55));color:var(--pad-embed-text);'
               . 'font:600 15px/1.3 system-ui,sans-serif;text-align:center;text-decoration:none;cursor:pointer}'
               . '.pad-embed-link{justify-content:flex-end;padding-bottom:18px;text-decoration:underline;text-shadow:0 1px 3px rgba(0,0,0,.7)}'
               . '.pad-embed-load svg{flex:none;filter:drop-shadow(0 2px 6px rgba(0,0,0,.35))}'
               . '.pad-embed-disc{fill:var(--pad-embed-button);transition:fill .15s}.pad-embed-arrow{fill:var(--pad-embed-arrow);stroke:var(--pad-embed-arrow)}'
               . '.pad-embed-load:hover .pad-embed-disc,.pad-embed-load:focus-visible .pad-embed-disc{fill:var(--pad-embed-button-hover)}'
               . '.pad-embed-load:focus-visible,.pad-embed-link:focus-visible,.pad-embed-open:focus-visible{outline:3px solid var(--pad-embed-focus);outline-offset:-3px}'
               . '.pad-embed-title{position:absolute;left:14px;right:14px;bottom:30px;text-shadow:0 1px 3px rgba(0,0,0,.6)}'
               . '.pad-embed-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap}'
               . '.pad-embed-site{position:absolute;left:14px;right:14px;bottom:12px;font-size:12px;font-weight:500;opacity:.85}'
               . '.pad-embed-open{display:inline-block;margin:6px 0 0;font:500 13px/1.3 system-ui,sans-serif}'
               . '.pad-embed-sound{margin:0}.pad-embed-audio{display:block;width:100%}'
               . '.pad-embed-sound figcaption{margin:6px 0 0;font:500 13px/1.3 system-ui,sans-serif;color:var(--pad-embed-caption)}'
               . '@media (scripting:none){.pad-embed-load{display:none}}'
               . '@media print{.pad-embed-load svg{display:none}}' );

    $code = <<<'SCRIPT'
(function () {
  if (window.padEmbedReady) return;
  window.padEmbedReady = true;
  document.addEventListener('click', function (event) {
    var button = event.target.closest && event.target.closest('.pad-embed-load');
    if (!button) return;
    event.preventDefault();
    var frame = document.createElement('iframe');
    if (button.hasAttribute('data-pad-sandbox')) frame.setAttribute('sandbox', button.getAttribute('data-pad-sandbox'));
    frame.src = button.getAttribute('data-pad-embed');
    frame.title = button.getAttribute('data-pad-title');
    if (button.hasAttribute('data-pad-allow')) frame.allow = button.getAttribute('data-pad-allow');
    frame.allowFullscreen = true;
    frame.referrerPolicy = 'strict-origin-when-cross-origin';
    var box = button.parentNode;
    box.querySelectorAll('.pad-embed-poster').forEach(function (poster) { poster.remove(); });
    box.replaceChild(frame, button);
    frame.focus();
  });
})();
SCRIPT;

    if ( $script )
      $html .= padWidgetScript ( 'embed', $code );

    foreach ( $GLOBALS ['padEmbedRatios'] ?? [] as $class => $rule )
      if ( padWidgetOnce ( "style:$class" ) )
        $html .= padProtect ( '<style' . padStyleNonce () . ">$rule</style>" );

    $GLOBALS ['padEmbedRatios'] = [];

    return $html;

  }

?>
