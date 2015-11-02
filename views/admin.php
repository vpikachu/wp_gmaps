<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * echo admin template when edit post or page
 * @param type $meta_props
 * @param type $is_default
 */
function admin_template($meta_props,$is_default){
    ?>
        <script>
            var gmap = {
                map:null,
                marker:null,
                init: function(){
                    var gmap_prop = {
                        center:new google.maps.LatLng(<?php echo $meta_props["gmap_lat"]; ?>,<?php echo $meta_props["gmap_lng"]; ?>),
                        zoom:<?php echo $meta_props["gmap_zoom"]; ?>,
                        mapTypeId:google.maps.MapTypeId.ROADMAP
                    };

                  gmap.marker = new google.maps.Marker({
                    position:gmap_prop.center
                  });

                  gmap.map = new google.maps.Map(document.getElementById("gmap_view"),gmap_prop);
                  gmap.map.addListener('dblclick', gmap.ondblclick);
                  gmap.map.addListener('zoom_changed', gmap.onzoom);                  

                  <?php if(!$is_default) {    ?>            
                    gmap.marker.setMap(gmap.map);
                  <?php  } ?>
                    
                },
                ondblclick: function(e){
                    gmap.marker.position = e.latLng;
                    gmap.marker.setMap(gmap.map);              
                    gmap.map.panTo(e.latLng);
                    document.getElementById("gmap_lat").value = e.latLng.G;
                    document.getElementById("gmap_lng").value = e.latLng.K;
                    document.getElementById("gmap_zoom").value = gmap.map.getZoom();            
                },
                onskip: function(){
                    gmap.marker.setMap(null);
                    document.getElementById("gmap_zoom").value = "";
                    document.getElementById("gmap_lat").value = "";
                    document.getElementById("gmap_lng").value = "";
                    document.getElementById("gmap_zoom").value = "";                     
                },
                onzoom: function(){
                    if(gmap.marker.getMap() != null)
                        document.getElementById("gmap_zoom").value = gmap.map.getZoom();
                }
            };
            
            google.maps.event.addDomListener(window, 'load', gmap.init);
        </script>
        <p class="howto">Double click on place on map if you want to set position for your post. 
            To skip and not show map at post press button "Skip" below.</p>
        <p>
            <button class="button hide-if-no-js" onclick="gmap.onskip(); return false;">Skip</button>
        </p>
        <div id="gmap_view" style="width:100%;height:400px;"></div>
<?php
        foreach ($meta_props as $key => $value){
            if($is_default) $value = "";            
            echo "<input type='hidden' id='$key' name='$key' value='$value' />";            
        }
}
