<?php

  // PHP as it runs the pages: the version and the server API, the settings that matter to
  // a PAD installation, OPcache, the extensions, and phpinfo () itself.

  $title = 'PHP';

  $iniRows = [];

  foreach ( [ 'memory_limit', 'max_execution_time', 'max_input_time', 'max_input_vars', 'post_max_size',
              'upload_max_filesize', 'max_file_uploads', 'display_errors', 'log_errors', 'error_log',
              'error_reporting', 'date.timezone', 'default_charset', 'session.save_handler', 'session.save_path',
              'session.gc_maxlifetime', 'session.cookie_secure', 'session.cookie_httponly', 'session.cookie_samesite',
              'opcache.enable', 'opcache.validate_timestamps', 'opcache.revalidate_freq', 'realpath_cache_size',
              'allow_url_fopen', 'disable_functions', 'open_basedir', 'zend.assertions', 'xdebug.mode' ] as $name ) {

    $value = ini_get ( $name );

    if ( $value === FALSE )
      continue;

    $iniRows [] = [ 'name' => $name, 'value' => $value === '' ? '(empty)' : $value ];

  }

  // The extensions PAD reaches for, whether they are there, and what each is for.

  $wanted = [
    'mbstring' => 'text functions, required', 'json' => 'JSON, required', 'ctype' => 'name checks, required',
    'mysqli' => 'MySQL through db ()', 'pdo_sqlite' => 'SQLite through db ()', 'curl' => '{curl}, padCurl, padPrefetch',
    'sodium' => 'padEncrypt and padDecrypt', 'intl' => 'currency, localDate, {trans} plurals',
    'gd' => '{img} thumbnails', 'apcu' => "the 'apcu' caches", 'redis' => "the 'redis' cache",
    'memcached' => "the 'memcached' cache", 'fileinfo' => 'padUpload, the real type of a file',
    'zlib' => 'gzip of the output', 'pcntl' => 'pad work --timeout, pad serve', 'tidy' => '$padTidy',
    'opcache' => 'compiled scripts kept between requests', 'xdebug' => 'the step debugger of the editor'
  ];

  $extRows = [];

  foreach ( $wanted as $ext => $what )
    $extRows [] = [ 'ext' => $ext, 'what' => $what, 'loaded' => extension_loaded ( $ext ) ? 1 : 0,
                    'version' => extension_loaded ( $ext ) ? (string) phpversion ( $ext ) : '' ];

  $allExt = get_loaded_extensions ();

  natcasesort ( $allExt );

  $allExtText = implode ( ', ', $allExt );

  // OPcache, when it runs for this server API.

  $opcache = function_exists ( 'opcache_get_status' ) ? @opcache_get_status ( FALSE ) : FALSE;
  $hasOpcache = is_array ( $opcache ) ? 1 : 0;
  $opcacheRows = [];

  if ( $hasOpcache ) {
    $mem   = $opcache ['memory_usage'] ?? [];
    $stats = $opcache ['opcache_statistics'] ?? [];
    $opcacheRows = [
      [ 'label' => 'Enabled',        'value' => ! empty ( $opcache ['opcache_enabled'] ) ? 'yes' : 'no' ],
      [ 'label' => 'Memory used',    'value' => adminBytes ( $mem ['used_memory'] ?? 0 ) . ' of ' . adminBytes ( ( $mem ['used_memory'] ?? 0 ) + ( $mem ['free_memory'] ?? 0 ) ) ],
      [ 'label' => 'Cached scripts', 'value' => (string) ( $stats ['num_cached_scripts'] ?? 0 ) ],
      [ 'label' => 'Hit rate',       'value' => round ( (float) ( $stats ['opcache_hit_rate'] ?? 0 ), 1 ) . '%' ],
      [ 'label' => 'Restarts',       'value' => (string) ( ( $stats ['oom_restarts'] ?? 0 ) + ( $stats ['hash_restarts'] ?? 0 ) + ( $stats ['manual_restarts'] ?? 0 ) ) ]
    ];
  }

  $facts = [
    [ 'label' => 'Version',        'value' => PHP_VERSION ],
    [ 'label' => 'Server API',     'value' => PHP_SAPI ],
    [ 'label' => 'Binary',         'value' => PHP_BINARY === '' ? '-' : PHP_BINARY ],
    [ 'label' => 'php.ini',        'value' => (string) php_ini_loaded_file () ],
    [ 'label' => 'More .ini',      'value' => (string) php_ini_scanned_files () ],
    [ 'label' => 'Zend engine',    'value' => zend_version () ],
    [ 'label' => 'OS',             'value' => PHP_OS_FAMILY . ' - ' . php_uname () ],
    [ 'label' => 'Memory now',     'value' => adminBytes ( memory_get_usage ( TRUE ) ) . ', peak ' . adminBytes ( memory_get_peak_usage ( TRUE ) ) ],
    [ 'label' => 'User',           'value' => function_exists ( 'posix_geteuid' ) ? ( posix_getpwuid ( posix_geteuid () ) ['name'] ?? posix_geteuid () ) : get_current_user () ]
  ];

  // phpinfo (), its body only; its own style rules go, the page's frame styles it.

  ob_start ();
  phpinfo ( INFO_ALL & ~INFO_ENVIRONMENT & ~INFO_VARIABLES );
  $phpinfo = (string) ob_get_clean ();

  if ( preg_match ( '#<body[^>]*>(.*)</body>#s', $phpinfo, $match ) )
    $phpinfo = $match [1];
  else
    $phpinfo = '<pre>' . htmlspecialchars ( $phpinfo ) . '</pre>';

?>
