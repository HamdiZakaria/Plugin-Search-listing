/* Main.js contains all main JS  */
/*  Author : CrdioStudio Dev team */

/*moin 31-03-017 strt*/
jQuery.noConflict();

    jQuery(window).on('load', function () {

        /*
         * L.TileLayer is used for standard xyz-numbered tile layers.
         */


        L.Google = L.Class.extend({
            includes: L.Mixin.Events,

            options: {
                zoom: 5,
                minZoom: 0,
                maxZoom: 8,
                tileSize: 256,
                subdomains: 'abc',
                errorTileUrl: '',
                attribution: '',
                opacity: 1,
                continuousWorld: false,
                noWrap: false,
                mapOptions: {
                    backgroundColor: '#dddddd'
                }
            },

            // Possible types: SATELLITE, ROADMAP, HYBRID, TERRAIN
            initialize: function (options) {
                L.Util.setOptions(this, options);

                this._ready = google.maps.Map != undefined;
                if (!this._ready)
                    L.Google.asyncWait.push(this);

                this._type = type || 'SATELLITE';
            },

            onAdd: function (map, insertAtTheBottom) {
                this._map = map;
                this._insertAtTheBottom = insertAtTheBottom;

                // create a container div for tiles
                this._initContainer();
                this._initMapObject();

                // set up events
                map.on('viewreset', this._resetCallback, this);

                this._limitedUpdate = L.Util.limitExecByInterval(this._update, 50, this);
                map.on('move', this._update, this);


                //20px instead of 1em to avoid a slight overlap with google's attribution
                map._controlCorners['bottomright'].style.marginBottom = "20px";

                this._reset();
                this._update();
            },

            onRemove: function (map) {
                this._map._container.removeChild(this._container);
                //this._container = null;

                this._map.off('viewreset', this._resetCallback, this);

                this._map.off('move', this._update, this);

                this._map.off('zoomanim', this._handleZoomAnim, this);

                map._controlCorners['bottomright'].style.marginBottom = "0em";
                //this._map.off('moveend', this._update, this);
            },

            getAttribution: function () {
                return this.options.attribution;
            },

            setOpacity: function (opacity) {
                this.options.opacity = opacity;
                if (opacity < 1) {
                    L.DomUtil.setOpacity(this._container, opacity);
                }
            },

            setElementSize: function (e, size) {
                e.style.width = size.x + "px";
                e.style.height = size.y + "px";
            },

            _initContainer: function () {
                var tilePane = this._map._container,
                    first = tilePane.firstChild;

                if (!this._container) {
                    this._container = L.DomUtil.create('div', 'leaflet-google-layer leaflet-top leaflet-left');
                    this._container.id = "_GMapContainer_" + L.Util.stamp(this);
                    this._container.style.zIndex = "auto";
                }

                tilePane.insertBefore(this._container, first);

                this.setOpacity(this.options.opacity);
                this.setElementSize(this._container, this._map.getSize());
            },

            _initMapObject: function () {
                if (!this._ready)
                    return;
                this._google_center = new google.maps.LatLng(0, 0);
                var map = new google.maps.Map(this._container, {
                    center: this._google_center,
                    zoom: 0,
                    tilte: 0,
                    mapTypeId: google.maps.MapTypeId[this._type],
                    disableDefaultUI: false,
                    keyboardShortcuts: false,
                    draggable: false,
                    disableDoubleClickZoom: true,
                    scrollwheel: false,
                    streetViewControl: true,
                    styles: this.options.mapOptions.styles,
                    backgroundColor: this.options.mapOptions.backgroundColor
                });

                var _this = this;
                this._reposition = google.maps.event.addListenerOnce(map, "center_changed",
                    function () {
                        _this.onReposition();
                    });
                this._google = map;

                google.maps.event.addListenerOnce(map, "idle",
                    function () {
                        _this._checkZoomLevels();
                    });
            },

           _checkZoomLevels: function () {
                //setting the zoom level on the Google map may result in a different zoom level than the one requested
                //(it won't go beyond the level for which they have data).
                // verify and make sure the zoom levels on both Leaflet and Google maps are consistent
                if (this._google.getZoom() !== this._map.getZoom()) {
                    //zoom levels are out of sync. Set the leaflet zoom level to match the google one
                    this._map.setZoom(this._google.getZoom());
                }
            },

            _resetCallback: function (e) {
                this._reset(e.hard);
            },

            _reset: function (clearOldContainer) {
                this._initContainer();
            },

            _update: function (e) {
                if (!this._google)
                    return;
                this._resize();

                var center = e && e.latlng ? e.latlng : this._map.getCenter();
                var _center = new google.maps.LatLng(center.lat, center.lng);

                this._google.setCenter(_center);
                this._google.setZoom(this._map.getZoom());

                this._checkZoomLevels();
                //this._google.fitBounds(google_bounds);
            },

            _resize: function () {
                var size = this._map.getSize();
                if (this._container.style.width == size.x &&
                    this._container.style.height == size.y)
                    return;
                this.setElementSize(this._container, size);
                this.onReposition();
            },

            _handleZoomAnim: function (e) {
                var center = e.center;
                var _center = new google.maps.LatLng(center.lat, center.lng);

                this._google.setCenter(_center);
                this._google.setZoom(e.zoom);
            },

            onReposition: function () {
                if (!this._google)
                    return;
                google.maps.event.trigger(this._google, "resize");
            }
        });

        L.Google.asyncWait = [];
        L.Google.asyncInitialize = function () {
            var i;
            for (i = 0; i < L.Google.asyncWait.length; i++) {
                var o = L.Google.asyncWait[i];
                o._ready = true;
                if (o._container) {
                    o._initMapObject();
                    o._update();
                }
            }
            L.Google.asyncWait = [];
        };



        L.HtmlIcon = L.Icon.extend({
            options: {
                /*
                 html: (String) (required)
                 iconAnchor: (Point)
                 popupAnchor: (Point)
                 */
            },

            initialize: function (options) {
                L.Util.setOptions(this, options);
            },

            createIcon: function () {
                var div = document.createElement('div');
                div.innerHTML = this.options.html;
                if (div.classList)
                    div.classList.add('leaflet-marker-icon');
                else
                    div.className += ' ' + 'leaflet-marker-icon';
                return div;
            },

            createShadow: function () {
                return null;
            }
        });

    });



