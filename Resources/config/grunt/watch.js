module.exports = function (grunt, options) {
    return {
        table_less: {
            files: ['src/Ekyna/Bundle/TableBundle/Resources/private/less/**/*.less'],
            tasks: ['less:table', 'copy:table_less', 'clean:table_less'],
            options: {
                spawn: false
            }
        },
        table_js: {
            files: ['src/Ekyna/Bundle/TableBundle/Resources/private/js/**/*.js'],
            tasks: ['copy:table_js'],
            options: {
                spawn: false
            }
        }
    }
};
