<?php

  // A notice the visitor dismisses - the toast widget.
  //
  //   {toast 'Saved', tone='success'}Your changes are kept.{/toast}
  //   {toast 'Payment failed', tone='error', place='top'/}
  //
  // The toast is a <dialog> with the open attribute: shown as the page arrives, not modal -
  // the page stays usable - and closed by its button, which submits a form of method dialog:
  // the browser closes the dialog it stands in, so dismissing needs no script. Its text is
  // a status region, an alert for a warning or an error, so a toast a fragment or a live
  // region brings later is read out.
  //
  // place= puts it in the top or the bottom corner of the window instead of in the flow of
  // the page; two toasts in one corner lie over each other, so a page puts one there.
  // timeout= closes it after so many seconds: the one thing that needs the script, written
  // once per page with the CSP nonce, which holds the time while the pointer or the focus is
  // on the toast - a notice that goes before it is read is an accessibility fault - and
  // closes a toast with Escape. Printed, no toast shows.
  //
  // padToast         the dialog: the title, the text, the close button
  // padToastTimeout  the seconds of timeout=, 0 without it
  // padToastStyle    the CSS, assets/toast.css, once per page
  // padToastScript   the script, assets/toast.js, once per page - only for a toast that
  //                  closes by itself

  function padToast ( $title, $text, $tone, $place, $timeout ) {

    $id     = padWidgetId ( 'toast', [ $title, $text, $tone, $place ] );
    $role   = in_array ( $tone, [ 'warning', 'error' ] ) ? 'alert' : 'status';
    $class  = "pad-toast pad-toast-$tone" . ( $place != 'inline' ? " pad-toast-$place" : '' );
    $name   = ( $title !== '' ) ? "aria-labelledby=\"$id-title\"" : 'aria-label="' . ucfirst ( $tone ) . '"';
    $later  = $timeout ? " data-pad-timeout=\"$timeout\"" : '';

    return padToastStyle ()
         . padProtect ( "<dialog open class=\"$class\" id=\"$id\" $name$later>"
                      . "<div class=\"pad-toast-body\" role=\"$role\">"
                      . ( $title !== '' ? "<p class=\"pad-toast-title\" id=\"$id-title\">" . padWidgetAttr ( $title ) . '</p>' : '' )
                      . ( $text  !== '' ? '<div class="pad-toast-text">' : '' ) )
         . $text
         . padProtect ( ( $text !== '' ? '</div>' : '' ) . '</div>'
                      . '<form class="pad-toast-form" method="dialog">'
                      . '<button class="pad-toast-close" aria-label="Dismiss"><span aria-hidden="true">&#215;</span></button>'
                      . '</form></dialog>' )
         . ( $timeout ? padToastScript () : '' );

  }

  // A whole number of seconds, 1 to 600 - a notice longer than ten minutes is no toast.

  function padToastTimeout () {

    global $padCheckSyntax;

    $value = padTagParm ( 'timeout', 0 );

    if ( $value === 0 )
      return 0;

    $value = is_scalar ( $value ) ? trim ( (string) $value ) : '';

    if ( preg_match ( '/^\d+$/D', $value ) and (int) $value >= 1 and (int) $value <= 600 )
      return (int) $value;

    if ( $padCheckSyntax )
      padError ( "the {toast} has no timeout '" . padMakeSafe ( $value, 20 ) . "' - a whole number of seconds from 1 to 600" );

    return 0;

  }

  function padToastStyle () {

    return padWidgetStyle ( 'toast',
      [ 'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#4f4e4a', '#bdbcb4' ],
        'line'    => [ '#e4e3df', '#3a3a37' ],
        'surface' => [ '#ffffff', '#242422' ],
        'info'    => [ '#2a78d6', '#5598e7' ],
        'success' => [ '#127a52', '#4fc794' ],
        'warning' => [ '#b46b00', '#f0a63a' ],
        'error'   => [ '#c62f2e', '#ef6b6a' ] ],
      padWidgetAsset ( 'toast.css' ) );

  }

  function padToastScript () {

    return padWidgetScript ( 'toast', padWidgetAsset ( 'toast.js' ) );

  }

?>