function lpshowsidemap() {
    if (jQuery('#map').is('.mapSidebar')) {
        if (jQuery('.v2-map-load').length == 1) {
            jQuery("<div class='v2mapwrap'></div>").appendTo(jQuery(".v2-map-load"));
            jQuery(".v2-map-load .v2mapwrap").trigger('click');
        }
        jQuery("<div class='sidemarpInside'></div>").appendTo(jQuery(".sidemap-fixed"));
        jQuery(".sidemap-fixed .sidemarpInside").trigger('click');
    }
}

if(window.attachEvent) {
    window.attachEvent('onload', lpshowsidemap);
} else if(window.addEventListener) {
    window.addEventListener('load', lpshowsidemap, false);
} else {
    document.addEventListener('load', lpshowsidemap, false);
}

jQuery(document).on('click', '.footer-btn-right.map-view-btn, .v2-map-load .v2mapwrap, .listing-app-view-bar .right-icons a, .sidemap-fixed .sidemarpInside', function (e) {
    if (jQuery('#map').is('.mapSidebar')) {

        var defmaplat = jQuery('body').data('defaultmaplat');
        var defmaplong = jQuery('body').data('defaultmaplot');
        jQuery('.map-pop').empty();
        jQuery('.map-pop').html('<div class="mapSidebar" id="map"></div>');
        var map       = null;
        $mtoken       = jQuery('#page').data("mtoken");
        $mtype        = jQuery('#page').data("mtype");
        $mapboxDesign = jQuery('#page').data("mstyle");

            var map = new L.Map('map', {
                zoom: 5,
                minZoom: 3, 
                maxZoom: 8,
            }).setView(new L.LatLng(0, 0), 5);
            if ($mtype == 'google') {
                L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 5,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    noWrap: true,
                });
                var googleLayer = new L.Google('ROADMAP');
                map.addLayer(googleLayer);
                
            } else {
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',5).addTo(map);
            }
            
            var markers = new L.MarkerClusterGroup();
            var resmarkers = initializeMap(markers);
            if (typeof resmarkers === 'undefined') {
            } else {
                map.fitBounds(markers.getBounds(), {padding: [50, 50]});
                map.scrollWheelZoom.enable();
                map.invalidateSize();
                map.dragging.enable();
                jQuery(document).on('click', '.open-map-view', function () {
                    L.Util.requestAnimFrame(map.invalidateSize, map, !1, map._container);
                });
            }



        function initializeMap(markers) {

            if (jQuery('.lp-grid-box-contianer').length != 0) {
                markers.clearLayers();
                jQuery(".lp-grid-box-contianer").each(function (i) {

                    var LPtitle = jQuery(this).data("title");
                    var LPposturl = jQuery(this).data("posturl");
                    var LPlattitue = jQuery(this).data("lattitue");
                    var LPlongitute = jQuery(this).data("longitute");
                    var LPpostid = jQuery(this).data("postid");
                    var LPiconSrc = '';

                    if (jQuery('.v2-map-load').length == 1) {
                        if (jQuery('.v2-map-load').hasClass('v2_map_load_old')) {
                            var LPaddress = jQuery(this).find('.gaddress').text();
                            var LPimageSrc = jQuery(this).find('.lp-grid-box-thumb').find('img').attr('src');
                            if (typeof jQuery("body").data('deficon') !== 'undefined') {
                                // your code here
                                LPiconSrc = jQuery("body").data('deficon');
                            } else {
                                LPiconSrc = jQuery(this).find('.cat-icon').find('img').attr('src');
                            }
                        } else {
                            var LPaddress = jQuery(this).find('.lp-listing-location').find('a').text();
                            var LPimageSrc = jQuery(this).find('.lp-listing-top-thumb').find('img').attr('src');

                            if (typeof jQuery("body").data('deficon') !== 'undefined') {
                                // your code here
                                LPiconSrc = jQuery("body").data('deficon');
                            } else {
                                LPiconSrc = jQuery(this).find('.cat-icon').find('img').attr('src');
                            }
                        }
                    } else if (jQuery('.lp-compact-view-outer').length > 0) {
                        var LPaddress = jQuery(this).find('.lp_list_address').text();
                        var LPimageSrc = jQuery(this).find('.lp-listing-top-thumb').find('img').attr('src');
                        var LPiconSrc = jQuery(this).find('.cat-icon').find('img').attr('src');
                        if (typeof jQuery("body").data('deficon') !== 'undefined') {
                            LPiconSrc = jQuery("body").data('deficon');
                        }
                    } else {
                        var LPaddress = jQuery(this).find('.gaddress').text();
                        var LPimageSrc = jQuery(this).find('.listing-app-view-new-wrap').find('img').attr('src');
                        if (LPimageSrc === undefined) {
                            LPimageSrc = jQuery(this).data('feaimg');
                        }
                        if (LPimageSrc === undefined) {
                            if (jQuery('.lp-grid-box-thumb .show-img').length > 0) {
                                LPimageSrc = jQuery(this).find('.lp-grid-box-thumb .show-img img').attr('src');
                            } else {
                                LPimageSrc = jQuery(this).find('.lp-grid-box-thumb .show img').attr('src');
                            }
                        }
                        if (typeof jQuery("body").data('deficon') !== 'undefined') {
                            // your code here
                            LPiconSrc = jQuery("body").data('deficon');
                        } else {
                            LPiconSrc = jQuery(this).find('.cat-icon').find('img').attr('src');
                        }
                    }
                    if (LPlattitue != '' && LPlongitute != '') {

                        var LPimage = '';
                        if (LPimageSrc != '') {
                            LPimage = LPimageSrc;
                        }

                        $siteURL = jQuery('#list-grid-view-v2').data("url");
                        $pinicon = $siteURL+"/wp-content/plugins/search-listing/img/pins/pin.png";

                        var markerLocation = new L.LatLng(LPlattitue, LPlongitute); // London

                        var CustomHtmlIcon = L.divIcon({
                            html: "<div class='lpmap-icon-shape pin card" + LPpostid + "'><div class='lpmap-icon-contianer'><img src='" + $pinicon + "' /></div></div>",
                        });

                        var marker = new L.Marker(markerLocation, {icon: CustomHtmlIcon}).bindPopup('<div class="map-post"><div class="map-post-thumb"><a target="_blank" href="' + LPposturl + '"><img src="' + LPimage + '" ></a></div><div class="map-post-des"><div class="map-post-title"><h5><a target="_blank" href="' + LPposturl + '">' + LPtitle + '</a></h5></div><div class="map-post-address"><p><i class="fa fa-map-marker"></i> ' + LPaddress + '</p></div></div></div>').addTo(map);
                        markers.addLayer(marker);
                        map.addLayer(markers);

                    }

                });
                return true;
            }
        }

    }
});




