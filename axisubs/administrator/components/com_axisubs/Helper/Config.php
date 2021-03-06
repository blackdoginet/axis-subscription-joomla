<?php
/**
 * @package   Axisubs - Subscription Management System
 * @copyright Copyright (c)2016-2020 Sasi varna kumar / Flycart Technologies
 * @license   GNU General Public License version 3, or later
 */

namespace Flycart\Axisubs\Admin\Helper;

defined ( '_JEXEC' ) or die ();

use \JObject;
use \stdClass;
use JFactory;
use JText;
use JModel;
use JLoader;
use JRoute;
use JURI;

class Config extends JObject {
	public static $instance = null;	
	var $_data;

	public function __construct($properties=null) {

		if(!isset($this->_data) && !is_array($this->_data)) {
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true)->select('*')->from('#__axisubs_configurations');
			$db->setQuery($query);
			$this->_data = $db->loadObjectList('config_meta_key');
		}
	}

	public static function getInstance(array $config = array())
	{
		if (!self::$instance)
		{
			self::$instance = new self($config);
		}

		return self::$instance;
	}
	
	public function set($namekey,$value=null){		
		if(!isset($this->_data[$namekey]) || !is_object($this->_data[$namekey])) $this->_data[$namekey] = new stdClass();
		$this->_data[$namekey]->config_meta_value=$value;
		$this->_data[$namekey]->config_meta_key=$namekey;
		return true;
	}

	/**
	 * Insert / update
	 * */
	public function update($namekey, $value=null){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('config_meta_key', 'config_meta_value');
		$values = array($db->quote($namekey), $db->quote($value));
		if(!isset($this->_data[$namekey])){
			$query
				->insert($db->quoteName('#__axisubs_configurations'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
		} else {
			$fields = array(
				$db->quoteName('config_meta_value') . ' = ' . $db->quote($value)
			);
			$conditions = array(
				$db->quoteName('config_meta_key') . ' = ' . $db->quote($namekey)
			);
			$query->update($db->quoteName('#__axisubs_configurations'))->set($fields)->where($conditions);
		}
		$db->setQuery($query);
		return $db->execute();
	}

	public function get($property, $default=null) {
		if(isset($this->_data[$property])) {			
			return $this->_data[$property]->config_meta_value;
		}
		return $default;
	}

}
