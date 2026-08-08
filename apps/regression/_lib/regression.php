<?php


  function getRegressionBuild ( ) {
    getRegression        ( '&padExamples&padReference' );
    getRegression        ( );
    getRegressionWarning ( 'ok' );
    getRegression        ( );
  }


  // Accepts every page the crawl has marked 'warning' - it walks the stored statuses and calls
  // ?ok on each, which is the one-page-at-a-time link the crawl list already offers.
  //
  // A warning means only that a page renders differently from the copy stored for it. After a
  // deliberate change - a case added, a menu entry, a wording fixed - that is every page the
  // change touched, and accepting them one link at a time is the tedium this replaces.
  //
  // It is not a way of making the list green. A page that renders differently every time it is
  // asked - a clock, a counter, an {ajax} id, anything drawing at random - comes straight back as
  // a warning on the next crawl, and should: the right answer for those is the 'random' marker in
  // the page, which stops the crawl comparing it at all. So read what this returns rather than
  // just running it; a name that keeps appearing is telling you something.
  //
  // Returns the items accepted, "app/item" each, in the order they were found.

  function getRegressionWarning ( $kind ) {

    global $padHost;

    $dir  = DATA . 'regression/';

    $directory = new RecursiveDirectoryIterator ( $dir );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname () );

      if ( ! str_ends_with ( $path, '.txt' ) )
        continue;

      if ( trim ( padFileGet ( $path ) ) != 'warning' )
        continue;

      $base = substr ( $path, strlen ( $dir ) );;

      list ( $app, $file ) = explode ( '/', $base, 2 );

      $item = substr ( $file, 0, -4 );

      set_time_limit ( 60 );

      if ( $kind == 'ok' )
        padCurl ( $padHost . "regression/?ok&app=$app&item=" . urlencode ( $item ) );
      else
        getRegressionGo ( $app, $item );

    }

  }

  
  // Where the two suites live. They belong to the regression application wherever they are driven
  // from - develop's build page calls getRegression() as well - so the directories are named
  // absolutely rather than against APP.
  //
  // Against APP they resolved to whatever application was running. A build run from develop
  // looked for apps/develop/sandbox/_cases/ and apps/develop/pages/, found neither, and wrote
  // "no cases for 'tags'" over every stored result; the crawl then stored the suite pages saying
  // that, and getRegressionWarning('ok') accepted it as the baseline. getPagesUrl() has always
  // named the application outright, so this only makes the rest of it agree.

  function getRegressionApp () {

    return APPS . 'regression/';

  }


  function getRegression ( $extra='' ) {

    getRegressionSandbox ();
    getRegressionPages   ();
    getRegressionAll     ( $extra );

  }


  function getRegressionAll ( $extra='' ) {

    set_time_limit ( 60 );

    foreach ( padAppsList () as $one ) {

      extract ( $one );

      getRegressionGo ( $app, $item, $extra );

    }

  }


  // The session and request ids a page carries are different on every request by design, so a
  // page that writes them into its own output can never match a stored copy. {ajax} does exactly
  // that - padAddIds() appends padSesID and padReqID to the url it builds - and that alone was
  // enough to keep reference/pages and regression/show/index permanently on warning.
  //
  // They are taken out of both sides before comparing, and only for comparing: what is stored is
  // still the page as it came, so the show page diffs the real thing.

  function getRegressionCompare ( $text, $draw = FALSE ) {

    $text = preg_replace ( '/padSesID=[A-Za-z0-9]+/', 'padSesID=', $text );
    $text = preg_replace ( '/padReqID=[A-Za-z0-9]+/', 'padReqID=', $text );

    // With $draw, every run of digits goes too. That is what lets a page which draws numbers be
    // compared at all: the values it happened to pick are the part that cannot match, and the
    // headings, rows, table structure and everything else around them are the 93% that can.

    // A run of drawn values collapses to one. A page can draw a different *number* of values as
    // well as different values - {mySeq randomly, unique} drops duplicates, so eight terms one
    // run and six the next - and without this the row count alone reported a change that was
    // only ever the draw. A heading, a column or a tag still shows, which is the point.

    // The names of a day and a month go with the digits. A page that prints a date prints them
    // too - "Friday, August 7, 2026" - and they are words, so masking numbers left the weekday
    // behind: demo/clock and demo/counter came up as changed the first time the crawl ran after
    // midnight, every night, and nothing but a fresh baseline would quiet them.

    // What a {demo} produced is the draw itself, and _common marks it out for us. On a page that
    // draws, the whole of each result goes: sequence/play/double/random draws nothing at all now
    // and then, so one render says a value where the last said none, and no amount of masking
    // digits makes those two agree. The sources, headings and table around them still compare,
    // which is what these pages are worth checking for.

    if ( $draw )
      $text = preg_replace ( '/(<!-- START DEMO RESULT -->).*?(<!-- END DEMO RESULT -->)/s', '$1#$2', $text );

    if ( $draw ) {
      $text = preg_replace ( '/\b(Mon|Tues|Wednes|Thurs|Fri|Satur|Sun)day\b/', 'DAY', $text );
      $text = preg_replace ( '/\b(January|February|March|April|May|June|July|August|September|October|November|December)\b/', 'MONTH', $text );
      $text = preg_replace ( '/\d+/',      '#', $text );
      $text = preg_replace ( '/#(\s+#)+/', '#', $text );
    }

    return $text;

  }


  // Whether a page says of itself that it draws. The three words are matched with str_contains
  // rather than strpos, which returns 0 for a word at the very start of a file and reads as false.

  function getRegressionDraws ( $source ) {

    return str_contains ( $source, 'random'  )
        or str_contains ( $source, 'shuffle' )
        or str_contains ( $source, 'chance'  );

  }


  // What to make of a page that draws. It used to be skipped outright, which meant a regression
  // in one of the twenty-seven such pages could not be seen: across them that is 4542 lines of
  // output, of which only 7% actually varies from run to run.
  //
  // So the draw is masked and the rest compared. Where that matches, the page is 'random' as
  // before - the colour still says it cannot be compared exactly - but it now means "looked at
  // and unchanged" instead of "not looked at".
  //
  // Where it does not match, the page is asked for once more. If two fresh draws differ from each
  // other even masked then the shape itself varies per run and there is nothing to compare, so it
  // stays 'random'. Otherwise the page really has changed, and that is a warning.

  function getRegressionDraw ( $url, $old, $new ) {

    if ( getRegressionCompare ( $old, TRUE ) == getRegressionCompare ( $new, TRUE ) )
      return 'random';

    // Asked for once more, and the second draw is held against both the first and the stored copy.
    //
    // A page can have more than one shape without having changed. sequence/play/double/random
    // draws nothing at all now and then, so one render says '#' where the last said nothing, and
    // comparing the two fresh draws alone reported that as a change roughly five times in six.
    // If the stored shape comes back on the second ask, the page still produces it and the odd
    // draw was just a draw.
    //
    // So a warning needs two independent draws that both differ from what was stored, and agree
    // with each other about it. Anything less is a page nothing can be concluded about, which is
    // what 'random' says.

    $again = getRegressionCompare ( padCurl ( $url ) ['data'], TRUE );

    if ( $again == getRegressionCompare ( $old, TRUE ) ) return 'random';
    if ( $again != getRegressionCompare ( $new, TRUE ) ) return 'random';

    return 'warning';

  }


  // Whether a page that answered with an error code was supposed to. The pages suites already
  // declare it - an expectation file opening with HTTP <code> sits beside the page - so the
  // crawl reads that declaration rather than asking the page for a marker of its own.

  function getRegressionExpects ( $app, $item, $code ) {

    $want = trim ( padFileGet ( APPS . "$app/$item.txt" ) );

    return str_starts_with ( $want, "HTTP $code" );

  }


  function getRegressionGo ( $app, $item, $extra='' ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';
    $store   = DATA . "regression/$app/$item.html";

    $curl   = padCurl    ( "$padHost$app/?$item$include$extra" );
    
    // The source is read to find the marker that says a page cannot be compared, and to see what
    // an example harvest should skip. A page with no template keeps both of those in its .php,
    // and looking only at the .pad missed them: sequence/sequences says random in its .php and
    // was compared exactly the moment the crawl started walking php-only pages.

    $source = padFileGet ( APPS . "$app/$item.pad" ) . padFileGet ( APPS . "$app/$item.php" );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = $curl ['data'];

    // The crawl's own overview lists the status of every page, and those change as the crawl
    // walks them - so by the time it reaches this one, what it renders no longer matches what
    // was stored a moment before, and never will. The application's index has the same nature
    // since it took to reporting the scan's counts. Comparing a report against the run
    // producing it is circular; both are marked and left alone.

    // A page that exists to fail, or to render nothing, is not news. The pages suites already
    // declare the first kind - an expectation starting with the same HTTP code sits beside the
    // page - and the second kind is a page whose stored copy is as empty as what just came
    // back. Both are 'expected': counted, coloured, and left off the list of what needs
    // looking at. An error nothing declared, or a page that went empty, still shouts.

    if     ( in_array ( "$app/$item", [ 'regression/scan/index', 'regression/index' ] ) )
                                          $status = 'random';
    elseif ( ! $good                    ) $status = getRegressionExpects ( $app, $item, $curl ['result'] ) ? 'expected' : 'error';
    elseif ( ! file_exists ($store)     ) $status = 'new';
    elseif ( ! trim ($new)              ) $status = ( trim ($old) ) ? 'empty' : 'expected';
    elseif ( getRegressionDraws ( $source ) )
                                          $status = getRegressionDraw ( "$padHost$app/?$item$include$extra", $old, $new );
    elseif ( getRegressionCompare ( $old ) == getRegressionCompare ( $new ) ) $status = 'ok';
    else                                  $status = 'warning';

    if ( $status == 'new' )
      padFilePut ( $store, $new ) ;

    padFilePut ( str_replace ( '.html', '.txt', $store ), $status ) ;

    // Examples are harvested only on the run that asked for them. The old condition -
    // if ( ! $extra != '&padExamples' ... ) - was inverted twice over: an ordinary scan fell
    // through and rewrote every example, and the build's combined flag never equalled the
    // single string, so the dedicated run returned early instead. The audit's F-04.

    if ( ! str_contains ( $extra, '&padExamples' ) or ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;
 
    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    padFilePut ( "examples/$app/$item.pad",  padTidySmall ( $source,        TRUE ) );
    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }


  function padAppsList () {

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( strpos ( $path, '/_')      ) continue;
      if ( strpos ( $path, 'develop') ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext != 'pad' and $ext != 'php' )
        continue;

      // A page with no template is still a page - what its .php echoes is its output - and the
      // crawl had never seen one. Nineteen of them across the tree, the five app-level error
      // raisers among them, and none had a stored copy or an entry on the scan. A pair keys by the
      // same name either way, so it still counts once.
      //
      // Except where the .php is not a page but an action. ?ok accepts a baseline, todoPost
      // writes one, structure/index sends you elsewhere - fetching those changes something, and
      // a crawl has to be able to run without changing anything. So a page with no template that
      // redirects, restarts or writes is left out.

      if ( $ext == 'php' and ! file_exists ( substr ( $path, 0, -4 ) . '.pad' ) ) {

        $source = padFileGet ( $path );

        if ( str_contains ( $source, 'padRedirect'      )
          or str_contains ( $source, 'padRestart'       )
          or str_contains ( $source, 'padFilePut'       )
          or str_contains ( $source, 'padDeleteDataDir' ) )
          continue;

      }

      $file  = str_replace ( APPS, '', $path );

      $app   = substr($file, 0, strpos($file, '/')   );
      $file  = substr($file,    strpos($file, '/')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      $files ["$app/$item"] ['path'] = $path;
      $files ["$app/$item"] ['app']  = $app;
      $files ["$app/$item"] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }


  // The runner behind pages/ and common/, the second kind of regression test in this
  // application.
  //
  // A sandbox case is a template string rendered inside the running request. That is quick and
  // self-contained, but it is not a request: a nested pass has its own variable scope, so a page
  // whose .php file leaves something behind for its .pad file cannot be written as one, and
  // neither can anything that reads $GLOBALS or depends on how a request is built.
  //
  // A pages test is an ordinary page - name.pad, name.php, or the pair - fetched over HTTP
  // exactly as a browser would, with &padInclude so it renders bare, without the menu and title
  // the wrapper puts round it. What comes back is compared with name.txt, written beside it.
  //
  // The tests live in two applications, one suite each, and which application a page is in is
  // itself the assertion: regression2 - the Pages suite, driven from pages/ - has _common
  // switched off, so its pages prove they need nothing but their own application, and
  // regression3 - the Common suite, driven from common/ - holds the pages that use _common:
  // {example}, {demo}, {table}, the {block} snippet, the colouring functions in _common/_lib/.
  //
  // A test can also be a whole directory - see getPagesList() for when an index stands for the
  // files beside it and when it does not.
  //
  // name.txt is written by hand and nothing here ever rewrites it. That is the whole point: an
  // expectation that the harness records for itself is not a prediction, it is a copy of
  // whatever the code did, and a change to it would be recorded rather than reported. A test
  // with no name.txt yet comes up 'new' and the overview shows exactly what came back, which is
  // what goes in the file.

  function getPagesSuites () {

    return [ 'pages' => 'regression2', 'common' => 'regression3' ];

  }


  function getPagesDir ( $app ) {

    return APPS . "$app/";

  }


  // Every test under pages/, walked to the bottom. Either half names a test, so the .pad-only and
  // .php-only forms both count and a pair counts once. An underscore hides a directory or a file
  // from the walk, the way it does everywhere else in PAD.
  //
  // The rule for a directory holding an index: if that index renders its siblings - if its source
  // has a {page of them - then it is the only test in the directory, because running it has
  // already run them. A {page} chain of nine is one answer, not nine, and eight of those files
  // assert a fragment nobody asks for on its own.
  //
  // An index that only links to its siblings collapses nothing. error/index is a menu of ten
  // error pages and each of those is its own test; deep/index renders deep/two, which renders
  // three, which renders four, and is one.
  //
  // A root index is a test like any other, and the one test of its application that is fetched
  // without &padInclude - see getPagesUrl() - so what it asserts is the frame itself: the
  // _common wrapper, the title its _inits.php derives, the tidied whole. Neither application
  // is obliged to have one; regression2 does not, since its frame is no frame at all.

  function getPagesList ( $app ) {

    return getPagesWalk ( getPagesDir ( $app ), '' );

  }


  function getPagesRenders ( $dir ) {

    foreach ( [ 'pad', 'php' ] as $half ) {

      $source = padFileGet ( "{$dir}index.$half" );

      if ( str_contains ( $source, '{page' ) )
        return TRUE;

    }

    return FALSE;

  }


  function getPagesWalk ( $dir, $prefix ) {

    $list = $names = $dirs = [];

    if ( ! is_dir ( $dir ) )
      return $list;

    foreach ( padFiles ( $dir ) as $file ) {

      if ( str_starts_with ( $file, '_' ) )
        continue;

      if ( is_dir ( "$dir$file" ) )
        $dirs [] = $file;

      elseif ( str_ends_with ( $file, '.pad' ) or str_ends_with ( $file, '.php' ) )
        $names [ substr ( $file, 0, -4 ) ] = TRUE;

    }

    if ( $prefix and isset ( $names ['index'] ) and getPagesRenders ( $dir ) )
      $names = [ 'index' => TRUE ];

    foreach ( array_keys ( $names ) as $name )
      $list [] = $prefix . $name;

    foreach ( $dirs as $one )
      $list = array_merge ( $list, getPagesWalk ( "$dir$one/", "$prefix$one/" ) );

    sort ( $list );

    return $list;

  }


  // The url a test is fetched from. $padHost carries the mount prefix, so this is
  // http://host/pad/regression2/?name&padInclude here and the right thing anywhere else. The
  // tests are applications of their own - regression2 with _common switched off, regression3
  // for the pages that use it - and the overview that drives them stays in regression.
  //
  // A root index renders full, the same rule the crawl applies: everything else asserts a bare
  // page, that one asserts the frame around it.

  function getPagesUrl ( $app, $name ) {

    global $padHost;

    $include = ( $name != 'index' ) ? '&padInclude' : '';

    return $padHost . "$app/?$name$include";

  }


  function getPagesWantFile ( $app, $name ) {

    return getPagesDir ( $app ) . "$name.txt";

  }


  // What each test is for. Kept here rather than in the pages themselves, because a page is
  // fetched and compared byte for byte: a comment added to one to describe it changes what it
  // renders, and several of these are sensitive enough to a stray line break that writing the
  // description into them broke twelve of the eighty-five. getCasesGroups() describes the sandbox
  // groups the same way, from the same file, for the same reason.
  //
  // A name with no entry shows an empty cell rather than an error, so adding a test does not mean
  // editing this first.

  // Looked up under app/name first, then under the name alone: the names rarely collide
  // across the two applications, so most entries stay short, and the one that does - each
  // suite has an error/index now - says which it means.

  function getPagesWhat ( $app, $name ) {

    $list = getPagesWhatList ();

    return $list ["$app/$name"] ?? $list [$name] ?? '';

  }


  function getPagesWhatList () {

    return [

      'index'                     => 'The one test fetched without padInclude: the _common wrapper, padOpen and padClose with the menu, and the title shown through showTitle',
      'menu'                      => 'The {menu} include - lines.pad around it, menu.json behind it, a link per application',
      'reference'                 => 'The _common _lib helpers a page calls directly - getReference() over the type handlers, and the Xref link builders',
      'misc/db'                   => 'The demo database through the credentials _common supplies - the one test that queries it',
      'regression2/error/index'   => 'The {error} tag with _common switched off - ending a request needs nothing shared',
      'error/throw'               => 'The {exception} tag, which throws a real PHP exception from the template',
      'error/exit'                => 'The {exit} tag, which ends the request where it stands - and ships nothing at all, which the pattern pins',
      'error/dump'                => 'The {dump} tag, which stops the request on the engine state dump',
      'misc/trace'                => 'The {trace} tag wrapping a scope in the execution trace - it broke two ways before this test existed',
      'misc/react'                => 'The {reactData} tag over a static provider, and the @providers reference reading the parked result back',
      'select/support/prefix'     => 'The select: prefix spelling of a declared table, which also says the word the reference matcher looks for',
      'catalog/tags'              => 'One line per built-in tag no other page reaches - the catalogue half of the reference coverage',
      'catalog/prefixes'          => 'One line per type prefix, each in its literal spelling',
      'catalog/properties'        => 'Every property and @ reference form on one page - the last three lines pin what does not answer',
      'catalog/options'           => 'One line per tag option, including the ones that assert being inert',
      'catalog/functions'         => 'One line per pipe function over a stated value',
      'catalog/handling'          => 'The data handling options, each over a list whose answer is obvious',
      'catalog/volatile'          => 'The two draws - {ajax} ids and now - pinned by shape in one regex',
      'catalog/common'            => 'The common: prefix reaching the shared application by name - the one catalogue line that needs _common',
      'callback'                  => 'The row phase of a streaming callback, which reads the occurrence fields as plain variables',
      'globals'                   => 'object: over a page variable, which in a request is a global like any other',
      'pairing'                   => 'A page pair: the .php half fills in what the .pad half prints',
      'phponly'                   => 'A page with no template at all - what its .php echoes is the page',
      'db/array'                  => 'The db() array command through a page variable, and the else branch when nothing matches',
      'db/array2'                 => 'The same through the {array} tag, which hands its parameter to db() as the command word',
      'db/check'                  => 'The db() check command, which answers whether a row exists rather than returning one',
      'db/check2'                 => 'The same through the {check} tag',
      'db/field'                  => 'The db() field command, which returns one value',
      'db/field2'                 => 'The same through the {field} tag',
      'db/record'                 => 'The db() record command, which returns one row',
      'db/record2'                => 'The same through the {record} tag',
      'deep/index'                => 'A page rendering a page rendering a page, each level with its own _lib and _include',
      'error/index'               => 'The menu of the pages that end the request; each of those is a test of its own',
      'error/error_1'             => 'An engine-raised PHP warning from a tag - an undefined variable - which $padErrorLevel promotes to an ended request',
      'error/error_2'             => 'The same engine-raised warning from the page rather than from a tag',
      'error/exception_1'         => 'An uncaught PHP exception from a tag',
      'error/exception_2'         => 'An uncaught PHP exception from a page',
      'error/pad_1'               => 'The {error} tag, which ends the request from the template',
      'error/pad_2'               => 'The same failure raised from the page',
      'error/shutdown_1'          => 'A fatal error, which the shutdown handler catches rather than the error handler',
      'error/shutdown_2'          => 'The same from a page',
      'error/warning_1'           => 'A PHP warning, which $padErrorLevel promotes to an ended request',
      'error/warning_2'           => 'The same from a page',
      'file/file'                 => 'The {file} tag writing four files, with date, stamp and id naming them',
      'file/done'                 => 'The page a file-output request lands on, reporting the name it was handed',
      'file/index'                => 'Output type file: the page is written to disk and the request restarts on file/done',
      'functions/date'            => 'The date function over a timestamp, a format and a strtotime modifier',
      'hello/index'               => 'The smallest page pair there is, and the one redirect and restart land on',
      'misc/eval'                 => 'The evaluator over the forms the manual shows - precedence, the @ placeholder, pipes',
      'misc/local'                => 'Reading a file from _data/ by name, in each format padData() accepts',
      'misc/parms'                => 'Parameters, options and variables on a tag, over a PHP array, a constant and a store',
      'manual/3_ways_to_make_a_table' => 'The start, end and else constructs over one table',
      'manual/doc3'               => 'Tag properties read through a content store',
      'manual/name'               => 'How PAD names a tag, and the name option that overrides it',
      'manual/table_fun'          => 'Ten ways of showing one table, each embedding one of the tableFun pages',
      'manual/variable_kinds'     => 'The variable prefixes, as far as the manual page got',
      'manual/z99'                => 'A page whose .php returns a string, which the build prepends to the page',
      'select/demo/join'          => 'The select subsystem walking a declared relation - a join written as nesting',
      'select/demo/union'         => 'A select table declared over a union of several tables',
      'select/support/test'       => 'A select table with a relation, read by id',
      'select/support/topic'      => 'A content store merged into a select result through @content@',
      'select/support/json'       => 'A note rather than a test - see _cases/README.md. It does exercise htmlAttrJson',
      'tags/redirect'             => 'The {redirect} tag, and the variable it hands the page it lands on',
      'tags/restart'              => 'The {restart} tag, which re-runs the request on another page carrying a variable',
      'tags/script'               => 'The script: prefix running a shell, python, perl and php script from _scripts/',
      'vars/at/1'                 => 'The @ reference over a flat global and a nested one, by name and by wildcard',
      'vars/at/2'                 => 'The same references where the value is buried five levels deep',
      'vars/at/7'                 => 'The @ reference walking an XML file, with optional hiding what is not there',
      'vars/at/8'                 => 'A dotted path with a condition at each step, into a data file',
      'vars/at/9'                 => 'The @ reference as a tag pair and through the {at} tag, over the same data',
      'vars/at/random'            => 'The wildcard forms of a dotted reference, each drawn fresh, asserted by shape',
      'start/index'               => 'Everything under start/ at once - the code, page and combi chains, nested and hello',
      'start/hello'               => 'One page rendered eight times through {page}, each option with a colour to tell it apart',
      'start/nested'              => 'A {page} inside a {page}, the shortest form of the chains beside it',
      'start/code/index'          => 'The eight {code} variants side by side',
      'start/page/index'          => 'The eight {page} chains side by side, which is what makes their differences readable',
      'start/combi/index'         => 'The three combi chains together',
      'start/combi1/index'        => 'A {page} chain mixing the options rather than using one throughout',
      'start/combi2/index'        => 'Another mixture of them',
      'start/combi3/index'        => 'A third mixture',
      'start/page/1/index'        => 'Nine nested {page} calls setting a variable - with no option it leaks outward',
      'start/page/2/index'        => 'The same chain with sandbox, so each pass has its own scope and the value unwinds',
      'start/page/3/index'        => 'The same chain with reset',
      'start/page/4/index'        => 'The same chain with clean',
      'start/page/5/index'        => 'The same chain incrementing rather than setting, with no option',
      'start/page/6/index'        => 'Incrementing with sandbox',
      'start/page/7/index'        => 'Incrementing with reset',
      'start/page/8/index'        => 'Incrementing with clean',
      'start/code/set/base'       => 'Nine nested {code} passes, each one setting the variable',
      'start/code/set/reset'      => 'Nine nested {code} passes, each one setting the variable, with reset',
      'start/code/set/clean'      => 'Nine nested {code} passes, each one setting the variable, with clean',
      'start/code/set/sandbox'    => 'Nine nested {code} passes, each one setting the variable, with sandbox',
      'start/code/increment/base' => 'Nine nested {code} passes, each one incrementting the variable',
      'start/code/increment/reset' => 'Nine nested {code} passes, each one incrementting the variable, with reset',
      'start/code/increment/clean' => 'Nine nested {code} passes, each one incrementting the variable, with clean',
      'start/code/increment/sandbox' => 'Nine nested {code} passes, each one incrementting the variable, with sandbox',
      'tableFun/fun_1_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_1_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_2_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_3_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_3_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_5_a'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_5_b'          => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_8'            => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_9'            => 'One of the ten ways of showing the same table, embedded by manual/table_fun',
      'tableFun/fun_0'            => 'The {content} definition the other nine print their cells through - it renders nothing itself',

    ];

  }


  // Fetches every test and compares. The comparison is exact apart from the whitespace at the two
  // ends, which is what a template's own line breaks leave and says nothing about the page.
  //
  // Two expectations are not a body. A file opening with HTTP <code> asserts the response code,
  // because the error dump carries a request id and absolute paths, so a page that exists to
  // fail can never match byte for byte. The code alone proved too little - the shutdown test
  // answered 500 for a while with the wrong thing broken - so a second line holding /a regular
  // expression/ asserts what the dump says as well. A file with slashes at both ends is a
  // regular expression over the whole body, the same convention the sandbox uses, for a page
  // that draws a different answer each time.

  // A page is one comparison, but not always one assertion: the catalog/ pages state one
  // assertion per labelled line, so for those the answer's "name: ..." lines are counted as
  // tests. Every other page - and a catalogue answer that is a code, a pattern, or one
  // rendering - counts one test however long its answer, because its lines are not a list.

  function getPagesCount ( $name, $expect ) {

    if ( ! str_starts_with ( $name, 'catalog/' ) )
      return 1;

    return max ( 1, preg_match_all ( '/^\S+: /m', $expect ) );

  }


  function getPagesRun ( $app ) {

    $tests  = [];
    $total  = 0;
    $count  = 0;
    $failed = 0;
    $new    = 0;

    foreach ( getPagesList ( $app ) as $name ) {

      set_time_limit ( 60 );

      $url  = getPagesUrl      ( $app, $name );
      $want = getPagesWantFile ( $app, $name );
      $curl = padCurl          ( $url );

      $got  = trim ( $curl ['data'] );
      $code = $curl ['result'];

      if ( ! file_exists ( $want ) ) {

        $status = 'new';
        $expect = '';

      } else {

        $expect = trim ( padFileGet ( $want ) );

        if ( str_starts_with ( $expect, 'HTTP ' ) ) {

          $expectLines   = padExplode ( $expect, "\n" );
          $expectCode    = trim ( substr ( $expectLines [0], 5 ) );
          $expectPattern = $expectLines [1] ?? '';

          $ok  = ( $expectCode === (string) $code );
          $got = "HTTP $code";

          if ( $ok and $expectPattern ) {

            $ok = (bool) preg_match ( $expectPattern, trim ( $curl ['data'] ) );

            $got = ( $ok ) ? "HTTP $code, matches $expectPattern"
                           : "HTTP $code\n" . trim ( $curl ['data'] );

          }

        } elseif ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

          // A pattern answer still needs a healthy response: without the status check a
          // fragment surviving inside an error dump could pass a test that meant to assert
          // a working page. A failing status is asserted with the HTTP form instead.

          $ok = (bool) preg_match ( $expect, $got ) and str_starts_with ( (string) $code, '2' );

          if ( $ok )
            $got = "matches $expect";

        } else

          $ok = ( $got === $expect and str_starts_with ( $code, '2' ) );

        $status = $ok ? 'ok' : 'FAILED';

      }

      $total++;
      $count += getPagesCount ( $name, $expect );

      if ( $status == 'FAILED' )
        $failed++;

      // A test with no recorded answer is not passing - it is waiting for one. It has its
      // own count rather than a place in 'failed', so the overview can say which it is, and
      // ci.sh gates on both.

      if ( $status == 'new' )
        $new++;

      $tests [] = [
        'name'   => $name,
        'url'    => $url,
        'what'   => getPagesWhat ( $app, $name ),
        'show'   => "?show&app=$app&item=$name",
        'want'   => htmlspecialchars ( $expect ),
        'got'    => htmlspecialchars ( $got    ),
        'status' => $status,
        'failed' => ( $status == 'FAILED' ) ? 1 : 0
      ];

    }

    return [
      'tests'   => $tests,
      'summary' => "$total pages, $count tests, $failed failed" . ( $new ? ", $new new" : '' ),
      'failed'  => $failed,
      'new'     => $new,
      'when'    => time ()
    ];

  }


  // Kept beside the sandbox runs, under the same name a group would have - one file per suite,
  // pages.json and common.json.

  function getPagesFile ( $suite ) {

    return DATA . "suites/$suite.json";

  }


  // Runs one suite and keeps the result. This is what the Test link on a suite page asks for,
  // the way getCasesTest() is for a sandbox group.

  function getPagesTest ( $suite ) {

    set_time_limit ( 60 );

    $result = getPagesRun ( getPagesSuites () [$suite] );

    padFilePut ( getPagesFile ( $suite ), json_encode ( $result ) );

    return $result;

  }


  // Runs both suites, and like getRegressionSandbox() asks for them over HTTP from anywhere but
  // the regression application. Each test is a fetch either way, but getPagesWhat() and the
  // directory walk are APP-relative through getRegressionApp(), and a page under test may read
  // its own application's resources - so they are run where they live.

  function getRegressionPages () {

    global $padHost;

    foreach ( array_keys ( getPagesSuites () ) as $suite )

      if ( APP != getRegressionApp () )
        padCurl ( $padHost . "regression/?$suite/index&test" );
      else
        getPagesTest ( $suite );

  }


  // What a page load reads: the last run, without starting a new one - and genuinely never
  // one: a missing or unreadable result used to fall through to running the whole suite,
  // which made opening a report on a fresh install cost hundreds of requests and broke the
  // page's own promise (the audit's F-12). It answers 'never run' now; Test is the only
  // thing that runs.

  function getPages ( $suite ) {

    $file = getPagesFile ( $suite );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['tests'] ) )
        return $result;

    }

    return [ 'tests' => [], 'summary' => 'never run', 'failed' => 0, 'new' => 0, 'when' => 0 ];

  }


  // The stored status of every page the scan keeps, counted by status. The index page reports
  // these totals; the scan page builds its fuller list itself.

  function getScanCounts () {

    $counts = [];

    $dir = DATA . 'regression/';

    if ( ! is_dir ( $dir ) )
      return $counts;

    $directory = new RecursiveDirectoryIterator ( $dir );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname () );

      if ( ! str_ends_with ( $path, '.txt' ) )
        continue;

      $status = trim ( padFileGet ( $path ) );

      $counts [$status] = ( $counts [$status] ?? 0 ) + 1;

    }

    ksort ( $counts );

    return $counts;

  }


  // The runner behind every page under sandbox/: it renders each case in a group and
  // compares what the engine produced against what it is supposed to produce.
  //
  // The pages of the manual demonstrate the framework; they do not assert anything, so a
  // change that quietly alters a result still renders and still looks right. Every case here
  // states its expected output, which makes a difference a failure rather than a new baseline.
  //
  // A case is [ name, template, expected ], laid out one statement to a line. See
  // _cases/README.md for the format and for what this kind of suite cannot check.
  //


    // The two fixtures the 'scope' cases read: $seqFixture is the list sequence/library.php
    // iterates to reach pqTruncate(), which no sequence tag goes near, and $objFixture is what
    // the object: case in expressions/references.php looks up - object: reads $GLOBALS, and a
    // sandboxed pass has the application globals taken out of it.
    //
    // Written into $GLOBALS rather than assigned, because where a _lib file's top level ends up
    // is not fixed. They were reachable when the suite was driven from ?sandbox/index and not
    // when it was driven from the scan page, and the whole crawl ended on "Field '$seqFixture' not
    // found". padCode() renders its pass over the globals, so that is where they have to be.

    $GLOBALS ['seqFixture']   = [ 1, 2, 3, 4, 5 ];
    $GLOBALS ['objFixture']   = [ 'a', 'b' ];

    // What the @saved case shadows: the saved group reads what padSetGlobalOcc() put aside,
    // and it only puts aside what really is a global - a sandboxed pass has none, so the
    // case runs with 'scope' and shadows this one.

    $GLOBALS ['savedFixture'] = 'outer';


  function getCasesRun ( $group ) {

    $tests  = [];
    $total  = 0;
    $failed = 0;
    $dir    = getRegressionApp () . "sandbox/_cases/$group";

    if ( ! is_dir ( $dir ) )
      return [ 'tests' => [], 'summary' => "no cases for '$group'", 'failed' => 1, 'when' => 0 ];

    foreach ( padFiles ( $dir ) as $file ) {

      if ( ! str_ends_with ( $file, '.php' ) )
        continue;

      foreach ( include "$dir/$file" as $case ) {

        list ( $name, $code, $want ) = $case;

        // Each case is rendered in its own scope, so one cannot see or disturb the next and
        // the order they run in cannot change the outcome. A case that needs a variable or a
        // stored value therefore sets it up itself, in the same template.
        //
        // A fourth entry says how, when the template alone cannot:
        //
        //   'scope'   render with padCode() rather than padSandbox(), sharing the page's own
        //             variables. That is the only way to reach engine code no tag calls -
        //             pqTruncate() is reached through the trim option and never by a sequence
        //             tag - and the page sets up whatever those cases read.
        //
        //   an array  variables to put in place first, name => value. A nested pass binds the
        //             globals that exist when it opens, so these are visible to the template
        //             even sandboxed, and arrays of any depth come through - {$a.b.c} reads
        //             one. They are dropped again afterwards, so a case still cannot leave
        //             anything behind for the next.
        //
        // The array form is what lets a page whose data came from a paired .php file be a case
        // at all: the data is stated in the case instead, where it can be read beside what it
        // is supposed to produce.

        $setup = $case [3] ?? '';

        // Setup names are restored, not just unset: a case borrowing a name that already
        // existed used to erase the original, and a throw skipped the cleanup entirely -
        // the finally puts the world back either way (the audit's F-15).

        $setupPrior = [];

        if ( is_array ( $setup ) )
          foreach ( $setup as $setupName => $setupValue ) {
            $setupPrior [$setupName] = array_key_exists ( $setupName, $GLOBALS )
                                     ? [ TRUE, $GLOBALS [$setupName] ] : [ FALSE, NULL ];
            $GLOBALS [$setupName] = $setupValue;
          }

        try {

          if ( $setup === 'scope' )
            $got = padCode    ( $code );
          else
            $got = padSandbox ( $code );

        } finally {

          foreach ( $setupPrior as $setupName => $setupWas )
            if ( $setupWas [0] )
              $GLOBALS [$setupName] = $setupWas [1];
            else
              unset ( $GLOBALS [$setupName] );

        }

        // A tag that produces PAD's own syntax characters - {ignore}, the open/close/tag
        // functions, a tag left standing because nothing claimed it - hands them back escaped
        // as &open; and &close;, and exits/exits.php turns those into braces once the whole
        // request is written. That has not happened yet here, so it is done to the case's
        // output first: a case then states what the page shows rather than an intermediate
        // spelling of it.

        $got = padUnescape ( $got );

        // A case is written one statement to a line, so the engine emits the line break and
        // the indentation that follow each of them - once per term for anything inside a
        // loop. That whitespace is the shape of the case, not an answer the framework gave,
        // so it comes out before the comparison: a line break and the indentation after it
        // go, and spacing written within a line stays, which is the only spacing a case can
        // be asserting.

        $got  = preg_replace ( '/\n\s*/', '', $got );
        $show = $got;

        // A regular expression is recognised by slashes at both ends, not just at the front:
        // '/test' is a value afterLast produces, and testing only the first character turned
        // that expectation into an unterminated pattern.

        if ( strlen ( $want ) > 1 and str_starts_with ( $want, '/' ) and str_ends_with ( $want, '/' ) ) {

          $ok = (bool) preg_match ( $want, $got );

          // A case pinned by shape answers differently every run, so a passing one reports
          // the shape it matched rather than the values it drew. Without that this page would
          // never render the same twice, and the regression run that compares it against its
          // previous output would report it changed on every pass.

          if ( $ok )
            $show = "matches $want";

        }

        else
          $ok = ( $got === $want );

        $total++;

        if ( ! $ok )
          $failed++;

        $tests [] = [
          'group'  => str_replace ( '.php', '', $file ),
          'name'   => $name,
          'code'   => getCasesShow ( $code ),
          'want'   => htmlspecialchars ( $want ),
          'got'    => htmlspecialchars ( $show ),
          'status' => $ok ? 'ok' : 'FAILED',
          'failed' => $ok ? 0 : 1
        ];

      }

    }

    // The count is kept apart from the per-row 'failed' field, and the pages take it as
    // $failedCount: a page variable sharing a name with a field of the rows it iterates is the
    // collision CLAUDE.md warns about, and this template reads both.

    return [
      'tests'   => $tests,
      'summary' => "$total tests, $failed failed",
      'failed'  => $failed,
      'when'    => time ()
    ];

  }


  // Where a run is kept. DATA is the writable tree and is not in git, which is what a result
  // that is regenerated on demand should be; the crawler's own baselines live beside it under
  // regression/, so the suites get a directory of their own rather than sharing that one.

  function getCasesFile ( $group ) {

    return DATA . "suites/$group.json";

  }


  // Runs a group and keeps the result. This is what the Test link asks for, and the only thing
  // that runs a case at all.

  function getCasesTest ( $group ) {

    $result = getCasesRun ( $group );

    padFilePut ( getCasesFile ( $group ), json_encode ( $result ) );

    return $result;

  }


  // What a page load reads: the last run, without starting a new one - and genuinely never
  // one: a group that had never been run used to be run on sight, against the page's own
  // promise (the audit's F-12). It answers 'never run' now; Test is the only thing that
  // runs.

  function getCases ( $group ) {

    $file = getCasesFile ( $group );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['summary'] ) )
        return $result;

    }

    return [ 'tests' => [], 'summary' => 'never run', 'failed' => 0, 'when' => 0 ];

  }


  // The template is shown coloured, through the padColorsString() the {demo} tag uses, so a
  // case reads the same way as the examples in the manual. The colouring hands back a <pre>,
  // which never wraps; a case can run past a hundred characters, and with its result beside it
  // the table would leave the window, so that block is allowed to wrap.

  function getCasesShow ( $code ) {

    if ( ! function_exists ( 'padColorsString' ) )
      return '<pre style="margin:0">' . htmlspecialchars ( $code ) . '</pre>';

    return str_replace ( '<pre>', '<pre style="white-space:pre-wrap; margin:0">',
                         padColorsString ( $code ) );

  }


  // Runs the sandbox suite. From anywhere but the regression application itself, it is asked for
  // over HTTP rather than run here.
  //
  // A case is rendered by padSandbox() inside the request that is running, so it reaches the
  // _tags, _callbacks, _functions and _data of whatever application that is. Driven from
  // develop's build page the cases were found - their directory is named outright now - but
  // their fixtures were not: {n callback='before.php'} looked for a _callbacks/ develop does not
  // have, the callback never ran, and the case died on the field it should have set. Asking for
  // it over HTTP runs it in the application it belongs to, which is the only place it can work.

  function getRegressionSandbox () {

    global $padHost;

    if ( APP != getRegressionApp () )
      return padCurl ( $padHost . 'regression/?sandbox/index&test' );

    set_time_limit ( 60 );

    foreach ( array_keys ( getCasesGroups () ) as $groupName )
      getCasesTest ( $groupName );

  }


  // The list every page under sandbox/ is built from, and what the overview walks.

  function getCasesGroups () {

    return [
      'tags'        => 'Control flow, output and the tags that carry them',
      'functions'   => 'The pipe functions, over the values they document',
      'options'     => 'Tag options, each against the same tag without it',
      'properties'  => 'The property@tag values an iteration publishes',
      'expressions' => 'Comparison, arithmetic, ranges and the @ placeholder',
      'variables'   => 'Assignment, scope, arrays and the two variable kinds',
      'data'        => 'The data tag over JSON, XML, CSV and a plain list',
      'prefixes'    => 'The type prefixes that say what a name means',
      'escaping'    => 'What stops PAD reading braces as tags, in each of its spellings',
      'custom'      => 'What an application supplies: _tags, _functions, _include, _data',
      'check'       => 'Pages carried over from the check application, which is gone',
      'sequence'    => 'The sequence subsystem - types, actions, plays, stores and options',
    ];

  }

?>