<?php

  // Declares and zeroes the engine's request-scoped globals.
  //
  // Most important is $pad, the current nesting level, which starts at -1 meaning "no level
  // yet"; inits/level.php then opens the root level. The rest are the accumulating request
  // state: the output being built ($padOutput, $padLen, $padEtag, $padStop), the restart
  // request, the counters used by strings and info, and the caches for data and
  // providers.
  //
  // The pqStore / padLastPush / padLastPull guards keep a sequence store alive across a
  // restart, and $padInclude records how the request arrived.
  //
  // The four stores of padStrSto are declared here rather than on first write. A nested pass
  // that runs inside a PHP function body - padCode() and padSandbox() - binds the globals it
  // can see at the moment it opens, so a store that does not exist yet is never bound, and
  // start/start/resetPad.php cannot empty what is not set either. Writing {data 'x'} then
  // reading {x} would land in two different scopes.

  $pad          = -1;
  $padLvlId     = 0;
  $padAppTime   = 0;
  $padRestart   = '';
  $padOutput    = '';
  $padStop      = '000';
  $padEtag      = '';
  $padLen       = 0;
  $padTime      = $_SERVER ['REQUEST_TIME'];
  $padCacheStop = 0;
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;

  // The response fragment a request asks for alone - lib/respond.php. The page's PHP may
  // set it too.

  $padFragmentOnly = is_string ( $_REQUEST ['padFragment'] ?? NULL ) ? $_REQUEST ['padFragment'] : '';
  $padFragmentSent = FALSE;

  // htmx names the element it swaps in an HX-Target header: a request of it that names no
  // fragment itself gets the fragment called like that element, when the page has one -
  // hx-get="?orders" hx-target="#order-list" needs no padFragment. A page without such a
  // fragment renders whole, as before (padFragmentMissing).

  $padFragmentTarget = ( $padFragmentOnly === '' and ( $_SERVER ['HTTP_HX_REQUEST'] ?? '' ) === 'true'
                         and preg_match ( '/^[A-Za-z][A-Za-z0-9_-]*$/', (string) ( $_SERVER ['HTTP_HX_TARGET'] ?? '' ) ) );

  if ( $padFragmentTarget )
    $padFragmentOnly = $_SERVER ['HTTP_HX_TARGET'];
  $padStrCnt    = -1;
  $padStrFunCnt = 0;
  $padInfo      = '';
  $padInfoCnt   = 0;

  $padCoverageRun = '';
  $padToolbarData = [];

  $padData      = [];
  $padProviders = [];

  if ( ! isset ( $pqStore )     ) $pqStore     = [];
  if ( ! isset ( $padLastPush ) ) $padLastPush = '';
  if ( ! isset ( $padLastPull ) ) $padLastPull = '';

  $padBetweenOrg   = $padBetweenOrg   ?? '';
  $padOrgSet       = $padOrgSet       ?? '';
  $padSwNow        = $padSwNow        ?? [];
  $padDataStore    = $padDataStore    ?? [];
  $padContentStore = $padContentStore ?? [];
  $padBoolStore    = $padBoolStore    ?? [];

  // The {push} stacks start empty on every run, a restart's included: what the abandoned
  // page pushed belongs to a page that is not sent.

  $padStackStore   = [];

  // The form rules of the page's template (lib/form.php): the text they are read from, set
  // by build/page.php, what was read from it, and the outcome of the post pass per form.

  $padFormText    = '';
  $padFormRules   = NULL;
  $padFormChecked = [];

  // The {file} tag's six path parts exist for the same reason as the stores above: the tag
  // writes them bare, padFileName() reads them through global, and a {file} inside a nested
  // pass needs the two to be the same variable. config/output/file.php runs after this and
  // still gets the two it sets.

  $padFileDir       = '';
  $padFileName      = '';
  $padFileExtension = '';
  $padFileDate      = '';
  $padFileTimeStamp = '';
  $padFileUniqId    = '';

?>
