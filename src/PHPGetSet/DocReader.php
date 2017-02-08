<?php 


namespace DocReader;

use \DocReader\DocReaderException as Exception;


class Reader
{
	// Create Some Private Variables
	private $rawDoc;
	private $params;
	private $keyPattern = "[A-z0-9\_\-]+";
	private $endPattern = "[ ]*(?:@|\r\n|\n)";
	private $parsedAll = FALSE;

	public function __construct($class, $method, $reflectionClass = 'ReflectionMethod')
	{
	    $reflectionClass = '\\' . $reflectionClass;
		$reflection = new $reflectionClass($class, $method);
		$this->rawDoc = $reflection->getDocComment();
		$this->params = array();
	}

	private function parseSingle($val)
	{
		if (isset($this->params[$val])) {
			return $this->params[$val];
		}else{
			if (preg_match("/@".preg_quote($val).$this->endPattern."/", $this->rawDoc, $match)) {
				return True;
			}else{
				preg_match_all("/@".preg_quote($val)." (.*)".$this->endPattern."/U", $this->rawDoc, $matches);
				$size = sizeof($matches[1]);

				if ($size == 0) {
					return null;
				}elseif ($size == 1){
					return $this->parseValue($matches[1][0]);
				}else{
					$this->params[$val] = array();
					foreach ($matches[1] as $match) {
						$this->params[$val][] = $this->parseValue($match);
					}

					return $this->params[$val];
				}
			}
		}
	}
	private function parse()
	{
		$pattern = "/@(?=(.*)".$this->endPattern.")/U";
		preg_match_all($pattern, $this->rawDoc, $matches);

		foreach ($matches[1] as $rawParam) {
			if(preg_match("/^(".$this->keyPattern.") (.*)$/", $rawParam, $match)){
				if (isset($this->params[$match[1]])) {
					$this->params[$match[1]] = array_merge((array)$this->params[$match[1]], (array)$match[2]);
				}else{
					$this->params[$match[1]] = $this->parseValue($match[2]);
				}
			}elseif(preg_match("/^".$this->keyPattern."$/", $rawParam, $match)){
				$this->params[$rawParam] = true;
			}else{
				$this->params[$rawParam] = null;
			}
		}
	}

	public function getVarDeclarations($var)
	{
		$declarations = (array)$this->getParam($var);

		foreach ($declarations as &$declaration) {
			$declaration = $this->parseVariableDeclaration($declaration, $var);
		}

		return $declarations;
	}

	private function parseVarDeclaration($declaration, $var)
	{
		$type = gettype($declaration);
		if($type !== 'string')
		{
			throw new \InvalidArgumentException(
				"Raw declaration must be string, $type given. Key='$var'.");
		}
		if(strlen($declaration) === 0)
		{
			throw new \InvalidArgumentException(
				"Raw declaration cannot have zero length. Key='$var'.");
		}
		$declaration = explode(" ", $declaration);
		if(sizeof($declaration) == 1)
		{
			// string is default type
			array_unshift($declaration, "string");
		}
		// take first two as type and name
		$declaration = array(
			'type' => $declaration[0],
			'name' => $declaration[1]
		);
		return $declaration;
	}

	private function parseValue($originalValue)
	{
		if($originalValue && $originalValue !== 'null')
		{
			// try to json decode, if cannot then store as string
			if( ($json = json_decode($originalValue,TRUE)) === NULL)
			{
				$value = $originalValue;
			}
			else
			{
				$value = $json;
			}
		}
		else
		{
			$value = NULL;
		}
		return $value;
	}
	public function getParams()
	{
		if(! $this->parsedAll)
		{
			$this->parse();
			$this->parsedAll = TRUE;
		}
		return $this->params;
	}
	public function getParam($key)
	{
		return $this->parseSingle($key);
	}

}


?>