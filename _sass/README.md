# ![---](https://dl.dropboxusercontent.com/u/65873649/CDN/Codepen/favico.png) Angular: Site Scaffolding & Bootstrap v0.3

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

#Twitter Bootstrap + SASS/SCSS

A modified Twitter Bootstrap is included in this scaffolding framework. In this case, there are 7 break points to allow a more precise control over elements that scale over different screen sizes and orientations.

###Breakpoints: Quick Reference

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
 - Tablets (iPads and other tablets > 7")
	 - ID: 3
	 - Size: 768 ~ 1024
	 - Container width: 750px
	 - #preloader width: 30px
	 - CSS Breakpoint identifier: sm
 - Standard Definition ~ MDPI (older desktops, laptops, netbooks, chromebooks)
	 - ID: 4
	 - Size: 1025 ~ 1199
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

### Media Queries: Quick Reference

####Breakpoint-Based Media Query Mixin
To use breakpoints more efficiently, you can use the following mixin:
```scss
@mixin breakpoint($minmaxonly, $breakpointID) { ... };
```
Replace ```$minmaxonly``` with either ```min```, ```max``` or ```only``` and a matching breakpoint ID.
```scss
@include breakpoint(max,4) {
	.element{
		width: 100%;
	}
};

/*or*/
.element{
	@include breakpoint(max,4) {
		width: 100%;
	};
}
```
*Applies* ```width: 100%;``` *to* ```.element``` *in any screen size with a maximum width of 1199px*
```scss
.element{
	@include breakpoint(min, 3) {
		width: 100%;
	};
}
```
*Applies* ```width: 100%;``` *to* ```.element``` *in any screen size with a minimum width of 768px*
```scss
@include breakpoint(only, 2) {
	.element{
		width: 100%;
	}
};
```
*Applies* ```width: 100%;``` *to* ```.element``` *in any screen size with a minimum width of 400px and a maximum width of 767px*

####Screen-size Based Media Query
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
*Applies *```width: 100%``` *to* ```.element``` *in any screen size with a minimum of 300px*
```scss
@include screen(300px, 500px) {
	.element{
		width: 100%;
	}
};
```
*Applies *```width: 100%``` *to* ```.element``` *in any screen size with a minimum of 300px and a maximum of 500px*
```scss
@include screen(false, 500px) {
	.element{
		width: 100%;
	}
};
```
*Applies *```width: 100%``` *to* ```.element``` *in any screen size with a maximum of 500px*

####Orientation-size Based Media Query
To do media queries based on screen orientation (mobile browsers only, as of date of writing):
```scss
@mixin orientation($orientation) { ... };
```
Replace ```$orientation``` with ```landscape``` or ```portrait``` only works on mobile. 

```scss
/*Example 1:*/
@include orientation(landscape) {
	.element{
		width: 100%;
	}
}

/*Example 2:*/
@include orientation(portrait) {
	.element{
		width: 100%;
	}
}

/*Example 3:*/
@include orientation(some-other-value) {
	.element{
		width: 100%;
	}
}
```
*The first example will be seen only on devices that are on landscape view and on desktop browsers. The second example will only bee seen on devices that are in portrait view. The third example simply ignores whatever styles you place under its* ```@content``` *value.*

####Print
To add a print stylesheet, use a mixin instead of needing to add another stylesheet.
```scss
@mixin print{ ... }
```
####Breakpoint Scaling
Applies values according to the breakpoint ID and fills in any missing values.
```scss
@mixin upScale($property, $bp1, $bp2, ..., $bp7, $prefix);
@mixin downScale($property, $bp7, $bp6, ..., $bp1, $prefix);
```
```$property``` is the css property you want to use. ```$bp#``` are the affected breakpoints. In the case of ```@mixin upScale```, any missing breakpoints will be filled in by the last argument. To use this along with broser prefixing, enter all the 7 breakpoints with the intended values and set the ```$prefix``` argument to ```true```.
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

####Shortcuts
#####Browser Prefixing
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
#####Responsive Hiding
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
#####The famous float "ClearFix"
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

####Center Vertically
*Note that mixin can only be used in the children of elements with a relative or absolute position for best results.*
```scss
@mixin verticalCenter($x, $y);

/* usage */
.element{
	@include vertCenter(-50%, -50%);
}
```
```css
/* yields */
.element{
	position: absolute;
	top: 50%;
	left: 50%;
	-webkit-transform: translate(-50%, -50%);
	-moz-transform: translate(-50%, -50%);
	-o-transform: translate(-50%, -50%);
	-ms-transform: translate(-50%, -50%);
	transform: translate(-50%, -50%);
}
```

####Viewport Width + Height
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
*By the time of writing, this does not support browser prefixes just yet.*


#### ~Happy coding!