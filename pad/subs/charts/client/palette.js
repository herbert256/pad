// palette= in the browser: subs/charts/_lib/palette.php written again in JavaScript. The server
// hands a chart given palette= the class and the rules of each number of categorical slots it
// may use (padChartPaletteStyles); the chart is drawn as ever, then the class for the slots it
// drew - the highest pc-c or pc-l class in the SVG - goes on its svg and the rules into its
// <style>, as padChartPaletteApply puts them there on the server. Written once per page, after
// core.js, by the first client chart with a palette: it wraps PadChart.render, which hands a
// chart without a palette back as it drew it.

(function (C) {

  'use strict';

  if (C.paletteReady) return;
  C.paletteReady = true;

  C.padChartPaletteSlots = function (svg) {
    var n = 0, re = /class="(?:[^"]*\s)?pc-[cl]([1-8])(?=[\s"])/g, m;
    while ((m = re.exec(svg)) !== null) n = Math.max(n, Number(m[1]));
    return n || 1;
  };

  C.padChartPaletteApply = function (svg, styles) {
    if (typeof svg !== 'string' || svg === '') return svg;
    var rule = styles[C.padChartPaletteSlots(svg) - 1];
    return svg.replace(/(<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart [^"]*)("[^>]*>[\s\S]*?)(<\/style>)/g,
                       function (whole, open, middle, close) { return open + ' ' + rule[0] + middle + rule[1] + close; });
  };

  var render = C.render;

  C.render = function (spec) {
    var svg = render(spec);
    return (spec && Array.isArray(spec.palette)) ? C.padChartPaletteApply(svg, spec.palette) : svg;
  };

})(typeof window !== 'undefined' ? (window.PadChart = window.PadChart || {}) : (globalThis.PadChart = globalThis.PadChart || {}));
