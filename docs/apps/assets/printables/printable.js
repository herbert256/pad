(function () {
  if (window.padPrintableReady) return;
  window.padPrintableReady = true;
  document.addEventListener('click', function (e) {
    var button = e.target.closest && e.target.closest('.pad-printable .pp-print');
    if (button) window.print();
  });
})();
