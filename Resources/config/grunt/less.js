module.exports = function (grunt, options) {
    // @see https://github.com/gruntjs/grunt-contrib-less
    return {
        table: {
            files: {
                'src/Ekyna/Bundle/TableBundle/Resources/public/tmp/css/table.css':
                    'src/Ekyna/Bundle/TableBundle/Resources/private/less/table.less'
            }
        }
    }
};
