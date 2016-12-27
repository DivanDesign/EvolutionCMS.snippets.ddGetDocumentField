<?php
/** 
 * ddGetDocumentField
 * @version 2.5 (2014-06-05)
 * 
 * @desc Snippet gets the necessary document fields (and TV) by its id.
 * 
 * @uses MODXEvo.library.ddTools >= 0.12.
 * @uses MODXEvo.snippet.ddTypograph >= 2.2 (if typographing is required).
 * 
 * @param $id {integer} — Document identifier. Default: current document.
 * @param $field {string_commaSeparated} — Documents fields to get separated by commas. @required
 * @param $field[] {string|string_separated} — Fields and their aliases must be separated by “::” if aliases are required while returning the results (for example: 'pagetitle::title,content::text'). @required
 * @param $alternateField {string_commaSeparated} — Alternate fields to get if the main is empty. Default: ''.
 * @param $tpl {string_chunkName} — Chunk to parse result. Default: ''.
 * @param $glue {string} — String for join the fields. Default: ''.
 * @param $typography {0|1} — Need to typography result? Default: 0.
 * @param $outputFormat {''|'json'} — Output format. Default: ''.
 * @param $placeholders {string_separated} — Additional data to be transfered. Format: string, separated by '::' between a pair of key-value, and '||' between the pairs. Default: ''.
 * @param $mode {''|'ajax'} — Режим работы. If mode is AJAX, the id gets from the $_REQUEST array. Use the “securityFields” param! Default: ''.
 * @param $securityFields {string_separated} — The fields for security verification. Format: field:value|field:value|etc. Default: ''. 
 * @param $escaping {0|1} — Need to escape special characters from result? Default: 0.
 * @param $urlencode {0|1} — Need to URL-encode result string? Default: 0.
 * 
 * @link http://code.divandesign.biz/modx/ddgetdocumentfield/2.5
 * 
 * @copyright 2008–2014 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Подключаем modx.ddTools
require_once $modx->getConfig('base_path').'assets/snippets/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams($params, array(
	'typography' => 'typographing',
	'outputFormat' => 'format',
	'escaping' => 'screening'
)));

//Если поля передали
if (isset($field)){
	$escaping = (isset($escaping) && $escaping == '1') ? true : false;
	$urlencode = (isset($urlencode) && $urlencode == '1') ? true : false;
	$typography = (isset($typography) && $typography == '1') ? true : false;
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
			//Получаем имена полей безопасности и значения
			$securityFields = explode('|', $securityFields);
			$securityVals = array();
			
			foreach ($securityFields as $key => $val){
				$temp = explode(':', $val);
				$securityFields[$key] = $temp[0];
				$securityVals[$temp[0]] = $temp[1];
			}
			
			//Получаем значения полей безопасности у конкретного документа
			//TODO: Надо бы сделать получение полей безопасности вместе с обычными полями и последующую обработку, но пока так
			$docSecurityFields = ddTools::getTemplateVarOutput($securityFields, $id);
			
			//Если по каким-то причинам ничего не получили — прерываем
			if (
				!$docSecurityFields ||
				count($docSecurityFields) == 0
			){
				return;
			}
			
			//Перебираем полученные значения, если хоть одно не совпадает с условием — прерываем
			foreach ($docSecurityFields as $key => $val){
				if ($val != $securityVals[$key]){return;}
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
			
			$fieldAliases = array();
			
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
			$result = array();
			
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
			if (isset($placeholders)){
				$result = array_merge($result, ddTools::explodeAssoc($placeholders));
			}
			
			$resultStr = $modx->parseChunk($tpl, $result,'[+','+]');
		}else{
			//TODO: При необходимости надо будет обработать удаление пустых значений
			$resultStr = implode($glue, $result);
		}
		
		//Если нужно типографировать
		if ($typography){$resultStr = $modx->runSnippet('ddTypograph', array('text' => $resultStr));}
		
		//Если надо экранировать спец. символы
		if ($escaping){$resultStr = ddTools::screening($resultStr);}
		
		//Если нужно URL-кодировать строку
		if ($urlencode){$resultStr = rawurlencode($resultStr);}
	}
	
	return $resultStr;
}
?>