<?php

  // Vite builds: the scripts and stylesheets a bundler made, linked from a PAD template by the
  // name of their source - {vite 'src/main.js'} - so the page never names a hashed file.
  //
  // In production Vite writes the build into www/<app>/<$padViteBuild>/ (build.outDir) with a
  // manifest at .vite/manifest.json (build.manifest): per source file the file it became, the
  // CSS it brought and the chunks it imports. {vite} writes, for each entry it names:
  //
  //   <link rel="stylesheet">     the CSS of the entry and of every chunk it imports
  //   <link rel="modulepreload">  every chunk it imports, so the browser fetches them at once
  //   <script type="module">      the entry itself
  //
  // While the dev server runs it is asked instead - its @vite/client for hot module
  // replacement and the sources as they are; with react, first the preamble the React plugin
  // needs for fast refresh. The dev server is $padViteDev when it is set, else the address in
  // the file hot of the build directory: the dev server's configureServer hook writes it when
  // it starts and removes it when it stops, so a page uses the build again by itself.
  //
  // padViteTags      the tags of the entries, for the request's application - once a page,
  //                  or with $once FALSE as they would be written, to show them
  // padViteDev       the dev server's address, '' when there is none
  // padViteManifest  the manifest, read once a request
  // padViteChunk     the CSS and the chunks of one entry, every import followed once
  // padViteNonce     the nonce attribute a script gets when the policy asks for one
  //
  // A file goes out once a page however many entries name it. The addresses are the build's
  // public path: $padRoot, the application, the build directory - for a vite.config.js with
  // base: '/<mount>/<app>/build/' or relative paths ('./').

  function padViteTags ( $entries, $react = FALSE, $once = TRUE ) {

    global $padRoot, $padApp, $padViteBuild;

    static $page = [];

    // What a page's PHP asks to show - $once FALSE - is not what the page wrote: the {vite}
    // of the template still writes it.

    $local = [];

    if ( $once )
      $written = &$page;
    else
      $written = &$local;

    $dev  = padViteDev ();
    $html = '';

    if ( $dev !== '' ) {

      $dev = rtrim ( $dev, '/' ) . '/';

      if ( ! isset ( $written ['@client'] ) ) {

        $written ['@client'] = TRUE;

        if ( $react )
          $html .= '<script type="module"' . padViteNonce () . ">\n"
                 . "  import RefreshRuntime from '" . $dev . "@react-refresh'\n"
                 . "  RefreshRuntime.injectIntoGlobalHook(window)\n"
                 . "  window.\$RefreshReg\$ = () => {}\n"
                 . "  window.\$RefreshSig\$ = () => (type) => type\n"
                 . "  window.__vite_plugin_react_preamble_installed__ = true\n"
                 . "</script>\n";

        $html .= '<script type="module" src="' . htmlspecialchars ( $dev . '@vite/client' ) . '"' . padViteNonce () . "></script>\n";

      }

      foreach ( $entries as $entry )
        if ( ! isset ( $written [$entry] ) ) {
          $written [$entry] = TRUE;
          $html .= '<script type="module" src="' . htmlspecialchars ( $dev . ltrim ( $entry, '/' ) ) . '"' . padViteNonce () . "></script>\n";
        }

      return $html;

    }

    $manifest = padViteManifest ();

    if ( $manifest === NULL )
      return '';

    $base    = $padRoot . $padApp . '/' . trim ( $padViteBuild, '/' ) . '/';
    $styles  = $preloads = $scripts = '';

    foreach ( $entries as $entry ) {

      if ( ! isset ( $manifest [$entry] ) ) {
        padError ( "{vite} has no entry '" . padMakeSafe ( $entry, 60 ) . "' in the manifest of the build - an input of build.rollupOptions, written as in vite.config.js" );
        continue;
      }

      [ $css, $chunks ] = padViteChunk ( $manifest, $entry );

      foreach ( $css as $file )
        if ( ! isset ( $written ["css:$file"] ) ) {
          $written ["css:$file"] = TRUE;
          $styles .= '<link rel="stylesheet" href="' . htmlspecialchars ( $base . $file ) . "\">\n";
        }

      foreach ( $chunks as $file )
        if ( ! isset ( $written ["js:$file"] ) ) {
          $written ["js:$file"] = TRUE;
          $preloads .= '<link rel="modulepreload" href="' . htmlspecialchars ( $base . $file ) . "\">\n";
        }

      $file = $manifest [$entry] ['file'];

      if ( ! isset ( $written ["js:$file"] ) ) {
        $written ["js:$file"] = TRUE;
        $scripts .= '<script type="module" src="' . htmlspecialchars ( $base . $file ) . '"' . padViteNonce () . "></script>\n";
      }

    }

    return $styles . $preloads . $scripts;

  }

  function padViteDev () {

    global $padViteDev, $padApp, $padViteBuild;

    if ( is_string ( $padViteDev ) and trim ( $padViteDev ) !== '' )
      return trim ( $padViteDev );

    $hot = dirname ( APPS ) . "/www/$padApp/" . trim ( $padViteBuild, '/' ) . '/hot';

    if ( ! is_file ( $hot ) )
      return '';

    $url = trim ( (string) file_get_contents ( $hot ) );

    return preg_match ( '#^https?://[^\s"<>]+$#i', $url ) ? $url : '';

  }

  function padViteManifest () {

    global $padApp, $padViteBuild;

    static $manifests = [];

    $file = dirname ( APPS ) . "/www/$padApp/" . trim ( $padViteBuild, '/' ) . '/.vite/manifest.json';

    if ( array_key_exists ( $file, $manifests ) )
      return $manifests [$file];

    $data = is_file ( $file ) ? json_decode ( (string) file_get_contents ( $file ), TRUE ) : NULL;

    if ( ! is_array ( $data ) ) {
      padError ( "{vite} has no manifest - www/$padApp/" . trim ( $padViteBuild, '/' ) . "/.vite/manifest.json is " . ( is_file ( $file ) ? 'no JSON' : 'not there' )
               . ": run vite build with build.manifest on, or start the dev server" );
      $data = NULL;
    }

    return $manifests [$file] = $data;

  }

  // The CSS and the chunks an entry needs: its own CSS, then for every chunk it imports - and
  // every chunk those import - the chunk and its CSS, each once. A dynamic import is left to
  // load when it is asked for.

  function padViteChunk ( $manifest, $entry ) {

    $css    = [];
    $chunks = [];
    $seen   = [];
    $todo   = [ $entry ];

    while ( $todo ) {

      $name = array_shift ( $todo );

      if ( isset ( $seen [$name] ) or ! isset ( $manifest [$name] ) )
        continue;

      $seen [$name] = TRUE;
      $one          = $manifest [$name];

      if ( $name !== $entry )
        $chunks [] = $one ['file'];

      foreach ( $one ['css'] ?? [] as $file )
        $css [] = $file;

      foreach ( $one ['imports'] ?? [] as $import )
        $todo [] = $import;

    }

    return [ array_values ( array_unique ( $css ) ), array_values ( array_unique ( $chunks ) ) ];

  }

  function padViteNonce () {

    global $padCsp;

    return str_contains ( (string) $padCsp, "'nonce'" ) ? ' nonce="' . padNonce () . '"' : '';

  }

?>
