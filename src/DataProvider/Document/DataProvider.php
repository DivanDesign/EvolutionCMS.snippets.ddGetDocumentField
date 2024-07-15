<?php
namespace ddGetDocumentField\DataProvider\Document;


class DataProvider extends \ddGetDocumentField\DataProvider\DataProvider {
	private
		$resourceFieldsAlternative = []
	;
	
	/**
	 * __construct
	 * @version 1.1 (2024-02-07)
	 *
	 * @param $params {stdClass|arrayAssociative}
	 */
	public function __construct($params){
		$params = (object) $params;
		
		if (!is_numeric($params->resourceId)){
			$params->resourceId = \ddTools::$modx->documentIdentifier;
		}
		
		//Call base constructor
		parent::__construct($params);
		
		if (!is_array($this->resourceFieldsAlternative)){
			$this->resourceFieldsAlternative = explode(
				',',
				$this->resourceFieldsAlternative
			);
		}
	}
	
	/**
	 * get
	 * @version 2.0 (2024-07-15)
	 * 
	 * @return {stdClass}
	 */
	public function get(){
		$resourceDataResult = new \stdClass();
		
		$resourceFields = $this->resourceFields;
		
		//Получаем все необходимые поля
		$resourceDataAll = \ddTools::getTemplateVarOutput(
			array_merge(
				$resourceFields,
				$this->resourceFieldsAlternative
			),
			$this->resourceId
		);
		
		//Перебираем полученные результаты, заполняем пустоту альтернативой, если есть
		foreach (
			$this->resourceFields
			as $fieldIndex
			=> $fieldName
		){
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $resourceDataAll,
					'propName' => $fieldName,
				])
			){
				if (
					//Если значение поля пустое
					$resourceDataAll[$fieldName] == ''
					//Но, возможно, имеется альтернатива
					&& !\ddTools::isEmpty($this->resourceFieldsAlternative)
					&& isset($this->resourceFieldsAlternative[$fieldIndex])
				){
					//В качестве значения берём значение альтернативного поля
					$resourceDataAll[$fieldName] = $resourceDataAll[
						//Имя альтернативного поля
						$this->resourceFieldsAlternative[$fieldIndex]
					];
				}
				
				//Save to output
				$resourceDataResult->{$fieldName} = $resourceDataAll[$fieldName];
			}else{
				$resourceDataResult->{$fieldName} = '';
			}
		}
		
		if (
			//Если надо было вернуть ещё и url документа
			(
				array_search(
					'url',
					$this->resourceFields
				)
				!== false
			)
			&& (
				//И если такого поля нет
				!isset($resourceDataResult->url)
				//Или оно пусто (а то мало ли, как TV назвали)
				|| trim($resourceDataResult->url) == ''
			)
		){
			$resourceDataResult->url = \ddTools::$modx->makeUrl($this->resourceId);
		}
		
		return $resourceDataResult;
	}
}