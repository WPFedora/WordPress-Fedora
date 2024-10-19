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

![image](_repo/vue-wordpress-logo.png)

# WordPress Starter Plugin - Vue.js

<div align="center">
  <p align="center">
This is a forked repo from <a href="https://github.com/EvanAgee/vuejs-wordpress-plugin-starter" target="_blank">EvanAgee/vuejs-wordpress-plugin-starter</a>. Evan hasn't updated this repo in over 4 years, so we decided to fork it and manage our own version. This is still the same product, but we hope we can make it better.

Our goal here is to make plugin development easier with Vue. While we didn't originally start this repo, we have exponentially surpassed it's predecessor.
<br />
<a href="https://github.com/jared-leddy/wordpress-plugin-starter-vue"><strong>Explore the docs »</strong></a>
<br />
<br />
<a href="https://github.com/jared-leddy/wordpress-plugin-starter-vue">View Demo</a>
&middot;
<a href="https://github.com/jared-leddy/wordpress-plugin-starter-vue/issues">Report Bug</a>
&middot;
<a href="https://github.com/jared-leddy/wordpress-plugin-starter-vue/issues">Request Feature</a>

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
        <li><a href="#setup">Setup</a></li>
        <li><a href="#clean-unused-files">Clean Unused Files</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#build-logic">Build Logic</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

WordPress plugin development is a cumbersome endeavor. This is much harder to manage when your background is Node.js and JavaScript, instead of Laravel and PHP. This boilerplate project will rapidly get you up and running with developing a new plugin.

In the spirit of a modern upgrade, we have converted this into a TypeScript Vue plugin template.

<p align="right">(<a href="#top">back to top</a>)</p>

### Built With

