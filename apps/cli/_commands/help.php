<?php

  // pad help: the commands there are.

  cliOut ( <<<'TEXT'
pad - the PAD command

  pad new <app>                         a new application: apps/<app>/ and www/<app>/index.php
  pad new <app>/<dir>/<page>            a new page in it: <page>.php and <page>.pad
  pad serve [port] [host] [--mount=x]   PHP's built-in server over www/, no Apache needed
  pad render <app> [page] [name=value]  a page of any application to stdout
  pad lint <app> [dir]                  every page rendered under the strict check, errors listed
  pad export <app> <dir>                a static copy: <page>.html files and the assets of www/<app>/
  pad test <app> [name] [--record]      the application's own tests, in apps/<app>/_tests/
  pad test --all [--brief]              the tests of every application that has them
  pad sample <app> [page]               the page rendered once, its variables kept in _samples/
  pad types <app> [page ...] [--out=f]  TypeScript types of the pages' variables and JSON answers
  pad migrate <app> [--pretend|--seed]  the pending migrations of apps/<app>/_migrations/, as one batch
  pad migrate <app> --status            every migration: ran or not, and its batch
  pad migrate <app> --rollback[=n]      the down of the last batch (or n batches)
  pad migrate <app> --fresh --force     every table dropped, then every migration from nothing
  pad migrate <app> --new=name [--php]  a new, empty migration stamped with the time now
  pad seed <app> [name]                 the seeders of apps/<app>/_seeds/, or the one named
  pad work <app> [--queue=x] [--once]   the queue worker: runs the queued jobs (--max, --sleep, --tries, --timeout)
  pad queue <app> [--failed]            the queues' sizes; --retry=<id|all>, --flush[=queue]
  pad schedule <app> [--list]           what _schedule.php has due this minute; --all for every application
  pad down <app> [--secret[=word]]      maintenance: every request 503 until pad up; ?<word> lets you through
           [--retry=s] [--message=x]
  pad down                              the applications that are down
  pad up <app>                          back from maintenance
  pad help                              this list

Without a command word, pad runs the cli application: pad [page].
TEXT );

  return 0;

?>
