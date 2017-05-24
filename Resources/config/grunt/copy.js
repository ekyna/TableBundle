module.exports = function (grunt, options) {
    return {
        table_less: { // For watch:table_less
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/TableBundle/Resources/public/tmp/css',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/TableBundle/Resources/public/css'
                }
            ]
        },
        table_js: { // For watch:table_js
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/TableBundle/Resources/private/js',
                    src: ['**/*.js'],
                    dest: 'src/Ekyna/Bundle/TableBundle/Resources/public/js'
                }
            ]
        }
    }
};
