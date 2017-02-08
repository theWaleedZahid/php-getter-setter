<?php 

namespace PHPGetSet;

use \PHPGetSet\GetterSetterException;

include "DocReader.php";

trait GetterSetter {
	public function __call($method, $params = null)
	{
		// First 3 chars of the method name
		$methodprefix = strtolower(substr($method, 0, 3));
		// and rest is our property
		$property = substr($method, 3);
		// First Letter Capital
		$property = lcfirst($property);

		if ($methodprefix != "get" & $methodprefix != "set") {
			throw new \Exception("Undefined method $method has been called!", 9);
		}

		// Parse the Documentation Comment
		$reader = new \DocReader\Reader(__CLASS__, $property, 'ReflectionProperty');

		// store All the vars
		$type = $reader->getParam('var');

		if (in_array(strtolower($type), ['string', 'number', 'array', 'object'])) {
			$type = strtolower($type);
		}	

		// @getter is marked as false on property annotation
		$getter = is_null($reader->getParam('getter')) ? true : (boolean)$reader->getParam('getter');
        // @setter is marked as false on property annotation
        $setter = is_null($reader->getParam('setter')) ? true : (boolean)$reader->getParam('setter');

        if ($methodprefix == 'set') {
        	// Check if user has set @setter to false, so we won't set the value
        	if (!$setter) {
                throw new \Exception('Can\'t set restricted property ' . $property, 1);
              // All parameters are given
            } elseif (count($params) < 1 || !isset($params[0])) {
                throw new \InvalidArgumentException("Invalid parameter given, method <strong>$method</strong> requires 1 parameter,  but ". count($params) . " given!", 2);
            }
             // The value given in parameter
            $val = $params[0];
            $this->verifyCorrectType($type, $val);
            // No problem, set the property value
            $this->$property = $val;
            // Return $this, so chaining is possible on setters.
            return $this;
        }elseif($methodprefix=="get"){
        	// Check if user has set @getter to false, so we won't get the value
            if (!$getter) {
                throw new \Exception('Can\'t get restricted property ' . $property, 8);
            }
            // No problem, get the value
            return $this->$property;
        }
	}
	protected function verifyCorrectType($type, $val)
	{
		// Switch various types
        switch ($type) {
            case 'string':
                if (!is_string($val)) {
                    throw new \Exception('String type expected', 3);
                }
                break;
            case 'number':
                if (!is_numeric($val)) {
                    throw new \Exception('Number type expected', 4);
                }
                break;
            case 'array':
                if (!is_array($val)) {
                    throw new \Exception('Array type expected', 5);
                }
                break;
            case 'object':
                if (!is_object($val)) {
                    throw new \Exception('Object type expected.', 6);
                }
                break;
            default:
                // If a @var type is given in annotation and we haven't received
                // proper type.
                if (!is_null($type) && !$val instanceof $type) {
                    throw new \Exception('Instance of ' . $type . ' expected.', 7);
                }
                break;
        }
	}
}

?>