(function( $ ) {
	'use strict';

	
	$(document).ready(function () {

		/* Begin Perfect Scrollbar Script */

		// $(".printing-editing-inner").perfectScrollbar({
		// 	suppressScrollX: true,
		// });

		/* End Perfect Scrollbar Script */

		// var printingCardWidth = $(".printing-card").width();

		// $(".printing-cards-groups").attr("style", "--printing-height:" + printingCardWidth + "px;");

		/** Begin View More/Less Script */

        $(".printing-more").click(function () {
            var indexZoom = parseInt($(".printing-nav").attr("data-zoom"));
            var zoomPlusOrMoins = ++indexZoom;
            $(".printing-card").attr("class", function () {
                return "printing-card printing-zoom-" + zoomPlusOrMoins.toString();
            });

            if(indexZoom === 4) {
                $(".printing-more").addClass("printing-disable");
            } else {
                $(".printing-less").removeClass("printing-disable");
            }
            $(".printing-nav").attr("data-zoom", indexZoom);
            setTimeout(function () {
                getCardImgContainerWidth();
            }, 400);
        });

        $(".printing-less").click(function () {
            var indexZoom = parseInt($(".printing-nav").attr("data-zoom"));
            var zoomPlusOrMoins = --indexZoom;
            $(".printing-card").attr("class", function () {
                return "printing-card printing-zoom-" + zoomPlusOrMoins.toString();
            });

            if(indexZoom === 1) {
                $(".printing-less").addClass("printing-disable");
            } else {
                $(".printing-more").removeClass("printing-disable");
            }
            $(".printing-nav").attr("data-zoom", indexZoom);
            setTimeout(function () {
                getCardImgContainerWidth();
            }, 400);
        });

        /** End View More/Less Script */

        /** Begin Dimension Height Image Cards Script */

        function getCardImgContainerWidth () {

            var itemWidth = $(".printing-card").css("width");

            $(".printing-cards-groups").attr("style", "--printing-height:" + itemWidth + "");

        }

        $(window).resize(function () {
            setTimeout(function () {
                getCardImgContainerWidth();
            }, 400);
        });

        getCardImgContainerWidth();

        /** End Dimension Height Image Cards Script */

		/** Begin Card Design Script */

		function renameValueForAttribut ($cloneTarget, targetField) {

			var totalCardLength = parseInt($(".printing-card").length);

			var totalCard = totalCardLength !== 0 ? totalCardLength+1 : 1 ;

			$cloneTarget.find(".printing-edit-single").attr('data-visible', false)

			if(targetField === "qty") {

				$cloneTarget.find(".printing-label-qty").attr("for", function () {
					return "printing-qty-" + totalCard + "a";
				});
	
				$cloneTarget.find(".printing-form-field-qty").attr("name", function () {
					return "printing-qty-" + totalCard + "a";
				});
	
				$cloneTarget.find(".printing-form-field-qty").attr("id", function () {
					return "printing-qty-" + totalCard + "a";
				});

			} else if(targetField === "size") {

				$cloneTarget.find(".printing-label-size").attr("for", function () {
					return "printing-qty-" + totalCard + "b";
				});

				$cloneTarget.find(".printing-form-field-size").attr("name", function () {
					return "printing-qty-" + totalCard + "b";
				});

				$cloneTarget.find(".printing-form-field-size").attr("id", function () {
					return "printing-qty-" + totalCard + "b";
				});

			}

		};

		$(".printing-duplicate-card").click(function () {

			var $targetCard = $(this).closest(".printing-card");

			setTimeout(function () {
				var $cloneTarget = $targetCard.clone(true);

				renameValueForAttribut ($cloneTarget, "qty");

				renameValueForAttribut ($cloneTarget, "size");
		
				$cloneTarget.insertAfter($targetCard);
			}, 200);

		});

		$(".printing-delete-card").click(function () {
			
			$(this).closest(".printing-card").remove();

		});

		/** End Card Design Script */

		/** Begin Edit all Design Script */

		$(".printing-edit-all").click(function () {
			$(".printing-preview-box-edit-all, .printing-shadow-edit-all").addClass("printing-show");
		
			$("body").css("overflow", "hidden");
		});
	
		$(".printing-shadow-edit-all").click(function () {
			$(this).removeClass("printing-show");
		
			$(".printing-preview-box-edit-all").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});
	
		$(".printing-icon-edit-all-cross").click(function (e) {
			$(".printing-shadow-edit-all").removeClass("printing-show");
		
			$(this).closest(".printing-preview-box-edit-all").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});

		$(".printing-cancel-edit-all").click(function () {
			$(".printing-icon-edit-all-cross").click();
		});

		// $(".printing-update-edit-all").click(function () {
		// 	var targetCurrent = $('.printing-preview-box-edit-all .printing-edit-all-current').val();
		// 	var targetSize = $('.printing-preview-box-edit-all .printing-edit-all-changed').val();
		// 	var targetQty = $('.printing-preview-box-edit-all .printing-edit-all-qty').val();

		// 	$(".printing-card").each(function () {
		// 		if($(this).find(".printing-form-field-select").val() === targetCurrent) {
		// 			$(this).find(".printing-form-field-select").val(targetSize);
		// 			$(this).find(".printing-form-field-number").val(targetQty);
		// 		}
		// 	});
			
		// 	$(".printing-icon-edit-all-cross").click();
		// });
	
		/** End Edit all Design Script */

		/** Begin Continue Design Script */

		$(".printing-continue").click(function () {
			$(".printing-preview-box-continue, .printing-shadow-continue").addClass("printing-show");
		
			$("body").css("overflow", "hidden");
		});
	
		$(".printing-shadow-continue").click(function () {
			$(this).removeClass("printing-show");
		
			$(".printing-preview-box-continue").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});
	
		$(".printing-icon-continue-cross").click(function (e) {
			$(".printing-shadow-continue").removeClass("printing-show");
		
			$(this).closest(".printing-preview-box-continue").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});

		$(".printing-go-back").click(function () {
			$(".printing-icon-continue-cross").click();
		});

		$(".printing-continue-anyway").click(function () {
			$(".printing-icon-continue-cross").click();
		});

		/** End Continue Design Script */

		/** End Edit Design Script */

		$(document).on("click",".printing-edit-single",function () {
			var $parent = $(this).closest(".printing-card");
			if(!$parent.hasClass("ready-to-print")) return;
			var targetQty = $parent.find(".printing-form-field-number").val();
			var targetImg = $(this).find(".printing-card-img-self").attr('src');
			$('.printing-preview-box-edit .printing-edit-qty').val(targetQty);
			$('.printing-preview-box-edit .printing-card-img-self').attr('src', targetImg);
			$(this).attr('data-visible', true);
			$(".printing-preview-box-edit, .printing-shadow-edit").addClass("printing-show");
		
			$("body").css("overflow", "hidden");
		});
	
		$(".printing-shadow-edit").click(function () {
			$(this).removeClass("printing-show");
		
			$(".printing-preview-box-edit").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});
	
		$(".printing-icon-edit-cross").click(function (e) {
			$(".printing-shadow-edit").removeClass("printing-show");
		
			$(this).closest(".printing-preview-box-edit").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});

		$(".printing-cancel-edit").click(function () {
			$(".printing-icon-edit-cross").click();
		});

		$(".printing-editing-edit").click(function () {
			//var targetQty = $(this).closest(".printing-column-right").find(".printing-form-field-number").val();
			// $(".printing-card").each(function () {
			// 	var targetVisible = $(this).find(".printing-edit-single").attr('data-visible');
			// 	if(targetVisible === "true") {
			// 		$(this).find(".printing-form-field-number").val(targetQty);
			// 	}
			// });
			$('.printing-preview-box-edit .printing-edit-qty').val();
			$(".printing-icon-edit-cross").click();
		});

		/** End Edit Design Script */

		// $(".printing-upload").click(function () {
			
		// 	$(".printing-file").click();

		// });


		// Product page
		$(".ahime-image-container_variable").hide();

		if($(".ahime-image-container").attr("data-type")=="simple")
		{
		   if(!hide_btn) $(".ahime-image-container").show();
		   else $(".ahime-image-container").hide();
	 
		   $(".ahime-image-container").find("a").attr("href", design_page_url.url);
		}
	 
		$(document).on("change", ".variations select", function () {
			$(this).each(function(){
			   if($(this).val() == "")
			   {
				$(".ahime-image-container_variable").hide();
			   }
			})

		   var variation_id = $("input[name='variation_id']").val();
		   if (variation_id) {
			  if($.inArray(parseInt(variation_id), hide_btn)!=-1)
			  {
				 $(".ahime-image-container_variable").show();
			  }
			  else $(".ahime-image-container_variable").hide();
	 
			  design_page_url.url = design_page_url.url +
			  "&id=" +
			  variation_id;
	 
			  $(".ahime-image-container_variable").find("a").attr("href", design_page_url.url);
		   }
	 
		});
	 

		/** Begin Upload File Design Script */

		$(".printing-upload").click(function () {
			
			$(".printing-preview-box-file, .printing-shadow-file").addClass("printing-show");
		
			$("body").css("overflow", "hidden");

		});

		$(".printing-shadow-file").click(function () {
			$(this).removeClass("printing-show");
		
			$(".printing-preview-box-file").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});
	
		$(".printing-icon-file-cross").click(function (e) {
			$(".printing-shadow-file").removeClass("printing-show");
		
			$(this).closest(".printing-preview-box-file").removeClass("printing-show");
		
			$("body").css("overflow", "scroll");
		});

		$(".printing-file").click(function (e) {
			$("#printing-file").click();
		});

		$(".printing-drag-area").on("drop", function (e) {
			$(".printing-icon-file-cross").click();
		});

		/** End Upload File Design Script */


		// Save attribue and value on product page
		$(".ahime-image-container_variable").on("click", function(){
			var ahime_image_attribute = {};
			var design_link = $(this).find("a").attr("href");
			
			$(this).find("a").attr("href", "#0");
			var attr = $(".variations").find("select");
			$(attr).each(function(){
			   ahime_image_attribute[$(this).attr("name")] = $(this).val();
			})
	  
			var frm = new FormData();
				frm.append("action", "ahime_image_save_user_choice");
				frm.append("ahime_image_data_attr", JSON.stringify(ahime_image_attribute));
				frm.append("variation_id", $("input[name='variation_id']").val());
	  
			$.ajax({
				type: "POST",
				url: ahime.ajax_url,
				data: frm,
				processData: false,
				contentType: false,
			  }).success(function (data) {
				if(data == "success")
				{
					document.location.href = design_link ;
				}
				// else if(data == "fail")
				// {

				// }
			  })
			  .fail(function (xhr, status, error) {
				alert(error);
			 });
	  
		  //  console.log(JSON.stringify(cld_attribute))
	  
		 })
	 });

})( jQuery );
