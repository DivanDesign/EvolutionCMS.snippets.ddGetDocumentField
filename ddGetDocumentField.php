<?php
/** 
 * ddGetDocumentField
 * @version 2.5 (2014-06-05)
 * 
 * @desc Snippet gets the necessary document fields (and TV) by its id.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo >= 1.1.
 * @uses MODXEvo.library.ddTools >= 0.16.2.
 * @uses MODXEvo.snippet.ddTypograph >= 2.2 (if typographing is required).
 * 
 * @param $id {integer} — Document identifier. Default: current document.
 * @param $field {string_commaSeparated} — Documents fields to get separated by commas. @required
 * @param $field[] {string|string_separated} — Fields and their aliases must be separated by “::” if aliases are required while returning the results (for example: 'pagetitle::title,content::text'). @required
 * @param $alternateField {string_commaSeparated} — Alternate fields to get if the main is empty. Default: ''.
 * @param $tpl {string_chunkName|string} — Chunk to parse result (chunk name or code via “@CODE:” prefix). Default: ''.
 * @param $tpl_placeholders {string_queryFormated} — Additional data as query string (https://en.wikipedia.org/wiki/Query_string) has to be passed into “tpl”. E. g. “pladeholder1=value1&pagetitle=My awesome pagetitle!”. Default: ''.
 * @param $glue {string} — String for join the fields. Default: ''.
 * @param $outputFormat {''|'json'} — Output format. Default: ''.
 * @param $mode {''|'ajax'} — Режим работы. If mode is AJAX, the id gets from the $_REQUEST array. Use the “securityFields” param! Default: ''.
 * @param $securityFields {string_queryFormated} — The fields for security verification as query string (https://en.wikipedia.org/wiki/Query_string). E. g.: “template=15&published=1”. Default: ''. 
 * @param $typographyResult {0|1} — Need to typography result? Default: 0.
 * @param $escapeResultForJS {0|1} — Need to escape special characters from result? Default: 0.
 * @param $urlencodeResult {0|1} — Need to URL-encode result string? Default: 0.
 * 
 * @link http://code.divandesign.biz/modx/ddgetdocumentfield/2.5
 * 
 * @copyright 2008–2014 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Подключаем modx.ddTools
require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams($params, [
	'tpl_placeholders' => 'placeholders',
	'typographyResult' => ['typography', 'typographing'],
	'outputFormat' => 'format',
	'escapeResultForJS' => ['escaping', 'screening'],
	'urlencodeResult' => 'urlencode'
]));

//Если поля передали
if (isset($field)){
	$escapeResultForJS = (isset($escapeResultForJS) && $escapeResultForJS == '1') ? true : false;
	$urlencodeResult = (isset($urlencodeResult) && $urlencodeResult == '1') ? true : false;
	$typographyResult = (isset($typographyResult) && $typographyResult == '1') ? true : false;
	$glue = isset($glue) ? $glue : '';
	$outputFormat = isset($outputFormat) ? strtolower($outputFormat) : '';
	
	//Если данные нужно получать аяксом
	if (
		isset($mode) &&
		strtolower($mode) == 'ajax'
	){
		$id = $_REQUEST['id'];
		
		//Если заданы поля для проверки безопасности
		if (isset($securityFields)){
			//Backward compatibility
			//If “=” exists
			if (strpos($securityFields, '=') !== false){
				//Parse a query string
				parse_str($securityFields, $securityFields);
			}else{
				//The old format
				$securityFields = ddTools::explodeAssoc($securityFields, '|', ':');
				$modx->logEvent(1, 2, '<p>String separated by “:” && “|” in the “securityFields” parameter is deprecated. Use a <a href="https://en.wikipedia.org/wiki/Query_string)">query string</a>.</p><p>The snippet has been called in the document with id '.$modx->documentIdentifier.'.</p>', $modx->currentSnippet);
			}
			
			//Получаем значения полей безопасности у конкретного документа
			//TODO: Надо бы сделать получение полей безопасности вместе с обычными полями и последующую обработку, но пока так
			$docSecurityFields = ddTools::getTemplateVarOutput(array_keys($securityFields), $id);
			
			//Если по каким-то причинам ничего не получили — прерываем
			if (
				!$docSecurityFields ||
				count($docSecurityFields) == 0
			){
				return;
			}
			
			//Перебираем полученные значения, если хоть одно не совпадает с условием — прерываем
			foreach ($docSecurityFields as $key => $val){
				if ($val != $securityFields[$key]){return;}
			}
		}
	}else{
		$id = (isset($id) && is_numeric($id)) ? $id : $modx->documentIdentifier;
	}
	
	//Никаких псевдонимов полей по умолчанию
	$fieldAliases = false;
	
	//Если заданы псевдонимы полей (хотя бы для одного)
	if (strpos($field, '::') !== false){
		//Разобьём поля на поля и псевдонимы
		$fieldAliases = ddTools::explodeAssoc($field, ',');
		
		//Полями являются ключи
		$field = array_keys($fieldAliases);
	}else{
		//Просто разбиваем по запятой
		$field = explode(',', $field);
		
		//For backward compatibility
		//Если задан устаревший параметр «$numericNames»
		if (
			isset($numericNames) &&
			$numericNames == '1'
		){
			//Ругаемся
			$modx->logEvent(1, 2, '<p>The “numericNames” parameter is deprecated. You can pass aliases inside of the “field” parameter instead.</p><p>The snippet has been called in the document with id '.$modx->documentIdentifier.'.</p>', $modx->currentSnippet);
			
			$fieldAliases = [];
			
			foreach ($field as $key => $val){
				$fieldAliases[$val] = 'field'.$key;
			}
		}
	}
	
	//Если вдруг передали, что надо получить id
	if (($field_idInd = array_search('id', $field)) !== false){
		//Удалим его, чтобы наличие результата от него не зависило (id ж ведь всегда есть)
		unset($field[$field_idInd]);
	}
	
	//Получаем все необходимые поля
	$result = ddTools::getTemplateVarOutput($field, $id);
	
	//Если по каким-то причинам ничего не получилось — прерываем
	if (!$result){return;}
	
	//Если заданы альтернативные поля
	//TODO: Можно переделать на получение альтернативных полей сразу с основными, а потом обрабатывать, но как-то не судьба пока
	if (isset($alternateField)){
		$alternateField = explode(',', $alternateField);
		$alter = ddTools::getTemplateVarOutput($alternateField, $id);
	}
	
	$resultStr = '';
	$emptyResult = true;
	
	//Перебираем полученные результаты
	foreach ($result as $key => $value){
		//Если значение поля пустое, пытаемся получить альтернативное поле (и сразу присваиваем) и если оно НЕ пустое, запомним 
		if (
			($result[$key] != '') ||
			isset($alternateField) && (($result[$key] = $alter[$alternateField[array_search($key, $field)]]) != '')
		){
			$emptyResult = false;
		}
	}
	
	//Если результаты непустые
	if (!$emptyResult){
		//Если надо было вернуть ещё и url документа и Если такого поля нет или оно пусто (а то мало ли, как TV назвали)
		if (
			array_search('url', $field) !== false &&
			(
				!isset($result['url']) ||
				trim($result['url']) == ''
			)
		){
			$result['url'] = $modx->makeUrl($id);
		}
		
		//Если в поля передавали id, выведем
		if ($field_idInd !== false){
			$result['id'] = $id;
		}
		
		//Если заданы псевдонимы полей
		if ($fieldAliases){
			//Запоминаем полученный результат
			$oldResult = $result;
			
			//Затираем
			$result = [];
			
			//Перебираем псевдонимы
			foreach ($fieldAliases as $fld => $alias){
				//Если псевдоним не задан, пусть будет поле
				if (trim($alias) == ''){
					$alias = $fld;
				}
				
				//Фигачим
				$result[$alias] = $oldResult[$fld];
			}
		}
		
		//Если вывод в формате JSON
		if ($outputFormat == 'json'){
			$resultStr = json_encode($result);
		//Если задан шаблон
		}else if (isset($tpl)){
			//Если есть дополнительные данные
			if (
				isset($tpl_placeholders) &&
				trim($tpl_placeholders) != ''
			){
				//Backward compatibility
				//If “=” exists
				if (strpos($tpl_placeholders, '=') !== false){
					//Parse a query string
					parse_str($tpl_placeholders, $tpl_placeholders);
				}else{
					//The old format
					$tpl_placeholders = ddTools::explodeAssoc($tpl_placeholders);
					$modx->logEvent(1, 2, '<p>String separated by “::” && “||” in the “tpl_placeholders” parameter is deprecated. Use a <a href="https://en.wikipedia.org/wiki/Query_string)">query string</a>.</p><p>The snippet has been called in the document with id '.$modx->documentIdentifier.'.</p>', $modx->currentSnippet);
				}
				
				$result = array_merge($result, $tpl_placeholders);
			}
			
			$resultStr = ddTools::parseText([
				'text' => $modx->getTpl($tpl),
				'data' => $result
			]);
		}else{
			//TODO: При необходимости надо будет обработать удаление пустых значений
			$resultStr = implode($glue, $result);
		}
		
		//Если нужно типографировать
		if ($typographyResult){$resultStr = $modx->runSnippet('ddTypograph', ['text' => $resultStr]);}
		
		//Если надо экранировать спец. символы
		if ($escapeResultForJS){$resultStr = ddTools::escapeForJS($resultStr);}
		
		//Если нужно URL-кодировать строку
		if ($urlencodeResult){$resultStr = rawurlencode($resultStr);}
	}
	
	return $resultStr;
}
?>