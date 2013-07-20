<?php
/** 
 * ddGetDocumentField.php
 * @version 2.4 (2013-03-21)
 * 
 * Snippet gets the necessary document fields (and TV) by its id.
 * 
 * @uses Library modx.ddTools 0.6.1.
 * @uses Snippet ddTypograph 1.4.1 (if need to typography).
 * 
 * @param id {integer} - Document identifier. Default: current document.
 * @param field {comma separated string; separated string} - Documents fields to get separated by commas. Fields and their aliases must be separated by «::» if aliases are required while returning the results (for example: 'pagetitle::title,content::text'). See the examples below. @required
 * @param alternateField {comma separated string} - Alternate fields to get if the main is empty. Default: ''.
 * @param numericNames {0; 1} - Field names (placeholder names) to numeric names (like 'field0', 'field1', etc) into chunk “tpl”. Default: 0.
 * @param typographing {0; 1} - Need to typography result? Default: 0.
 * @param screening {0; 1} - Need to escape special characters from result? Default: 0.
 * @param urlencode {0; 1} - Need to URL-encode result string? Default: 0.
 * @param tpl {string: chunkName} - Chunk to parse result. Default: ''.
 * @param glue {string} - String for join the fields. Default: ''.
 * @param format {''; 'JSON'} - Output format. Default: ''.
 * @param placeholders {separated string} - Additional data to be transfered. Format: string, separated by '::' between a pair of key-value, and '||' between the pairs. Default: ''.
 * @param mode {''; 'ajax'} - Режим работы. If mode is AJAX, the id gets from the $_REQUEST array. Use the “securityFields” param! Default: ''.
 * @param securityFields {separated string} - The fields for security verification. Format: field:value|field:value|etc. Default: ''. 
 * 
 * @link http://code.divandesign.biz/modx/ddgetdocumentfield/2.4
 * 
 * @copyright 2013, DivanDesign
 * http://www.DivanDesign.biz
 */

//Подключаем modx.ddTools
require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

//Если поля передали
if (isset($field)){
	$numericNames = ($numericNames == '1') ? true : false;
	$screening = ($screening == '1') ? true : false;
	$urlencode = ($urlencode == '1') ? true : false;
	$typographing = ($typographing == '1') ? true : false;
	$glue = isset($glue) ? $glue : '';
	$format = isset($format) ? $format : '';

	//Если данные нужно получать аяксом
	if ($mode == 'ajax'){
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
			
			//Если по каким-то причинам ничего не получили, нахуй с пляжу
			if (!$docSecurityFields || count($docSecurityFields) == 0) return;
			
			//Перебираем полученные значения, если хоть одно не совпадает с условием, выкидываем
			foreach ($docSecurityFields as $key => $val){
				if ($val != $securityVals[$key]) return;
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
	}
	
	//Если вдруг передали, что надо получить id
	if (($field_idInd = array_search('id', $field)) !== false){
		//Удалим его, чтобы наличие результата от него не зависило (id ж ведь всегда есть)
		unset($field[$field_idInd]);
	}
	
	//Получаем все необходимые поля
	$result = ddTools::getTemplateVarOutput($field, $id);
	
	//Если по каким-то причинам ничего не получилось, с пляжу
	if (!$result) return;
	
	//Если заданы альтернативные поля
	//TODO: Можно переделать на получение альтернативных полей сразу с основными, а потом обрабатывать, но как-то не судьба пока
	if (isset($alternateField)){
		$alternateField = explode(',', $alternateField);
		$alter = ddTools::getTemplateVarOutput($alternateField, $id);
	}
	
	$resultStr = ''; $emptyResult = true; $i = 0;

	//Перебираем полученные результаты
	foreach ($result as $key => $value){
		//Если значение поля пустое, пытаемся получить альтернативное поле (и сразу присваиваем) и если оно НЕ пустое, запомним 
		if (($result[$key] != '') || isset($alternateField) && (($result[$key] = $alter[$alternateField[array_search($key, $field)]]) != '')){
			$emptyResult = false;
		}
		
		//Если имена полей надо преобразовывать в цифровые
		if ($numericNames){
			//Запоминаем имя по номеру
			$result['field'.$i] = $result[$key];
			//Убиваем старое (ибо зачем нам дубликаты?)
			unset($result[$key]);
		}
		
		$i++;
	}
	
	//Если результаты непустые
	if (!$emptyResult){
		//Если надо было вернуть ещё и url документа и Если такого поля нет или оно пусто (а то мало ли, как TV назвали)
		if (array_search('url', $field) !== false && (!isset($result['url']) || trim($result['url']) == '')){
			$result['url'] = $modx->makeUrl($id);
		}
		
		//Если в поля передавали id, выведем
		if ($field_idInd !== false){
			$result['id'] = $id;
		}
		
		//Если заданы псевдонимы полей и имена полей не являются порядковыми номерами (вообще, это можно было сделать в цикле выше, но не стоит, т.к. псевдонимы одних полей могут пересекаться с нормальными именами других полей, так что лучше здесь)
		if ($fieldAliases && !$numericNames){
			//Запоминаем полученный результат
			$oldResult = $result;
			
			//Затираем
			$result = array();
			
			//Перебираем псевдонимы
			foreach($fieldAliases as $fld => $alias){
				//Если псевдоним не задан, пусть будет поле
				if (trim($alias) == ''){
					$alias = $fld;
				}
				
				//Фигачим
				$result[$alias] = $oldResult[$fld];
			}
		}
		
		//Если вывод в формате JSON
		if ($format == 'JSON'){
			$resultStr = json_encode($result);
		//Если задан шаблон
		}else if (isset($tpl)){
			//Если есть дополнительные данные
			if (isset($placeholders)){
				//Разбиваем по парам
				$placeholders = explode('||', $placeholders);
				foreach ($placeholders as $val){
					//Разбиваем на ключ-значение
					$val = explode('::', $val);
					$result[$val[0]] = $val[1];
				}
			}
			
			$resultStr = $modx->parseChunk($tpl, $result,'[+','+]');
		}else{
			//TODO: При необходимости надо будет обработать удаление пустых значений
			$resultStr = implode($glue, $result);
		}
			
		//Если нужно типографировать
		if ($typographing) $resultStr = $modx->runSnippet('ddTypograph', array('text' => $resultStr));
		
		//Если надо экранировать спец. символы
		if ($screening)	$resultStr = ddTools::screening($resultStr);
		
		//Если нужно URL-кодировать строку
		if ($urlencode) $resultStr = rawurlencode($resultStr);
	}
	
	return $resultStr;
}
?>