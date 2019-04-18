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
 *
 */
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
defined('_JEXEC') or die('Restricted access');
class ModJWeatherByIp
{
    public static function getStart($params)
    {
        if ($params->get('api_choose') == 1)
        {
            $loc_array = self::getRestsxgeo($params);
        }
        elseif ($params->get('api_choose') == 2)
        {
            $loc_array = array(
                self::getIpgeoloc($params) ["latitude"],
                self::getIpgeoloc($params) ["longitude"],
                self::getIpgeoloc($params) ["city"]
            );
        }
        else
        {
            $loc_array = self::getSxgeo($params);

        }
        return $loc_array;
    }

    public static function getSource($params)
    {
        if ($params->get('weather_source_choose') == 1)
        {
            $api_key = $params->get('api_key_owm');
            $num_of_days = 1;
            $json = file_get_contents('http://api.openweathermap.org/data/2.5/weather?lat=' . self::getStart($params) [0] . '&lon=' . self::getStart($params) [1] . '&appid=' . $api_key . '&units=metric');
            $obj = json_decode($json, true);
            return [$obj['weather']['0']['icon'], $obj['weather']['0']['main'], $obj['main']['temp'], $obj['wind']['speed'], $obj['main']['pressure'], $obj['main']['humidity'], $obj['clouds']['all'], $obj['visibility'], $obj['weather']['0']['description']];
        }
        else
        {
            $api_key = $params->get('api_key');
            $num_of_days = 1;
            $coord = self::getStart($params);
            unset($coord[2]);
            $loc_string = implode(',', $coord);
            $basicurl = sprintf('http://api2.worldweatheronline.com/premium/v1/weather.ashx?key=%s&q=%s&num_of_days=%s', $api_key, $loc_string, intval($num_of_days));
            $xml = simplexml_load_file($basicurl);
            return [$xml
                ->current_condition->weatherIconUrl, $xml
                ->current_condition->weatherDesc, $xml
                ->current_condition->temp_C, $xml
                ->current_condition->windspeedKmph, $xml
                ->current_condition->pressure, $xml
                ->current_condition->humidity, $xml
                ->current_condition->cloudcover, $xml
                ->current_condition->visibility, $xml
                ->current_condition->weatherDesc, $xml
                ->weather
                ->astronomy->sunrise, $xml
                ->weather
                ->astronomy->sunset, $xml
                ->weather
                ->astronomy->moonrise, $xml
                ->weather
                ->astronomy->moonset, $xml
                ->weather->date, $xml
                ->current_condition->temp_F];
        }
    }

    public static function getNames($params)
    {
        if ($params->get('weather_source_choose') == 1)
        {
            return array(
                JText::_('WEATHER_ICON') ,
                JText::_('WEATHER_MAIN') ,
                JText::_('TEMPERC') ,
                JText::_('WINDSPEEDKMPH_FR') ,
                JText::_('PRESSURE_FR') ,
                JText::_('HUMIDITY_FR') ,
                JText::_('CLOUDCOVER_FR') ,
                JText::_('VISIBILITY_FR') ,
                JText::_('WEATHER_DESC')
            );
        }
        else
        {

            return array(
                JText::_('WEATHER_ICON') ,
                JText::_('WEATHER_MAIN') ,
                JText::_('TEMPERC') ,
                JText::_('WINDSPEEDKMPH_FR') ,
                JText::_('PRESSURE_FR') ,
                JText::_('HUMIDITY_FR') ,
                JText::_('CLOUDCOVER_FR') ,
                JText::_('VISIBILITY_FR') ,
                JText::_('WEATHER_DESC') ,
                JText::_('SUNRISE_FR') ,
                JText::_('SUNSET_FR') ,
                JText::_('MOONRISE_FR') ,
                JText::_('MOONSET_FR') ,
                JText::_('TODAYSDATE') ,
                JText::_('TEMPER_FR')
            );
        }
    }

    public static function getValues($params)
    {
        if ($params->get('weather_source_choose') == 1)
        {
            return [$params->get('img') , $params->get('title') , $params->get('temp_C') , $params->get('windspeedKmph') , $params->get('pressure') , $params->get('humidity') , $params->get('cloudcover') , $params->get('visibility') , $params->get('weatherDesc') ];
        }
        else
        {
            return [$params->get('img') , $params->get('title') , $params->get('temp_C') , $params->get('windspeedKmph') , $params->get('pressure') , $params->get('humidity') , $params->get('cloudcover') , $params->get('visibility') , $params->get('weatherDesc') , $params->get('sunrise') , $params->get('sunset') , $params->get('moonrise') , $params->get('moonset') , $params->get('date') , $params->get('temp_F') ];
        }
    }

    public static function getLang($params)
    {
        $lang = JFactory::getLanguage()->getTag();
        return substr($lang, 0, 2);
    }

    public static function getIp($params)
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getRestsxgeo($params)
    {
        $api_key_sypexgeo = $params->get('api_key_sypexgeo');
        $city = simplexml_load_file('http://api.sypexgeo.net/' . $api_key_sypexgeo . '/xml/' . self::getIp($params))
            ->ip->city;

        if (self::getLang($params) == "ru")
        {
            $name_city = $city->name_ru;
        }
        else
        {
            $name_city = $city->name_en;
        }

        $loc_array = array(
            $city->lat,
            $city->lon,
            $name_city
        );
        return $loc_array;
    }

    public static function getSxgeo($params)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/modules/mod_jweather_by_ip/src/SxGeo.php";
        $SxGeo = new SxGeo('modules/mod_jweather_by_ip/SxGeoCity.dat');
        $city = (Object)$SxGeo->get(self::getIp($params)) ['city'];

        if (self::getLang($params) == "ru")
        {
            $name_city = $city->name_ru;
        }
        else
        {
            $name_city = $city->name_en;
        }

        $loc_array = array(
            $city->lat,
            $city->lon,
            $name_city
        );
        return $loc_array;
    }

    public static function getIpgeoloc($params)
    {
        $apiKey = $params->get('api_key_geoloc');

        if (self::getLang($params) == "ru")
        {
            $lang = "ru";
        }
        else
        {
            $lang = "en";
        }

        $fields = "*";
        $excludes = "";
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=" . $apiKey . "&ip=" . self::getIp($params) . "&lang=" . $lang . "&fields=" . $fields . "&excludes=" . $excludes;
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        return json_decode(curl_exec($cURL) , true);
    }

}
