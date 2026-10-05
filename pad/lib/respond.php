<?php

  // Response fragments: a named part of a page that a request can ask for alone.
  //
  //   {fragment 'order-list'}             ?orders&padFragment=order-list
  //     {orders}<li>{$number}</li>{/orders}
  //   {/fragment}
  //
  // A normal request renders the whole page, the fragment in its place. A request that
  // names a fragment - the padFragment parameter, or $padFragmentOnly set by the page's PHP,
  // say when an HX-Request header is there - gets that fragment's rendering and nothing
  // else: the page renders up to the end of the fragment, which then ends the request
  // through exits/exits.php as the whole response, unwrapped and untidied, the way a
  // padInclude request is. One template serves the full page and the region an HTMX swap
  // or an {ajax 'orders', fragment='order-list'} refreshes.
  //
  // The first fragment of that name to finish rendering is the response. A request for a
  // fragment the page never rendered is an error under the strict check, and an empty 404
  // otherwise - put a condition inside the fragment rather than round it.

  function padFragmentWanted () {

    global $pad, $padTag, $padType, $padOpt, $padFragmentOnly;

    return ( (string) $padFragmentOnly !== ''
             and $padTag [$pad] == 'fragment' and $padType [$pad] == 'pad'
             and (string) ( $padOpt [$pad] [1] ?? '' ) === (string) $padFragmentOnly );

  }

  // At the end of the request, a fragment that was asked for and never came.

  function padFragmentMissing () {

    global $padFragmentOnly, $padFragmentSent, $padCheckSyntax;

    if ( (string) $padFragmentOnly === '' or $padFragmentSent )
      return;

    if ( $padCheckSyntax )
      padError ( "there is no fragment named '$padFragmentOnly' on this page" );

    padExit ( 404 );

  }

?>
