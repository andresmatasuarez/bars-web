
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
		
		// Concat
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
			}
		},
		
		// Imagemin
		imagemin: {
			max: {
				options: { optimizationLevel: 7 },
				files: [{
					expand: true, flatten: true, filter: 'isFile', src: '<%= bars.src.img %>', dest: '<%= bars.dest.img %>' }
				]
			},
			low: {
				options: { optimizationLevel: 3 },
				files: [{
					expand: true, flatten: true, filter: 'isFile', src: '<%= bars.src.img %>', dest: '<%= bars.dest.img %>' }
				]
			}
		},

		// Watch
		watch: {
			dev: {
				options: {
					livereload: true,
					nospawn: true
				},
				files: [
					'<%= pkg.gruntfile %>',
					'<%= bars.src.lib %>',
					'<%= bars.src.js %>',
					'<%= bars.src.css %>',
					'<%= bars.src.img %>',
					'<%= bars.src.misc %>'
				],
				// Dev
				tasks: [ 'concat:js', 'concat:css', 'copy:misc' ]
			},

			prod: {
				files: [
					'<%= pkg.gruntfile %>',
					'<%= bars.src.lib %>',
					'<%= bars.src.js %>',
					'<%= bars.src.css %>',
					'<%= bars.src.img %>',
					'<%= bars.src.misc %>'
				],
				tasks: [ 'prod' ]
			}
		}

	});
	
	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Register tasks
	grunt.registerTask('default', 'dev');
	grunt.registerTask('dev', [ 'concat:js',  'concat:css', 'copy:misc', 'imagemin:max', 'watch:dev' ]);
	grunt.registerTask('prod', [ ]);
	
};