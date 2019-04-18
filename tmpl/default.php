<?php
   /**
   * jWeather by ip. Module for Joomla 3.x
   * @version $Id: mod_jweather_by_ip.php 2015-12-31 $
   * @package: jWeather by ip
   * ===================================================
   * @author
   * Name: Masterpro project, www.masterpro.ws
   * Email: info@masterpro.ws
   * Url: http://www.masterpro.ws
   * ===================================================
   * @copyright (C) 2015 Alex Smirnov. All rights reserved.
   * @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
   **/
   /*
   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   version 2 as published by the Free Software Foundation.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
   */
   // No direct access
   defined('_JEXEC') or die; 
   
   if ($params->get('greeting') == 1) {
   require_once $_SERVER['DOCUMENT_ROOT']."/modules/mod_jweather_by_ip/src/greeting.js";
   }
   require_once $_SERVER['DOCUMENT_ROOT']."/modules/mod_jweather_by_ip/src/style.css";
   ?>
<div class="weather_mstp">
<?php
   $html = "<table>";
   $i = 1;
   if (in_array(1, $values)) {
   if ($params->get('weather_source_choose') == 1) {
   $html .= '<tr><td> <img src="https://openweathermap.org/img/w/'.$source[0].'.png" title=' .$source[1]. ' ></td></tr>';
   } else {
   $html .= '<tr><td> <img src="' .$source[0]. '" title=' .$source[1]. ' ></td></tr>';
   }
   }
   foreach (array_combine($names, $source) as $names =>  $source):
       $html .= "<tr>";
      if (in_array($i, $values) and $i > 1) {
       $html .= "<td>".$names."</td>";
       $html .= "<td>".$source."</td>";
       }
       $html .= "</tr>";
   
       $i++;
   endforeach;
   $html .= "</table>";
   if ($params->get('google_map') == 0) {
   echo $html;
   } else {
   ?>
<div id="map"></div>
<script>
   function initMap() {
     var coord = {lat: <?php echo $start[0]; ?>, lng: <?php echo $start[1]; ?>};
     var map = new google.maps.Map(document.getElementById('map'), {
       zoom: 8,
       center: coord
     });
   
     var contentString = '<div id="content">'+
     
         '<div id="bodyContent">'+
         '<p><?php echo $html ?></p>'+
         '</div></div>';
   
     var infowindow = new google.maps.InfoWindow({
       content: contentString
     });
   
     var marker = new google.maps.Marker({
       position: coord,
       map: map
     });
     marker.addListener('click', function() {
       infowindow.open(map, marker);
     });
     infowindow.open(map, marker);
   }
</script>
<script async defer
   src="https://maps.googleapis.com/maps/api/js?key=<?php echo $params->get('api_googlemap'); ?>&callback=initMap&language=<?php echo $lang; ?>"></script>
<?php
   }
?>
