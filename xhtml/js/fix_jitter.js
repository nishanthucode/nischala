const fs = require('fs');

// 1. Fix custom.js
let jsPath = 'c:\\xampp\\htdocs\\Nishchal\\xhtml\\js\\custom.js';
if (fs.existsSync(jsPath)) {
    let content = fs.readFileSync(jsPath, 'utf8');
    content = content.replace(
        /if \(\$\(window\)\.scrollTop\(\) > menu\.offset\(\)\.top\) \{\s*menu\.addClass\('is-fixed'\);\s*\} else \{\s*menu\.removeClass\('is-fixed'\);\s*\}/g,
        `if ($(window).scrollTop() > menu.offset().top) {
					if(!menu.hasClass('is-fixed')){
						menu.css('height', menu.outerHeight());
					}
					menu.addClass('is-fixed');
				} else {
					if(menu.hasClass('is-fixed')){
					    menu.removeClass('is-fixed');
					    menu.css('height', '');
					}
				}`
    );
    fs.writeFileSync(jsPath, content);
}

// 2. Fix custom.min.js
let minJsPath = 'c:\\xampp\\htdocs\\Nishchal\\xhtml\\js\\custom.min.js';
if (fs.existsSync(minJsPath)) {
    let content = fs.readFileSync(minJsPath, 'utf8');
    // The minified version looks like:
    // $(window).scrollTop()>e.offset().top?e.addClass("is-fixed"):e.removeClass("is-fixed")
    
    content = content.replace(
        /\$\(window\)\.scrollTop\(\)>e\.offset\(\)\.top\?e\.addClass\("is-fixed"\):e\.removeClass\("is-fixed"\)/g,
        `$(window).scrollTop()>e.offset().top?(!e.hasClass("is-fixed")&&e.css("height",e.outerHeight()),e.addClass("is-fixed")):(e.hasClass("is-fixed")&&(e.removeClass("is-fixed"),e.css("height","")))`
    );
    fs.writeFileSync(minJsPath, content);
}
console.log('Done fixing jitter');
