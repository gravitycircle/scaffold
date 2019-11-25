
 ![---](http://richardbryanong.com/public/shortcut-icon.png) 

# Angular: Site Scaffolding & Bootstrap v0.8 (WP-stable)

This is the starting point of every new site I create using a combination of the following frameworks:

*   **Javascript**
    *   Modernizr
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
    *   [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/)
    *   ~~[Classic Editor](https://en-ca.wordpress.org/plugins/classic-editor/)~~
    *   ~~[WP DB Migrate](https://en-ca.wordpress.org/plugins/wp-migrate-db/)~~

Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to adapt to the URLs of your files based on where the active Apache directory is.

Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application. *(Side Note: CANONICAL will be phased out eventually.)*

**Past Changes:**

 - Moved theme directory structure into plugin folder.
 - Added a function where images/media that has a "loading-" prefix tacked on their names will preload the file before loading the actual site.
 - Added and integrated Global Navmenus, submenus still in testing phases.

**Framework Changes:**
 - Build modular page navigation system and sub-navigation for reusability.
 - Integrated new Gutenberg functions for WordPress 5.0++
 - Added initalize function for preloading assets for a loading screen.
 - Refactored `analyze.php` to prevent memory leaks when going through repeatable `post_objects`.
 - Created new blank plugin for per-site customization.
 - Added "pre-built fields" for hard-coded objects like registration forms.
 - Automated form submission - still unstable.
 - Fixed various issues on array management, image preloading and by-request data pull algorithms.


**Future Update Notes:**
 - Streamline setup, currently very clunky. Utilize Grunt / Composer or other automation applications to do this.
 - Build CSS Component for WordPress integration as well as customised ".css" files.
 - Fix form submission automation.


#### ~Happy coding!

---

## Setup Instructions
- Download & Extract Wordpress from [wordpress.org](https://wordpress.org "Blog Tool, Publishing Platform, and CMS &mdash; WordPress") into `_bin/` folder
- Create "media" Folder inside `_bin/`
- Install WordPress normally
- Add in `include_once(str_replace('/_bin', '', dirname(__FILE__)).'/setup.php');` in `wp-config.php` right after `define('DB_COLLATE', '');`.
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

#### ~Stay Tuned!