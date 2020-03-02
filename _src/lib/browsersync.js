'use strict';

const bsync = require('browser-sync');
const exec = require('child_process').exec;
const portfinder = require('portfinder');

let initFlag = true;

portfinder.getPort({
	port: 3000,    // minimum port
	stopPort: 3099 // maximum port
}, function (err, port) {

	if (!err) {

		const serverPort = port;

		bsync.init({
			files: [
				'./app/*.html',
				'./app/css/*.css',
				'./app/js/**/*.js'
			],
			reloadThrottle: 5000,
			reloadDelay: 1000,
			port: serverPort,
			minify: true,
			server: {
				baseDir: './app',
				index: 'index.html'
			}
		}, function () {
			setTimeout(function () {
				initFlag = false;
			}, 2000);
		});

		bsync.watch('./app/assemble/**/*.hbs', {
			awaitWriteFinish: {
				stabilityThreshold: 200,
				pollInterval: 500
			}
		}, function (event, file) {
			// console.log(event, file);
			switch (event) {

				case 'add':

					if (!initFlag) {
						console.log('building');
						exec('npm run assemble:build', (error, stdout, stderr) => {
							if (error) {
								console.error(`exec error: ${error}`);
								return;
							}
							if (stderr) {
								console.error(`ERROR:\n ${stderr}`);
							}
						});
					}

					break;

				case 'change':

					exec('npm run assemble:build', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

				default:

					exec('npm run assemble:build', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

			}

		});

		bsync.watch('./app/ejs/**/*.js', {
			awaitWriteFinish: {
				stabilityThreshold: 1000,
				pollInterval: 500
			}
		}, function (event, file) {
			// console.log(event, file);
			switch (event) {

				case 'add':

					if (!initFlag) {
						console.log('building');
						exec('npm run preprocess:js', (error, stdout, stderr) => {
							if (error) {
								console.error(`exec error: ${error}`);
								return;
							}
							if (stderr) {
								console.error(`ERROR:\n ${stderr}`);
							}
						});
					}

					break;

				case 'change':

					exec('npm run preprocess:js', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

				default:

					exec('npm run preprocess:js', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

			}

		});

		bsync.watch('./app/scss/**/*.scss', {
			awaitWriteFinish: {
				stabilityThreshold: 1000,
				pollInterval: 2000
			}
		}, function (event, file) {
			// console.log(event, file);
			switch (event) {

				case 'add':

					if (!initFlag) {
						console.log('building');
						exec('npm run preprocess:css', (error, stdout, stderr) => {
							if (error) {
								console.error(`exec error: ${error}`);
								return;
							}
							if (stderr) {
								console.error(`ERROR:\n ${stderr}`);
							}
						});
					}

					break;

				case 'change':

					exec('npm run preprocess:css', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

				default:

					exec('npm run preprocess:css', (error, stdout, stderr) => {
						if (error) {
							console.error(`exec error: ${error}`);
							return;
						}
						if (stderr) {
							console.error(`ERROR:\n ${stderr}`);
						}
					});

					break;

			}

		});

	}

});
