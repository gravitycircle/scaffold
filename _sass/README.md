# ![---](http://angular.richardbryanong.com/img/favico.png)
# Angular: Site Scaffolding & Bootstrap v0.6

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
    *   CSS

Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to adapt to the URLs of your files based on where the active Apache directory is.

Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application.

# Twitter Bootstrap + SASS/SCSS

A *heavily* modified Twitter Bootstrap codebase is included in this scaffolding framework. In this case, there are 7 break points to allow a more precise control over elements that scale over different screen sizes and orientations.

### Breakpoints: Quick Reference

 - Extra Small (Smartwatches ~ iPhone 4)
	 - ID: 1
	 - Size: 0 ~ 399
	 - Container width: 100%
	 - #preloader width: 10px
	 - CSS Breakpoint Identifier: xs
 - Phones (Most Smartphones)
	 - ID: 2
	 - Size: 400 ~ 767
	 - Container width: 100%
	 - #preloader width: 20px
	 - CSS Breakpoint identifier: ph
 - Tablets (iPads - Portrait and other tablets > 7")
	 - ID: 3
	 - Size: 768 ~ 1023
	 - Container width: 750px
	 - #preloader width: 30px
	 - CSS Breakpoint identifier: sm
 - Standard Definition ~ MDPI (iPads - Landscape, older desktops, laptops, netbooks, chromebooks)
	 - ID: 4
	 - Size: 1024 ~ 1199
	 - Container width: 993px
	 - #preloader width: 40px
	 - CSS Breakpoint identifier: md
 - High Definition ~ MDPI (newer models, less than 2k screens)
	 - ID: 5
	 - Size: 1200 ~ 1599
	 - Container width: 1164px
	 - #preloader width: 50px
	 - CSS Breakpoint identifier: lg
 - HD Definition ~ HiDPI (most modern laptops and desktops + older Retina / IPS monitors)
	 - ID: 6
	 - Size: 1600 ~ 1999
	 - Container width: 1552px
	 - #preloader width: 60px
	 - CSS Breakpoint identifier: hd
 - 2k ~ 4k Displays (IPS, Retina, and other large format displays)
	 - ID: 7
	 - Size: 1600 ~ 1999
	 - Container width: 1552px
	 - #preloader width: 70px
	 - CSS Breakpoint identifier: qd
---
# Media Queries: Quick Reference

### Breakpoint-Based Media Query Mixin
Breakpoint-based Media Queries have been heavily modified from the previous version of the CSS codebase. The new version has a shorter mixin name for time-efficient coding and a new argument ```$orientation``` for better control.
```scss
@mixin min($breakpoint, $orientation);
@mixin max($breakpoint, $orientation);
@mixin only($breakpoint, $orientation);
```
##### The mixins explained:
The ```min``` mixin provides a media query starting from the low end of a breakpoint to infinity. In the case of ```min(6)```, where the sizes range from ```1600 - 1999 pixels```, ```min``` counts from ```1600px``` and goes all the way to infinity.

The ```max``` mixin does the complete opposite. It counts from the high end of a breakpoint down to zero. In a similar case with min, ```max(6)``` counts from ```1999px``` down to zero.

The ```only``` mixin locks the media query to a certain breakpoint. Anything above and below the breakpoint sizes won't be considered. ```only(6)``` will only apply styles to screen widths ```1600 - 1999 pixels```.

##### Usage:
To use the mixin (in this case 'min'), just fill in the arguments and add a ```{ ••• }``` block for your css:
```scss
@include min(3, 'landscape') {
    display: block; //only applies to smartphones (and smaller-sized tablets) on landscape
}
```

#### Screen-size Based Media Query
To do media queries by specific screen sizes, the following mixin applies:
```scss
@mixin screen($min, $max) { ... };
```
Replace ```$min``` with the minimum screen width your style applies to and ```$max``` with the maximum screen width you want your style to apply to.
```scss
@include screen(300px) {
	.element{
		width: 100%;
	}
};

/* or */

.element{
	@include screen(300px) {
		width: 100%;
	}
}
```
*Applies*```width: 100%``` *to* ```.element``` *in any screen size with a minimum of 300px*
```scss
@include screen(300px, 500px) {
	.element{
		width: 100%;
	}
};
```
*Applies*```width: 100%``` *to* ```.element``` *in any screen size with a minimum of 300px and a maximum of 500px*
```scss
@include screen(false, 500px) {
	.element{
		width: 100%;
	}
};
```
*Applies*```width: 100%``` *to* ```.element``` *in any screen size with a maximum of 500px*

#### Print
To add a print stylesheet, use a mixin instead of needing to add another stylesheet.
```scss
@mixin print{ ... }
```
#### Breakpoint Scaling
Applies values according to the breakpoint ID and fills in any missing values.
```scss
@mixin upScale($property, $bp1, $bp2, ..., $bp7, $prefix);
@mixin downScale($property, $bp7, $bp6, ..., $bp1, $prefix);
```
```$property``` is the css property you want to use. ```$bp#``` are the affected breakpoints. Any missing breakpoints will be filled in by the last argument. To use this along with browser prefixing, enter all the 7 breakpoints with the intended values and set the ```$prefix``` argument to ```true```.
```scss
.element{
	@include upScale(width, 10px, 20px, 30px);
}
```
*Applies 10px width on screens < 400, 20px on screens 400 to 767 and 30px width to screens > 767.*
```scss
.element{
	@include downScale(height, 40px, 30px, 20px);
}
```
*Applies 40 width on screens > 1999, 30px on screens 1600 to 1999 and 20px width to screens < 1600.*

---
# Shortcuts

### Browser Prefixing
This is to add all browser prefixes (example: ```-webkit-``` ) styles without typing the duplications yourself.
```scss
@mixin prefix($property, $value);

/* usage */
.element{
	@include prefix(box-sizing, border-box);
}
```

```css
/* yields */
.element{
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-o-box-sizing: border-box;
	-ms-box-sizing: border-box;
	box-sizing: border-box;
}
```
### Responsive Hiding
Hide element (```display: none```) when the screen with fits the breakpoints specified in the arguments.
```scss
@mixin hide($x, ..., $x);

/* usage */
.element{
	@include hide(4,5,6);
}
```
```css
/* yields */
@media only screen and (min-width: 1025px) and (max-width: 1199px) {
	.element{
		display: none;
	}
}

@media only screen and (min-width: 1200px) and (max-width: 1999px) {
	.element{
		display: none;
	}
}

@media only screen and (min-width: 2000px) {
	.element{
		display: none;
	}
}
```
### The famous float "ClearFix"
Adds a pseudo element to *clear* any floating elements so that its container sizes up correctly.
```scss
@mixin clearFix();

/* usage */
.element{
	@include clearFix();
}
```
```css
/* yields */
.element::after{
	content: '';
	width: 100%;
	height: 0;
	clear: both;
	pointer-events: none;
}
```

### Viewport Width + Height
There was an issue with iOS devices, but these have already been fixed.
```scss
@mixin calc-vh($property, $value);
@mixin calc-vw($property, $value);

/* usage */
.element{
	@include calc-vw(width, 50);
}
```

```css
/* yields */
.element{
	width : 50vw;
}

@media all and (device-width: 1024px) and (device-height: 768px) and (orientation:portrait){
	.element{
		width: 384px;
	}
}

@media all and (device-width: 1024px) and (device-height: 768px) and (orientation:landscape){
	.element{
		width: 512px;
	}
}

@media all and (device-width: 320px) and (device-height: 480px) and (orientation:portrait){
	.element{
		width: 160px;
	}
}

@media all and (device-width: 320px) and (device-height: 480px) and (orientation:landscape){
	.element{
		width: 120px;
	}
}

@media all and (device-width: 320px) and (device-height: 568px) and (orientation:portrait){
	.element{
		width: 160px;
	}
}

@media all and (device-width: 320px) and (device-height: 568px) and (orientation:landscape){
	.element{
		width: 284px;
	}
}

@media all and (device-width: 375px) and (device-height: 667px) and (orientation:portrait){
	.element{
		width: 188px;
	}
}

@media all and (device-width: 375px) and (device-height: 667px) and (orientation:landscape){
	.element{
		width: 334px;
	}
}
```
### Transitions
The issue with webkit browser animation stuttering has been addressed and the fixes have been put in place. However you may override the fixes yourself anytime.
```scss
@mixin transition($what...);
@mixin duration($time);
@mixin timing($preset);

/*Usage: All three will always be used in tandem with each other.*/
@include transition(opacity, color); /* or alternatively */ @include transition(all);
@include duration(0.6s); /* or alternatively */ @include duration(600ms);
@include timing('snap'); /* or alternatively */ @include timing(cubic-bezier(0.55, 0.055, 0.675, 0.19));
```
For the ```timing``` mixin, there are presets already preprogrammed in: ```snap```, ```linear```, ```sine``` & ```fall```.
**Example:**
```scss
div{
    @include transition(all);
    @include timing(snap);
    @include duration(0.6s);
}
```
Will yield the following CSS:
```css
.div{
    -webkit-transform: translateZ(0);
	-moz-transform: translateZ(0);
	-ms-transform: translateZ(0);
	-o-transform: translateZ(0);
	transform: translateZ(0);
	-webkit-backface-visibility: hidden;
	-moz-backface-visibility: hidden;
	-ms-backface-visibility: hidden;
	backface-visibility: hidden;
	-webkit-perspective: 1000;
	-moz-perspective: 1000;
	-ms-perspective: 1000;
	perspective: 1000;
    -moz-transition-property : all;
	-webkit-transition-property : all;
	-o-transition-property : all;
	-ms-transition-property : all;
	transition-property : all;
	-moz-transition-timing-function : curve(cubic-bezier(0.000, 0.700, 0.285, 1.000));
    -webkit-transition-timing-function : curve(cubic-bezier(0.000, 0.700, 0.285, 1.000));
    -o-transition-timing-function : curve(cubic-bezier(0.000, 0.700, 0.285, 1.000));
    -ms-transition-timing-function : curve(cubic-bezier(0.000, 0.700, 0.285, 1.000));
    transition-timing-function : curve(cubic-bezier(0.000, 0.700, 0.285, 1.000));
    -moz-transition-duration : 0.6s;
    -webkit-transition-duration : 0.6s;
    -o-transition-duration : 0.6s;
    -ms-transition-duration : 0.6s;
    transition-duration : 0.6s;
}
```
### Animation and Keyframes
Animation and Keyframe code duplication (and vendor prefixing) has already been addressed by the ```animation``` and ```keyframes``` mixins.
```scss
@mixin animate($keyframe_name, $duration, $timing, $iterations: infinite, $direction: normal, $delay: false);
@mixin keyframes($keyframe_name) { ••• };
```
**Usage**
These two mixins should be used in tandem with each other, like the ```transition``` mixin trio.
```scss
@include keyframes(animation){
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
div{
	@include animate('animation', 0.8s, linear);
}
```
Translates to the following CSS:
```css
@-webkit-keyframes animation{
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
@-moz-keyframes animation{
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
@-o-keyframes animation{
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
@-ms-keyframes animation{
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
@keyframes animation{
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}

div{
	-webkit-animation-name: animation;
	-moz-animation-name: animation;
	-o-animation-name: animation;
	-ms-animation-name: animation;
	animation-name: animation;
	-webkit-animation-duration: 0.8s;
	-moz-animation-duration: 0.8s;
	-o-animation-duration: 0.8s;
	-ms-animation-duration: 0.8s;
	animation-duration: 0.8s;
	-webkit-animation-timing-function: linear;
	-moz-animation-timing-function: linear;
	-o-animation-timing-function: linear;
	-ms-animation-timing-function: linear;
	animation-timing-function: linear;
	-webkit-animation-iteration-count: infinite;
	-moz-animation-iteration-count: infinite;
	-o-animation-iteration-count: infinite;
	-ms-animation-iteration-count: infinite;
	animation-iteration-count: infinite;
	-webkit-animation-direction: normal;
	-moz-animation-direction: normal;
	-o-animation-direction: normal;
	-ms-animation-direction: normal;
	animation-direction: normal;
}
```
### Autosizing
To autosize or 'zoom' your measurements depending on a certain factor per screen size, use the ```zoom``` mixin, which in turn uses the ```zoomFactor``` function and the ```upScale``` mixin. The purpose of ```zoom``` is to quickly auto-resize sizes depending on the breakpoint and its respective 'zoom factor' denoted in the ```_vars.scss``` file (```$zf1 - $zf7 variables```).
**Usage:**
```scss
div{
	@include zoom(margin-left, 20px);
}
```
### Positioning and Re-positioning - Absolute / Fixed Elements
This mixin works the best with ```position: absolute;``` or ```position: fixed;``` elements since it uses the ```top```, ```left```, ```bottom```, ```right``` and ```transform``` properties.
```scss
div{
	position: absolute;
	display: block;
	width: 30px;
	height: 30px;
	@include position(50%, 50%); //(top 50%, left 50%)
	@include reposition(-50%, -50%); //(translate(-50%, -50%)
}
```
This will center the div in a positioned (```absolute```, ```relative```, ```fixed```) parent regardless of the parent's size.
```scss
div{
	position: absolute;
	display: block;
	width: 30px;
	height: 30px;
	@include position(50%, 50%, true); //(top 50%, right 50%)
	@include reposition(50%, -50%); //(translate(50%, -50%)
}
```
The ```position``` mixin, positions the element in percentage width/height -- depending on the axis -- of the parent element or in pixels relative to the top, left, right or bottom of the parent element. 

The ```reposition``` mixin, positions the element in percentage width/height -- depending on the axis -- of the current element or in pixels relative to the current position of the current element.

##### Typography
-- to be added --
#### ~Happy coding!