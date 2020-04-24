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

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//Output format
$outputter =
	isset($outputter) ?
	strtolower($outputter) :
	'string'
;
$outputterParams =
	isset($outputterParams) ?
	$outputterParams :
	''
;
//Prepare outputter params
$outputterParams = (object) \ddTools::encodedStringToArray($outputterParams);


//Backward compatibility
extract(\ddTools::verifyRenamedParams(
	$params,
	[
		'docId' => 'id',
		'docField' => 'field',
		'docFieldAlternative' => 'alternateField',
		'result_outputFormat' => [
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
));
if (isset($result_outputFormat)){
	$outputter = strtolower($result_outputFormat);
}
if (isset($result_typography)){
	$outputterParams->typography = $result_typography;
}
if (isset($result_escapeForJS)){
	$outputterParams->escapeForJS = $result_escapeForJS;
}
if (isset($result_URLEncode)){
	$outputterParams->URLEncode = $result_URLEncode;
}
if (isset($result_emptyResult)){
	$outputterParams->emptyResult = $result_emptyResult;
}
if (isset($result_tpl)){
	$outputterParams->tpl = $result_tpl;
}
if (isset($result_tpl_placeholders)){
	$outputterParams->placeholders = $result_tpl_placeholders;
}
if (isset($result_docFieldsGlue)){
	$outputterParams->docFieldsGlue = $result_docFieldsGlue;
}


$snippetResult =
	isset($outputterParams->emptyResult) ?
	$outputterParams->emptyResult :
	//The snippet must return an empty string even if result is absent
	''
;

$outputterParams->tpl =
	isset($outputterParams->tpl) ?
	$modx->getTpl($outputterParams->tpl) :
	''
;

//If document fields is not set try to get they from template
if (
	!isset($docField) &&
	!empty($outputterParams->tpl)
){
	$docFieldsFromTpl = \ddTools::getPlaceholdersFromText([
		'text' => $outputterParams->tpl
	]);
	
	if (!empty($docFieldsFromTpl)){
		$docField = implode(
			',',
			$docFieldsFromTpl
		);
	}
}

//Если поля передали
if (isset($docField)){
	$outputterParams->escapeForJS =
		(
			isset($outputterParams->escapeForJS) &&
			$outputterParams->escapeForJS == '1'
		) ?
		true :
		false
	;
	$outputterParams->URLEncode =
		(
			isset($outputterParams->URLEncode) &&
			$outputterParams->URLEncode == '1'
		) ?
		true :
		false
	;
	$outputterParams->typography =
		(
			isset($outputterParams->typography) &&
			$outputterParams->typography == '1'
		) ?
		true :
		false
	;
	$outputterParams->docFieldsGlue =
		isset($outputterParams->docFieldsGlue) ?
		$outputterParams->docFieldsGlue :
		''
	;
	
	//Если данные нужно получать аяксом
	if (
		isset($mode) &&
		strtolower($mode) == 'ajax'
	){
		$docId = intval($_REQUEST['id']);
		
		//Если заданы поля для проверки безопасности
		if (isset($securityFields)){
			//If `=` exists
			if (strpos(
				$securityFields,
				'='
			) !== false){
				//Parse a query string
				parse_str(
					$securityFields,
					$securityFields
				);
			//Backward compatibility
			}else{
				//The old format
				$securityFields = \ddTools::explodeAssoc(
					$securityFields,
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
				array_keys($securityFields),
				$docId
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
				if ($val != $securityFields[$key]){
					return $snippetResult;
				}
			}
		}
	}else{
		$docId =
			(
				isset($docId) &&
				is_numeric($docId)
			) ?
			$docId :
			$modx->documentIdentifier
		;
	}
	
	//Никаких псевдонимов полей по умолчанию
	$docFieldAliases = false;
	
	//Backward compatibility
	$docField = str_replace(
		'::',
		'=',
		$docField
	);
	
	//Если заданы псевдонимы полей (хотя бы для одного)
	if (strpos(
		$docField,
		'='
	) !== false){
		//Разобьём поля на поля и псевдонимы
		$docFieldAliases = \ddTools::explodeAssoc(
			$docField,
			',',
			'='
		);
		
		//Полями являются ключи
		$docField = array_keys($docFieldAliases);
	}else{
		//Просто разбиваем по запятой
		$docField = explode(
			',',
			$docField
		);
		
		//Backward compatibility
		//Если задан устаревший параметр «$numericNames»
		if (
			isset($numericNames) &&
			$numericNames == '1'
		){
			//Ругаемся
			$modx->logEvent(
				1,
				2,
				'<p>The <code>numericNames</code> parameter is deprecated. You can pass aliases inside of the <code>docField</code> parameter instead.</p><p>The snippet has been called in the document with id ' . $modx->documentIdentifier . '.</p>',
				$modx->currentSnippet
			);
			
			$docFieldAliases = [];
			
			foreach (
				$docField as
				$key =>
				$val
			){
				$docFieldAliases[$val] =
					'field' .
					$key
				;
			}
		}
	}
	
	//Если вдруг передали, что надо получить id
	if (
		($docField_idInd = array_search(
			'id',
			$docField
		)) !== false
	){
		//Удалим его, чтобы наличие результата от него не зависило (id ж ведь всегда есть)
		unset($docField[$docField_idInd]);
	}
	
	//Получаем все необходимые поля
	$result = \ddTools::getTemplateVarOutput(
		$docField,
		$docId
	);
	
	//Если по каким-то причинам ничего не получилось — прерываем
	if (!$result){
		return $snippetResult;
	}
	
	//Если заданы альтернативные поля
	//TODO: Можно переделать на получение альтернативных полей сразу с основными, а потом обрабатывать, но как-то не судьба пока
	if (isset($docFieldAlternative)){
		$docFieldAlternative = explode(
			',',
			$docFieldAlternative
		);
		$alter = \ddTools::getTemplateVarOutput(
			$docFieldAlternative,
			$docId
		);
	}
	
	$isEmptySnippetResult = true;
	
	//Перебираем полученные результаты, заполняем пустоту альтернативой, если есть
	foreach (
		$result as
		$key =>
		$value
	){
		if (
			//Если значение поля не пустое
			$result[$key] != '' ||
			//Либо пустое
			(
				//Но имеется альтернатива
				isset($docFieldAlternative) &&
				//И альтернатива не пуста
				($result[$key] = $alter[$docFieldAlternative[array_search(
					$key,
					$docField
				)]]) != ''
			)
		){
			$isEmptySnippetResult = false;
		}
	}
	
	//Если результаты непустые
	if (!$isEmptySnippetResult){
		if (
			//Если надо было вернуть ещё и url документа
			array_search(
				'url',
				$docField
			) !== false &&
			//И если такого поля нет или оно пусто (а то мало ли, как TV назвали)
			(
				!isset($result['url']) ||
				trim($result['url']) == ''
			)
		){
			$result['url'] = $modx->makeUrl($docId);
		}
		
		//Если в поля передавали id, выведем
		if ($docField_idInd !== false){
			$result['id'] = $docId;
		}
		
		//Если заданы псевдонимы полей
		if ($docFieldAliases){
			//Запоминаем полученный результат
			$oldResult = $result;
			
			//Затираем
			$result = [];
			
			//Перебираем псевдонимы
			foreach (
				$docFieldAliases as
				$fld =>
				$alias
			){
				//Если псевдоним не задан, пусть будет поле
				if (trim($alias) == ''){
					$alias = $fld;
				}
				
				//Фигачим
				$result[$alias] = $oldResult[$fld];
			}
		}
		
		//Если вывод в формате JSON
		if ($outputter == 'json'){
			$snippetResult = json_encode($result);
		//Если задан шаблон
		}else if (!empty($outputterParams->tpl)){
			//Если есть дополнительные данные
			if (
				isset($outputterParams->placeholders) &&
				trim($outputterParams->placeholders) != ''
			){
				$result = array_merge(
					$result,
					\ddTools::encodedStringToArray($outputterParams->placeholders)
				);
			}
			
			$snippetResult = \ddTools::parseText([
				'text' => $outputterParams->tpl,
				'data' => $result
			]);
		}else{
			//TODO: При необходимости надо будет обработать удаление пустых значений
			$snippetResult = implode(
				$outputterParams->docFieldsGlue,
				$result
			);
		}
		
		//Если нужно типографировать
		if ($outputterParams->typography){
			$snippetResult = $modx->runSnippet(
				'ddTypograph',
				[
					'text' => $snippetResult
				]
			);
		}
		
		//Если надо экранировать спец. символы
		if ($outputterParams->escapeForJS){
			$snippetResult = \ddTools::escapeForJS($snippetResult);
		}
		
		//Если нужно URL-кодировать строку
		if ($outputterParams->URLEncode){
			$snippetResult = rawurlencode($snippetResult);
		}
	}
}

return $snippetResult;
?>