var AHIME = (function ($, Ahime) {
   'use strict';
   Ahime = {};
   $(document).ready(function () {

      $(".printing-editing-inner").perfectScrollbar({
         suppressScrollX: true,
      });

      const canvasProperty = {
         width: 400,
         height: 400,
         fill: "transparent"
      };

      const canvas = new fabric.Canvas('canvas', canvasProperty);

      /**
    * Converts inch sizes in pixels
    * @param {*} val 
    * @returns float
    */
      Ahime.convertInchToPx = function (val) {
         return parseFloat(val * 96);
      }

      const boxRatio = {
         "6x4": 6 / 4,
         "7x5": 7 / 5,
         "8x6": 8 / 6,
         "10x8": 10 / 8,
         "12x8": 12 / 8
      }

      const printBy = {
         "6x4": canvas.width / boxRatio["6x4"],
         "7x5": canvas.width / boxRatio["7x5"],
         "8x6": canvas.width / boxRatio["8x6"],
         "10x8": canvas.width / boxRatio["10x8"],
         "12x8": canvas.width / boxRatio["12x8"],
      };


      const rectProperty = {
         fill: 'transparent',
         stroke: 'red',
         height: canvas.height / 2,
         width: canvas.width / 2,
         strokeWidth: 3,
         selectable: true,
         boundingBox: true,
         centeredScaling: true,
         lockRotation: false,
         hasRotatingPoint: true,
         cornerColor: "red",
         transparentCorners: false,
      };

      const part = {
         fill: '#62636394',
         selectable: false,
         lockMovementY: true,
         lockMovementX: true,
      };

      var canvas_elm = { Rect: [], uniq_id: [] },
         resizeRect = {},
         saveImage = {},
         slot1 = {},
         part_top = new fabric.Rect(part),
         part_left = new fabric.Rect(part),
         part_right = new fabric.Rect(part),
         part_bottom = new fabric.Rect(part);

      function get_optimal_size($size, $size2) {
         var gHeight = Math.min($size, $size2);
         var gWidth = Math.max($size, $size2);
         var gRatio = gHeight / gWidth;
         return canvas.width * gRatio;
      }

      function get_image_ratio(image) {
         if (image.height > image.width) var r = image.height / image.width;
         else var r = image.width / image.height;

         let outSize = "resize";

         var compt = 0;

         $.each(boxRatio, function (index, ratio) {
            if ((r == boxRatio[index] || (r >= (boxRatio[index] - .04) && r <= (boxRatio[index] + .04))) && compt == 0) {
               outSize = index;
               compt += 1;
            }
         })

         return outSize;
      }

      function get_print_size(size, out) {
         switch (size) {
            case "6x4": {
               let outwidth = printBy["6x4"];
               if (out == size) outwidth = canvas.height - 10;
               return ["6x4", outwidth];
            }

            case "7x5": {
               let outwidth = printBy["7x5"];
               if (out == size) outwidth = canvas.height - 10;
               return ["7x5", outwidth];
            }

            case "8x6": {
               let outwidth = printBy["8x6"];
               if (out == size) outwidth = canvas.height - 10;
               return ["8x6", outwidth];
            }

            case "10x8": {
               let outwidth = printBy["10x8"];
               if (out == size) outwidth = canvas.height - 10;
               return ["10x8", outwidth];
            }

            case "12x8": {
               let outwidth = printBy["12x8"];
               if (out == size) outwidth = canvas.height - 10;
               return ["12x6", outwidth];
            }
         }
      }

      /**
       * On upload file, add file on canvas and set bounding box
       */
      $("#printing-file").on("change", function (e) {
         if (e.target.files.length > 0) {
            var files = e.target.files;
            for (var i = 0, f; f = files[i]; i++) {

               // Only process image files.
               if (!f.type.match('image.*')) {
                  continue;
               }
               uploadMultiFiles(f)
            }
         }
         $(".printing-icon-file-cross").click();
      });

      // Upload image with drag and drop
      $.event.props.push('dataTransfer');
      $('.printing-drag-area').on({
         dragover: function (e) {
            e.stopPropagation();
            e.preventDefault();
            return false;
         },
         dragleave: function (e) {
            e.stopPropagation();
            e.preventDefault();
            return false;
         },
         drop: function (e) {
            e.stopPropagation();
            e.preventDefault();
            var files = e.dataTransfer.files;
            for (var i = 0, f; f = files[i]; i++) {
               // Only process image files.
               if (!f.type.match('image.*')) {
                  continue;
               }
               uploadMultiFiles(f)
            }
            return false;
         }
      })


      function uploadMultiFiles(file) {
         var slot = new fabric.Rect(rectProperty);
         slot.setControlsVisibility({
            mtr: false,
            ml: false,
            mr: false,
            mt: false,
            mb: false,
            // 'tr':true,
            // 'bl':false,
            // 'br':false,
         });
         var image_id = Ahime.generate_uniq_id();

         while ($.inArray(image_id, canvas_elm.uniq_id) != -1) {
            var image_id = Ahime.generate_uniq_id();
         }

         saveImage.active = image_id;
         canvas_elm.uniq_id.push(image_id);
         saveImage[image_id] = [];
         saveImage[image_id].scaling = false;
         saveImage[image_id].old = '';
         //saveImage[image_id].original = file;
         saveImage[image_id].image = file;
         saveImage[image_id].bound = slot;
         saveImage[image_id].img = canvas_elm;
         var $clone = $("#clone-printing-card").clone();
         $clone.attr("data-id", image_id);
         $clone.find("input").val(1);
         $clone.attr("id", "");
         $clone.find(".printing-card-title").text(file.name);
         $clone.css("display", "block");
         $clone.appendTo(".printing-cards-groups");
         $clone.find(".printing-loader").addClass("printing-show");
         var new_canvas = $(".printing-clone-canvas").clone();
         new_canvas.attr("id", "myCanvas_" + image_id);
         new_canvas.removeClass("printing-clone-canvas");
         new_canvas.addClass("is-printing-clone-canvas");
         new_canvas.find("canvas").attr("id", "canvas_" + image_id);
         $("#myCanvas").hide();
         new_canvas.insertAfter("#myCanvas");
         saveImage[image_id].status = 1;
         saveImage[image_id].canvas = new fabric.Canvas("canvas_" + image_id, canvasProperty);
         saveImage[image_id].exist = false;
         Ahime.setCanvasBackgroundImage(file, "default", image_id);

      }

      function setCardImage(image_id) {
         saveImage[image_id].canvas.calcOffset();
         saveImage[image_id].canvas.renderAll();
         var $card = $(".printing-card[data-id='" + image_id + "']");
         var $url = Ahime.printResizeImage(image_id);
         $card.find("img").attr("src", $url[0]);
         $card.attr("data-url", $url[1]);
         $card.find("select").trigger("change");
         saveImage[image_id].url = $url[1];
      }

      // On  click to image
      $(document).on("click", ".printing-card", function () {
         var $data_key = $(this).attr("data-id");
         var image = saveImage[$data_key].image;
         //var new_img =  saveImage[$data_key].img.image;
         saveImage[$data_key].canvas.remove(part_top, part_bottom, part_left, part_right);
         saveImage.active = $data_key;
         $(".is-printing-clone-canvas").hide();
         $("#myCanvas_" + $data_key).show()

         var slot = saveImage[$data_key].bound;
         //saveImage[$data_key].canvas.remove(slot1);

         if (slot.width > slot.height) $("#printing-edit-crop-orientation").val("landscape");
         else $("#printing-edit-crop-orientation").val("portrait");
         $(".printing-editing-inner").attr("data-id", $data_key);
         saveImage[$data_key].canvas.setActiveObject(slot);
         saveImage[$data_key].canvas.renderAll();
         saveImage[$data_key].canvas.calcOffset();
         onTouchObjectFunction();
         //canvas.remove(part_top, part_bottom, part_left, part_right);
      })

      // Set background image
      Ahime.setCanvasBackgroundImage = function (file, size, $key) {
         var slot = saveImage[$key].bound;
         var reader = new FileReader();
         reader.onload = function (f) {
            var data = f.target.result;
            fabric.Image.fromURL(data, function (image) {
               if (image.height >= image.width) {
                  var optimal = get_optimal_size(image.height, image.width, get_image_ratio(image));
                  if (optimal >= canvas.width) optimal = canvas.width - 20;
                  Ahime.setHeight(slot, canvas.height - 20);
                  Ahime.setWidth(slot, optimal);
               }
               else {
                  var optimal = get_optimal_size(image.height, image.width, get_image_ratio(image));
                  if (optimal >= canvas.width) optimal = canvas.width - 20;
                  Ahime.setHeight(slot, optimal);
                  Ahime.setWidth(slot, canvas.width - 20);
               }
               // else{
               //    var optimal = get_optimal_size(image.height,image.width, get_image_ratio(image));
               //    if(optimal >= canvas.width) optimal = canvas.width - 20;
               //    Ahime.setHeight(slot, optimal);
               //    Ahime.setWidth(slot, optimal);
               // }

               saveImage[$key].canvas.setBackgroundImage(image, saveImage[$key].canvas.renderAll
                  .bind(saveImage[$key].canvas), {
                  scaleX: slot.width / image.width,
                  scaleY: slot.height / image.height,
                  hoverCursor: 'default',
               });


               if (image.height > image.width) {
                  // slot.set({
                  //    lockMovementX: true,
                  //    lockMovementY: false,
                  // })
                  resizeRect.resizeBy = "height";
                  resizeRect.width = slot.width;
                  resizeRect.height = slot.height;
                  resizeRect.scaleY = null;
                  resizeRect.scaleX = slot.scaleX
               }
               else if (image.height < image.width) {
                  // slot.set({
                  //    lockMovementX: false,
                  //    lockMovementY: true,
                  // })
                  resizeRect.resizeBy = "width";
                  resizeRect.height = slot.height;
                  resizeRect.width = slot.width;
                  resizeRect.scaleX = null;
                  resizeRect.scaleY = slot.scaleY
               }

               if (size != "default" && size != "duplicate") {
                  if (image.height >= image.width) {
                     var print_size = get_print_size(size, get_image_ratio(image));
                     Ahime.setHeight(slot, print_size[1]);
                  }
                  else {
                     var print_size = get_print_size(size, get_image_ratio(image));
                     Ahime.setWidth(slot, print_size[1]);
                  }
                  // else{
                  //    var print_size = get_print_size(size, get_image_ratio(image));
                  //    Ahime.setHeight(slot, print_size[1]);
                  // }
               }

               saveImage[$key].canvas.add(slot);
               canvas_elm.slot = slot;
               slot.centerH();
               slot.centerV();
               image.centerH();
               image.centerV();

               if (image.height > image.width) var this_orientation = "portrait";
               else var this_orientation = "landscape";

               if (typeof saveImage[$key].flip === "undefined")
                  saveImage[$key].flip = {
                     height: slot.height,
                     width: slot.width,
                     orientation: this_orientation
                  }
               canvas_elm.image = image;
               canvas_elm.original = data;
               saveImage[$key].bound = slot;
               saveImage[$key].original = data;
               saveImage[$key].bgImage = image;
               saveImage[$key].box = {};
               saveImage[$key].box.height = slot.height;
               saveImage[$key].box.width = slot.width;
               saveImage[$key].canvas.calcOffset();
               saveImage[$key].canvas.renderAll();
               saveImage[$key].canvas.setActiveObject(slot);
               //saveImage[$key].status = reader.readyState;
               if (reader.readyState == 2 && saveImage[$key].status != "done") {
                  setCardImage($key);
                  saveImage[$key].status = "done";
               }
            });
         };
         reader.readAsDataURL(file);
      }

      // Scale image to rect size
      Ahime.scaleImageToSlot = function (image, slot, pos, $key) {
         var image = saveImage[$key].bgImage;
         var h = slot.height,
            v = slot.width;
         var orientation = $("#printing-edit-crop-orientation").val();
         var img_orient = image.height > image.width;

         if (img_orient) img_orient = "portrait";
         else img_orient = "landscape";
         if (typeof saveImage[$key].box !== "undefined") {
            h = saveImage[$key].box.height;
            v = saveImage[$key].box.width;
         }

         if (slot.height > slot.width) {
            var slot_orientation = "portrait";
            var orient = "h";
         }
         else {
            var slot_orientation = "landscape";
            var orient = "v";
         }

         switch (img_orient) {
            case "portrait": {
               if (slot_orientation == "portrait" && orientation == "portrait") {
                  if (pos == "fill") {
                     Ahime.setHeight(slot, h);
                     Ahime.setWidth(slot, v);
                  }
                  else if (pos == "fit") {
                     Ahime.setHeight(slot, canvas.height - 5);
                     Ahime.setWidth(slot, v);
                  }

               }
               else {
                  if (pos == "fill") {
                     image.scaleToHeight(v);
                  }
                  else {
                     $("#printing-edit-crop-orientation").val("landscape");
                     $("#printing-edit-crop-orientation").trigger("change");
                  }
               }
               break;
            }

            case "landscape": {
               if (slot_orientation == "landscape" && orientation == "landscape") {
                  if (pos == "fill") {
                     Ahime.setHeight(slot, h);
                     Ahime.setWidth(slot, v);
                  }
                  else if (pos == "fit") {
                     Ahime.setHeight(slot, v);
                     Ahime.setWidth(slot, canvas.width - 5);
                  }

               }
               else {
                  if (pos == "fill") {
                     image.scaleToWidth(h);
                  }
                  else {
                     $("#printing-edit-crop-orientation").val("portrait");
                     $("#printing-edit-crop-orientation").trigger("change");
                  }
               }
               break;
            }
         }

         slot.centerH();
         slot.centerV();
         image.centerH();
         image.centerV();
         saveImage[$key].canvas.calcOffset();
         saveImage[$key].canvas.renderAll();
         saveImage[$key].bound = slot;
      }

      // Get canvas url start
      var get_URL = function () {
         return window.URL || window.webkitURL || window;
      };

      Ahime.printResizeImage = function ($key) {
         saveImage[$key].canvas.calcOffset();
         saveImage[$key].canvas.renderAll();

         var dataURI = saveImage[$key].canvas.toDataURL('image/png', 1.0),
            byteString = atob(dataURI.split(',')[1]),
            mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0],
            arrayBuffer = new ArrayBuffer(byteString.length),
            _ia = new Uint8Array(arrayBuffer);

         for (var i = 0; i < byteString.length; i++) {
            _ia[i] = byteString.charCodeAt(i);
         }

         var dataView = new DataView(arrayBuffer);
         var blob = new Blob([dataView], { type: mimeString });
         //Ahime.fillUnselectedPart();
         return [get_URL().createObjectURL(blob), dataURI];
      }
      // Get canvas url end

      //Set rect box size
      Ahime.setResizeBoxSize = function (size = "6x4", slot, $key) {
         var getImageSize = size.split("x"),
            slotHeight = slot.height,
            slotWidth = slot.width;
         //image = canvas_elm.image;

         if (saveImage[$key].old == '') {
            saveImage[$key].oldwidth = slot.width;
            saveImage[$key].oldheight = slot.height;
            saveImage[$key].old = "save"
         }
         var image = saveImage[$key].image
         // var newHeight = saveImage[$key].oldheight;
         // var newWidth = saveImage[$key].oldwidth;
         if (resizeRect.resizeBy == "height") {
            var print_size = get_print_size(size, get_image_ratio(image));
            Ahime.setHeight(slot, print_size[1]);
         }
         else if (resizeRect.resizeBy == "width") {
            var print_size = get_print_size(size, get_image_ratio(image));
            Ahime.setWidth(slot, print_size[1]);
         }
         saveImage[$key].canvas.remove(slot1);
         saveImage[$key].canvas.add(slot);
         saveImage[$key].canvas.calcOffset();
         saveImage[$key].canvas.renderAll();
         saveImage[$key].bound = slot;
         slot.centerV();
         slot.centerH();

         return [slotHeight, slotWidth];
      }

      /**
       * Set the height to the element
       * @param {*} elm 
       * @param {*} height 
       */
      Ahime.setHeight = function (elm, height) {
         if (typeof elm !== "undefined" && !isNaN(parseFloat(height))) {
            elm.set({
               height: height
            })
         }
      }

      /**
       * Set the width to the element
       * @param {*} elm 
       * @param {*} width 
       */

      Ahime.setWidth = function (elm, width) {
         if (typeof elm !== "undefined" && !isNaN(parseFloat(width))) {
            elm.set({
               width: width
            })
         }
      }

      /**
       * Calculates and returns the optimal ratio
       * @param {*} height 
       * @param {*} height2 
       * @param {*} width 
       * @param {*} width2 
       * @returns scale factor
       */
      Ahime.getOptimalScale = function (height, height2, width, width2) {

         if (height > height2) var scaleHeight = height2 / height;
         else var scaleHeight = height / height2;

         if (width > width2) var scaleWidth = width2 / width;
         else var scaleWidth = width / width2;

         return Math.min(scaleWidth, scaleHeight);
      }

      // Field select size on change appy this function
      $(document).on("change", ".printing-card select",
         function (e) {
            var $data_key = $(this).closest(".printing-card").first().attr("data-id");
            var image = saveImage[$data_key].image;

            var $this = $(this)
            saveImage.active = $data_key;
            saveImage[$data_key].canvas.clear();
            $($this).closest(".printing-card").removeClass("ready-to-print");

            saveImage[$data_key].status = 1;
            $($this).closest(".printing-card").find(".printing-loader").addClass("printing-show");
            Ahime.setCanvasBackgroundImage(image, $(this).val(), $data_key);
            setTimeout(function () {
               Ahime.fillUnselectedPart($data_key);
               saveImage[$data_key].canvas.renderAll()
               var img_url = Ahime.printResizeImage($data_key);
               $($this).closest(".printing-card").attr("data-url", img_url[1])
               $($this).closest(".printing-card").find("img").attr("src", img_url[0]);
               $($this).closest(".printing-card").find(".printing-loader").removeClass("printing-show");
               $($this).closest(".printing-card").find("img").show();
               $($this).closest(".printing-card").addClass("ready-to-print");
               saveImage[saveImage.active].url = img_url[1];
            }, 5000);
         })

      function onTouchObjectFunction() {
         if (typeof saveImage[saveImage.active] !== "undefined") {
            // Apply this function when object is scaling on canvas
            saveImage[saveImage.active].canvas.on("object:scaled", function (e) {
               var $key = $(".printing-editing-inner").attr("data-id");
               var slot = saveImage[$key].bound;
               var image = saveImage[$key].bgImage;
               var target = e.target;
               if (!target || target.type !== 'rect') {
                  return;
               }
               var sX = target.scaleX;
               var sY = target.scaleY;
               target.width *= sX;
               target.height *= sY;
               target.scaleX = 1;
               target.scaleY = 1;
               target.dirty = true;
               // if(slot.height >= saveImage[$key].canvas.width) Ahime.setHeight(slot, saveImage[$key].canvas.width);
               // if(slot.width >= saveImage[$key].canvas.width) Ahime.setWidth(slot, saveImage[$key].canvas.width - 20);

               if (image.height > image.width) {
                  var print_size = get_print_size("6x4", get_image_ratio(image));
                  if (slot.height >= saveImage[$key].canvas.height) Ahime.setHeight(slot, saveImage[$key].canvas.height - 20);
                  if (slot.width >= print_size[1]) Ahime.setWidth(slot, print_size[1]);
               }
               else {
                  var print_size = get_print_size("6x4", get_image_ratio(image));
                  if (slot.height >= print_size[1]) Ahime.setHeight(slot, print_size[1], get_image_ratio(image));
                  if (slot.width >= saveImage[$key].canvas.width) Ahime.setWidth(slot, saveImage[$key].canvas.width - 20);
               }
               saveImage[$key].canvas.renderAll();
               saveImage[$key].bound = slot;
               saveImage[$key].scaling = true;
               saveImage[$key].bound.centerH();
               saveImage[$key].bound.centerV();
               saveImage[$key].canvas.setActiveObject(e.target);
            })

            // Apply this function when object is moving on canvas
            saveImage[saveImage.active].canvas.on("object:moving", function (e) {
               var data_key = $(".printing-editing-inner").attr("data-id");
               var slot = saveImage[data_key].bound;
               if (e.target.left < 0) e.target.left = 2;
               if (e.target.left > (canvas.width - slot.width)) e.target.left = (canvas.width - slot.width) - 20;
               if (e.target.top < 0) e.target.top = 2;
               if (e.target.top > (canvas.height - slot.height)) e.target.top = (canvas.height - slot.height) - 20;

               saveImage[data_key].canvas.renderAll();
               saveImage[data_key].canvas.setActiveObject(e.target)
            })

            saveImage[saveImage.active].canvas.on("object:modified", function (e) {
               var data_key = $(".printing-editing-inner").attr("data-id");
               var slot = saveImage[data_key].bound;
               if (e.target.left < 0) e.target.left = 2;
               if (e.target.left > (canvas.width - slot.width)) e.target.left = (canvas.width - slot.width) - 20;
               if (e.target.top < 0) e.target.top = 2;
               if (e.target.top > (canvas.height - slot.height)) e.target.top = (canvas.height - slot.height) - 20;

               saveImage[data_key].canvas.renderAll();
               saveImage[data_key].canvas.setActiveObject(e.target)
            })
         }
      }

      // change rect position
      Ahime.flipPosition = function (data_key) {
         $("printing-edit-crop-preset").val("fit");
         $("printing-edit-crop-preset").trigger("change");
         var slot = saveImage[data_key].bound;
         var image = saveImage[data_key].bgImage;
         var def_orientation = $("#printing-edit-crop-orientation").val();
         saveImage[data_key].canvas.backgroundImage = null;
         saveImage[data_key].canvas.remove(part_top, part_bottom, part_left, part_right);
         saveImage[data_key].canvas.setBackgroundImage(image);

         if (image.height > image.width) var this_orientation = "portrait";
         else var this_orientation = "landscape";

         if (typeof saveImage[data_key].flip === "undefined")
            saveImage[data_key].flip = {
               height: slot.height,
               width: slot.width,
               orientation: this_orientation
            }

         if (typeof image !== "undefined") {

            if (saveImage[data_key].flip.orientation != def_orientation) {
               var h = saveImage[data_key].flip.width;
               var w = saveImage[data_key].flip.height;
            }
            else {
               var h = saveImage[data_key].flip.height;
               var w = saveImage[data_key].flip.width;
            }

            if (this_orientation == "portrait") {
               image.scaleToHeight(h);
               image.scaleToWidth(w);
            }
            else image.scaleToHeight(h);


            Ahime.setWidth(slot, w);
            Ahime.setHeight(slot, h);
            image.centerV();
            image.centerH();
            slot.centerV();
            slot.centerH();
            saveImage[data_key].canvas.renderAll();
            saveImage[data_key].canvas.calcOffset();

         }
      }

      // Generate uniq key
      Ahime.generate_uniq_id = function () {

         var $uniq_id = '';

         for (var compt = 0; compt <= 4; compt++) {
            $uniq_id += Math.floor((1 + Math.random()) * 0x10000)
               .toString(16)
               .substring(1);
         }

         return $uniq_id;
      }


      /**
       * Fill unselected canvas part
       */
      Ahime.fillUnselectedPart = function ($key) {
         var slot = saveImage[$key].canvas.getActiveObject();
         if (!saveImage[$key].scaling || slot == null)
            slot = saveImage[$key].bound;

         var fillLeft = {
            width: slot.left,
            height: canvas.width - slot.top,
            top: canvas.width - (canvas.width - slot.top),
            left: 0.2,
         }

         var fillRight = {
            width: canvas.width - (fillLeft.width + slot.width),
            height: fillLeft.height,
            top: fillLeft.top,
            left: (slot.width + slot.left) - .2,
         }

         var fillTop = {
            width: canvas.width,
            height: canvas.height - fillLeft.height,
            top: 0.2,
            left: 0,
         }

         var fillBottom = {
            width: slot.width,
            height: fillLeft.height - slot.height,
            top: fillLeft.top + slot.height,
            left: slot.left,
         }

         part_left.set(fillLeft);
         part_right.set(fillRight);
         part_top.set(fillTop);
         part_bottom.set(fillBottom);
         saveImage[$key].canvas.remove(part_top, part_bottom, part_left, part_right);
         saveImage[$key].canvas.add(part_left, part_right, part_top, part_bottom);
         saveImage[$key].canvas.calcOffset();
         saveImage[$key].canvas.renderAll();
      }

      //Add rect size
      $(document).on("click", ".printing-control-plus", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         slot1 = saveImage[data_key].bound;
         var ownHeight = saveImage[data_key].oldheight;
         var ownWidth = saveImage[data_key].oldwidth;
         var newWidth = 0;
         var newHeight = 0;

         newHeight = slot1.height + 1;
         newWidth = slot1.width + 1;

         if (newWidth > ownWidth && resizeRect.resizeBy == "height") newWidth = ownWidth;
         if (newHeight > ownHeight && resizeRect.resizeBy == "width") newHeight = ownHeight;

         slot1.width = newWidth;
         slot1.height = newHeight;
         saveImage[data_key].canvas.calcOffset();
         slot1.centerH();
         slot1.centerV();
         saveImage[data_key].canvas.renderAll();
      })

      //reduce rect size
      $(document).on("click", ".printing-control-minus", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         slot1 = saveImage[data_key].bound;

         var newWidth = 0;
         var newHeight = 0;

         newWidth = slot1.width - 1;
         newHeight = slot1.height - 1;

         if (newWidth < 10) newWidth = 10;
         if (newHeight < 10) newHeight = 10;

         slot1.width = newWidth;
         slot1.height = newHeight;
         saveImage[data_key].canvas.calcOffset();
         slot1.centerH();
         slot1.centerV();
         saveImage[data_key].canvas.renderAll();
      })

      $(document).on("click", ".printing-control-rotate", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         slot1 = saveImage[data_key].bound;
         var $orientation = $("#printing-edit-crop-orientation").val();

         if ($orientation == "portrait") $("#printing-edit-crop-orientation").val("landscape");
         else $("#printing-edit-crop-orientation").val("portrait");
         $("#printing-edit-crop-orientation").trigger("change");
         //Ahime.flipPosition(data_key);
      })

      $(document).on("change", "#printing-edit-crop-orientation", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         slot1 = saveImage[data_key].bound;
         saveImage[data_key].crop = $("#printing-edit-crop-preset").val();
         Ahime.flipPosition(data_key);

         // Ahime.scaleImageToSlot(canvas_elm.image, slot1, $("#printing-edit-crop-preset").val());
         saveImage[data_key].canvas.calcOffset();
         saveImage[data_key].canvas.renderAll();
      })

      $("#printing-edit-crop-preset").on("change", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         var slot = saveImage[data_key].bound;
         saveImage[data_key].crop = $(this).val();

         if (typeof saveImage[data_key].bgImage != undefined) {
            Ahime.scaleImageToSlot(saveImage[data_key].bgImage, slot, $(this).val(), data_key);
         }
         saveImage[data_key].canvas.calcOffset();
         saveImage[data_key].canvas.renderAll();
      })

      // Add to cart

      $(".printing-continue-anyway").click(function () {
         var image = {};
         $(".printing-card").each(function () {
            var data_id = $(this).attr("data-id");
            if (typeof data_id !== "undefined") {
               image[data_id] = {};
               image[data_id].name = saveImage[data_id].image.name;
               image[data_id].url = $(this).attr("data-url");
               image[data_id].original = saveImage[data_id].original;
               image[data_id].bound = $(this).find("select").val();
               image[data_id].qty = $(this).find("input").val();
               //image[data_id].fragmentUrl = Ahime.fragmentImageIntoPieces();
            }
         })

         var frm = new FormData();
         frm.append("action", "ahime_image_add_to_cart");
         frm.append("image_data", JSON.stringify(image));
         frm.append("product_id", $("#myCanvas").attr("data-id"));
         frm.append("quantity", 1);

         $.ajax({
            type: "POST",
            url: ahime.ajax_url,
            data: frm,
            processData: false,
            contentType: false,
         }).success(function (data) {
            if (data !== "echec") {
               document.location.href = data;
            }
         })
            .fail(function (xhr, status, error) {
               alert(error);
            });
      })

      // Apply modification
      $(document).on("click", ".printing-editing-edit", function () {
         var data_key = $(".printing-editing-inner").attr("data-id");
         var qty = $(".printing-editing-inner")
            .find("input")
            .val();
         saveImage[data_key].canvas.calcOffset();
         saveImage[data_key].canvas.renderAll();
         //saveImage[data_key].json = saveImage[data_key].canvas.toJSON();
         Ahime.fillUnselectedPart(data_key);
         $(".printing-card[data-id='" + data_key + "']").find(".printing-loader").addClass("printing-show");
         setTimeout(function () {
            Ahime.fillUnselectedPart(data_key);

            var img_url = Ahime.printResizeImage(data_key);

            $(".printing-card[data-id='" + data_key + "']").find("img")
               .attr("src", img_url[0]);

            $(".printing-card[data-id='" + data_key + "']")
               .attr("data-url", img_url[1]);
            $(".printing-card[data-id='" + data_key + "']").find("img").show();
            saveImage[data_key].url = img_url[1];
            saveImage[data_key].orientation = $("#printing-edit-crop-orientation").val();
            saveImage[data_key].crop = $("#printing-edit-crop-preset").val();
            $(".printing-card[data-id='" + data_key + "']").find(".printing-loader").removeClass("printing-show");
            $(".printing-card[data-id='" + data_key + "']").find("input").val(qty)
         }, 2000);
      })

      // Dupplicate card
      $(document).on("click", ".printing-card .printing-duplicate-card", function () {
         var $key = $(this).closest(".printing-card").attr("data-id"),
            $size = $(this).closest(".printing-card").find("select.printing-form-field").val(),
            $qty = $(this).closest(".printing-card").find("input.printing-form-field").val(),
            $clone = $("#clone-printing-card").clone();
         $clone.find("select.printing-form-field").val($size);
         $clone.find("input.printing-form-field").val($qty);
         var card = $(this).closest(".printing-card");
         var new_key = Ahime.generate_uniq_id();

         while ($.inArray(new_key, canvas_elm.uniq_id) != -1) {
            var new_key = Ahime.generate_uniq_id();
         }
         canvas_elm.uniq_id.push(new_key);
         $clone.attr("data-id", new_key);

         var canvas_clone = $(".printing-clone-canvas").clone();
         canvas_clone.attr("id", "myCanvas_" + new_key);
         canvas_clone.removeClass("printing-clone-canvas");
         canvas_clone.addClass("is-printing-clone-canvas");
         canvas_clone.find("canvas").attr("id", "canvas_" + new_key);
         $("#myCanvas").hide();
         canvas_clone.insertAfter("#myCanvas");

         saveImage[new_key] = {};
         var canvas_parent = saveImage[$key].canvas;
         // var file = saveImage[$key].image;
         // var bg_canvas = canvas_parent.backgroundImage;
         var new_canvas = new fabric.Canvas("canvas_" + new_key, canvasProperty);
         saveImage[new_key].bound = new fabric.Rect(rectProperty);
         saveImage[new_key].canvas = new_canvas;
         saveImage[new_key].image = saveImage[$key].image;
         saveImage[$key].canvas.remove(part_top, part_bottom, part_left, part_right);
         var $json = canvas_parent.toJSON();

         new_canvas.loadFromJSON($json, function () {
            new_canvas.renderAll();
         }, function (o, object) {
            //console.log(o,object)
            object.set({
               strokeWidth: 3,
               selectable: true,
               boundingBox: true,
               // lockMovementY: true,
               centeredScaling: true,
               lockRotation: false,
               hasRotatingPoint: true,
               cornerColor: "red",
               transparentCorners: false,
            })
            object.setControlsVisibility({
               mtr: false,
               ml: false,
               mr: false,
               mt: false,
               mb: false,
               // 'tr':true,
               // 'bl':false,
               // 'br':false,
            });

            saveImage[new_key].bound = object;
            saveImage[new_key].canvas.setActiveObject(object);
         })

         //Ahime.setCanvasBackgroundImage(file, "default", new_key);

         $clone.insertAfter(card);
         $clone.show();
         saveImage[new_key].canvas.renderAll();
         saveImage[new_key].canvas.calcOffset();
         Ahime.fillUnselectedPart(new_key);
         $(".printing-card[data-id='" + new_key + "']").find(".printing-loader").addClass("printing-show");
         setTimeout(function () {
            saveImage[new_key].bgImage = saveImage[$key].bgImage;
            Ahime.fillUnselectedPart(new_key);
            saveImage[new_key].canvas.renderAll();
            saveImage[new_key].canvas.calcOffset();
            var img_c = $(".printing-card[data-id='" + $key + "']").find("img")
               .attr("src");
            var $url = $(".printing-card[data-id='" + $key + "']").attr("data-url");
            $(".printing-card[data-id='" + new_key + "']").addClass("ready-to-print");
            $(".printing-card[data-id='" + new_key + "']").find("img")
               .attr("src", img_c);
            $(".printing-card[data-id='" + new_key + "']")
               .attr("data-url", $url);

            $(".printing-card[data-id='" + new_key + "']").find("img").show();
            $(".printing-card[data-id='" + new_key + "']").find(".printing-loader").removeClass("printing-show");
            saveImage[new_key].url = $url;
         }, 2000);
      })

      $(document).on("click", ".printing-card .printing-delete-card", function () {
         $(this).closest(".printing-card").remove();
      })

      // Apply modification for all card
      $(document).on("click", ".printing-update-edit-all", function () {
         var research = $("#printing-edit-all-1").val();
         var apply_in = $("#printing-edit-all-2").val();
         var qty = parseInt($("#printing-edit-all-3").val());

         $(".printing-card").each(function () {
            if ($(this).find("select").val() == research && typeof $(this).attr("data-id") !== "undefined") {
               $(this).find(".printing-loader").addClass("printing-show");
               $(this).find("select").val(apply_in);
               $(this).find("select").trigger("change");
               if (!isNaN(qty) && qty > 0) {
                  $(this).find("input").val(qty)
               }
            }
         })
         $(".printing-icon-edit-all-cross").click();
      })

      $(document).on("mouseover mousedown click", function () {
         var elm = $(".printing-card").size();
         var disabl = 0;
         var qty = 0;
         $(".printing-badge-image").text(elm - 1);
         $(".printing-card").each(function () {
            qty += parseInt($(this).find("input").val());
            if (!$(this).hasClass("ready-to-print")) disabl += 1;
         })
         $(".printing-badge-print").text(qty);
         $("#printing-file").attr("accept", "image/*");

         if (qty <= 0 || disabl > 1) $(".printing-continue").addClass("printing-disable")
         else $(".printing-continue").removeClass("printing-disable")
      })

      Ahime.fragmentImageIntoPieces = function ($data) {
         var size = $data.length;
         var split_number = parseInt(size / 130);
         var rest = size - (split_number * 130);
         split_number += rest;
         var data = $data.match(new RegExp('[\\s\\S]{1,' + +split_number + '}', 'g'));
         return data;
      }
   })
   return Ahime;
})(jQuery, AHIME);