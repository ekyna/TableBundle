(function(doc, $) {
	$(doc).ready(function() {
		$('.table-filter-close').click(function(e) {
			$(this).parents('.table-filters-form').remove();
			e.stopPropagation();
			e.preventDefault();
		});
	});
})(document, jQuery);