// The combined showcases in a real browser, with their live local assets and shared frame.
// Run: node tests/subsystems/showcase-browser.cjs (local Apache and Chrome required).
const fs = require('node:fs'), path = require('node:path'), os = require('node:os');
const cp = require('node:child_process'), assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../..');
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'pad-showcase-browser-'));
const chrome = process.env.PAD_CHROME || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const host = process.env.PAD_HOST || 'http://localhost/pad/';
const cases = ['games/?index', 'images/?index', 'tables/?index', 'widgets/?index', 'calendars/?index', 'charts/?index',
               'widgets/?examples/tabs/product_page', 'images/?examples/img/gallery'];
(async () => {
  let count = 0;
  for (const [i, route] of cases.entries()) {
    const response = await fetch(host + route);
    assert.equal(response.status, 200, route);
    const test = `
      window.addEventListener('load', () => {
        const checks = [], check = (ok, name) => checks.push({ ok: Boolean(ok), name });
        check(document.querySelectorAll('.frame-main').length === 1, 'one application frame');
        check(document.documentElement.scrollWidth <= innerWidth + 2, 'no horizontal page overflow');
        check(!document.querySelector('.pad-brand a[href$="/toys/"]'), 'no separate Toys application');
        const source = document.querySelector('#demo-template');
        if (source) {
          check(source.textContent.includes('{'), 'source preserved');
          const tag = source.querySelector('.hl-tag');
          check(tag && getComputedStyle(tag).color !== getComputedStyle(source).color, 'source highlighting');
          check(document.querySelector('[data-demo-copy="demo-template"]'), 'copy control');
          check(document.querySelector('.demo-result a[href*="#s="]'), 'playground link');
          const radios = document.querySelectorAll('.demo-result input[type="radio"]');
          if (radios.length) {
            radios[0].click();
            check(radios[0].checked && !radios[1].checked, 'tabs remain interactive');
          }
          for (const img of document.querySelectorAll('.demo-result img')) {
            img.loading = 'eager';
            check(img.complete && img.naturalWidth > 0, 'image asset loaded');
          }
        } else check(document.querySelector('.demo-catalog a[href^="?examples/"]'), 'owner examples discoverable');
        const result = document.createElement('pre'); result.id = 'showcase-checks';
        result.textContent = JSON.stringify(checks); document.body.append(result);
      });`;
    const html = (await response.text()).replace('<head>', `<head><base href="${host + route}"><script>${test}</script>`);
    const file = path.join(tmp, `page-${i}.html`); fs.writeFileSync(file, html);
    for (const width of [1440, 390]) {
      const run = cp.spawnSync(chrome, ['--headless=new', '--no-sandbox', '--disable-gpu', '--no-first-run',
        '--user-data-dir=' + path.join(tmp, 'profile'), '--window-size=' + width + ',1000',
        '--virtual-time-budget=4000', '--dump-dom', 'file://' + file], {encoding:'utf8', maxBuffer:20e6, timeout:30000});
      fs.writeFileSync(path.join(tmp, `page-${i}-${width}.log`), run.stderr || '');
      assert.equal(run.status, 0, 'Chrome: ' + route);
      const match = run.stdout.match(/<pre id="showcase-checks">([\s\S]*?)<\/pre>/);
      assert.ok(match, 'No browser result: ' + route);
      const checks = JSON.parse(match[1].replaceAll('&amp;', '&').replaceAll('&lt;', '<').replaceAll('&gt;', '>'));
      const failed = checks.filter(check => !check.ok);
      assert.deepEqual(failed, [], route + ' at ' + width + 'px; artifacts: ' + tmp);
      count += checks.length;
    }
  }
  console.log(`${count} desktop/mobile showcase browser checks passed; artifacts: ${tmp}`);
})().catch(error => { console.error(error); process.exitCode = 1; });
