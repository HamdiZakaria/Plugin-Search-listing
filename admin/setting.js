jQuery(function($){

    jQuery('#example-select').select2();
	jQuery('select').select2({
		theme: "classic"
	});
	 $('.listes_tax input[type="checkbox"]').click(function(){

		if($(this).prop("checked") == true){
			$(this).val(1);
			valcheked = $(this).serializeArray();
		}
		else if($(this).prop("checked") == false){
			$(this).val(0);
			valcheked = $(this).attr('name');
		}
		$.ajax({
			url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
			type: 'POST',
			// this is the function in your functions.php that will be triggered
			data:{ action: 'wp_update' ,data:valcheked},
			success: function( data ){
				console.log(data);
			  //Do something with the result from serverµ
			   // insert data
			}
		  });

	});

	$('.listes_ctp input[type="checkbox"]').click(function(){

		if($(this).prop("checked") == true){
			$(this).val(1);
			valcheked = $(this).serializeArray();
			alert($(this).data("data-value"));
		}
		else if($(this).prop("checked") == false){
			$(this).val(0);
			valcheked = $(this).attr('name');
		}

		$.ajax({
			url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
			type: 'POST',
			// this is the function in your functions.php that will be triggered
			data:{ action: 'wp_update' ,reps:valcheked,status : $(this).data("data-value")},
			success: function(data){
				console.log(data);
			}
		  });
	});

	$('.listes_ctp input[type="radio"]').click(function(){

		if($(this).prop("checked") == true){
			valcheked = $(this).serializeArray();
			$(this).attr("data-value", 1);

		}else{
			
			valcheked = $(this).attr('name');
			$(this).attr("data-value", 0);
		}

		$.ajax({
			url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
			type: 'POST',
			// this is the function in your functions.php that will be triggered
			data:{ action: 'wp_update' ,reps:valcheked,status : $(this).attr("data-value")},
			success: function(data){
				console.log(data);
			}
		  });

	});

	function getListOrder(tObj) {
		var list = $(tObj).sortable("toArray");
		return list.toString();
	  }
	var list = $('#mySortable'),
	updatePosition = function() {
	  var listOrder = getListOrder("#mySortable");	  
	  $.ajax({
		url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		type: 'POST',
		// this is the function in your functions.php that will be triggered
		data:{ action: 'wp_update' ,data:listOrder},
		success: function(data){
			console.log(data);
		  //Do something with the result from serverµ
		   // insert data
		}
	  });
	};

list.sortable({
  placeholder: "ui-state-highlight",
  items: ".item",
  update: updatePosition,
});

$('table#tblAttachAttributes').find('div.sortable').sortable({
    connectWith: 'div.sortable'
});

$( "#Search_Listing_Setting_Form" ).submit(function() {
	$.ajax({
		url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		type: 'POST',
		// this is the function in your functions.php that will be triggered
		data:{ action: 'wp_create_listing_taxonomies' ,tax:$('.listes_tax input[type="checkbox"]').serializeArray(),cpt:  $('.listes_ctp input[type="checkbox"]').serializeArray()},
		success: function( data ){
			console.log(data);
		  //Do something with the result from serverµ
		   // insert data
		}
	  });


	  //Auto Insert Terms regions

	  $.ajax({
		url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		type: 'POST',
		// this is the function in your functions.php that will be triggered
		data:{ action: 'auto_insert'},
		success: function( data ){
			console.log(data);
		  //Do something with the result from serverµ
		   // insert data
		}
	  });
  });

 /*
     * Select/Upload image(s) event
     */
 $('body').on('click', '.misha_upload_image_button', function(e){
	e.preventDefault();
		var button = $(this),
			custom_uploader = wp.media({
		title: 'Insert image',
		library : {
			// uncomment the next line if you want to attach image to the current post
			// uploadedTo : wp.media.view.settings.post.id, 
			type : 'image'
		},
		button: {
			text: 'Use this image' // button label text
		},
		multiple: false // for multiple image selection set to true
	}).on('select', function() { // it also has "open" and "close" events 
		var attachment = custom_uploader.state().get('selection').first().toJSON();
		$(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
		/* if you sen multiple to true, here is some code for getting the image IDs
		var attachments = frame.state().get('selection'),
			attachment_ids = new Array(),
			i = 0;
		attachments.each(function(attachment) {
			attachment_ids[i] = attachment['id'];
			console.log( attachment );
			i++;
		});
		*/
	})
	.open();
});

/*
 * Remove image event
 */
$('body').on('click', '.misha_remove_image_button', function(){
	$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
	return false;
});

  $( "#Add_Listing_Post" ).submit(function() {
	Pnameval=$('input[name="Pname"]').val();
	$.ajax({
		url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		type: 'POST',
		// this is the function in your functions.php that will be triggered
		data:{ action: 'create_posttype' ,fslug:$('input[name="fslug"]').serializeArray(),Sname:$('input[name="Sname"]').serializeArray()},
		success: function(data){
			console.log(data);
		  //Do something with the result from serverµ
		   //insert data
		}
	  });
  });

  $('body').on('click', '.wc_multi_upload_image_button', function(e) {
	e.preventDefault();

	var button = $(this),
	custom_uploader = wp.media({
		title: 'Insert image',
		button: { text: 'Use this image' },
		multiple: true 
	}).on('select', function() {
		var attech_ids = '';
		attachments
		var attachments = custom_uploader.state().get('selection'),
		attachment_ids = new Array(),
		i = 0;
		attachments.each(function(attachment) {
			attachment_ids[i] = attachment['id'];
			attech_ids += ',' + attachment['id'];
			if (attachment.attributes.type == 'image') {
				$(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.url + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
			} else {
				$(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.icon + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
			}

			i++;
		});

		var ids = $(button).siblings('.attechments-ids').attr('value');
		if (ids) {
			var ids = ids + attech_ids;
			$(button).siblings('.attechments-ids').attr('value', ids);
		} else {
			$(button).siblings('.attechments-ids').attr('value', attachment_ids);
		}
		$(button).siblings('.wc_multi_remove_image_button').show();
	})
	.open();
});

$('body').on('click', '.wc_multi_remove_image_button', function() {
	$(this).hide().prev().val('').prev().addClass('button').html('Add Media');
	$(this).parent().find('ul').empty();
	return false;
});
jQuery(document).on('click', '.multi-upload-medias ul li i.delete-img', function() {
	var ids = [];
	var this_c = jQuery(this);
	jQuery(this).parent().remove();
	jQuery('.multi-upload-medias ul li').each(function() {
		ids.push(jQuery(this).attr('data-attechment-id'));
	});
	jQuery('.multi-upload-medias').find('input[type="hidden"]').attr('value', ids);
});

});

jQuery(document).ready(function() {
    jQuery('.example-select').select2();

	jQuery(".modal_open").on("click",function(){
		jQuery(".modal_box").addClass('active');
		return false;
	  });
	  jQuery(".close").on("click",function(){
		jQuery(".modal_box").removeClass('active');
		return false;
	  });
	
});

