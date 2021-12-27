<?php
namespace ddGetDocumentField\DataProvider\Document;


class DataProvider extends \ddGetDocumentField\DataProvider\DataProvider {
	private
		$resourceFieldsAlternative = []
	;
	
	/**
	 * __construct
	 * @version 1.0 (2020-04-24)
	 *
	 * @param $params {stdClass|arrayAssociative}
	 */
	public function __construct($params){
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
	 * @version 1.0.6 (2021-12-27)
	 * 
	 * @return {stdClass}
	 */
	public function get(){
		$resourceDataResult = new \stdClass();
		
		$resourceFields = $this->resourceFields;
		
		//Если вдруг передали, что надо получить id
		if (
			(
				$resourceFields_idIndex = array_search(
					'id',
					$resourceFields
				)
			) !==
			false
		){
			//Удалим его, чтобы наличие результата от него не зависило (id ж ведь всегда есть)
			unset($resourceFields[$resourceFields_idIndex]);
		}
		
		//Получаем все необходимые поля
		$resourceDataAll = \ddTools::getTemplateVarOutput(
			array_merge(
				$resourceFields,
				$this->resourceFieldsAlternative
			),
			$this->resourceId
		);
		
		//Если по каким-то причинам ничего не получилось — прерываем
		if (!$resourceDataAll){
			return $resourceDataResult;
		}
		
		$isEmptyResult = true;
		
		//Перебираем полученные результаты, заполняем пустоту альтернативой, если есть
		foreach (
			$this->resourceFields as
			$fieldIndex =>
			$fieldName
		){
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $resourceDataAll,
					'propName' => $fieldName
				])
			){
				if (
					//Если значение поля пустое
					$resourceDataAll[$fieldName] == '' &&
					//Но, возможно, имеется альтернатива
					!empty($this->resourceFieldsAlternative) &&
					isset($this->resourceFieldsAlternative[$fieldIndex])
				){
					//В качестве значения берём значение альтернативного поля
					$resourceDataAll[$fieldName] = $resourceDataAll[
						//Имя альтернативного поля
						$this->resourceFieldsAlternative[$fieldIndex]
					];
				}
				
				if ($resourceDataAll[$fieldName] != ''){
					$isEmptyResult = false;
				}
				
				//Save to output
				$resourceDataResult->{$fieldName} = $resourceDataAll[$fieldName];
			}
		}
		
		//Если результаты непустые
		if (!$isEmptyResult){
			if (
				//Если надо было вернуть ещё и url документа
				(
					array_search(
						'url',
						$this->resourceFields
					) !==
					false
				) &&
				(
					//И если такого поля нет
					!isset($resourceDataResult->url) ||
					//Или оно пусто (а то мало ли, как TV назвали)
					trim($resourceDataResult->url) == ''
				)
			){
				$resourceDataResult->url = \ddTools::$modx->makeUrl($this->resourceId);
			}
			
			//Если в поля передавали id, выведем
			if ($resourceFields_idIndex !== false){
				$resourceDataResult->id = $this->resourceId;
			}
		}else{
			$resourceDataResult = new \stdClass();
		}
		
		return $resourceDataResult;
	}
}