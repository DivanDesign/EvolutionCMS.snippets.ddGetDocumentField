<?php
namespace ddGetDocumentField;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '2.11.1',
		
		$params = [
			//Defaults
			'dataProvider' => 'document',
			'dataProviderParams' => [
				'resourceFields' => []
			],
			'outputter' => 'string',
			'outputterParams' => [
				'resourceFieldsAliases' => [],
				'emptyResult' => ''
			],
			
			'mode' => '',
			'securityFields' => null
		],
		
		$paramsTypes = [
			'dataProviderParams' => 'objectStdClass',
			'outputterParams' => 'objectStdClass',
			'securityFields' => 'objectArray'
		],
		
		$renamedParamsCompliance = [
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
		]
	;
		
	/**
	 * prepareParams
	 * @version 1.0 (2021-03-25)
	 *
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 *
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		//Call base method
		parent::prepareParams($params);
		
		$this->prepareParams_backwardCompatibility();
		
		$this->params->mode = strtolower($this->params->mode);
	}
	
	/**
	 * prepareParams_backwardCompatibility
	 * @version 1.1.2 (2021-03-24)
	 * 
	 * @return {void}
	 */
	private function prepareParams_backwardCompatibility(){
		$isLogMessageNeeded = false;
		
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
				'params' => $this->params,
				'compliance' => $paramsCompilance,
				//Without log
				'writeToLog' => false
			]);
			
			//If something old was set
			if (count((array) $newParams) > 0){
				$isLogMessageNeeded = true;
				
				$this->params->{$propertyName} = \DDTools\ObjectTools::extend([
					'objects' => [
						$this->params->{$propertyName},
						$newParams
					]
				]);
				
				//Remove outdated snippet params
				foreach(
					$paramsCompilance as
					$oldParamName
				){
					if (
						property_exists(
							$this->params,
							$oldParamName
						)
					){
						unset($this->params->{$oldParamName});
					}
				}
			}
		}
		
		$this->prepareParams_resourceFieldsAndAliases();
		
		//Если задан устаревший параметр «$numericNames»
		if (
			isset($this->params->numericNames) &&
			$this->params->numericNames == '1'
		){
			$isLogMessageNeeded = true;
			
			foreach (
				$this->params->dataProviderParams->resourceFields as
				$fieldNameIndex =>
				$fieldName
			){
				$this->params->outputterParams->resourceFieldsAliases[$fieldName] =
					'field' .
					$fieldNameIndex
				;
			}
		}
		
		if (isset($this->params->securityFields)){
			$this->params->securityFields = str_replace(
				[
					'|',
					':'
				],
				[
					'||',
					'::'
				],
				$this->params->securityFields
			);
		}
		
		if ($isLogMessageNeeded){
			\ddTools::logEvent([
				'message' => '<p>You are using one of deprecated snippet parameters.</p><p>Backward compatibility is maintained and everything is working fine right now. But we strongly recommend to stay up to date.</p><p>Please checkout documentation and fix it ASAP.</p>'
			]);
		}
	}
	
	/**
	 * prepareParams_resourceFieldsAndAliases
	 * @version 1.0.1 (2021-03-24)
	 * 
	 * @desc Split field names and aliases for data provider and outputter.
	 * 
	 * @return {void}
	 */
	private function prepareParams_resourceFieldsAndAliases(){
		//If can be prepared (not prepared before)
		if (is_string($this->params->dataProviderParams->resourceFields)){
			//The old alias separator
			$this->params->dataProviderParams->resourceFields = str_replace(
				'::',
				'=',
				$this->params->dataProviderParams->resourceFields
			);
			
			//Если заданы псевдонимы полей (хотя бы для одного)
			if (
				strpos(
					$this->params->dataProviderParams->resourceFields,
					'='
				) !==
				false
			){
				//Разобьём поля на поля и псевдонимы
				$this->params->outputterParams->resourceFieldsAliases = \ddTools::explodeAssoc(
					$this->params->dataProviderParams->resourceFields,
					',',
					'='
				);
				
				//Полями являются ключи
				$this->params->dataProviderParams->resourceFields = array_keys($this->params->outputterParams->resourceFieldsAliases);
			}else{
				//Просто разбиваем по запятой
				$this->params->dataProviderParams->resourceFields = explode(
					',',
					$this->params->dataProviderParams->resourceFields
				);
			}
		}
	}
	
	/**
	 * run
	 * @version 1.0 (2021-03-23)
	 * 
	 * @return {string}
	 */
	public function run(){
		//The snippet must return an empty string even if result is absent
		$result = $this->params->outputterParams->emptyResult;
		
		//Если данные нужно получать аяксом
		if ($this->params->mode == 'ajax'){
			$this->params->dataProviderParams->resourceId = intval($_REQUEST['id']);
			
			//Если заданы поля для проверки безопасности
			if (!empty($this->params->securityFields)){
				//Получаем значения полей безопасности у конкретного документа
				//TODO: Надо бы сделать получение полей безопасности вместе с обычными полями и последующую обработку, но пока так
				$docSecurityFields = \ddTools::getTemplateVarOutput(
					array_keys($this->params->securityFields),
					$this->params->dataProviderParams->resourceId
				);
				
				//Если по каким-то причинам ничего не получили — прерываем
				if (
					!$docSecurityFields ||
					count($docSecurityFields) == 0
				){
					return $result;
				}
				
				//Перебираем полученные значения, если хоть одно не совпадает с условием — прерываем
				foreach (
					$docSecurityFields as
					$key =>
					$val
				){
					if ($val != $this->params->securityFields[$key]){
						return $result;
					}
				}
			}
		}
		
		$dataProviderObject = \ddGetDocumentField\DataProvider\DataProvider::createChildInstance([
			'name' => $this->params->dataProvider,
			'parentDir' =>
				$this->paths->src .
				'DataProvider'
			,
			//Passing parameters into constructor
			'params' => $this->params->dataProviderParams
		]);
		
		//Save the data provider object in outputter for possibility to add fields to get
		$this->params->outputterParams->dataProvider = $dataProviderObject;
		
		$outputterObject = \ddGetDocumentField\Outputter\Outputter::createChildInstance([
			'name' => $this->params->outputter,
			'parentDir' =>
				$this->paths->src .
				'Outputter'
			,
			//Passing parameters into constructor
			'params' => $this->params->outputterParams
		]);
		
		$result = $outputterObject->render($dataProviderObject->get());
		
		return $result;
	}
}