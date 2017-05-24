module.exports = function (grunt, options) {
    return {
        table_js: {
            files: [{
                expand: true,
                cwd: 'src/Ekyna/Bundle/TableBundle/Resources/private/js',
                src: '**/*.js',
                dest: 'src/Ekyna/Bundle/TableBundle/Resources/public/js'
            }]
        }
    }
};
