<?php
namespace ddGetDocumentField;


use DDTools\ObjectTools;

class Input extends \DDTools\BaseClass {
	/**
	 * @property $snippetParams {stdClass} — Snippet parameters. Don't use this field if you can.
	 * @property $dataProviderParams {stdClass}
	 * @property $outputterParams {stdClass}
	 */
	public
		$snippetParams,
		
		$dataProvider = 'document',
		$dataProviderParams,
		
		$outputter = 'string',
		$outputterParams
	;
	
	/**
	 * __construct
	 * @version 1.0 (2020-04-25)
	 * 
	 * @param $snippetParams {stdClass|arrayAssociative} — Snippet parameters. @see REAMDE.md
	 */
	public function __construct($snippetParams = []){
		$snippetParams = (object) $snippetParams;
		
		//Init fields
		$this->snippetParams = $snippetParams;
		$this->dataProviderParams = (object) [];
		$this->outputterParams = (object) [];
		
		//Backward compatibility
		$this->paramsBackwardCompatibility();
		
		//Prepare data provider params
		if (!empty($snippetParams->dataProviderParams)){
			$this->dataProviderParams = \ddTools\ObjectTools::extend([
				'objects' => [
					$this->dataProviderParams,
					(object) \ddTools::encodedStringToArray($snippetParams->dataProviderParams)
				]
			]);
		}
		
		//Prepare outputter params
		if (!empty($snippetParams->outputterParams)){
			$this->outputterParams = \ddTools\ObjectTools::extend([
				'objects' => [
					$this->outputterParams,
					(object) \ddTools::encodedStringToArray($snippetParams->outputterParams)
				]
			]);
		}
		
		//This fields are already set before
		unset($this->snippetParams->dataProviderParams);
		unset($this->snippetParams->outputterParams);
		
		//Set object properties from snippet parameters
		$this->setExistingProps($this->snippetParams);
		
		$this->prepareResourceFieldsAndAliases();
	}
	
	/**
	 * prepareResourceFieldsAndAliases
	 * @version 1.0 (2020-04-25)
	 * 
	 * @desc Split field names and aliases for data provider and outputter.
	 * 
	 * @return {void}
	 */
	private function prepareResourceFieldsAndAliases(){
		//If can be prepared (not prepared before)
		if (is_string($this->dataProviderParams->resourceFields)){
			//Если заданы псевдонимы полей (хотя бы для одного)
			if (strpos(
				$this->dataProviderParams->resourceFields,
				'='
			) !== false){
				//Разобьём поля на поля и псевдонимы
				$this->outputterParams->resourceFieldsAliases = \ddTools::explodeAssoc(
					$this->dataProviderParams->resourceFields,
					',',
					'='
				);
				
				//Полями являются ключи
				$this->dataProviderParams->resourceFields = array_keys($this->outputterParams->resourceFieldsAliases);
			}else{
				//Просто разбиваем по запятой
				$this->dataProviderParams->resourceFields = explode(
					',',
					$this->dataProviderParams->resourceFields
				);
			}
		}
	}
	
	/**
	 * paramsBackwardCompatibility
	 * @version 1.0 (2020-04-25)
	 * 
	 * @desc Prepare params preserve backward compatibility.
	 * 
	 * @return {void}
	 */
	private function paramsBackwardCompatibility(){
		//Backward compatibility first with the ancient versions first
		$snippetParamsNew = (object) \ddTools::verifyRenamedParams([
			'params' => $this->snippetParams,
			'compliance' => [
				'docId' => 'id',
				'docField' => 'field',
				'docFieldAlternative' => 'alternateField',
				'outputter' => [
				 	'result_outputFormat',
					'outputFormat',
					'format'
				],
				'result_tpl' => 'tpl',
				'result_tpl_placeholders' => [
					'tpl_placeholders',
					'placeholders'
				],
				'result_docFieldsGlue' => 'glue',
				'result_typography' => [
					'typographyResult',
					'typography',
					'typographing'
				],
				'result_escapeForJS' => [
					'escapeResultForJS',
					'escaping',
					'screening'
				],
				'result_URLEncode' => [
					'urlencodeResult',
					'urlencode'
				]
			],
			'returnCorrectedOnly' => false,
			//Without log
			'writeToLog' => false
		]);
		
		$isLogMessageNeeded = $this->snippetParams == $snippetParamsNew;
		
		$this->snippetParams = $snippetParamsNew;
		
		//Fill data provider and outputter params from old snippet params
		$compilance = [
			'dataProviderParams' => [
				'resourceId' => 'docId',
				'resourceFields' => 'docField',
				'resourceFieldsAlternative' => 'docFieldAlternative'
			],
			'outputterParams' => [
				'typography' => 'result_typography',
				'escapeForJS' => 'result_escapeForJS',
				'URLEncode' => 'result_URLEncode',
				'emptyResult' => 'result_emptyResult',
				'tpl' => 'result_tpl',
				'placeholders' => 'result_tpl_placeholders',
				'docFieldsGlue' => 'result_docFieldsGlue'
			]
		];
		
		foreach (
			$compilance as
			$propertyName =>
			$paramsCompilance
		){
			//Correct params names
			$newParams = (object) \ddTools::verifyRenamedParams([
				'params' => $this->snippetParams,
				'compliance' => $paramsCompilance,
				//Without log
				'writeToLog' => false
			]);
			
			//If something old was set
			if (!empty($newParams)){
				$isLogMessageNeeded = true;
				
				$this->{$propertyName} = \DDTools\ObjectTools::extend([
					'objects' => [
						$this->{$propertyName},
						$newParams
					]
				]);
				
				//Remove outdated snippet params
				foreach(
					$paramsCompilance as
					$oldParamName
				){
					if (property_exists(
						$this->snippetParams,
						$oldParamName
					)){
						unset($this->snippetParams->{$oldParamName});
					}
				}
			}
		}
		
		//The old alias separator
		$this->dataProviderParams->resourceFields = str_replace(
			'::',
			'=',
			$this->dataProviderParams->resourceFields
		);
		
		//Если задан устаревший параметр «$numericNames»
		if (
			isset($this->snippetParams->numericNames) &&
			$this->snippetParams->numericNames == '1'
		){
			$isLogMessageNeeded = true;
			
			$this->prepareResourceFieldsAndAliases();
			
			foreach (
				$this->dataProviderParams->resourceFields as
				$fieldNameIndex =>
				$fieldName
			){
				$this->outputterParams->resourceFieldsAliases[$fieldName] =
					'field' .
					$fieldNameIndex
				;
			}
		}
		
		if ($isLogMessageNeeded){
			\ddTools::logEvent([
				'message' => '<p>You are using one of deprecated snippet parameters.</p><p>Backward compatibility is maintained and everything is working fine right now. But we strongly recommend to stay up to date.</p><p>Please checkout documentation and fix it ASAP.</p>'
			]);
		}
	}
}