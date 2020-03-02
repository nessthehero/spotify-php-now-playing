'use strict';

const fs = require('fs');
const _ = require('lodash');
const collection = [
	{
		'name': 'atoms',
		'searchName': 'atoms',
		'dir': './app/assemble/atoms',
		'recursive': true
	},
	{
		'name': 'molecules',
		'searchName': 'molecules',
		'dir': './app/assemble/molecules',
		'recursive': true
	},
	{
		'name': 'organisms',
		'searchName': 'organisms',
		'dir': './app/assemble/organisms',
		'recursive': true
	},
	{
		'name': 'templates',
		'searchName': 'assemble',
		'dir': './app/assemble/.',
		'recursive': false
	}
];

const ignored = [
	'.git',
	'node_modules',
	'.DS_Store',
	'thumbs.db',
	'Thumbs.db'
];

/*

This script runs through each directory in the collection object above and generates an appropriate .scss file in the
respective directory under /app/scss/.

The recursive parameter is important. It is set to false on templates so that the templates folder isn't filled with
all the sass files for molecules and organisms.

Ideas for the future for this include:
- Scanning the handlebars files for a flag that prevents a sass file from being generated.
- Scanning for a flag that determines a different composite sass file for the intention of including in a different
	master css file other than main.css (for example, level.css) https://github.com/BarkleyREI/generator-brei-app/issues/64

 */

collection.forEach(function (data) {

	let ff = fs.readdirSync(data.dir);

	ff = _.difference(ff, ignored);

	ff = ff.filter(hbs => hbs.indexOf('.hbs') !== -1);

	var names = [];
	var finalScssFile = '';
	var finalPath = './app/scss/' + data.name + '/_assemble-' + data.name + '.scss';

	ff.forEach(function (entry) {
		// Add names to be added to .scss file
		var regex = new RegExp('^.+' + data.searchName + '/');

		if (!/^_+/.test(entry)/* && data.name !== 'templates'*/) {
			entry = '_' + entry;
		}

		if (data.name === 'templates') {
			if (entry !== '_index.hbs') {
				names.push(entry);
			}
		} else {
			names.push(entry);
		}
	});

	names.sort();

	let rmf = writeMissingFiles(data, names);

	rmf.then(function () {

		names.forEach(function (name) {
			var importPath = '@import \'';

			name = name.replace('.hbs', '');

			importPath = importPath + name;
			finalScssFile = finalScssFile + importPath + '\';\n';
		});

		if (finalScssFile !== '') {

			fs.writeFileSync(finalPath, finalScssFile, function (err) {
				if (err) {
					throw err;
				}
			});

		}

	});
});

// Check to see if same name .scss file exists. If not, create one
function writeMissingFiles(data, names) {

	// console.log("\n====\nwriteMissingFiles()\n\n" + data + "\n" + entry + "\n====\n");

	// var name = entry.replace('.hbs', '');
	// var filename = data.name + '/' + name + '.scss';
	// var readPath = './app/scss/' + filename;

	let fle = fs.readdirSync('./app/scss/' + data.name);

	const exclu = [
		'_assemble-organisms.scss',
		'_assemble-molecules.scss',
		'_assemble-templates.scss'
	];

	fle = _.difference(fle, exclu);
	fle = _.difference(fle, ignored);

	fle = fle.map(item => item.replace('.scss', ''));
	names = names.map(item => item.replace('.hbs', ''));

	let diff = _.difference(names, fle);

	return new Promise(function (resolve, reject) {

		let errored = false;

		diff.forEach(function (x) {

			var cleanName = x.replace('_', '');
			var content = '.' + cleanName + ' {\n\n}\n';
			var filename = x + '.scss';

			if (cleanName.length >= 1) {
				fs.writeFile('./app/scss/' + data.name + '/' + filename, content, function (err) {

					if (err) {
						errored = err;
					}

					console.log(properName(data.name) + ' not found. Creating ' + filename);

				});
			}

		});

		if (errored) {
			reject(errored);
		} else {
			resolve();
		}

	});

};

function singular(str) {
	return str.replace(/s$/, '');
}

function properName(str) {
	let name = str[0].toUpperCase() + str.substring(1);
	return singular(name);
}