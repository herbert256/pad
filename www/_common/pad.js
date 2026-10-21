// PAD - the little the shared frame does in the browser (apps/_common/_tags/padFrame.pad):
// the light/dark choice, one for every PAD application, and the application menu under the
// logo. Loaded in <head>, so the choice is on the page before it paints. Everything works
// without it: the menu is a <details>, and the theme follows the system.
//
// padTheme.get ()      'light', 'dark' or 'auto' (the system decides)
// padTheme.set ( t )   keeps that choice - and tells the page with a 'padtheme' event on
//                      document, its detail the choice, so a page with colours of its own
//                      (the editor's Monaco) follows
// padTheme.dark ()     whether the page shows dark now

( function () {

  var root = document.documentElement;
  var key  = 'padTheme';

  function stored () {
    try { return localStorage.getItem ( key ); } catch ( e ) { return null; }
  }

  function known ( theme ) {
    return theme === 'light' || theme === 'dark' ? theme : 'auto';
  }

  function apply ( theme ) {
    if ( known ( theme ) !== 'auto' ) root.setAttribute ( 'data-theme', theme );
    else root.removeAttribute ( 'data-theme' );
  }

  apply ( stored () );

  window.padTheme = {

    get: function () { return known ( stored () ); },

    set: function ( theme ) {
      theme = known ( theme );
      try {
        if ( theme === 'auto' ) localStorage.removeItem ( key );
        else localStorage.setItem ( key, theme );
      } catch ( e ) {}
      apply ( theme );
      document.dispatchEvent ( new CustomEvent ( 'padtheme', { detail: theme } ) );
    },

    dark: function () {
      var theme = root.getAttribute ( 'data-theme' );
      return theme ? theme === 'dark' : !! ( window.matchMedia && matchMedia ( '(prefers-color-scheme: dark)' ).matches );
    }

  };

  document.addEventListener ( 'DOMContentLoaded', function () {

    var toggle = document.querySelector ( '.frame-theme' );

    if ( toggle )
      toggle.addEventListener ( 'click', function () {
        padTheme.set ( padTheme.dark () ? 'light' : 'dark' );
      } );

    // The application menu closes on a click elsewhere and on Escape.

    var brand = document.querySelector ( '.frame-brand' );

    if ( brand ) {

      document.addEventListener ( 'click', function ( event ) {
        if ( brand.open && ! brand.contains ( event.target ) )
          brand.open = false;
      } );

      document.addEventListener ( 'keydown', function ( event ) {
        if ( event.key === 'Escape' && brand.open ) {
          brand.open = false;
          brand.querySelector ( 'summary' ).focus ();
        }
      } );

    }

    // A long menu - the manual's - is scrolled to the page it is on, the menu only: down in
    // the side, across in the one row it is on a narrow screen.

    var nav  = document.querySelector ( '.frame-nav' );
    var here = nav && nav.querySelector ( '[aria-current="page"]' );

    if ( here ) {
      var box  = nav.getBoundingClientRect ();
      var item = here.getBoundingClientRect ();
      if ( nav.scrollHeight > nav.clientHeight && ( item.top < box.top || item.bottom > box.bottom ) )
        nav.scrollTop += item.top - box.top - ( nav.clientHeight - item.height ) / 2;
      if ( nav.scrollWidth > nav.clientWidth && ( item.left < box.left || item.right > box.right ) )
        nav.scrollLeft += item.left - box.left - ( nav.clientWidth - item.width ) / 2;
    }

  } );

} ) ();
