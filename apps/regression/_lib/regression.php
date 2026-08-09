<?php


  // The suites run once, the crawl three times. A suite is a deterministic assert against
  // the .txt files, so a second run answers the same as the first; the crawl is what needs
  // the cycle - a harvest pass whose extras can leak into pages that echo their query
  // string, a plain pass that stores the real bodies, the accept, and a verify pass.
  // Running the suites first also settles their overview pages before the crawl stores
  // them: a rerun would change the 'ran' stamps and put those pages on warning forever.

  function getRegressionBuild ( ) {
    getRegression        ( '&padExamples&padReference' );
    getRegressionAll     ( );
    getRegressionWarning ( );
    getRegressionAll     ( );
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

  function getRegressionWarning ( ) {

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

      padCurl ( $padHost . "regression/?ok&app=$app&item=" . urlencode ( $item ) );

    }

  }

  
  // Where the two suites live. They belong to the regression application wherever they are driven
  // from - develop's build page calls getRegression() as well - so the directories are named
  // absolutely rather than against APP.
  //
  // Against APP they resolved to whatever application was running. A build run from develop
  // looked for apps/develop/sandbox/_cases/ and apps/develop/pages/, found neither, and wrote
  // "no cases for 'tags'" over every stored result; the crawl then stored the suite pages saying
  // that, and getRegressionWarning() accepted it as the baseline. getPagesUrl() has always
  // named the application outright, so this only makes the rest of it agree.

  function getRegressionApp () {

    return APPS . 'regression/';

  }


  function getRegression ( $extra='' ) {

    getRegressionPages     ();
    getRegressionFramework ();
    getRegressionAll       ( $extra );

  }


  // The plain crawl goes twelve pages at a time: the fetches are independent GETs against
  // a server with as many workers, so a window of them is fetched through padCurlMulti and
  // the answers are compared and stored one by one, in order, as before.
  //
  // A run with extras stays one page at a time. With &padReference every crawled page
  // appends what it used to the shared reference files as it renders, behind a read-check-
  // append that is not safe against itself running twelve-wide - and the harvest is one
  // run per build, so its pace hardly matters.

  function getRegressionAll ( $extra='' ) {

    set_time_limit ( 60 );

    // A run with extras is the build's harvest pass, and it walks everything: the reference
    // and the examples are gathered from the suite applications' pages too, so they are
    // fetched here even though the plain crawl below leaves them out.

    if ( $extra ) {

      foreach ( padAppsList () as $one ) {

        extract ( $one );

        getRegressionGo ( $app, $item, $extra );

      }

      return;

    }

    // The plain crawl - the store pass, the verify pass, and every standalone scan - leaves
    // the three suite applications out. Their pages are already fetched and checked against
    // a handwritten answer by the Pages, Common and Framework suites, a stricter test than a
    // stored baseline, and they are 1,100-odd of the 1,750 pages a crawl would walk. They
    // keep no baseline of their own; only the once-per-build harvest pass above visits them.

    // The self-testing applications - regression_cache_*, regression_error_*,
    // regression_output_*, regression_info - stay out of the window. Their index pages
    // prove their subsystem by fetching their own probe from inside the request - the
    // error apps only behind their Test link, the others on load - and the window works
    // against that twice over: a concurrent crawl fetch of a cache probe lands between an
    // index's two fetches and turns a working backend into a NO, and twelve pages each
    // spawning a nested request can momentarily starve the worker pool, failing a fetch
    // that is the verdict. They test shared state and their own requests; they get the
    // server to themselves, after the flock.

    $flock = [];
    $solo  = [];

    foreach ( padAppsList () as $one )
      if ( in_array ( $one ['app'], [ 'regression2', 'regression3', 'regression4' ] ) )
        continue;
      elseif ( str_starts_with ( $one ['app'], 'regression_' ) )
        $solo  [] = $one;
      else
        $flock [] = $one;

    foreach ( array_chunk ( $flock, 48 ) as $chunk ) {

      set_time_limit ( 60 );

      $urls = [];

      foreach ( $chunk as $i => $one )
        $urls [$i] = getRegressionUrl ( $one ['app'], $one ['item'] );

      $fetched = padCurlMulti ( $urls );

      foreach ( $chunk as $i => $one )
        getRegressionGo ( $one ['app'], $one ['item'], '', $fetched [$i] ?? NULL );

    }

    foreach ( $solo as $one ) {

      set_time_limit ( 60 );

      getRegressionGo ( $one ['app'], $one ['item'] );

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


  // What to make of a page that draws: the draw is masked and the rest compared. Where that
  // matches, the page is 'random' - the colour says it cannot be compared exactly, and the
  // status means "looked at and unchanged". Across the drawing pages only a small part of
  // the output actually varies from run to run; everything around the draw still compares.
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


  // A page whose declared answer is a regular expression has said of itself that no two
  // renderings are byte-equal - the suite holds it to its pattern, and the crawl marks it
  // random rather than warning about every fresh draw.

  function getRegressionPatterned ( $app, $item ) {

    $want = trim ( padFileGet ( APPS . "$app/$item.txt" ) );

    return ( strlen ( $want ) > 1 and str_starts_with ( $want, '/' ) and str_ends_with ( $want, '/' ) );

  }


  function getRegressionUrl ( $app, $item, $extra='' ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    return "$padHost$app/?$item$include$extra";

  }


  // $fetched lets the crawl hand in a response it already holds - getRegressionAll fetches
  // a window of pages concurrently and walks the answers - and a call without one fetches
  // for itself, exactly as before.

  function getRegressionGo ( $app, $item, $extra='', $fetched=NULL ) {

    global $padHost;

    $include = ( $item != 'index' ) ? '&padInclude' : '';
    $store   = DATA . "regression/$app/$item.html";

    $curl   = $fetched ?? padCurl ( getRegressionUrl ( $app, $item, $extra ) );

    // The source is read to find the marker that says a page cannot be compared, and to see what
    // an example harvest should skip. A page with no template keeps both of those in its .php,
    // and looking only at the .pad missed them: sequence/sequences says random in its .php and
    // was compared exactly the moment the crawl started walking php-only pages.

    $source = padFileGet ( APPS . "$app/$item.pad"  )
            . padFileGet ( APPS . "$app/$item.html" )
            . padFileGet ( APPS . "$app/$item.php"  );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = $curl ['data'];

    // The crawl's own overview lists the status of every page, and those change as the crawl
    // walks them - so by the time it reaches this one, what it renders no longer matches what
    // was stored a moment before, and never will. The application's index has the same nature
    // since it took to reporting the scan's counts, and the demo clock says a different word
    // - AM or PM, the weekday - whenever enough time has passed, which digit masking cannot
    // cover. Comparing any of them is noise; each is marked and left alone. The error check
    // comes first, though: a marked page that stops answering still shouts.

    // A harvest run harvests, and nothing more. padReference renders every page bare -
    // pad/inits/info.php sets $padInclude for it - so what that run fetched is the wrong
    // shape to store or compare for an index page, which the plain crawl asks for with its
    // wrapper on. Storing it anyway put a bare body behind every index baseline after a
    // wipe, and the next crawl warned about each one just so the accept could refetch it.
    // The baselines and statuses are left to the plain crawls that follow.
    //
    if ( $extra )
      $status = '';

    // A page that exists to fail is not news: the pages suites declare it - an expectation
    // starting with the same HTTP code sits beside the page - and the answer is 'expected',
    // counted, coloured, and left off the list of what needs looking at. An error nothing
    // declared still shouts. A page whose render is as empty as its stored copy is simply a
    // match - 'ok', like any other - and only a page that *went* empty is called out.

    elseif ( ! $good                    ) $status = getRegressionExpects ( $app, $item, $curl ['result'] ) ? 'expected' : 'error';
    elseif ( in_array ( "$app/$item", [ 'regression/scan/index', 'regression/index', 'demo/clock',
                                        'regression_cache_apcu/probe', 'regression_cache_file/probe', 'regression_cache_db/probe',
                                        'regression_cache_memcached/probe', 'regression_cache_redis/probe' ] ) )
                                          $status = 'random';
    elseif ( ! file_exists ($store)     ) $status = 'new';
    elseif ( ! trim ($new)              ) $status = ( trim ($old) ) ? 'empty' : 'ok';
    elseif ( getRegressionPatterned ( $app, $item ) )
                                          $status = 'random';
    elseif ( getRegressionDraws ( $source ) )
                                          $status = getRegressionDraw ( "$padHost$app/?$item$include$extra", $old, $new );
    elseif ( getRegressionCompare ( $old ) == getRegressionCompare ( $new ) ) $status = 'ok';
    else                                  $status = 'warning';

    if ( $status == 'new' )
      padFilePut ( $store, $new ) ;

    if ( $status )
      padFilePut ( str_replace ( '.html', '.txt', $store ), $status ) ;

    // Examples are harvested only on the run that asked for them with the padExamples flag,
    // and only from a page that answered.

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

      if ( $ext != 'pad' and $ext != 'php' and $ext != 'html' )
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

      if ( $ext == 'php' and ! file_exists ( substr ( $path, 0, -4 ) . '.pad' )
                         and ! file_exists ( substr ( $path, 0, -4 ) . '.html' ) ) {

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

      elseif ( str_ends_with ( $file, '.html' ) )
        $names [ substr ( $file, 0, -5 ) ] = TRUE;

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
  // description into them broke twelve of the eighty-five.
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
      'misc/trace'                => 'The {trace} tag wrapping a scope in the execution trace',
      'misc/react'                => 'The {reactData} tag over a static provider, and the @providers reference reading the parked result back',
      'select/support/prefix'     => 'The select: prefix spelling of a declared table, which also says the word the reference matcher looks for',
      'select/demo/options'       => 'Every option a declared table takes on the tag - where, order, fields, group, having, rollup, distinctrow, db and union - one labelled line each over the staff table',
      'catalog/tags'              => 'One line per built-in tag no other page reaches - the catalogue half of the reference coverage',
      'catalog/prefixes'          => 'One line per type prefix, each in its literal spelling',
      'catalog/properties'        => 'Every property and @ reference form on one page',
      'catalog/options'           => 'One line per tag option, including the ones that assert being inert',
      'catalog/functions'         => 'One line per pipe function over a stated value',
      'catalog/handling'          => 'The data handling options, each over a list whose answer is obvious',
      'catalog/volatile'          => 'The two draws - {ajax} ids and now - pinned by shape in one regex',
      'catalog/common'            => 'The common: prefix reaching the shared application by name - the one catalogue line that needs _common',
      'callback'                  => 'The row phase of a streaming callback, which reads the occurrence fields as plain variables',
      'globals'                   => 'object: over a page variable, which in a request is a global like any other',
      'pairing'                   => 'A page pair: the .php half fills in what the .pad half prints',
      'phponly'                   => 'A page with no template at all - what its .php echoes is the page',
      'htmlpage'                  => 'An .html file as the template half of a pair - variables, pipes, a loop and a property all resolve in it',
      'htmlboth'                  => 'A page with both a .pad and an .html - the .pad wins',
      'htmlonly'                  => 'An .html page with no .php half, carrying a tag of its own',
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
      'misc/datatypes'            => 'The html and range data types: a tidied document walked to its values, and both range spellings',
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
  // fail can never match byte for byte. The code alone proves only that something failed, so
  // a second line holding /a regular expression/ asserts that the right thing did. A file
  // with slashes at both ends is a regular expression over the whole body, for a page that
  // draws a different answer each time.

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


  // Where a run is kept - one file per suite, pages.json, common.json and framework.json.
  // DATA is the writable tree and is not in git, which is what a result regenerated on
  // demand should be; the crawler's own baselines live beside it under regression/, so the
  // suites get a directory of their own rather than sharing that one.

  function getPagesFile ( $suite ) {

    return DATA . "suites/$suite.json";

  }


  // Runs one suite and keeps the result. This is what the Test link on a suite page asks for,
  // and the only thing that runs a suite at all.

  function getPagesTest ( $suite ) {

    set_time_limit ( 60 );

    $result = getPagesRun ( getPagesSuites () [$suite] );

    padFilePut ( getPagesFile ( $suite ), json_encode ( $result ) );

    return $result;

  }


  // Runs both suites, asked for over HTTP from anywhere but the regression application.
  // Each test is a fetch either way, but getPagesWhat() and the
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


  // What a page load reads: the last run, without starting a new one - a missing or
  // unreadable result answers 'never run', and Test is the only thing that runs. Fetching
  // every test is a request each, so opening a report must never cost a suite.

  function getPages ( $suite ) {

    $file = getPagesFile ( $suite );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['tests'] ) )
        return $result;

    }

    return [ 'tests' => [], 'summary' => 'never run', 'failed' => 0, 'new' => 0, 'when' => 0 ];

  }


  // The Framework suite: every engine case as a fetched page, exactly the Pages model.
  // Every case is a triple under apps/regression4/<group>/ - the .pad is the template, the
  // .txt the outcome beside it, an optional .php the variables - and each is fetched
  // directly as the page it is, so a case gets the isolation a request brings and the
  // crawl walks it like any other page.
  //
  // A case is written one statement to a line, so every line break and the indentation
  // after it come out of the body before it meets the outcome - and the two ends are
  // trimmed, which is what padCurl does to a body anyway. A .txt with slashes at both
  // ends is a regular expression, as everywhere else.

  function getFrameworkDir () {

    return APPS . 'regression4/';

  }


  function getFrameworkList () {

    $list = [];

    foreach ( padFiles ( getFrameworkDir () ) as $group ) {

      if ( str_starts_with ( $group, '_' ) )
        continue;

      if ( ! is_dir ( getFrameworkDir () . $group ) )
        continue;

      foreach ( padFiles ( getFrameworkDir () . $group ) as $file )
        if ( str_ends_with ( $file, '.pad' ) )
          $list [] = "$group/" . substr ( $file, 0, -4 );

    }

    sort ( $list );

    return $list;

  }


  function getFrameworkUrl ( $name ) {

    global $padHost;

    return $padHost . "regression4/?$name&padInclude";

  }


  function getFrameworkRun () {

    $tests  = [];
    $groups = [];
    $total  = 0;
    $failed = 0;
    $new    = 0;

    // Nine hundred independent GETs, so they go a window at a time through padCurlMulti,
    // and the answers are judged in order as before.

    foreach ( array_chunk ( getFrameworkList (), 48 ) as $chunk ) {

      set_time_limit ( 60 );

      $urls = [];

      foreach ( $chunk as $i => $name )
        $urls [$i] = getFrameworkUrl ( $name );

      $fetched = padCurlMulti ( $urls );

      foreach ( $chunk as $i => $name ) {

        $url  = $urls [$i];
        $want = getFrameworkDir () . "$name.txt";
        $curl = $fetched [$i] ?? [ 'data' => '', 'result' => '999' ];

        $got  = trim ( preg_replace ( '/\n\s*/', '', $curl ['data'] ) );
        $code = $curl ['result'];

        if ( ! file_exists ( $want ) ) {

          $status = 'new';
          $expect = '';

        } else {

          $expect = trim ( padFileGet ( $want ) );

          if ( strlen ( $expect ) > 1 and str_starts_with ( $expect, '/' ) and str_ends_with ( $expect, '/' ) ) {

            $ok = (bool) preg_match ( $expect, $got ) and str_starts_with ( (string) $code, '2' );

            if ( $ok )
              $got = "matches $expect";

          } else

            $ok = ( $got === $expect and str_starts_with ( (string) $code, '2' ) );

          $status = $ok ? 'ok' : 'FAILED';

        }

        $total++;
        $groups [ substr ( $name, 0, strpos ( $name, '/' ) ) ] = TRUE;

        if ( $status == 'FAILED' ) $failed++;
        if ( $status == 'new'    ) $new++;

        $tests [] = [
          'name'   => $name,
          'url'    => $url,
          'want'   => htmlspecialchars ( $expect ),
          'got'    => htmlspecialchars ( $got    ),
          'status' => $status,
          'failed' => ( $status == 'FAILED' ) ? 1 : 0
        ];

      }

    }

    return [
      'tests'   => $tests,
      'summary' => count ( $groups ) . " groups, $total tests, $failed failed" . ( $new ? ", $new new" : '' ),
      'failed'  => $failed,
      'new'     => $new,
      'when'    => time ()
    ];

  }


  function getFrameworkTest () {

    set_time_limit ( 60 );

    $result = getFrameworkRun ();

    padFilePut ( getPagesFile ( 'framework' ), json_encode ( $result ) );

    return $result;

  }


  function getRegressionFramework () {

    global $padHost;

    if ( APP != getRegressionApp () )
      padCurl ( $padHost . "regression/?framework/index&test" );
    else
      getFrameworkTest ();

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

?>