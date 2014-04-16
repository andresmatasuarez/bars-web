
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

			phpmailer: {
				files: [
					{ expand: true, flatten: true, filter: 'isFile', src: '<%= bars.phpmailer.src %>', dest: '<%= bars.phpmailer.dest %>' }
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

			phpmailer: {
				files: [
					'<%= bars.phpmailer.src %>'
				],
				tasks: [ 'newer:copy:phpmailer' ]
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

	// Register tasks
	grunt.registerTask('default', 'dev');
	grunt.registerTask('dev', [ 'clean:build', 'concat', 'newer:copy:php', 'newer:copy:fancybox', 'newer:copy:phpmailer', 'newer:copy:misc', 'newer:imagemin:max', 'watch' ]);
	grunt.registerTask('prod', [ ]);
	
};