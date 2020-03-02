'use strict';

const fs = require('fs');
const nodePath = require('path');
const Handlebars = require('handlebars');

let isValidString = (string) => typeof string !== 'undefined' && string !== '';

module.exports = {

	replaceStr: (haystack, needle, replacement) => {

		if (haystack && needle) {
			return haystack.replace(needle, replacement);
		} else {
			return '';
		}

	},

	// Pass in the name of the JSON fixture file in assemble/fixtures for scope
	parseFixture: (path, ctx, options) => {

		if (!path || typeof path !== 'string') {
			return false;
		}

		// ctx is the context of data of the partial this helper is called from. If it is missing,
		// just set the options parameter to ctx, since assemble auto assigns the last parameter to options.
		if (!options || typeof options === 'undefined') {
			options = ctx;
			ctx = Object.assign({}, options.hash); // If you pass hashed values along with the helper, they get placed in the hash value of options, instead of being set to ctx.
		}

		let fixture;

		path = nodePath.join(__dirname, '../fixtures/' + path);

		try {
			fixture = fs.readFileSync(path);
			fixture = fixture.toString('utf8');
			fixture = JSON.parse(fixture);
		} catch (err) {
			return console.error(err);
		}

		// Merge the context data into the fixture's data by finding the diff between it and the root data.
		let rootData = options.data.root;

		let diff = Object.keys(ctx).reduce((diff, key) => {
			if (rootData[key] === ctx[key]) return diff
			return {
				...diff,
				[key]: ctx[key]
			}
		}, {});

		fixture = Object.assign(fixture, diff);

		return options.fn(fixture);

	},

	log: (data) => console.log(data),

	stringCompare: (a, b, opts) => {

		if (a === b) {
			return opts.fn(this);
		} else {
			return opts.inverse(this);
		}

	},

	toLowerCase: (str) => str.toLowerCase(),

	math: (lvalue, operator, rvalue, options) => {

		lvalue = parseFloat(lvalue);
		rvalue = parseFloat(rvalue);

		return {
			'+': lvalue + rvalue,
			'-': lvalue - rvalue,
			'*': lvalue * rvalue,
			'/': lvalue / rvalue,
			'%': lvalue % rvalue
		}[operator];

	},

	ifOr: (a, b, opts) => {

		if (a || b) {
			return opts.fn(this);
		} else {
			return opts.inverse(this);
		}

	},

	ifAnd: (a, b, opts) => {

		if (a && b) {
			return opts.fn(this);
		} else {
			return opts.inverse(this);
		}

	},

	svg: (name) => new Handlebars.SafeString('<svg class="brei-icon brei-icon-' + name + '"><use xlink:href="#brei-icon-' + name + '"></use></svg>'),

	link: (link) => {

		let url = (isValidString(link.url)) ? link.url : '#';
		let icon = (isValidString(link.icon)) ? '{{svg "' + link.icon + '"}}' : '';
		let title = (isValidString(link.title)) ? link.title : '';
		let style = (isValidString(link.style)) ? ' class="' + link.style + '"' : '';
		let alt = (isValidString(link.alt)) ? ' title="' + link.alt + '"' : ' title="' + title + '"';

		let formatted_link = '';

		if (url !== '' && title !== '') {
			formatted_link = '<a href="' + url + '"{0}{1}>{2}' + new Handlebars.SafeString(title) + '</a>';
			formatted_link = formatted_link.replace('{0}', alt);
			formatted_link = formatted_link.replace('{1}', style);
			formatted_link = formatted_link.replace('{2}', Handlebars.compile(icon));
		}

		return new Handlebars.SafeString(formatted_link);

	},

	socialMediaLink: (socialMediaLink) => {

		let url = (isValidString(socialMediaLink.url)) ? socialMediaLink.url : '#';
		let icon = (isValidString(socialMediaLink.icon)) ? '{{svg "' + socialMediaLink.icon + '"}}' : '';
		let title = (isValidString(socialMediaLink.title)) ? socialMediaLink.title : '';
		let style = (isValidString(socialMediaLink.style)) ? ' class="' + socialMediaLink.style + '"' : '';
		let alt = (isValidString(socialMediaLink.alt)) ? ' title="' + socialMediaLink.alt + '"' : ' title="' + title + '"';

		let formatted_link = '';

		if (url !== '' && title !== '') {
			formatted_link = '<a href="' + url + '"{0}{1}>{2}<span class="show-for-sr">' + new Handlebars.SafeString(title) + '</span></a>';
			formatted_link = formatted_link.replace('{0}', alt);
			formatted_link = formatted_link.replace('{1}', style);
			formatted_link = formatted_link.replace('{2}', Handlebars.compile(icon));
		}

		return new Handlebars.SafeString(formatted_link);

	},

	backgroundImage: (backgroundImage) => {

		let url = (isValidString(backgroundImage.url)) ? backgroundImage.url : '#';
		let alt = (isValidString(backgroundImage.alt)) ? backgroundImage.alt : '';
		let style = (isValidString(backgroundImage.style)) ? ' class="image ' + backgroundImage.style + '"' : ' class="image"';
		let id = (isValidString(backgroundImage.id)) ? ' id="' + backgroundImage.id + '"' : '';

		let formatted_image = '';

		if (url !== '') {
			formatted_image = '<div style="background-image: url(' + url + ');"{0}{1} title="' + new Handlebars.SafeString(alt) + '" role="img"></div>';
			formatted_image = formatted_image.replace('{0}', style);
			formatted_image = formatted_image.replace('{1}', id);
		}

		return new Handlebars.SafeString(formatted_image);

	},

	placeholderImage: (w, h, text) => {

		let width = (isValidString(w)) ? w : '300';
		let height = (isValidString(h)) ? 'x' + h : '';
		let caption = (isValidString(text)) ? '?text=' + encodeURI(text) : '';
		let url = 'http://via.placeholder.com/' + width + height + caption;

		return new Handlebars.SafeString('<img src="' + url + '" alt="Placeholder Image" />')

	},

	// Type can be short, medium, or long
	placeholderText: (type) => {

		let placeholderTextString = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

		if (type === 'medium') {
			placeholderTextString += ' Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		} else if (type === 'long') {
			placeholderTextString += ' Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.';
		}

		return new Handlebars.SafeString(placeholderTextString);

	},
	formatIndexPageUrl: function (page) {

		return page.data.path
			.replace(page.data.base, '')
			.replace('.hbs', '.html');

	},
	formatIndexComponentUrl: function (organism) {

		return organism.key
			.replace(organism._base, '/components')
			.replace('.hbs', '.html');

	}

};
