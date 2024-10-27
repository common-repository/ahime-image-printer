(function( $ ) {
	'use strict';

	$(document).ready(function () {

		$("#image_printing_save_setting").on("click",
		function(){
			if(!confirm("Do you want to continue?"))
				return
			$.post(
				ahime.ajax_url,
			{
				action: "ahime_save_setting",
				setting: $("#setting-page").val()
			},
			function (data) {
				data = JSON.parse(data)
				alert(data.msg);
			})
			.fail(function (xhr, status, error) {
				alert(error);
			});
		})

		$("#product-type").on("change", 
		function(){
			if($(this).val()!="simple") $(".printing-image[data-type='simple']").hide();
			else $(".printing-image[data-type='simple']").show();
		})

		$(".printing-order-file button").on("click",
		function(e){
			e.preventDefault();
			var folder_name = $(this).attr("data-id");
			$.post(
				ahime.ajax_url,
			{
				action: "ahime_image_download_zip",
				folder: folder_name
			},
			function (data) {
				if (data != "error") {
					document.location.href = data;
				}
			})
			.fail(function (xhr, status, error) {
				alert(error);
			});
		})

		$("#printing-clear-tmp").on("click",
		function(e){
			if(confirm("Do you want to continue?"))
			{
				$.post(
					ahime.ajax_url,
				{
					action: "image_delete_tmp_file",
				},
				function (data) {
					if (data == "success") {
						alert("The cache has been cleared.");
					}
				})
				.fail(function (xhr, status, error) {
					alert(error);
				});
			}
			
		})


  })

})( jQuery );
