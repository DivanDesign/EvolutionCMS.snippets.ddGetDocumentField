<?php
/** 
 * ddGetDocumentField
 * @version 2.9 (2020-04-23)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddgetdocumentfield
 * 
 * @copyright 2008–2020 DD Group {@link http://www.DivanDesign.biz }
 */

global $modx;

$snippetPath =
	$modx->getConfig('base_path') .
	'assets/snippets/ddGetDocumentField/'
;
$snippetPath_src =
	$snippetPath .
	'src' .
	DIRECTORY_SEPARATOR
;

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

if(!class_exists('\ddGetDocumentField\Input')){
	require_once(
		$snippetPath .
		'require.php'
	);
}

//Prepare parameters
$inputObject = new \ddGetDocumentField\Input($params);

//The snippet must return an empty string even if result is absent
$snippetResult =
	isset($inputObject->outputterParams->emptyResult) ?
	$inputObject->outputterParams->emptyResult :
	''
;

//Если данные нужно получать аяксом
if (
	isset($inputObject->snippetParams->mode) &&
	strtolower($inputObject->snippetParams->mode) == 'ajax'
){
	$inputObject->dataProviderParams->resourceId = intval($_REQUEST['id']);
	
	//Если заданы поля для проверки безопасности
	if (isset($inputObject->snippetParams->securityFields)){
		//If `=` exists
		if (strpos(
			$inputObject->snippetParams->securityFields,
			'='
		) !== false){
			//Parse a query string
			parse_str(
				$inputObject->snippetParams->securityFields,
				$inputObject->snippetParams->securityFields
			);
		//Backward compatibility
		}else{
			//The old format
			$inputObject->snippetParams->securityFields = \ddTools::explodeAssoc(
				$inputObject->snippetParams->securityFields,
				'|',
				':'
			);
			$modx->logEvent(
				1,
				2,
				'<p>String separated by <code>:</code> && <code>|</code> in the <code>securityFields</code> parameter is deprecated. Use a <a href="https://en.wikipedia.org/wiki/Query_string)">query string</a>.</p><p>The snippet has been called in the document with id ' . $modx->documentIdentifier . '.</p>',
				$modx->currentSnippet
			);
		}
		
		//Получаем значения полей безопасности у конкретного документа
		//TODO: Надо бы сделать получение полей безопасности вместе с обычными полями и последующую обработку, но пока так
		$docSecurityFields = \ddTools::getTemplateVarOutput(
			array_keys($inputObject->snippetParams->securityFields),
			$inputObject->dataProviderParams->resourceId
		);
		
		//Если по каким-то причинам ничего не получили — прерываем
		if (
			!$docSecurityFields ||
			count($docSecurityFields) == 0
		){
			return $snippetResult;
		}
		
		//Перебираем полученные значения, если хоть одно не совпадает с условием — прерываем
		foreach (
			$docSecurityFields as
			$key =>
			$val
		){
			if ($val != $inputObject->snippetParams->securityFields[$key]){
				return $snippetResult;
			}
		}
	}
}

$dataProviderObject = \ddGetDocumentField\DataProvider\DataProvider::createChildInstance([
	'name' => $inputObject->dataProvider,
	'parentDir' =>
		$snippetPath_src .
		'DataProvider'
	,
	//Passing parameters into constructor
	'params' => $inputObject->dataProviderParams
]);

//Save the data provider object in outputter for possibility to add fields to get
$inputObject->outputterParams->dataProvider = $dataProviderObject;

$outputterObject = \ddGetDocumentField\Outputter\Outputter::createChildInstance([
	'name' => $inputObject->outputter,
	'parentDir' =>
		$snippetPath_src .
		'Outputter'
	,
	//Passing parameters into constructor
	'params' => $inputObject->outputterParams
]);

$snippetResult = $outputterObject->render($dataProviderObject->get());

return $snippetResult;
?>