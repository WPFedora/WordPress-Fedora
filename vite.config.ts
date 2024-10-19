// Core Modules
import { exec } from 'child_process';
import path, { resolve } from 'path';
import { existsSync, readFileSync } from 'fs';

// NPM Modules
import vue from '@vitejs/plugin-vue';
import { glob } from 'glob';
import { defineConfig } from 'vite';

// Use glob to find all valid TypeScript and Vue files, excluding .d.ts files
const jsEntryPoints = glob.sync(resolve(__dirname, 'src/**/*.ts')).filter((file) => !file.endsWith('.d.ts'));

// Create an input object where each JS/TS file is treated as a separate entry
const inputFiles = jsEntryPoints.reduce((entries, file) => {
  const name = file.replace(/.*[\\\/]/, '').replace(/\.ts$/, ''); // Use filename without extension as entry name
  entries[name] = file;
  return entries;
}, {});

// Add the SCSS entry point explicitly
inputFiles['styles'] = resolve(__dirname, 'src/assets/scss/styles.scss');

// Function to check if a file is empty or trivial
function isOriginalFileEmptyOrTrivial(filePath: string) {
  // Skip transformed files with query params like '?vue&type=script'
  if (filePath.includes('?')) {
    return false;
  }

  // Check if the file exists and read its contents
  if (!existsSync(filePath)) {
    return false;
  }

  const content = readFileSync(filePath, 'utf-8').trim();
  return !content || content === 'export {};' || content.length < 10;
}

export default defineConfig({
  plugins: [
    vue(),
    {
      name: 'watch-src-folder-and-run-script',
      handleHotUpdate({ file, server }) {
        const relativeFilePath = path.relative(server.config.root, file);

        // Only trigger when files in the /src folder are changed
        if (relativeFilePath.startsWith('src/')) {
          console.log(`File in /src changed: ${file}`);

          // Trigger your custom NPM script
          exec('npm run build:dev', (err, stdout, stderr) => {
            if (err) {
              console.error(`Error running script: ${err}`);
              return;
            }
            console.log(`Script output: ${stdout}`);
          });
        }
      }
    }
  ],
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: inputFiles, // Use the dynamically created input object
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: (assetInfo) => {
          // Explicitly name styles.scss as styles.css
          if (assetInfo.name === 'styles.css' || assetInfo.name === 'styles.scss') {
            return 'assets/styles.css';
          }
          return 'assets/[name].[ext]'; // Default behavior for other assets
        },
        manualChunks(id) {
          // Exclude files that are minimal or trivial
          if (isOriginalFileEmptyOrTrivial(id)) {
            return undefined; // Do not create a chunk for empty files
          }
        }
      }
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@use "src/assets/scss/_config.scss" as config;` // Global SCSS import
      }
    }
  }
});
