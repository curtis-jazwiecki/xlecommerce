<?php
/*
  $Id: mzmt.php,v 1.100 2004-11-09 Josh Dechant Exp $

  Copyright (c) 2004 Josh Dechant

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class mzmt {
    var $code, $title, $description, $icon, $enabled, $num_zones, $num_tables, $delivery_geozone, $geozone_mode, $order_total;
  
    function mzmt() {
      global $order, $vendors_id;

      $this->code = 'mzmt';
      $this->title = MODULE_SHIPPING_MZMT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_MZMT_TEXT_DESCRIPTION;
      //$this->sort_order = MODULE_SHIPPING_MZMT_SORT_ORDER;
      //$this->tax_class = MODULE_SHIPPING_MZMT_TAX_CLASS;
      //$this->enabled = ((MODULE_SHIPPING_MZMT_STATUS == 'True') ? true : false);

      $this->num_geozones = MODULE_SHIPPING_MZMT_NUMBER_GEOZONES;
      $this->num_tables = MODULE_SHIPPING_MZMT_NUMBER_TABLES;

      /*if ($this->enabled == true) {
        $this->enabled = false;
        for ($n=1; $n<=$this->num_geozones; $n++) {
          if ( ((int)constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID') > 0) && ((int)constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID') == $this->getGeoZoneID($order->delivery['country']['id'], $order->delivery['zone_id'])) ) {
            $this->enabled = true;
            $this->delivery_geozone = $n;
            break;
          } elseif ( ((int)constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID') == 0) && ($n == (int)$this->num_geozones) ) {
            $this->enabled = true;
            $this->delivery_geozone = $n;
            break;
          }
        }
      }*/
    }

