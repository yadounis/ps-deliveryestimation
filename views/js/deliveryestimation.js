/**
 * deliveryestimation.js
 *
 * Main js file
 *
 * @author     Younes Adounis
 * @copyright  2015 Younes Adounis
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

$(document).ready(function() {
	// Open Fancybox from Cart page
	$("a.open_model").fancybox({
		'autoSize': false,
		'autoWidth': false,
		'autoHeight': true,
		'width': '1000',
		beforeShow: function() {
			$("#deliveryestimation_zone").val($.cookie("deliveryestimation_zone"));
			getShippingEstimation($("#deliveryestimation_zone"));
		},
	});
	// Trigger ajax call when select zone
	$(document).on('change', '#deliveryestimation_zone', function() {
		getShippingEstimation($(this));
		$.cookie("deliveryestimation_zone", $(this).val(), { expires: 7, path: '/' });
		return true;
	});
});

function getShippingEstimation(link) {
	if (link.data('type') == 'product' && parseInt(link.val()) > 0) {
		$.ajax({
			type: 'POST',
			url: baseDir + 'modules/deliveryestimation/ajax.php',
			data: {
				product_id: $("#product_page_product_id").val(),
				id_product_attribute: $('input[name="id_product_attribute"]').val(),
				quantity: $("#quantity_wanted").val(),
				zone_id: $("#deliveryestimation_zone").val(),
				type: 'product'
			},
			dataType: 'html',
			success: function(html) {
				$("#deliveryestimation_tab").html(html);
				$.fancybox.update();
			}
		});
	}
	else if (link.data('type') == 'order' && parseInt(link.val()) > 0) {
		$.ajax({
			type: 'POST',
			url: baseDir + 'modules/deliveryestimation/ajax.php',
			data: {
				zone_id: $("#deliveryestimation_zone").val(),
				type: 'order'
			},
			dataType: 'html',
			success: function(html) {
				$("#deliveryestimation_tab").html(html);
				$.fancybox.update();
			}
		});
	}
	return true;
}