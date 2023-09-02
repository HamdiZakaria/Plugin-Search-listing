jQuery(function($){

	$(document).ready(function() {

		Cookies.remove('listingcategory', { path: ''}); 
		Cookies.remove('departement', { path: ''}); 
		Cookies.remove('region', { path: ''}); 
		Cookies.remove('villes', { path: ''}); 

	/*** filter home  *** */

		$.ajax({
		 url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		 type: 'POST',
		 // this is the function in your functions.php that will be triggered
		 data:{ action: 'getAllcategory'},
		 success: function(data){
			$.each(data.slug,function( key, value){
				var objtID = value;
			if (($('body').hasClass('home'))) {
				$("#"+objtID).click( function(e) {
					e.preventDefault();
					valueSelected = this.value;
					if (valueSelected != ''){
						Cookies.set(objtID, valueSelected);
					}else{
						Cookies.remove(objtID);
					}
					//$('#region option').removeAttr('selected');
					//Some event will trigger the ajax call, you can push whatever data to the server, simply passing it to the "data" object in ajax call
					$.ajax({
					url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
					type: 'POST',
					// this is the function in your functions.php that will be triggered
					data:{ action: 'getListeHome' ,'idCat':objtID,'valueSelected' : valueSelected,"excerpt":data.post_excerpt},
					success: function(data){
					//Do something with the result from serverµ
					if(objtID =="region"){
						reg_selected = $("#region input").val();
						if (reg_selected !=''){
						$("select").prop('disabled', true);
						$(".lp-search-bar").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
						setTimeout(function() {
							$("select").prop('disabled', false);
							$(".load-container").hide();
							$(".departement").addClass('active');
							$(".villes").addClass('active');


						}, 1000);
						setTimeout(function() {
						$('#departement').html("");
						$('#departement').html('<option value="">Sélectionnez Départements</option>');
						$.each(data, function( key, value ) {
							$('#departement').append('<option value="'+value.slug+'" name="'+value.slug+'" data-id="'+value.id+'">'+value.name+'</option>').delay(5000);
						})
					}, 1000);

					}else{
						$("select").prop('disabled', true);
						$(".lp-search-bar").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
						setTimeout(function() {
							$("select").prop('disabled', false);
							$(".load-container").hide();
							$(".form-group").removeClass('active');
						}, 1000);
					}
				}
					if(objtID =="departement"){
						$("select").prop('disabled', true);
						$(".lp-search-bar").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");

						setTimeout(function() {

							$("select").prop('disabled', false);
							$(".load-container").hide();
							$(".departement").addClass('active');
							$(".villes").addClass('active');

						}, 1000);
						setTimeout(function() {

						$('#villes').html("");
						$('#villes').html('<option value="">Sélectionnez Villes</option>');
						$.each(data, function( key, value ) {
							$('#villes').append('<option value="'+value.slug+'" name="'+value.slug+'" data-id="'+value.id+'">'+value.name+'</option>');
						})
					}, 1000);

					}
						// insert data
					}
				});
				});
			}else{
				$("#"+objtID).click( function(e) {
					if(objtID =="region"){
						$regionli = $('input').val(''); 
						alert($regionli);
						$('select#villes').val('');
						Cookies.remove('villes');
						Cookies.remove('departement');

					}
					Selctedliste = $("select").serializeArray();
					valueSelected = this.value;
					if (valueSelected != ''){
						Cookies.set(objtID, valueSelected);
					}else{
						Cookies.remove(objtID);
					}

					$("select").prop('disabled', true);
					$(".G_titre").after("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
					$(".s_titre").after("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
					$("body").css("background-color", "rgba(0, 0, 0, 0.5)");
					$(".post-with-map-container").css("background-color", "rgba(0, 0, 0, 0.5)");

					setTimeout(function() {

						$("select").prop('disabled', false);
						$(".load-container").hide();
						$(".departement").addClass('active');
						$(".villes").addClass('active');
						$(".archive").css("opacity", "1");


					}, 100000);

					$.ajax({
						url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
						type: 'POST',
						// this is the function in your functions.php that will be triggered
						data:{ action: 'getListe' ,'idCat':objtID,'valueSelected' : valueSelected,"excerpt":data.post_excerpt,'Selctedliste':Selctedliste},
						success: function(data){
						//Do something with the result from serverµ
						if(objtID =="region"){
							$('#departement').attr("value", "");  
							$('#villes').attr("value", "");  


							if (!Cookies.get('region')) {
							$('#departement').html("");


							$('#departement').html('<option value="">Sélectionnez départements</option>');
							$.each(data, function( key, value ) {
								$(".departement").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
								setTimeout(function() {
								  $(".search").addClass("hide-loading");
								  // For failed icon just replace ".done" with ".failed"
								  $(".done").addClass("finish");
								}, 100000);
								setTimeout(function() {
								  $(".search").removeClass("loading");
								  $(".search").removeClass("hide-loading");
								  $(".done").removeClass("finish");
								  $(".failed").removeClass("finish");
								}, 105000);
								$('#departement').append('<option value="'+value.slug+'" name="'+value.slug+'" data-id="'+value.id+'">'+value.name+'</option>');
							})
						}else{
							$("select#villes").prop('disabled', true);
							Cookies.remove('departement');
							Cookies.remove('villes');
							$("select#villes").prop('disabled', true);
						}
						}
						if(objtID =="departement"){

							$('#villes').html("");
							$('#villes').html('<option value="">Sélectionnez villes</option>');
							$.each(data, function( key, value ) {
								$('#villes').append('<option value="'+value.slug+'" name="'+value.slug+'" data-id="'+value.id+'">'+value.name+'</option>');
							})
						}
						$.each(data, function( key, value ) {
							window.location = value.url
						})


						},error: function (error) {
							alert('error; ' + eval(error));
						}
					});


				});
			}

				
			});

		 },
		 error: function (error) {
			alert('error; ' + eval(error));
		}

	   })

		$(".searchandfilter").submit(function() {

			Selctedliste = $("select").serializeArray();
			$(".search").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
			setTimeout(function() {
			  $(".search").addClass("hide-loading");
			  // For failed icon just replace ".done" with ".failed"
			  $(".done").addClass("finish");
			}, 100000);
			setTimeout(function() {
			  $(".search").removeClass("loading");
			  $(".search").removeClass("hide-loading");
			  $(".done").removeClass("finish");
			  $(".failed").removeClass("finish");
			}, 105000);
			$.ajax({
				url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
				type: 'POST',
				// this is the function in your functions.php that will be triggered
				data:{ action: 'getListe' ,'Selctedliste':Selctedliste,id_home:'_home'},
				success: function(data){
				//Do something with the result from serverµ
				$.each(data, function( key, value ) {

					window.location = value.url
				})


				},
				error: function (error) {
				   alert('error; ' + eval(error));
			   }
	   
			});


	});


	if( jQuery('.lp-listing-slider').length != 0 )

    {

        var totalSlides     =   jQuery('.lp-listing-slider').attr('data-totalSlides'),

            slidesToShow    =   3;

        if( totalSlides == 1 )

        {

            slidesToShow    =   1;

        }

        if( totalSlides ==  2 )

        {

            slidesToShow    =   2;

        }

        jQuery('.lp-listing-slider').slick({

            infinite: true,

            slidesToShow: slidesToShow,

            slidesToScroll: 1,

            prevArrow: "<i class=\"fa fa-angle-right arrow-left\" aria-hidden=\"true\"></i>",

            nextArrow: "<i class=\"fa fa-angle-left arrow-right\" aria-hidden=\"true\"></i>",
            responsive: [
                {
                    breakpoint: 480,
                    settings: {
                        arrows: true,
                        centerMode: false,
                        centerPadding: '0px',
                        slidesToShow: 2
                    }
                }
            ]

        });



        jQuery('.lp-listing-slider').show();

    }

	/* single maps **/

	

$siteURL = jQuery('#single-detail').data("url");
$pinicon = jQuery('#singlepostmap').data('pinicon');
if($pinicon===""){
    $pinicon = $siteURL+"/wp-content/plugins/search-listing/img/pins/pin.png";
}
    $lat = jQuery('#singlepostmap').data("lat");
    $lan = jQuery('#singlepostmap').data("lan");
    "use strict";
    $mtoken = jQuery('#single-detail').data("mtoken");

    $mtype = jQuery('#single-detail').data("mtype");	
        /* mapbox */
        L.HtmlIcon = L.Icon.extend({
            options: {
                /*
                html: (String) (required)
                iconAnchor: (Point)
                popupAnchor: (Point)
                */
            },
            initialize: function(options) {
                L.Util.setOptions(this, options);
            },
            createIcon: function() {
                var div = document.createElement('div');
                div.innerHTML = this.options.html;
                if (div.classList)
                    div.classList.add('leaflet-marker-icon');
                else
                    div.className += ' ' + 'leaflet-marker-icon';
                return div;
            },
            createShadow: function() {
                return null;
            }
        });

		var map = new L.Map('singlepostmap', {
			zoom: 5,
			minZoom: 3, 
			maxZoom: 6,
		}).setView(new L.LatLng($lat, $lan), 5);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var markers = new L.MarkerClusterGroup();
        var markerLocation = new L.LatLng($lat, $lan);
        var CustomHtmlIcon = L.HtmlIcon.extend({
            options : {
                html : "<div class='lpmap-icon-shape pin '><div class='lpmap-icon-contianer'><img src='"+$pinicon+"'  /></div></div>",
            }
        });
        var customHtmlIcon = new CustomHtmlIcon();
        var marker = new L.Marker(markerLocation, {icon: customHtmlIcon}).bindPopup('').addTo(map);
        markers.addLayer(marker);
        map.fitBounds(markers.getBounds());
        map.scrollWheelZoom.disable();
        map.invalidateSize();
	   
	   
	});


/*
  Dropdown with Multiple checkbox select with jQuery - May 27, 2013
  (c) 2013 @ElmahdiMahmoud
  license: https://www.opensource.org/licenses/mit-license.php
*/

$(".dropdown dt a").on('click', function() {
	$(".dropdown dd ul").slideToggle('fast');
  });
  
  $(".dropdown dd ul li a").on('click', function() {
	$(".dropdown dd ul").hide();
  });
  
 
  
  $(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
  });
  
$('input[type=radio][name="region"]').change(function () {
	
	$('ul.listChild').html('');
	$('ul.listChild').remove();

	$('.mutliSelect').find('li').removeClass("activeChild");


	var ret = $(".hida");
	$('.dropdown dt a').append(ret);
	var title = $(this).closest('.mutliSelect').find('input[type="radio"]').val(), title = $(this).val() + ",";

	var titleChild = $(this).closest('.activeChild').find('input[type="radio"]').val(),
    titleChild = $(this).val() + ",";
	  $('.multiSel').html('');

  
	if ($(this).is(':checked')) {

		valueSelected = this.value;

		$(this).closest('li').addClass('activeChild');
		if (valueSelected != ''){
			Cookies.set('region', valueSelected);
		}else{
			Cookies.remove('region');
		}

					//$('#region option').removeAttr('selected');
					//Some event will trigger the ajax call, you can push whatever data to the server, simply passing it to the "data" object in ajax call
					$.ajax({
						url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
						type: 'POST',
						// this is the function in your functions.php that will be triggered
						data:{ action: 'getListeHome' ,'idCat':'region','valueSelected' : valueSelected},
						success: function(data){
						//Do something with the result from serverµ
							$(".region").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
							setTimeout(function() {
								$(".load-container").hide();
	
							}, 100);
							setTimeout(function() {
								$('.activeChild').append('<ul class="listChild"></ul>');
							$.each(data, function(key, value) {
								console.log($(this).closest('input').val());
								$('.activeChild ul').append('<li class="'+value.id+'" data-id="'+value.id+'"><input type="checkbox" value="'+value.slug+'" name="departement" value="'+value.slug+'">'+value.name+'</li>');
							})
						}, 100);
	
					}
				});

	  var html = '<span title="' + title + '">' + title + '</span>';

	  var ValChild = '<span title="' + titleChild + '">' + titleChild + '</span>';


	  $('.multiSel').append(html);

	} else {
		$('.mutliSelect').find('li').removeClass("activeChild");
		$('ul.listChild').remove();
	  $('span[title="' + title + '"]').html('');
	  var ret = $(".hida");
	  $('.dropdown dt a').append(ret);
  
	}
});





$(document).ready(function() {

/** departement listes */


$('input[type="checkbox"]').on('click', function() {
	alert('ttt');
	/*var titleChild = $(this).closest('.activeChild').find('input[type="radio"]').val(),
    titleChild = $(this).val() + ",";
	  $('.multiSel').html('');

  
	if ($(this).is(':checked')) {
		alert('ttt');

		valueSelected = this.value;

		$(this).closest('li').addClass('activeChildRegion');
		if (valueSelected != ''){
			Cookies.set('region', valueSelected);
		}else{
			Cookies.remove('region');
		}

					//$('#region option').removeAttr('selected');
					//Some event will trigger the ajax call, you can push whatever data to the server, simply passing it to the "data" object in ajax call
					$.ajax({
						url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
						type: 'POST',
						// this is the function in your functions.php that will be triggered
						data:{ action: 'getListeHome' ,'idCat':'region','valueSelected' : valueSelected},
						success: function(data){
						//Do something with the result from serverµ
							$(".region").append("<div class='load-container'><div class='load'><span></span><span></span><span></span><span></span></div></div>");
							setTimeout(function() {
								$(".load-container").hide();
	
							}, 1000);
							setTimeout(function() {
								$('.activeChild').append('<ul class="listChild"></ul>');
							$.each(data, function(key, value) {
								console.log($(this).closest('input').val());
								$('.activeChild ul').append('<li data-id="'+value.id+'"><input type="radio" value="'+value.slug+'" name="departement">'+value.name+'</li>');
							})
						}, 1000);
	
					}
				});


	  var ValChild = '<span title="' + titleChild + '">' + titleChild + '</span>';
	  $('.multiSel').append(ValChild);


	} else {
		$(this).closest('li').removeClass('activeChild');
		$('ul.listChild').remove();
	  var ret = $(".hida");
	  $('.dropdown dt a').append(ret);
  
	}*/
});
});
});
