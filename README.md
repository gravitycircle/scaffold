# ![---](http://angular.richardbryanong.com/img/favico.png) Angular: Site Scaffolding & Bootstrap v0.6 (Stable)

This is the starting point of every new site I create using a combination of the following frameworks:

*   **Javascript**
    *   AngularJS v1.3.15
    *   jQuery v2.1.3
*   **Preprocessors**
    *   Compass (requires Ruby and gem installation)
    *   SASS (requires Ruby and gem installation)
    *   PHP v5.6.11 (requires running on a server. Preferrably Apache 2.0)
*   **Markup and Styling**
    *   HTML 5
    *   CSS 3

Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to adapt to the URLs of your files based on where the active Apache directory is.

Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application.

SEO has been set up. Under the __seo_ folder, the _index.php_ file has an _seo function. Utilize that for your SEO needs. _$page_ would be the last part of the url to track. There is no deep linking available yet.

This is only the introduction page. The installation file for this scaffolding setup has not yet been configured and I'm not too sure if it'll work out of the box since the framework stack is still in active development. To start working with the stack anyway, please rename the 'index.php file' into something different and rename the _index.php file to index.php.

**Past Changes:**

 - Refactored php/mailer.php file to accept and verify forms. Made to be reusable.
 - Refactored the 'lasso' directive under config.js. Made to be, again, reusable.
 - Refactored the form field directives for easy scripting, main.php will also be refactored.
 - _seo/ folder updated for easy metadata coding.
 - Deployed for live testing on test server for CORS - related adjustments.

**Framework Changes:**
 - Refactored _data structure into a more staggered non-CMS data management system.
 - JS files are all minified into 3 files: library.js, config.js and script.js. These files do not exist, they are made on the fly.
 - Note: Need to further optimize minification.
 - CSS Base has been heavily modified, moved all CSS calls into 'scaffolding.scss'. All base.scss contains are mixins.
 - Increased number of breakpoints from 4 to 7. Created min/max mixins for sizes, and orientation mixin for orientation.
 - Removed unecessary Boostrap CSS code. Kept the base grid the same.
 - Converted all Bootstrap CSS into self-contained in '.grid' elements.

**Future Update Notes:**

 - Fix WordPress integration - This still has to go through a lot of adjustments in order to work. WP has to have a REST API - based theme or at least the REST API Plugin to work.
 - Integrate Google Map integration into new directive in order to specify stylers, markers and behavioral attributes.

#### ~Happy coding!