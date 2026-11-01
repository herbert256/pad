// PAD Showcase - the little the showcase does in the browser: copy buttons on the source cells.
// The light/dark switch and the themes are the frame's (www/_common/pad.js); the tags follow
// them by themselves, their colours use light-dark().

document.addEventListener ( 'DOMContentLoaded', function () {

  document.querySelectorAll ( '.copy' ).forEach ( function ( button ) {
    button.addEventListener ( 'click', function () {
      var source = document.getElementById ( button.getAttribute ( 'data-copy' ) );
      if ( ! source || ! navigator.clipboard ) return;
      navigator.clipboard.writeText ( source.textContent ).then ( function () {
        button.textContent = 'Copied';
        button.classList.add ( 'is-done' );
        setTimeout ( function () { button.textContent = 'Copy'; button.classList.remove ( 'is-done' ); }, 1400 );
      } );
    } );
  } );

} );
