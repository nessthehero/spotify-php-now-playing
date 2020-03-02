'use strict';

const assemble = require('assemble');
const assembleHelpers = require('assemble-helpers');
const extname = require('gulp-extname');
let app = assemble();

const _dir = __dirname + '/';

app.use(assembleHelpers());

app.task('load', function (cb) {
	app.create('includes', {viewType: 'partial'});
	app.create('organisms', {viewType: 'renderable'});

	app.helpers([
		'handlebars-helpers/lib',
		require(_dir + '../app/assemble/helpers/helpers.js')
	]);

	app.partials([
		_dir + '../app/assemble/includes/*.hbs',
		_dir + '../app/assemble/molecules/*.hbs',
		_dir + '../app/assemble/organisms/*.hbs',
		_dir + '../app/assemble/atoms/*.hbs'
	]);

	app.layouts(_dir + '../app/assemble/layouts/*.hbs');

	app.pages(_dir + '../app/assemble/*.hbs');
	app.organisms(_dir + '../app/assemble/organisms/*.hbs');

	app.option('layout', 'default.hbs');

	cb();
});

app.task('default', ['load'], function () {

	return app.toStream('pages')
		.pipe(app.renderFile())
		.pipe(extname({ext: '.html'}))
		.pipe(app.dest('app'));

});

app.task('organisms', ['load'], function () {

	return app.toStream('organisms')
		.pipe(app.renderFile())
		.pipe(extname({ext: '.html'}))
		.pipe(app.dest('app/components'));

});

module.exports = app;
