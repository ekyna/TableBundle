module.exports = function (grunt, options) {
    return {
        table_less: {
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/TableBundle/Resources/public/tmp/css',
                    src: ['*.css'],
                    dest: 'src/Ekyna/Bundle/TableBundle/Resources/public/css',
                    ext: '.css'
                }
            ]
        }
    }
};
