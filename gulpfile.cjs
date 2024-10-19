// Core Modules
const fs = require('fs');
const path = require('path');

// NPM Modules
const gulp = require('gulp');

// Function to check if a file is trivial (small or empty)
function isTrivialFile(filePath) {
  const stat = fs.statSync(filePath);

  // Ignore files larger than 1KB (adjust size as needed)
  if (stat.size > 1024) {
    return false;
  }

  // Read the file content to check if it's trivial (like 'export {};', empty files, etc.)
  const content = fs.readFileSync(filePath, 'utf8').trim();

  // Consider a file trivial if it is small, empty, or contains only export {}; or similar
  return (
    !content || content === 'export {};' || content === '{}' || content.length < 10 // Length threshold for trivial content
  );
}

// Function to move contents of a directory to another directory, retaining folder structure
function moveFolderContents(sourceDir, targetDir) {
  if (!fs.existsSync(sourceDir)) {
    console.log(`Source directory ${sourceDir} does not exist.`);
    return;
  }

  fs.readdirSync(sourceDir).forEach((file) => {
    const sourcePath = path.join(sourceDir, file);
    const targetPath = path.join(targetDir, file);

    if (fs.statSync(sourcePath).isDirectory()) {
      // If it's a directory, move the entire directory, keeping the structure
      const newTargetDir = path.join(targetDir, file); // Retain subfolder structure
      if (!fs.existsSync(newTargetDir)) {
        fs.mkdirSync(newTargetDir, { recursive: true });
      }
      moveFolderContents(sourcePath, newTargetDir);
    } else {
      // Move the file to the corresponding directory in the target path
      fs.renameSync(sourcePath, targetPath);
      console.log(`Moved ${sourcePath} to ${targetPath}`);
    }
  });
}

// Function to recursively check all files and remove trivial ones
function removeTrivialFilesRecursively(dir) {
  fs.readdirSync(dir).forEach((file) => {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);

    if (stat.isDirectory()) {
      // Recursively handle directories
      removeTrivialFilesRecursively(filePath);
    } else if (isTrivialFile(filePath)) {
      console.log(`Removing trivial file: ${filePath}`);
      fs.unlinkSync(filePath); // Delete the trivial file
    }
  });
}

// Gulp task to move PHP folder contents to root and delete the PHP folder
gulp.task('clean-php-folder', function (done) {
  const phpDir = path.resolve(__dirname, 'vwp-plugin/php');
  const rootDir = path.resolve(__dirname, 'vwp-plugin');

  // Check if the PHP directory exists before proceeding
  if (!fs.existsSync(phpDir)) {
    console.log(`PHP directory ${phpDir} does not exist, skipping...`);
    done();
    return;
  }

  // Move the contents of the PHP directory to the root, retaining subfolders
  moveFolderContents(phpDir, rootDir);

  done();
});

// Copy necessary files to the package directory
gulp.task('copy-files-to-folder', function () {
  return gulp.src('src/**/*').pipe(gulp.dest('vwp-plugin'));
});

// Recursively remove empty folders from the vwp-plugin directory
gulp.task('delete-empty-folders', function (done) {
  const distDir = path.resolve(__dirname, 'vwp-plugin');

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
gulp.task('delete-folders', function (done) {
  const distDir = path.resolve(__dirname, 'dist');
  const pluginDir = path.resolve(__dirname, 'vwp-plugin');

  // Function to clean directories but exclude .zip files
  function cleanDirectoryExcludingZip(directory) {
    if (fs.existsSync(directory)) {
      fs.readdirSync(directory).forEach((file) => {
        const filePath = path.join(directory, file);
        const stat = fs.statSync(filePath);

        if (stat.isDirectory()) {
          fs.rmSync(filePath, { recursive: true, force: true });
        } else if (!file.endsWith('.zip')) {
          fs.unlinkSync(filePath);
        }
      });
    }
  }

  cleanDirectoryExcludingZip(pluginDir);
  cleanDirectoryExcludingZip(distDir);

  done();
});

// Remove .scss files from the vwp-plugin directory recursively
gulp.task('delete-scss-files', function (done) {
  const distDir = path.resolve(__dirname, 'vwp-plugin');

  function removeScssFiles(dir) {
    const entries = fs.readdirSync(dir, { withFileTypes: true });

    entries.forEach((entry) => {
      const filePath = path.join(dir, entry.name);

      if (entry.isDirectory()) {
        removeScssFiles(filePath); // Recurse into subdirectories
      } else if (entry.isFile() && entry.name.endsWith('.scss')) {
        console.log(`Removing .scss file: ${filePath}`);
        fs.unlinkSync(filePath); // Delete the .scss file
      }
    });
  }

  removeScssFiles(distDir);
  done();
});

// Gulp task to remove trivial files after build
gulp.task('delete-trivial-files', function (done) {
  const distDir = path.resolve(__dirname, 'vwp-plugin'); // Set the directory to clean
  // Start recursive deletion in the vwp-plugin directory
  removeTrivialFilesRecursively(distDir);
  done();
});

// Remove .ts files from the vwp-plugin directory recursively
gulp.task('delete-ts-files', function (done) {
  const distDir = path.resolve(__dirname, 'vwp-plugin');

  function removeTsFiles(dir) {
    const entries = fs.readdirSync(dir, { withFileTypes: true });

    entries.forEach((entry) => {
      const filePath = path.join(dir, entry.name);

      if (entry.isDirectory()) {
        removeTsFiles(filePath); // Recurse into subdirectories
      } else if (entry.isFile() && entry.name.endsWith('.ts')) {
        console.log(`Removing .ts file: ${filePath}`);
        fs.unlinkSync(filePath); // Delete the .ts file
      }
    });
  }

  removeTsFiles(distDir);
  done();
});

// Move built assets from dist to plugin package
gulp.task('move-assets-from-dist', function (done) {
  if (fs.existsSync('dist')) {
    return gulp.src('dist/**/*').pipe(gulp.dest('vwp-plugin'));
  } else {
    console.log('dist folder not found. Skipping move-dist task.');
    done();
  }
});

// Ensure the package directory exists
gulp.task('verify-folders-exist', function (done) {
  if (!fs.existsSync('vwp-plugin')) {
    fs.mkdirSync('vwp-plugin', { recursive: true });
  }
  done();
});

// Gulp task to dynamically load and zip the plugin package
gulp.task('zip-plugin', async function () {
  const zip = (await import('gulp-zip')).default;
  const pluginDir = 'vwp-plugin';
  const outputName = 'vwp-plugin.zip';

  return gulp
    .src(`${pluginDir}/**/*`, { base: pluginDir }) // Grab all files inside the 'vwp-plugins' folder
    .pipe(zip(outputName)) // Create the zip
    .pipe(gulp.dest('./')); // Save it to the root directory
});

// Build task (run all steps in sequence)
gulp.task(
  'build-package',
  gulp.series('verify-folders-exist', 'copy-files-to-folder', 'move-assets-from-dist', function (done) {
    console.log('Plugin packaged successfully!');
    done();
  })
);

// Clean task (run all clean-up steps in sequence)
gulp.task(
  'clean-package',
  gulp.series(
    'delete-trivial-files',
    'delete-ts-files',
    'delete-scss-files',
    'clean-php-folder',
    'delete-empty-folders',
    function (done) {
      console.log('Package cleaned successfully!');
      done();
    }
  )
);

// Project clean task
gulp.task(
  'clean-project',
  gulp.series('delete-folders', function (done) {
    console.log('Project cleaned successfully!');
    done();
  })
);
