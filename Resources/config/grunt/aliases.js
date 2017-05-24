module.exports = {
    'build:table_css': [
        'less:table',
        'cssmin:table_less',
        'clean:table_less'
    ],
    'build:table_js': [
       'uglify:table_js'
    ],
    'build:table': [
        'clean:table_pre',
        'build:table_css',
        'build:table_js',
        'clean:table_post'
    ]
};
