// palette= in the browser: subs/charts/_lib/palette.php written again in JavaScript. The server
// hands a chart given palette= the style attribute of each number of categorical slots it may
// use (padChartPaletteStyles); the chart is drawn as ever, then the one for the slots it drew -
// the highest pc-c or pc-l class in the SVG - goes on its svg, as padChartPaletteApply puts it
// there on the server. Written once per page, after core.js, by the first client chart with a
// palette: it wraps PadChart.render, which hands a chart without a palette back as it drew it.

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
    var style = styles[C.padChartPaletteSlots(svg) - 1];
    var rules = style.indexOf('--pad-chart-heat-ink-') >= 0 ? C.padChartPaletteRules() : '';
    return svg.replace(/(<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart [^"]*")([^>]*>[\s\S]*?)(<\/style>)/g,
                       function (whole, open, middle, close) { return open + ' style="' + style + '"' + middle + rules + close; });
  };

  C.padChartPaletteRules = function () {
    var rules = '';
    for (var step = 0; step <= 6; step++)
      rules += '.pad-chart[style*=heat-ink] .pc-gap.pc-h' + step + '+text{fill:var(--pad-chart-heat-ink-' + step + ');stroke:none}';
    return rules;
  };

  var render = C.render;

  C.render = function (spec) {
    var svg = render(spec);
    return (spec && Array.isArray(spec.palette)) ? C.padChartPaletteApply(svg, spec.palette) : svg;
  };

})(typeof window !== 'undefined' ? (window.PadChart = window.PadChart || {}) : (globalThis.PadChart = globalThis.PadChart || {}));
