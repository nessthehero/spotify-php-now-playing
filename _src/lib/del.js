'use strict';

// Delete globs of files.
const del = require('del');

// Generate globs via configuration. We're turning an object into a list of files to feed to delete.
const files = require('expand-files');

// Get copy globs from the config file
const config = require('../_config/del');

// Files helper
const c = files({});

// Grab task from arguments. If no task, use dist.
const process = require('process');

let task = 'dist';
process.argv.forEach(function (val, index, array) {
	if (val.indexOf('--') !== -1) {
		task = val.replace('--', '');
	}
});

// Get the array of globs
let dist = config[task];

for (let i in dist) {

	if (dist.hasOwnProperty(i)) {

		// Expand the config into an array of files objects. We get src, dest, and options.
		var dd = c.expand(dist[i]);

		for (let j in dd.files) {

			if (dd.files.hasOwnProperty(j)) {

				// Loop through each object and delete files.
				del(dd.files[j].src, dd.files[j].options);

			}

		}

	}

}

