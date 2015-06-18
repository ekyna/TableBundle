(function(root, factory) {
    "use strict";

    // CommonJS module is defined
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'));
    }
    // AMD module is defined
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-table', ['jquery'], function($) {
            return factory($);
        });
    } else {
        // planted over the root!
        root.EkynaTable = factory(root.jQuery);
    }

}(this, function($) {
    "use strict";

    var EkynaTable = function ($elem, options) {
        this.$elem = typeof $elem == 'jQuery' ? $elem : $($elem);
        this.options = options || [];
        this.metadata = this.$elem.data('options');
    };

    EkynaTable.prototype = {
        defaults: {
            onSelection: function(elements){},
            ajax: false
        },
        init: function () {
            this.config = $.extend({}, this.defaults, this.options, this.metadata);
            if (this.config.ajax) {
                /* TEMP disable all links / TODO ajax handlers */
                this.$elem.find('a').bind('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Not yet implemented.');
                });
            }
            if (this.config.selector) {
                /* END TEMP */
                this.initSelector();
            }
            this.initTreeNodes();

            this.$elem.on('click', '.table-filter-close', function (e) {
                $(this).parents('.table-filters-form').remove();
                e.stopPropagation();
                e.preventDefault();
            });

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
        },
        initTreeNodes: function() {
            var t = this;

            function toggle($button) {
                if ($button.hasClass('toggle-open')) {
                    open($button);
                } else if ($button.hasClass('toggle-close')) {
                    close($button);
                }
            }

            function open($button) {
                if (!$button.hasClass('toggle-open')) {
                    return;
                }
                $($button.data('children')).each(function(index, id) {
                    t.$elem.find('tr[data-id=' + id + ']').show();
                });
                $button.removeClass('toggle-open').addClass('toggle-close');
            }

            function close($button) {
                if (!$button.hasClass('toggle-close')) {
                    return;
                }
                $($button.data('children')).each(function(index, id) {
                    t.$elem.find('tr[data-id=' + id + ']').hide()
                        .find('a.toggle').each(function(index, button) {
                            close($(button));
                        });
                });
                $button.removeClass('toggle-close').addClass('toggle-open');
            }

            t.$elem.find('td.nested a.toggle').bind('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggle($(e.target));
            });

            t.$elem.find('td.nested > a.toggle-close').each(function(index, button) {
                close($(button));
            });
        }
    };

    EkynaTable.defaults = EkynaTable.prototype.defaults;

    return {
        create: function($element, options) {
            var table = new EkynaTable($element, options);
            table.init();
            return table;
        }
    };

}));