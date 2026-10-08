// The build of the islands application: the components of every framework in src/, made
// into www/islands/build/ with a manifest - {vite 'src/main.js'} in the templates reads it.
//
//   npm install
//   npm run build      www/islands/build/ - what the pages load
//   npm run dev        the dev server: its address goes into build/hot, and {vite} asks it
//
// Each framework compiles its own files: React *.react.jsx/tsx, Solid *.solid.jsx, Vue
// *.vue, Svelte *.svelte; Preact needs no compiler here - its components are written with
// htm. PAD_ISLANDS_WWW names www/islands/ when the project is built from a copy elsewhere.

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import vue from '@vitejs/plugin-vue';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import solid from 'vite-plugin-solid';

const here = path.dirname(fileURLToPath(import.meta.url));
const www = process.env.PAD_ISLANDS_WWW || path.resolve(here, '../../../www/islands');
const outDir = path.join(www, 'build');

// The dev server's address in build/hot while it runs, gone when it stops: {vite} asks the
// dev server while the file is there, and the build when it is not.

function padHot() {
  const hot = path.join(outDir, 'hot');
  const clear = () => fs.rmSync(hot, { force: true });
  return {
    name: 'pad-hot',
    apply: 'serve',
    configureServer(server) {
      server.httpServer?.once('listening', () => {
        const { port } = server.httpServer.address();
        fs.mkdirSync(outDir, { recursive: true });
        fs.writeFileSync(hot, `http://localhost:${port}`);
      });
      // however the dev server stops - Ctrl-C, a kill, its own exit - the file goes with it
      server.httpServer?.once('close', clear);
      process.once('exit', clear);
      for (const signal of ['SIGINT', 'SIGTERM', 'SIGHUP'])
        process.once(signal, () => process.exit());
    }
  };
}

export default defineConfig({
  root: here,
  base: './',
  plugins: [
    react({ include: /\.react\.[jt]sx$/ }),
    solid({ include: /\.solid\.jsx$/ }),
    vue(),
    svelte(),
    padHot()
  ],
  build: {
    outDir,
    emptyOutDir: true,
    manifest: true,
    rollupOptions: { input: [ 'src/main.js' ] }
  },
  server: { port: 5173, strictPort: true, cors: true }
});
