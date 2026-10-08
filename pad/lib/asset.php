<?php

  // Asset versioning: the address of a stylesheet, a script or an image beside the entry
  // point - www/<application>/ - with ?v= and a hash of the file's contents, the way
  // Laravel's Vite manifest, Rails' digests and Django's ManifestStaticFilesStorage write
  // theirs. A changed file is a new address, so the server may tell browsers to keep these
  // files for a year: they never hold on to an old copy after a deploy.
  //
  // padAsset     /<application>/charts.css?v=3f9a1c02be - the address, root-relative
  // padAssetTag  the element for it by its extension: <link rel="stylesheet" href> for
  //              .css, <script src defer> for .js, <script type="module" src> for .mjs -
  //              with this request's nonce when $padCsp asks for one ('nonce')
  //
  // padAssetHash / padAssetName are the private helpers below them.
  //
  // The name is a path below www/<application>/ - charts.css, js/app.js - and never climbs
  // out of it: no .., no leading /, no backslash, and the real path must still be inside.
  // A file that is not there answers the plain address, without a version, and is an error
  // under the strict check: a typo in a template is a stylesheet that silently never loads.
  //
  // The hash is of the contents, not of the file time - a deploy that copies files anew
  // gives every file a new time, and a time kept by rsync -t could hide a change - made
  // with xxh128 (fast enough for a large bundle on every request) and kept per request,
  // so a file written twice on a page is read once. The first ten hex digits are enough to
  // tell versions of one file apart. {asset 'charts.css'} and {asset 'app.js', tag} are
  // the template's forms.

  function padAsset ( $file ) {

    global $padApp, $padRoot;

    $name = padAssetName ( $file, 'padAsset' );

    if ( $name === NULL )
      return '';

    $url  = ( $padRoot ?? '/' ) . "$padApp/" . implode ( '/', array_map ( 'rawurlencode', explode ( '/', $name ) ) );
    $hash = padAssetHash ( $name );

    return ( $hash === '' ) ? $url : "$url?v=$hash";

  }

  function padAssetTag ( $file ) {

    global $padCsp;

    $type = is_string ( $file ) ? strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) ) : '';

    if ( is_string ( $file ) and ! in_array ( $type, [ 'css', 'js', 'mjs' ], TRUE ) ) {
      padError ( "padAssetTag: there is no element for a .$type file - .css, .js and .mjs have one; padAsset gives the address" );
      return '';
    }

    $url = padAsset ( $file );

    if ( $url === '' )
      return '';

    $href  = htmlspecialchars ( $url, ENT_QUOTES );
    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    if ( $type == 'css' ) return "<link rel=\"stylesheet\" href=\"$href\"$nonce>";
    if ( $type == 'js'  ) return "<script src=\"$href\" defer$nonce></script>";

    return "<script type=\"module\" src=\"$href\"$nonce></script>";

  }

  // ------------------------------------------------------------------------------------
  // The private helpers.
  // ------------------------------------------------------------------------------------

  // A name that stays inside www/<application>/, or NULL after saying why.

  function padAssetName ( $file, $function ) {

    if ( ! is_string ( $file ) or trim ( $file ) === '' ) {
      padError ( "$function: the file is a name below www/<application>/, not " . padRequestShow ( $file ) );
      return NULL;
    }

    $name = trim ( $file );

    if ( ! preg_match ( '#^[A-Za-z0-9_@+~-][A-Za-z0-9_.@+~/-]*$#D', $name )
         or in_array ( '..', explode ( '/', $name ), TRUE ) or str_contains ( $name, '//' ) ) {
      padError ( "$function: '" . padMakeSafe ( $name, 80 ) . "' is no file name below www/<application>/" );
      return NULL;
    }

    return $name;

  }

  // The version of a file: ten hex digits of its contents' hash, '' when it is not there.

  function padAssetHash ( $name ) {

    global $padApp, $padCheckSyntax;

    static $memo = [];

    if ( isset ( $memo [$padApp] [$name] ) )
      return $memo [$padApp] [$name];

    $dir  = realpath ( dirname ( APPS ) . "/www/$padApp" );
    $path = ( $dir === FALSE ) ? FALSE : realpath ( "$dir/$name" );
    $hash = '';

    if ( $path !== FALSE and str_starts_with ( $path, $dir . DIRECTORY_SEPARATOR ) and is_file ( $path ) ) {
      $algo = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
      $hash = substr ( (string) hash_file ( $algo, $path ), 0, 10 );
    } elseif ( $padCheckSyntax )
      padError ( "padAsset: there is no file www/$padApp/$name" );

    return $memo [$padApp] [$name] = $hash;

  }

?>
