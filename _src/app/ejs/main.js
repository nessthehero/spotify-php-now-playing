/*global window:false */
/*global Vue:false */
/*global axios:false */

import './lib/jquery.js';

// const endpointSongAndPosition = 'http://local.spotify/position.php';
const endpointSongAndPosition = '/position.php';

window.vm = new Vue({
	delimiters: ['{$', '$}'],
	el: '#player',
	data: {
		frameId: '',
		fallbackImg: '/_files/img/silence.jpg',
		song: {
			albumart: '',
			albumname: '',
			artist: '',
			song: '',
			playing: false,
			progress: {
				progress: 0,
				length: 0,
				remaining: 0
			},
			playlist: ''
		},
		lastChecked: 0,
		progress: 0,
		songPosition: '0:00',
		timeout: 6000
	},
	created: function () {

		this.getSongAndPosition();

		let now = new Date();

		let seconds = now.getTime();

		this.lastChecked = seconds;

		this.frameId = window.requestAnimationFrame(
			this.getProgressBar
		);

	},
	computed: {

		albumArt: function () {

			let albumArt = this.fallbackImg;
			if (this.song.albumart !== '') {
				albumArt = this.song.albumart;
			}

			return {
				backgroundImage: 'url(' + albumArt + ')'
			};
		},

		progressBar: function () {
			return {
				width: this.progress + '%'
			};
		},

		songLength: function () {

			let seconds = Math.floor(this.song.progress.length / 1000);
			let minutes = Math.floor(seconds / 60);
			let remainingSeconds = seconds - (minutes * 60);

			return minutes + ':' + this.n(remainingSeconds);

		}

	},
	methods: {

		sec2time(timeInSeconds) {
			let pad = function (num, size) {
					return ('000' + num).slice(size * -1);
				},
				time = parseFloat(timeInSeconds).toFixed(3),
				hours = Math.floor(time / 60 / 60),
				minutes = Math.floor(time / 60) % 60,
				seconds = Math.floor(time - minutes * 60);

			return pad(hours, 2) + ':' + pad(minutes, 2) + ':' + pad(seconds, 2);
		},

		getSongAndPosition() {

			let ep = endpointSongAndPosition;

			axios.get(ep).then(function (response) {

				let now = new Date();

				let seconds = now.getTime();

				this.song = response.data;
				this.lastChecked = seconds;

				let pageTitle = 'zZzZ';

				if (this.song.song !== '') {
					pageTitle = this.song.song + ' - ' + this.song.artist;
				}

				window.document.title = pageTitle;

				setTimeout(this.getSongAndPosition, this.timeout);

			}.bind(this));

		},

		getProgressBar() {

			this.frameId = window.requestAnimationFrame(this.getProgressBar);

			let now = new Date();
			let seconds = now.getTime();
			let diff = seconds - this.lastChecked;

			let remaining = this.song.progress.remaining - diff;

			this.progress = 100 - ((remaining / this.song.progress.length) * 100);

			this.songPosition = this.getSongPosition();

		},

		getSongPosition: function () {

			let seconds = Math.floor(this.song.progress.progress / 1000);
			let minutes = Math.floor(seconds / 60);
			let remainingSeconds = seconds - (minutes * 60);

			let now = new Date();
			let nowTime = now.getTime();
			let diff = nowTime - this.lastChecked;

			let remaining = remainingSeconds + Math.floor(diff / 1000);

			if (remaining >= 60) {
				remaining -= 60;
				minutes += 1;
			}

			return minutes + ':' + this.n(remaining);

		},

		n(n) {
			return n > 9 ? '' + n : '0' + n;
		}

	}
});