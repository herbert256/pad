// PAD - the little the shared frame does in the browser (apps/_common/_tags/padFrame.pad):
// light or dark for a page that has a setting of its own - the editor's - and the side's menu
// scrolled to the page shown. The pull-down of the brand top left is www/_common/brand.js.
//
// The frame is light - <html data-theme="light"> as padFrame writes it, whatever the system
// prefers - and a visitor colours it with a theme from the switcher in the bar, Dark among
// them. The bar had a light/dark switch whose choice was kept for every application; it is
// gone, and so is the choice it kept, so a page chosen dark with it does not stay dark.
//
// padTheme.get ()      'light', 'dark' or 'auto' (the system decides) - the page's own now
// padTheme.set ( t )   puts the page in it - and tells the page with a 'padtheme' event on
//                      document, its detail the choice, so a page with colours of its own
//                      (the editor's Monaco) follows; nothing is kept
// padTheme.dark ()     whether the page shows dark now

( function () {

  var root = document.documentElement;

  try { localStorage.removeItem ( 'padTheme' ); } catch ( e ) {}

  function known ( theme ) {
    return theme === 'light' || theme === 'dark' ? theme : 'auto';
  }

  function apply ( theme ) {
    if ( known ( theme ) !== 'auto' ) root.setAttribute ( 'data-theme', theme );
    else root.removeAttribute ( 'data-theme' );
  }

  window.padTheme = {

    get: function () { return known ( root.getAttribute ( 'data-theme' ) ); },

    set: function ( theme ) {
      theme = known ( theme );
      apply ( theme );
      document.dispatchEvent ( new CustomEvent ( 'padtheme', { detail: theme } ) );
    },

    dark: function () {
      var theme = root.getAttribute ( 'data-theme' );
      return theme ? theme === 'dark' : !! ( window.matchMedia && matchMedia ( '(prefers-color-scheme: dark)' ).matches );
    }

  };

  document.addEventListener ( 'DOMContentLoaded', function () {

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
