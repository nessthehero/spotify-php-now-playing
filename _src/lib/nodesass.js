'use strict';

const sass = require('node-sass');
const fs = require('fs');
const mkdirp = require('mkdirp');
const getDirName = require('path').dirname;

const options = {
	includePaths: [
		'node_modules/foundation-sites/scss'
	],
	outputStyle: 'expanded',
	sourceMapContents: true
};

let regex = /^([^_])(.+)\.scss$/;

let cssdir = fs.readdirSync('./app/scss');
let css = cssdir.filter(c => c.match(regex));

for (let i in css) {

	if (css.hasOwnProperty(i)) {

		let cssfilename = './app/css/' + css[i].replace('scss', 'css');
		let cssmapfilename = './app/css/' + css[i].replace('scss', 'css.map');

		let tmp = {
			file: './app/scss/' + css[i],
			outFile: cssfilename,
			sourceMap: cssmapfilename
		};

		let final = Object.assign({}, tmp, options);

		sass.render(final, function (error, result) {

			if (!error) {
				mkdirp(getDirName(cssfilename), function (err) {
					if (!err) {
						fs.writeFile(cssfilename, result.css, function (er) {
							if (!er) {

							} else {
								console.error(er);
							}
						});
					}
				});

				mkdirp(getDirName(cssfilename), function (err) {
					if (!err) {
						fs.writeFile(cssmapfilename, result.map, function (err) {
							if (!err) {

							} else {
								console.error(err);
							}
						});
					}
				});
			} else {
				console.error(error);
			}

		});

	}

}