<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

![image](_repo/cover-image.jpg)

# WordPress Fedora Plugin

<div align="center">
  <p align="center">
   WP Fedora is a powerful, performance-optimized WordPress distro designed specifically for SEOs. Our community is dedicated to helping SEO professionals maximize their WordPress sites through WP Fedora, and it's available built-in tools and utilities.
   <br />
   <br />
   <a href="https://github.com/WPFedora/WordPress-Fedora/issues/new?assignees=&labels=bug%2Cpending+triage&projects=&template=bug_report.yaml">Report Bug</a>
   &middot;
   <a href="https://github.com/WPFedora/WordPress-Fedora/issues/new?assignees=&labels=enhancement%2Cpending+triage&projects=&template=feature_request.yaml">Feature Request</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#build-logic">Build Logic</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

### Built With

- [Node Version Manager (NVM)](https://github.com/nvm-sh/nvm)
- [Node.js](https://nodejs.org/)
- [Vite](https://vite.dev/)
- [SASS/SCSS](https://sass-lang.com/)
- [Gulp.js](https://gulpjs.com/)
- [ESLint](https://eslint.org/)
- [Prettier](https://prettier.io/)
- [Warp Terminal](https://warp.dev)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

All plugin files live inside of the `/src` folder. These are the instructions on setting up your project locally. To get a local copy up and running follow these simple steps.

### Prerequisites

For all of the awesome people using Node Version Manager (NVM) instead of Node.js, we have an `.nvmrc` file in the repo. For everyone else, please check this file to make sure that your Node.js version matches.

- Switch to correct Node.js Version

```zsh
nvm use
```

### Installation

1. Clone the repo.
   ```sh
   git clone https://github.com/WPFedora/WordPress-Fedora.git
   ```
2. Install NPM packages.
   ```zsh
   npm install
   ```
3. Run the start command to watch and build files.

```zsh
npm run start:dev
```

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- USAGE EXAMPLES -->

## Usage

Below, you will find our common commands and notes for general usage.

1. Run `npm run build:dev`.
   - When you build in dev, the plugin folder **IS NOT ZIPPED**. This is for those situations where you're working with local instance of WordPress using XAMP, LAMP, MAMP, etc. or even the LocalWP tool (which we use). Those steps are:
     - Build the new plugin folder.
     - Delete the current folder in your WP website.
     - Copy your new plugin folder into the website's plugins folder.
2. Run `npm run build:prod`.

   - When you build in prod, the plugin folder **IS ZIPPED** and ready for upload to a WP website.

3. Run `npm run start:dev`.

   - This is runs the default `vite` command. The terminal will tell you to open the browser to a `localhost` port. **We do not use the browser.**
   - A custom hot reload plugin is located in the Vite config file to watch all files in the `/src` folder.
   - Each time a file is changed, the hot reload will trigger a `npm run build:dev`.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- BUILD PROCESS LOGIC -->

## Build Logic

### Vite Build

Vite will convert all of your `.scss` to `.css`. These files, along with all `.js` files, will all be placed in a `/dist` folder.

### Build Plugin Core

This covers the series of tasks that are used to initially build the plugin folder. Found in both `build:dev` and `build:prod` scripts.

1. create-plugin-folders

   - This will check to see if folders `wp-fedora-core` and `wp-fedora-core/wp-fedora` both exist. If not, they will be created.

2. copy-php-to-plugin-folder

   - This copies the `php` folder over to `wp-fedora-core/wp-fedora`.

3. copy-assets-from-dist-to-plugin-folder

   - This copies the `assets` folder from `dist/assets` over to `wp-fedora-core/wp-fedora/assets`.

4. copy-img-to-plugin-assets

   - This copies the `img` folder from `src/assets/img` over to `wp-fedora-core/wp-fedora/assets/img`.

5. copy-xsl-to-plugin-assets

   - This copies the `*.xsl` file from `src/**/*` (which is where the sitemap file is located) over to `wp-fedora-core/wp-fedora/assets`.

6. move-wp-fedora-core-php
   - When the `php` is copied over, the `wp-fedora-core.php` file gets put into the `wp-fedora-core/wp-fedora` folder. This task will move it up one level to the `wp-fedora-core` folder.

### Clean Plugin Core

This covers the series of tasks that are used to clean up the plugin folder. Found in both `build:dev` and `build:prod` scripts.

1. convert-php-encoding

   - With Vite, the PHP files don't get the correct file encoding. This step properly converts them to `utf-8` for WordPress.

2. delete-empty-folders

   - This will recursively delete all empty folders from the parent plugin folder `wp-fedora-core`.

### Zip Plugin Core

For this one, we will "compress" or "zip" the plugin folder. This is the step that will produce the final plugin zip file that you upload into the WordPress website.

Found in `build:prod` script.

### Clean Project

This covers the series of tasks that are used to clean up the plugin folder after the zip file is created. Found in both `build:dev` and `build:prod` scripts.

1. delete-build-folders

   - Delete the `/dist` folder that is generated by Vite.
   - Delete the plugin folder (i.e., `/wp-fedora-core`).

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- ROADMAP -->

## Roadmap

We don't have a dedicated roadmap outside of Github. Simply check the [open issues](https://github.com/WPFedora/WordPress-Fedora/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE.md` for more information.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

Without these people and tools, life would be too complicated.

- Good food.
- Good company.
- Good tools.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/WPFedora/WordPress-Fedora.svg?style=for-the-badge
[contributors-url]: https://github.com/WPFedora/WordPress-Fedora/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/WPFedora/WordPress-Fedora.svg?style=for-the-badge
[forks-url]: https://github.com/WPFedora/WordPress-Fedora/network/members
[stars-shield]: https://img.shields.io/github/stars/WPFedora/WordPress-Fedora.svg?style=for-the-badge
[stars-url]: https://github.com/WPFedora/WordPress-Fedora/stargazers
[issues-shield]: https://img.shields.io/github/issues/WPFedora/WordPress-Fedora.svg?style=for-the-badge
[issues-url]: https://github.com/WPFedora/WordPress-Fedora/issues
[license-shield]: https://img.shields.io/github/license/WPFedora/WordPress-Fedora.svg?style=for-the-badge
[license-url]: https://github.com/WPFedora/WordPress-Fedora/blob/main/license.md