- [Node Version Manager (NVM)](https://github.com/nvm-sh/nvm)
- [Node.js](https://nodejs.org/)
- [TypeScript](https://www.typescriptlang.org/)
- [Vue.js](https://vuejs.org/)
- [Vite](https://vite.dev/)
- [SASS/SCSS](https://sass-lang.com/)
- [Gulp.js](https://gulpjs.com/)
- [ESLint](https://eslint.org/)
- [Prettier](https://prettier.io/)

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

### Setup

Before you start working, you will need to rename all of the correct files, functions, assets, etc. **BEFORE** you run `npm install`. This setp is also about naming your plugin with your own namespacing.

1. Search and replace **the entire repo** for any occurances of `vwp-plugin`.
2. Search and replace **the entire repo** for any occurances of `vwp_plugin`.
3. Search and replace **the entire repo** for any occurances of `VwpPlugin`.
4. Manually rename any files in **the entire repo** that are prefixed with `vwp-plugin`.

### Clean Unused Files

There are a few instances where you may not need the starting files and folder structure. In these instances, it's a good practice to delete them. Please see a few examples below.

1. i18n Languages: `/src/languages`

   - If you have no intentions to add multiple languages, also known as "internationalization" (or i18n for short), then you can delete this folder.

2. Public Front-End: `/src/php/public`

   - If the plugin you are developing doesn't have a need for front-end support, and is only used in the WP Admin area, then you can delete this folder.

3. TypeScript Types: `/src/utils/types`

   - If you don't end up adding to or updating this folder, then you can remove it.

4. TypeScript Enumerations: `/src/utils/enums`

   - If you don't end up adding to or updating this folder, then you can remove it.

5. TypeScript Interfaces: `/src/utils/interfaces`

   - If you don't end up adding to or updating this folder, then you can remove it.

6. Vue Components: `/src/components`
   - If you don't end up adding to or updating this folder, then you can remove it.

### Installation

1. Clone the repo.
   ```sh
   git clone https://github.com/jared-leddy/wordpress-plugin-starter-vue.git
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

4. The `admin/index.php` is the base PHP template for the plugin settings admin page UI. It has an element `<div id="app"></div>` where the Vue app is getting mounted.

5. The provided Vue template can be found in `src/vue/App.vue`.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- BUILD PROCESS LOGIC -->

## Build Logic

### Vite Build

Vite will convert all of your `.ts` to `.js` and all `.scss` to `.css`. THey will all be placed in a `/dist` folder.

### Build Package

This covers the series of tasks that are used to initially build the plugin folder. Found in both `build:dev` and `build:prod` scripts.

1. Verify Folders Exist

   - Here, we are checking to see if you have manually created your plugin folder. If you have not already created your plugin folder, we will create one for you.
   - This folder needs to be present for subsequent tasks.

2. Copy Files to Folder

   - Here, we are copying your entire `/src` folder contents into your plugin folder.

3. Move Assets from Dist
   - Vite is run before Gulp. All of your `.js` and `.css` files will be located there.
   - For this task, those files built into the `/dist` folder get moved into your plugin folder.

### Clean Package

This covers the series of tasks that are used to clean up the plugin folder. Found in both `build:dev` and `build:prod` scripts.

1. Delete Trivial Files
   - Here, we delete all of the trivial files.

A trivial file typically refers to a file that contains very little or no meaningful content. These files often have minimal or placeholder code, which serves little purpose in the context of a larger project. In the context of software builds and packaging, trivial files are usually considered unnecessary and can be removed to optimize the final package.

Here are some common examples of trivial files:

- **Empty files:** Files that have no content at all.
- **Export placeholder files:** Files that contain only trivial exports like export {};, often created to satisfy module systems but don't serve any real purpose.
- **_Files with a single constant or trivial content:_** For example, a file that only defines a single constant but is never used meaningfully. See examples below:

Empty File:

```ts
// This is a completely empty file
```

Export Placeholder:

```ts
export {};
```

Trivial Content:

```ts
const someVar = 'value'; // A single trivial statement with no broader impact
```

Removing these files can help reduce clutter, decrease the size of your final builds, and improve overall project performance.

2. Delete TypeScript Files

   - Delete all files with a `.ts` extension from the plugin folder . We have already used Vite to build these `.js` files, so we don't need to retain them.

3. Delete SCSS Files

   - Delete all files with a `.scss` extension from the plugin folder . We have already used Vite to build these `.css` files, so we don't need to retain them.

4. Clean PHP Folder

   - The majority of our PHP is located in `/src/php`.
   - During the move step above, we are moving this `php` folder into out plugin folder.
     - Example: `/vwp-plugin/php`
   - Here in this task, we will move all of those contents up a tree level (i.e., to `/vwp-plugin`).
   - This step will leave an empty `php` folder intentionally.

5. Delete Empty Folders

   - Deleting files out of our plugin folder, and moving the PHP files up a level, will leave empty folders lying around.
   - Here, we will recursively delete all empty folders from the parent plugin folder (i.e., `/vwp-plugin`).

### Clean Project

This covers the series of tasks that are used to clean up the plugin folder after the zip file is created. Found in both `build:dev` and `build:prod` scripts.

1. Delete Folders

   - Delete the `/dist` folder that is generated by Vite.
   - Delete the plugin folder (i.e., `/vwp-plugin`).

### Zip Project

For this one, we will "compress" or "zip" the plugin folder. This is the step that will produce the final plugin zip file that you upload into the WordPress website. Since this one is just a task, we don't have a need to do anything more than zip the folder.

Found in `build:prod` script.

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- ROADMAP -->

## Roadmap

We don't have a dedicated roadmap outside of Github. Simply check the [open issues](https://github.com/jared-leddy/wordpress-plugin-starter-vue/issues) for a full list of proposed features (and known issues).

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

<!-- CONTACT -->

## Contact

[X @jaredleddy](https://x.com/jaredleddy)

[Carbon Digital](https://carbondigital.us)

Project Link: [https://github.com/jared-leddy/wordpress-plugin-starter-vue](https://github.com/jared-leddy/wordpress-plugin-starter-vue)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

Without these people and tools, life would be too complicated.

- Good food.
  - Steak 🥩
  - Ramen 🍜
  - Rice 🍚
  - Tacos 🌮
  - Breakfast Burritos 🌯
  - Burgers 🍔
  - Pizza 🍕
  - Coffee ☕
- Good company.
  - Family 👫 👨‍🍼
  - Friends 👨‍🏫 👨‍🚀 🏋️‍♂️ ⛹️‍♂️ 🏄‍♂️ 🏌️‍♂️ 🏂 🤺 🧗‍♂️ 🏃‍♂️ 🎣
- Good tools.
  - [VS Code]()
  - [Node Version Manager (NVM)](https://github.com/nvm-sh/nvm)
  - [Warp Terminal](https://warp.dev)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/jared-leddy/wordpress-plugin-starter-vue.svg?style=for-the-badge
[contributors-url]: https://github.com/jared-leddy/wordpress-plugin-starter-vue/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/jared-leddy/wordpress-plugin-starter-vue.svg?style=for-the-badge
[forks-url]: https://github.com/jared-leddy/wordpress-plugin-starter-vue/network/members
[stars-shield]: https://img.shields.io/github/stars/jared-leddy/wordpress-plugin-starter-vue.svg?style=for-the-badge
[stars-url]: https://github.com/jared-leddy/wordpress-plugin-starter-vue/stargazers
[issues-shield]: https://img.shields.io/github/issues/jared-leddy/wordpress-plugin-starter-vue.svg?style=for-the-badge
[issues-url]: https://github.com/jared-leddy/wordpress-plugin-starter-vue/issues
[license-shield]: https://img.shields.io/github/license/jared-leddy/wordpress-plugin-starter-vue.svg?style=for-the-badge
[license-url]: https://github.com/jared-leddy/wordpress-plugin-starter-vue/blob/master/license.md