// class methods

	function enabled($vendors_id='1'){
		$this->enabled = (( @constant('MODULE_SHIPPING_MZMT_STATUS_' . $vendors_id) == 'True') ? true : false);
		
		global $order;
		if ($this->enabled == true) {
			$this->enabled = false;
			for ($n=1; $n<=$this->num_geozones; $n++) {
				if ( ((int)@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID_' . $vendors_id) > 0) && ((int)@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID_' . $vendors_id) == $this->getGeoZoneID($order->delivery['country']['id'], $order->delivery['zone_id'])) ) {
					$this->enabled = true;
					$this->delivery_geozone = $n;
					break;
				} elseif ( ((int)constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID') == 0) && ($n == (int)$this->num_geozones) ) {
					$this->enabled = true;
					$this->delivery_geozone = $n;
					break;
				}
			}
		}
		
		return $this->enabled;
	}
	
	function zones($vendors_id='1'){
		global $order;
		if ($this->enabled == true) {
			$this->enabled = false;
			for ($n=1; $n<=$this->num_geozones; $n++) {
				if ( ((int)@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID_' . $vendors_id) > 0) && ((int)@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID_' . $vendors_id) == $this->getGeoZoneID($order->delivery['country']['id'], $order->delivery['zone_id'])) ) {
					$this->enabled = true;
					$this->delivery_geozone = $n;
					break;
				} elseif ( ((int)constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID') == 0) && ($n == (int)$this->num_geozones) ) {
					$this->enabled = true;
					$this->delivery_geozone = $n;
					break;
				}
			}
		}
		
		return $this->enabled;
	}

	function sort_order($vendors_id='1'){
		$this->sort_order = @constant('MODULE_SHIPPING_MZMT_SORT_ORDER_' . $vendors_id);
		return $this->sort_order;
	}
	
	function tax_class($vendors_id='1'){
		$this->tax_class = @constant('MODULE_SHIPPING_MZMT_TAX_CLASS_' . $vendors_id);
	}
	
	function num_geozones($vendors_id='1'){
		//$this->num_geozones = @constant('MODULE_SHIPPING_MZMT_NUMBER_GEOZONES_' . $vendors_id);
		return $this->num_geozones;
	}
	
	function num_tables($vendors_id='1'){
		//$this->num_tables = @constant('MODULE_SHIPPING_MZMT_NUMBER_TABLES_' . $vendors_id);
		return $this->num_tables;
	}
	
	function quote($method = '', $module='', $vendors_id='1') {
		global $order;

		$this->quotes = array(
			'id' => $this->code,
			'module' => @constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TEXT_TITLE') . '' . $shipping_weight . '',
			'methods' => array());

		$this->determineTableMethod(@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_MODE_' . $vendors_id));  
      
		if ($method) {
			$j = substr($method, 5);
        
			$shipping = $this->determineShipping(split("[:,]" , @constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TABLE_' . $j . '_' . $vendors_id )));
        
			$this->quotes['methods'][] = array(
				'id' => 'table' . $j,
				'title' => constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TABLE_' . $j . '_TEXT_WAY'),
				'cost' => $shipping + @constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_HANDLING_' . $vendors_id));
		} else {
			for ($j=1; $j<=$this->num_tables; $j++) {
				if (!tep_not_null(@constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TABLE_' . $j . '_' . $vendors_id ))) continue;

				$shipping = $this->determineShipping(explode("[:,]" , @constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TABLE_' . $j . '_' . $vendors_id )));

				$this->quotes['methods'][] = array(
					'id' => 'table' . $j,
					'title' => constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_TABLE_' . $j . '_TEXT_WAY'),
					'cost' => $shipping + @constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_HANDLING_' . $vendors_id ));
			}
		}

		if ($this->tax_class() > 0) {
			$this->quotes['tax'] = tep_get_tax_rate($this->tax_class(), $order->delivery['country']['id'], $order->delivery['zone_id']);
		}

		if (tep_not_null(constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_ICON'))) 
			$this->quotes['icon'] = tep_image(DIR_WS_ICONS . constant('MODULE_SHIPPING_MZMT_GEOZONE_' . $this->delivery_geozone . '_ICON'), $this->title);

		return $this->quotes;
	}  

    function check($vendors_id='1') {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_MZMT_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install($vendors_id='1') {
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable MultiRegion MultiTable Method', 'MODULE_SHIPPING_MZMT_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer multi-region multi-table rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_MZMT_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_MZMT_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '0', now())");

      for ($n=1; $n<=$this->num_geozones(); $n++) {
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('<hr />Geo Zone $n', 'MODULE_SHIPPING_MZMT_GEOZONE_{$n}_ID_" . $vendors_id . "', '', 'Enable this for the following geo zone.', '6', '0', 'tep_get_zone_class_title', '_cfg_pull_down_geozones(', now())");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Geo Zone $n Table Method', 'MODULE_SHIPPING_MZMT_GEOZONE_{$n}_MODE_" . $vendors_id . "', 'weight', 'The shipping cost is based on the total weight, total price, or total count of the items ordered.', '6', '0', 'tep_cfg_select_option(array(\'weight\', \'price\', \'count\'), ', now())");
        
        for ($j=1; $j<=$this->num_tables(); $j++) {
          tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Geo Zone $n Shipping Table $j', 'MODULE_SHIPPING_MZMT_GEOZONE_{$n}_TABLE_{$j}_" . $vendors_id . "', '', 'Shipping table $j for this geo zone', '6', '0', now())");
        }

        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Geo Zone $n Handling Fee', 'MODULE_SHIPPING_MZMT_GEOZONE_{$n}_HANDLING_" . $vendors_id . "', '0', 'Handling Fee for this shipping geo zone', '6', '0', now())");
      }
    }

    function remove($vendors_id) {
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

    function keys($vendors_id) {
      $keys = array('MODULE_SHIPPING_MZMT_STATUS_' . $vendors_id, 'MODULE_SHIPPING_MZMT_TAX_CLASS_' . $vendors_id, 'MODULE_SHIPPING_MZMT_SORT_ORDER_' . $vendors_id);

      for ($n=1; $n<=$this->num_geozones(); $n++) {
        $keys[] = 'MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_ID_' . $vendors_id;
        $keys[] = 'MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_MODE_' . $vendors_id;
        $keys[] = 'MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_HANDLING_' . $vendors_id;

        for ($j=1; $j<=$this->num_tables(); $j++) {
          $keys[] = 'MODULE_SHIPPING_MZMT_GEOZONE_' . $n . '_TABLE_' . $j . '_' . $vendors_id;
        }
      }

      return $keys;
    }

    function determineTableMethod($geozone_mode) {
      global $total_count, $shipping_weight, $cart;

      $this->geozone_mode = $geozone_mode;

      if ($this->geozone_mode == 'price') {
        $this->order_total = $cart->show_total();
      } elseif ($this->geozone_mode == 'count') {
        $this->order_total = $total_count;
      } else {
        $this->order_total = $shipping_weight;
      }

      return true;    
    }

    function determineShipping($table_cost)
		{
    		global $shipping_num_boxes;

			for ($i=0, $n=sizeof($table_cost); $i<$n; $i+=2)
				{
					if ($this->order_total >= $table_cost[$i])
						{ $shipping_factor = $table_cost[$i+1]; }
				}

			if (substr_count($shipping_factor, '%') > 0)
				{ $shipping = ((($this->order_total*10)/10)*((str_replace('%', '', $shipping_factor))/100)); }
			else
				{ $shipping = str_replace('$', '', $shipping_factor); }

			if ($this->geozone_mode == 'weight')
				{ $shipping = $shipping * $shipping_num_boxes; }
			return $shipping;
		}

    function getGeoZoneID($country_id, $zone_id) {
      // First, check for a Geo Zone that explicity includes the country & specific zone (useful for splitting countries with zones up)
      $zone_query = tep_db_query("select gz.geo_zone_id from " . TABLE_GEO_ZONES . " gz left join " . TABLE_ZONES_TO_GEO_ZONES . " ztgz on (gz.geo_zone_id = ztgz.geo_zone_id) where ztgz.zone_country_id = '" . (int)$country_id . "' and ztgz.zone_id = '" . (int)$zone_id . "' and LOWER(gz.geo_zone_name) like 'shp%'");

      if (tep_db_num_rows($zone_query)) {
        $zone = tep_db_fetch_array($zone_query);
        return $zone['geo_zone_id'];
      } else {
        // No luck…  Now check for a Geo Zone for the country and "All Zones" of the country.
        $zone_query = tep_db_query("select gz.geo_zone_id from " . TABLE_GEO_ZONES . " gz left join " . TABLE_ZONES_TO_GEO_ZONES . " ztgz on (gz.geo_zone_id = ztgz.geo_zone_id) where ztgz.zone_country_id = '" . (int)$country_id . "' and (ztgz.zone_id = '0' or ztgz.zone_id is NULL) and LOWER(gz.geo_zone_name) like 'shp%'");

        if (tep_db_num_rows($zone_query)) {
          $zone = tep_db_fetch_array($zone_query);
          return $zone['geo_zone_id'];
        } else {
          return false;
        }
      }
    }

  }

  function _cfg_pull_down_geozones($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_class_array = array(array('id' => '0', 'text' => 'Rest of the World'));
    $zone_class_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " where LOWER(geo_zone_name) like 'shp%' order by geo_zone_name");

    while ($zone_class = tep_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
                                  'text' => $zone_class['geo_zone_name']);
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }
?>
