
module.exports = function(grunt){

	'use strict';

	// Package options
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		bars: grunt.file.readJSON('bars.json'),

		// JSHint
		jshint: {
			// http://www.jshint.com/docs/options/
			// Review this on the future
			options: {
				'jshintrc': '.jshintrc'
			},
			all: [
				'<%= pkg.gruntfile %>',
				'<%= bars.src.js %>'
			]
		},

		//Concat
		concat: {
			options: {
				separator: '\n/*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*/\n'
			},

			js: {
				src: [ '<%= bars.src.lib %>', '<%= bars.src.js %>' ],
				dest: '<%= bars.dest.libOutput %>'
			},

			css: {
				src: '<%= bars.src.css %>',
				dest: '<%= bars.dest.cssOutput %>'
			}
		},

		// Uglify
		uglify: {
			minify: {
				files: {
					'<%= bars.dest.libOutput %>' : [ '<%= bars.src.lib %>', '<%= bars.src.js %>' ]
				}
			}
		},

		// CSSMin
		cssmin: {
			minify: {
				keepSpecialComments: '*',
				files: {
					'<%= bars.dest.cssOutput %>' : '<%= bars.src.css %>'
				}
			}
		},

		// Copy
		copy: {
			misc: {
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src: '<%= bars.src.misc %>', dest: '<%= bars.dest.misc %>' }
				]
			},

			php: {
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src:'<%= bars.src.php %>', dest: '<%= bars.dest.php %>' }
				]
			},

			fancybox: {
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src: '<%= bars.fancybox.src %>', dest: '<%= bars.fancybox.dest %>' }
				]
			},

			vendor: {
				files: [
					{ src: '<%= bars.vendor.src %>', dest: '<%= bars.vendor.dest %>' }
				]
			}
		},

		// Imagemin
		imagemin: {
			max: {
				options: { optimizationLevel: 7 },
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src: '<%= bars.src.img %>', dest: '<%= bars.dest.img %>' }
				]
			},
			low: {
				options: { optimizationLevel: 3 },
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src: '<%= bars.src.img %>', dest: '<%= bars.dest.img %>' }
				]
			}
		},

		clean: {
			build: ['<%= bars.clean %>']
		},

		// Watch
		watch: {
			options: { livereload: true, nospawn: true },
			js: {
				files: [
					'<%= bars.src.lib %>',
					'<%= bars.src.js %>'
				],
				tasks: [ 'concat:js' ]
			},

			css: {
				files: [
					'<%= bars.src.css %>'
				],
				tasks: [ 'concat:css' ]
			},

			img: {
				files: [
					'<%= bars.src.img %>'
				],
				tasks: [ 'newer:imagemin:max' ]
			},

			php: {
				files: [
					'<%= bars.src.php %>'
				],
				tasks: [ 'newer:copy:php' ]
			},

			misc: {
				files: [
					'<%= bars.src.misc %>'
				],
				tasks: [ 'newer:copy:misc' ]
			},

			fancybox: {
				files: [
					'<%= bars.fancybox.src %>'
				],
				tasks: [ 'newer:copy:fancybox' ]
			},

			vendor: {
				files: [
					'<%= bars.vendor.src %>'
				],
				tasks: [ 'newer:copy:vendor' ]
			}
		},

		sprite:{
      all: {
        src: '<%= bars.src.sprites %>',
        destImg: '<%= bars.dest.sprites %>',
        destCSS: 'css/sprites.css',
        imgPath: '<%= bars.dest.cssSpritesImgPath %>',
        cssOpts: {
					// CSS template allows for overriding of CSS selectors
					cssClass: function (item) {
						var itemName = item.name;
						var cssSuffix = '';
						var cssClass = '.sprite-' + itemName;

						// Check if hover image.
						var imgHoverPrefix = 'hover_';
						if (itemName.substring(0, imgHoverPrefix.length) === imgHoverPrefix){
							itemName = itemName.substring(imgHoverPrefix.length);
							cssSuffix = ':hover';
						}

						// Associate with existing classes.
						if (	itemName === 'facebook' || itemName === 'twitter' || itemName === 'youtube' ||
									itemName === 'tumblr' || itemName === 'flickr'){
							cssClass = '#bars-header-social-' + itemName;
						/*
						} else if (itemName === 'convocatoria2014'){
							cssClass = '#header-menu .nav-menu li:nth-child(4) a.call-is-open';
						*/
						} else if (itemName === 'festeringslime'){
							cssClass = '#footer .footer-logos .fs-image';
						} else if (itemName === 'footer_logo'){
							cssClass = '#footer .footer-logos .bars-image';
						} else if (itemName === 'loguito'){
							cssClass = '.bars-widget-logo';
						} else if (itemName === 'header'){
							cssClass = '#header-image';
						} else if (itemName === 'arrows'){
							cssClass = '#slider .slider-controls .arrow';
						}

						return cssClass + cssSuffix;
					}
				}
      }
    }

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-newer');
	grunt.loadNpmTasks('grunt-usemin');
	grunt.loadNpmTasks('grunt-spritesmith');

	// Register tasks
	grunt.registerTask('default', 'dev');
	grunt.registerTask('dev', [ 'clean:build', 'concat', 'newer:copy:php', 'newer:copy:fancybox', 'newer:copy:vendor', 'newer:copy:misc', 'newer:imagemin:max', 'sprite:all', 'watch' ]);
	grunt.registerTask('prod', [ ]);

};