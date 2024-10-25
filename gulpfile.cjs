// Core Modules
const fs = require('fs');
const path = require('path');

// NPM Modules
const gulp = require('gulp');
const iconv = require('gulp-iconv-lite');

// Gulp task to convert all PHP files encoding from any encoding (like us-ascii) to utf-8
gulp.task('convert-php-encoding', function () {
  return gulp
    .src('./wp-fedora-core/**/*.php') // Include all PHP files inside the 'wp-fedora' directory and subdirectories
    .pipe(iconv({ from: 'us-ascii', to: 'utf-8' })) // Convert encoding to utf-8
    .pipe(gulp.dest('./wp-fedora-core')); // Save the converted files back to the same directory
});

// Copy /dist/assets/ folder to the plugin folder (retain `assets` folder)
gulp.task('copy-assets-from-dist-to-plugin-folder', function () {
  return gulp.src('dist/assets/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets'));
});

// Copy /src/assets/img/ folder to the plugin folder inside /assets
gulp.task('copy-img-to-plugin-assets', function () {
  return gulp.src('src/assets/img/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets/img'));
});

// Copy /src/php/ folder to the plugin folder
gulp.task('copy-php-to-plugin-folder', function () {
  return gulp.src('src/php/**/*').pipe(gulp.dest('wp-fedora-core/wp-fedora'));
});

// Copy /src/assets/*.xsl files to the plugin folder inside /assets
gulp.task('copy-xsl-to-plugin-assets', function () {
  return gulp.src('src/assets/*.xsl').pipe(gulp.dest('wp-fedora-core/wp-fedora/assets'));
});

// Ensure the package directories wp-fedora-core/wp-fedora exist
gulp.task('create-plugin-folders', function (done) {
  const coreDir = path.resolve(__dirname, 'wp-fedora-core');
  const pluginDir = path.join(coreDir, 'wp-fedora');

  // Check if wp-fedora-core exists, if not create it
  if (!fs.existsSync(coreDir)) {
    fs.mkdirSync(coreDir, { recursive: true });
    console.log(`Created directory: ${coreDir}`);
  }

  // Check if wp-fedora directory inside wp-fedora-core exists, if not create it
  if (!fs.existsSync(pluginDir)) {
    fs.mkdirSync(pluginDir, { recursive: true });
    console.log(`Created directory: ${pluginDir}`);
  }

  done();
});

// Recursively remove empty folders from the wp-fedora directory
gulp.task('delete-empty-folders', function (done) {
  const distDir = path.resolve(__dirname, 'wp-fedora');

  function removeEmptyDirs(dir) {
    // Check if the directory exists before proceeding
    if (!fs.existsSync(dir)) {
      console.log(`Directory ${dir} does not exist, skipping...`);
      return false;
    }

    const entries = fs.readdirSync(dir, { withFileTypes: true });
    let isEmpty = true;

    entries.forEach((entry) => {
      const entryPath = path.join(dir, entry.name);

      if (entry.isDirectory()) {
        // Recursively remove subdirectories
        if (removeEmptyDirs(entryPath)) {
          fs.rmdirSync(entryPath);
          console.log(`Removed empty folder: ${entryPath}`);
        } else {
          isEmpty = false;
        }
      } else {
        isEmpty = false;
      }
    });

    return isEmpty; // Return true if the directory is empty, otherwise false
  }

  removeEmptyDirs(distDir);
  done();
});

// Clean up directories, excluding zip files
gulp.task('delete-build-folders', function (done) {
  const distDir = path.resolve(__dirname, 'dist');
  const pluginDir = path.resolve(__dirname, 'wp-fedora-core');

  // Function to clean directories but exclude .zip and critical files like index.php
  function cleanDirectoryExcludingZip(directory) {
    if (fs.existsSync(directory)) {
      fs.readdirSync(directory).forEach((file) => {
        const filePath = path.join(directory, file);
        const stat = fs.statSync(filePath);

        if (stat.isDirectory()) {
          fs.rmSync(filePath, { recursive: true, force: true }); // Remove the directory itself
        } else if (!file.endsWith('.zip') && file !== 'index.php') {
          // Exclude index.php
          console.log(`Deleting file: ${filePath}`);
          fs.unlinkSync(filePath); // Remove the file
        } else {
          console.log(`Skipping: ${filePath}`);
        }
      });

      // After cleaning out all files, remove the root directory itself
      fs.rmSync(directory, { recursive: true, force: true }); // Updated to use fs.rmSync instead of fs.rmdirSync
      console.log(`Removed directory: ${directory}`);
    }
  }

  cleanDirectoryExcludingZip(pluginDir);
  cleanDirectoryExcludingZip(distDir);

  done();
});

// Move wp-fedora-core.php one level up
gulp.task('move-wp-fedora-core-php', function (done) {
  const sourcePath = 'wp-fedora-core/wp-fedora/wp-fedora-core.php';
  const targetPath = 'wp-fedora-core/wp-fedora-core.php';

  if (fs.existsSync(sourcePath)) {
    fs.renameSync(sourcePath, targetPath);
    console.log(`Moved wp-fedora-core.php to ${targetPath}`);
  } else {
    console.log('wp-fedora-core.php not found in the plugin folder.');
  }

  done();
});

// Gulp task to dynamically load and zip the entire wp-fedora-core folder
gulp.task('zip-plugin', async function () {
  const zip = (await import('gulp-zip')).default;
  const pluginDir = 'wp-fedora-core';
  const outputName = 'wp-fedora-core.zip';

  return gulp
    .src(`${pluginDir}/**/*`, { base: '.' }) // Include the folder itself and all its contents
    .pipe(zip(outputName)) // Create the zip
    .pipe(gulp.dest('./')); // Save it to the root directory
});

// Build task (run all steps in sequence)
gulp.task(
  'build-plugin-core',
  gulp.series(
    'create-plugin-folders',
    'copy-php-to-plugin-folder',
    'copy-assets-from-dist-to-plugin-folder',
    'copy-img-to-plugin-assets',
    'copy-xsl-to-plugin-assets',
    'move-wp-fedora-core-php',
    function (done) {
      console.log('Plugin packaged successfully!');
      done();
    }
  )
);

// Clean task (run all clean-up steps in sequence)
gulp.task(
  'clean-plugin-core',
  gulp.series('convert-php-encoding', 'delete-empty-folders', function (done) {
    console.log('Package cleaned successfully!');
    done();
  })
);
// Zip Core plugin
gulp.task(
  'zip-plugin-core',
  gulp.series('zip-plugin', function (done) {
    console.log('Package zipped successfully!');
    done();
  })
);

// Project clean task
gulp.task(
  'clean-project',
  gulp.series('delete-build-folders', function (done) {
    console.log('Project cleaned successfully!');
    done();
  })
);
