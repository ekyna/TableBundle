(function (window, $) {

    var EkynaTable = function (elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options || [];
        this.metadata = this.$elem.data('options');
    };

    EkynaTable.prototype = {
        defaults: {
            onSelection: function(elements){}
        },
        init: function () {
            this.config = $.extend({}, this.defaults, this.options, this.metadata);
            if (this.config.selector) {
                /* TEMP / TODO */
                this.$elem.find('a').bind('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Not yet implemented.');
                });
                /* END TEMP */
                this.initSelector();
            }

            return this;
        },
        initSelector: function () {
            var t = this;
            t.$elem.find('button.table-selection-validate').bind('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (t.config.onSelection === undefined) {
                    return;
                }

                var elements = [];
                t.$elem.find('td.selector input:checked').each(function () {
                    elements.push($(this).data('element'));
                });

                t.config.onSelection(elements);
            });
        }
    };

    EkynaTable.defaults = EkynaTable.prototype.defaults;

    $.fn.ekynaTable = function (options) {
        return this.each(function () {
            new EkynaTable(this, options).init();
        });
    };

    window.EkynaTable = EkynaTable;

    $(window.document).ready(function () {
        $('.table-filter-close').on('click', function (e) {
            $(this).parents('.table-filters-form').remove();
            e.stopPropagation();
            e.preventDefault();
        });
    });
})(window, jQuery);