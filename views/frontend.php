<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * return frontend formated string to add to content string in filter
 * @param type $lat
 * @param type $lng
 * @param type $zoom
 * @return type
 */
function fronend_template($lat,$lng,$zoom){
    return <<<ENDCONTENT
                    <script>
            var gmap = {
                map:null,
                marker:null,
                init: function(){
                    var gmap_prop = {
                        center:new google.maps.LatLng($lat,$lng),
                        zoom:$zoom,
                        mapTypeId:google.maps.MapTypeId.ROADMAP
                    };
                  gmap.marker = new google.maps.Marker({
                    position:gmap_prop.center
                  });
                  gmap.map = new google.maps.Map(document.getElementById("gmap_view"),gmap_prop);
                  gmap.marker.setMap(gmap.map);                  
                    
                }
            };            
            google.maps.event.addDomListener(window, 'load', gmap.init);
        </script> 
        
        <div id="gmap_view" style="width:100%;height:400px;margin-bottom:30px;"></div>
        
ENDCONTENT;
}

