<?php

class mp_ssv_location extends WP_Widget
{

    // constructor
    function mp_ssv_location()
    {
        parent::__construct(false, $name = __('Event Location Widget', 'mp_ssv_location'));
    }

    // widget form creation
    function form($instance)
    {
        /* ... */
    }

    // widget update
    function update($new_instance, $old_instance)
    {
        /* ... */
    }

    // widget display
    function widget($args, $instance)
    {
        global $post;
        if (get_post_type($post) != 'events') {
            return;
        }
        $locations = array();
        while (have_posts()) {
            the_post();
            $location = get_post_meta($post->ID, 'location', true);
            // API Key: AIzaSyBSLKTf5i2FMM9mGYWFYV2-ydzhpxHGQo8 (only 2.000.000 requests per day)
            //TODO Events Page
            if ($location != "") {
                ?>
                <!--
                <input id="search_location" type="text"/>
                <ul id="results">

                </ul>
                <div id="map" style="height: 280px;"></div>
                <script type="text/javascript">
                var map;
                function mp_ssv_initMap() {
                    var displaySuggestions = function(predictions, status) {
                        if (status != google.maps.places.PlacesServiceStatus.OK) {
                            alert(status);
                            return;
                        }

                        predictions.forEach(function(prediction) {
                            var li = document.createElement('li');
                            li.appendChild(document.createTextNode(prediction.description));
                            document.getElementById('results').appendChild(li);
                        });
                    };

                    var service = new google.maps.places.AutocompleteService();
                    service.getQueryPredictions({ input: 'pizza near Syd' }, displaySuggestions);
                    var myLatLng = {lat: -25.363, lng: 131.044};

                    var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: myLatLng
                    });

                    var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    title: 'Hello World!'
                    });
                    var input = document.getElementById('search_location');
                    var searchBox = new google.maps.places.SearchBox(input, { bounds: map.getBounds() });
                }
                </script>
                <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSLKTf5i2FMM9mGYWFYV2-ydzhpxHGQo8&callback=initMap"></script>
                -->
                <?php
                array_push($locations, $location);
            }
        }
        if (!empty($locations)) {
            ?>
            <section>
                <h3>Location</h3>

                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSLKTf5i2FMM9mGYWFYV2-ydzhpxHGQo8&callback=initialize"></script>
                <script>
                    function mp_ssv_initialize() {
                        var mapProp = {
                            center: new google.maps.LatLng(51.508742, -0.120850),
                            zoom: 5,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };

                        var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
                        var geocoder = new google.maps.Geocoder();
                        var bounds = new google.maps.LatLngBounds();
                        <?php
                        foreach ($locations as &$location) {
                        ?>
                        geocoder.geocode({'address': "<?php echo $location; ?>"}, function (results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                map.setCenter(results[0].geometry.location);
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location
                                });

                                var infowindow = new google.maps.InfoWindow({
                                    content: "<?php echo $location; ?>"
                                });

                                marker.addListener('click', function () {
                                    infowindow.open(map, marker);
                                });

                                bounds.extend(marker.position);
                            } else {
                                alert('Geocode was not successful for the following reason: ' + status);
                            }
                        });
                        <?php
                        }
                        ?>
                        map.fitBounds(bounds);
                    }
                    $(document).ready(mp_ssv_initialize);
                </script>
                <div id="googleMap" style="width:100%;height:300px;"></div>
                <!--
					<div id="map" style="height: 280px;"></div>
					<script type="text/javascript">
					var map;
					function mp_ssv_initMap() {
						var displaySuggestions = function(predictions, status) {
							if (status != google.maps.places.PlacesServiceStatus.OK) {
								alert(status);
								return;
							}
	
							predictions.forEach(function(prediction) {
								var li = document.createElement('li');
								li.appendChild(document.createTextNode(prediction.description));
								document.getElementById('results').appendChild(li);
							});
						};
	
						var service = new google.maps.places.AutocompleteService();
						service.getQueryPredictions({ input: 'pizza near Syd' }, displaySuggestions);
						var myLatLng = {lat: -25.363, lng: 131.044};
	
						var map = new google.maps.Map(document.getElementById('map'), {
						zoom: 4,
						center: myLatLng
						});
	
						var marker = new google.maps.Marker({
						position: myLatLng,
						map: map,
						title: 'Hello World!'
						});
						var input = document.getElementById('search_location');
						var searchBox = new google.maps.places.SearchBox(input, { bounds: map.getBounds() });
					}
					</script>
					<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSLKTf5i2FMM9mGYWFYV2-ydzhpxHGQo8&callback=initMap"></script>
						<iframe src="https://maps.google.it/maps?q=<?php echo $location; ?>&output=embed" width="300" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>
				-->
            </section>
            <?php
        } else {
            ?>
            <section>
                <h3>Location</h3>
                Location Unknown
            </section>
            <?php
        }
    }
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("mp_ssv_location");'));
?>