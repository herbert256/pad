<?php

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
  // $seqFixture is the list the 'scope' cases in sequence/library.php iterate, and $objFixture
  // the variable the object: case in expressions/references.php reads. Both are declared here
  // rather than on one page because the overview runs every group as well, and padCode() renders
  // its pass over the globals - a variable local to getCases() or set on one page only would
  // leave the other page reporting a missing field.
  //
  // $objFixture cannot be handed over as a fourth-element setup the way other data is: object:
  // reads $GLOBALS, and a sandboxed pass has the application globals taken out of it, which is
  // what sandboxing means. So that case renders in this file's scope instead.

  $seqFixture = [ 1, 2, 3, 4, 5 ];
  $objFixture = [ 'a', 'b' ];

  function getCasesRun ( $group ) {

    $tests  = [];
    $total  = 0;
    $failed = 0;
    $dir    = APP . "sandbox/_cases/$group";

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

        if ( is_array ( $setup ) )
          foreach ( $setup as $setupName => $setupValue )
            $GLOBALS [$setupName] = $setupValue;

        if ( $setup === 'scope' )
          $got = padCode    ( $code );
        else
          $got = padSandbox ( $code );

        if ( is_array ( $setup ) )
          foreach ( array_keys ( $setup ) as $setupName )
            unset ( $GLOBALS [$setupName] );

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


  // What a page load reads: the last run, without starting a new one. A group that has never
  // been run has nothing to show, so that one is run and kept - after which a page load is a
  // page load again.

  function getCases ( $group ) {

    $file = getCasesFile ( $group );

    if ( file_exists ( $file ) ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      if ( is_array ( $result ) and isset ( $result ['summary'] ) )
        return $result;

    }

    return getCasesTest ( $group );

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
      'check'       => 'Pages carried over from the check application',
      'sequence'    => 'The sequence subsystem - types, actions, plays, stores and options',
    ];

  }

?>