// PAD - the pull-down of the brand every PAD application shows top left
// (apps/_common/_tags/padBrand.pad): it closes on a click elsewhere and on Escape. It works
// without this - it is a <details> - only it then stays open until its name is clicked again.

( function () {

  function menus () {
    return document.querySelectorAll ( '.pad-brand-menu[open]' );
  }

  document.addEventListener ( 'click', function ( event ) {
    menus ().forEach ( function ( menu ) {
      if ( ! menu.contains ( event.target ) )
        menu.open = false;
    } );
  } );

  document.addEventListener ( 'keydown', function ( event ) {
    if ( event.key !== 'Escape' )
      return;
    menus ().forEach ( function ( menu ) {
      menu.open = false;
      menu.querySelector ( 'summary' ).focus ();
    } );
  } );

} ) ();
