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

  C.padChartPaletteApply = function (svg, styles, intent, check) {
    if (typeof svg !== 'string' || svg === '') return svg;
    var slots = C.padChartPaletteSlots(svg);
    if (intent) {
      var issue = '';
      if (!['light','dark'].includes(intent.scheme)) issue = 'Scheme must be light or dark';
      else if (intent.usage !== '' && !['categorical','sequential','diverging','cyclic'].includes(intent.usage)) issue = 'Unknown scale type';
      else if (intent.usage !== '' && intent.usage !== intent.type) issue = 'This ' + intent.type + ' palette does not represent a ' + intent.usage + ' scale';
      else if (intent.usage === 'categorical' && intent.recommendedCategories !== null && slots > intent.recommendedCategories) issue = 'Category count exceeds the distinct palette colors';
      if (issue) { if (check !== false) throw new Error('the chart palette: ' + issue); return ''; }
    }
    var rule = styles[slots - 1];
    return svg.replace(/(<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart [^"]*)("[^>]*>[\s\S]*?)(<\/style>)/g,
                       function (whole, open, middle, close) { return open + ' ' + rule[0] + middle + rule[1] + close; });
  };

  var render = C.render;

  C.render = function (spec) {
    var svg = render(spec);
    return (spec && Array.isArray(spec.palette)) ? C.padChartPaletteApply(svg, spec.palette, spec.paletteUsage, spec.check) : svg;
  };

})(typeof window !== 'undefined' ? (window.PadChart = window.PadChart || {}) : (globalThis.PadChart = globalThis.PadChart || {}));
