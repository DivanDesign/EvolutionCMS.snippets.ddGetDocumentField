<?php
namespace ddGetDocumentField\Outputter;


abstract class Outputter extends \DDTools\BaseClass {
	protected
		/**
		 * @property $resourceFields {array} — Document fields including TVs used in the output.
		 * @property $resourceFields[i] {string} — Field name.
		 * @property $resourceFieldsAliases {stdClass} — Document field aliases for output.
		 * @property $resourceFieldsAliases->{$fieldName} {string} — An alias.
		 */
		$resourceFields = [],
		$resourceFieldsAliases = [],
		$hasResourceFieldAliases = false,
		
		$typography = false,
		$escapeForJS = false,
		$URLEncode = false,
		$emptyResult = ''
	;
	
	/**
	 * __construct
	 * @version 1.1 (2020-04-26)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->dataProvider {\ddGetDocumentField\DataProvider\DataProvider}
	 */
	public function __construct($params = []){
		//Все параметры задают свойства объекта
		$this->setExistingProps($params);
		
		$this->hasResourceFieldAliases = count((array) $this->resourceFieldsAliases) > 0;
		
		$this->resourceFieldsAliases = (object) $this->resourceFieldsAliases;
		
		$this->typography = boolval($this->typography);
		$this->escapeForJS = boolval($this->escapeForJS);
		$this->URLEncode = boolval($this->URLEncode);
		
		//Ask dataProvider to get them
		$params->dataProvider->addResourceFields($this->resourceFields);
	}
	
	/**
	 * render
	 * @version 1.0.1 (2021-12-27)
	 * 
	 * @param $resourceData {stdClass|arrayAssociative} — Resources fields. @required
	 * @param $resourceData->{$key} {string} — A field. @required
	 * 
	 * @return {string}
	 */
	public final function render($resourceData){
		$result = $this->emptyResult;
		
		//if resource data is not impty
		if (count((array) $resourceData) > 0){
			$resourceData = (object) $resourceData;
			
			//Apply aliases
			$resourceData = $this->render_resourceDataApplyAliases($resourceData);
			
			//Run outputter main render
			$result = $this->render_main($resourceData);
			
			//Typography
			if ($this->typography){
				$result = \DDTools\Snippet::runSnippet([
					'name' => 'ddTypograph',
					'params' => [
						'text' => $result
					]
				]);
			}
		}
		
		//Если надо экранировать спец. символы
		if ($this->escapeForJS){
			$result = \ddTools::escapeForJS($result);
		}
		
		//Если нужно URL-кодировать строку
		if ($this->URLEncode){
			$result = rawurlencode($result);
		}
		
		return $result;
	}
	
	/**
	 * render_resourceDataApplyAliases
	 * @version 1.0.3 (2021-12-27)
	 * 
	 * @param $resourceData {stdClass} — Document fields. @required
	 * @param $resourceData->{$key} {string} — A field. @required
	 * 
	 * @return {stdClass}
	 */
	private function render_resourceDataApplyAliases($resourceData){
		//IF aliases exists
		if ($this->hasResourceFieldAliases){
			//Clear
			$result = new \stdClass();
			
			foreach (
				$resourceData as
				$fieldName =>
				$fieldValue
			){
				if (
					//IF alias for field is set
					isset($this->resourceFieldsAliases->{$fieldName}) &&
					trim($this->resourceFieldsAliases->{$fieldName}) != ''
				){
					$fieldName = $this->resourceFieldsAliases->{$fieldName};
				}
				
				//Save
				$result->{$fieldName} = $fieldValue;
			}
		}else{
			$result = $resourceData;
		}
		
		return $result;
	}
	
	/**
	 * render_main
	 * @version 1.0 (2020-04-25)
	 * 
	 * @param $resourceData {stdClass} — Document fields. @required
	 * @param $resourceData->{$key} {string} — A field. @required
	 * 
	 * @return {string}
	 */
	protected abstract function render_main($resourceData);
}