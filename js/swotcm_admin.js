/*
*
* Seller Widgets Admin Dashboard JS
* @jQuery datatables
* @Admin Wrap
*
*/

 jQuery(document).ready(function($) {
		$('#campaigns').dataTable({
			"paging":   true,
			"order": [[ 1, "desc" ]],
			"info":     true,
			"pagingType": "full_numbers"
		});
});

function copyToClipboard(element) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val($(element).text()).select();
	document.execCommand("copy");
	$temp.remove();
}