<?php
namespace ddGetDocumentField\DataProvider;


abstract class DataProvider extends \DDTools\BaseClass {
	protected
		/**
		 * @property $resourceId {integer} — Document ID. Default: $modx->documentIdentifier.
		 */
		$resourceId,
		$resourceFields = []
	;
	
	/**
	 * __construct
	 * @version 1.0 (2020-04-24)
	 *
	 * @param $params {stdClass|arrayAssociative}
	 */
	public function __construct($params){
		//Все параметры задают свойства объекта
		$this->setExistingProps($params);
		
		$this->resourceId = intval($this->resourceId);
	}
	
	/**
	 * addResourceFields
	 * @version 1.0 (2020-04-24)
	 * 
	 * @param $resourceFields {array}
	 * @param $resourceFields[i] {string} — Name of document field or TV.
	 * 
	 * @return {void}
	 */
	public function addResourceFields($resourceFields){
		$this->resourceFields = array_unique(array_merge(
			$this->resourceFields,
			$resourceFields
		));
	}
	
	/**
	 * get
	 * @version 1.0 (2020-04-24)
	 * 
	 * @return {stdClass}
	 */
	abstract function get();
}