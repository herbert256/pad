(function () {
  if (window.padWizardReady) return;
  window.padWizardReady = true;
  document.addEventListener('click', function (e) {
    var button = e.target.closest && e.target.closest('.pad-wizard button[data-pad-step]');
    var radio = button && document.getElementById(button.getAttribute('data-pad-step'));
    if (!radio) return;
    radio.click();
    radio.focus({ preventScroll: true });
    var wizard = radio.closest('.pad-wizard');
    if (wizard && wizard.getBoundingClientRect().top < 0) wizard.scrollIntoView({ block: 'start' });
  });
})();
