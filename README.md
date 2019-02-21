
 ![---](http://richardbryanong.com/public/shortcut-icon.png) 

# Angular: Site Scaffolding & Bootstrap v0.7 (WP-unstable)

This is the starting point of every new site I create using a combination of the following frameworks:

*   **Javascript**
    *   AngularJS v1.3.15
    *   jQuery v2.1.3
*   **Preprocessors**
    *   Compass (requires Ruby and gem installation)
    *   SASS (requires Ruby and gem installation)
    *   PHP v7 or higher (requires running on a server. Preferrably Apache 2.0)
* **Content Management**
  * Wordpress - Customized wp-config.php & config.php
*   **Markup and Styling**
    *   HTML 5
    *   CSS 3
*   **Wordpress Plugin Dependencies**
    *   Advanced Custom Fields Pro - You'll need a licence key for this.
    *   WP-DB Migrate

Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to adapt to the URLs of your files based on where the active Apache directory is.

Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application. *(Side Note: CANONICAL will be phased out next version.)*

**Past Changes:**

 - Refactored _data structure into a more staggered non-CMS data management system.
 - JS files are all minified into 3 files: library.js, config.js and script.js. These files do not exist, they are made on the fly.
 - Note: Need to further optimize minification.
 - CSS Base has been heavily modified, moved all CSS calls into 'scaffolding.scss'. All base.scss contains are mixins.
 - Increased number of breakpoints from 4 to 7. Created min/max mixins for sizes, and orientation mixin for orientation.
 - Removed unecessary Boostrap CSS code. Kept the base grid the same.
 - Converted all Bootstrap CSS into self-contained in '.grid' elements.

**Framework Changes:**

 - It's finally here: WordPress Integration. Setup instructions are further down in this document.
 - Modified Breakpoint System to include orientations, original orientation mixin still present.
 - Google Map fully integrated
 - Emailer system will still be standalone, plugin emailer will be removed.
 - SEO fully integrated in /php/server.php

**Future Update Notes:**

 - Streamline setup, currently very clunky. Utilize Grunt / Composer or other automation applications to do this.
 - Build modular page navigation system and sub-navigation for reusability.
 - Build CSS Component for WordPress integration as well as customised ".css" files.

#### ~Happy coding!

---

## Setup Instructions
- Download & Extract Wordpress from [wordpress.org](https://wordpress.org "Blog Tool, Publishing Platform, and CMS &mdash; WordPress") into `_bin/` folder
- Create "media" Folder inside `_bin/`
- Install WordPress normally
- Add in `include_once(str_replace('/_bin', '', dirname(__FILE__)).'/setup.php');` in `wp-config.php` right after `define('DB_COLLATE', '');`.
- Copy in the theme in its respective folder (`_bin/wp-content/themes/`). The default theme is found in `_src/wordpress/themes`
- Activate the theme and all newly installed plugins.
- Update everything from the WordPress admin.
- Delete all extra themes / plugins. We're departing from those.

## Alright Sparky!
- The correct Google Cloud Platform API must be placed in the database. To do this, log into Wordpress by going into `_bin/wp-admin` on your browser.
- On the left hand panel, look for *Settings > General*
- Scroll down to the bottom and add an API Key named `Google Cloud Platform` and a matching API key to the right. Save.
- Documentation is still in the works.
- **Note:** Make sure to set your home page displays with the correct info under *General Settings > Reading*
   - Create a new blank page, so you can get this setting saved.
   - Set the *Your Homepage Displays* option to *A Static Page*
   - Define pages on the 2 options presented. Use the blank page for either the home page or the posts page.
- That's it for now.

#### ~Stay Tuned!