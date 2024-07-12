<?php
namespace ddGetDocumentField\Outputter\String;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	private
	//TODO: Use $templates instead
		$tpl = '',
		$placeholders = [],
		$docFieldsGlue = ''
	;
	
	/**
	 * __construct
	 * @version 1.0.2 (2024-07-12)
	 */
	public function __construct($params){
		$params = (object) $params;
		
		if (!empty($params->tpl)){
			$params->tpl = \ddTools::getTpl($params->tpl);
			
			$params->resourceFields = \ddTools::getPlaceholdersFromText([
				'text' => $params->tpl,
			]);
		}
		
		//Call base constructor
		parent::__construct($params);
		
		$this->placeholders = (object) $this->placeholders;
	}
	
	/**
	 * render_main
	 * @version 1.0.2 (2024-07-12)
	 * 
	 * @return {string}
	 */
	protected function render_main($resourceData){
		$result = '';
		
		//Если задан шаблон
		if (!empty($this->tpl)){
			//Если есть дополнительные данные
			if (!\ddTools::isEmpty($this->placeholders)){
				$resourceData = \DDTools\ObjectTools::extend([
					'objects' => [
						$resourceData,
						$this->placeholders,
					],
				]);
			}
			
			$result = \ddTools::parseText([
				'text' => $this->tpl,
				'data' => $resourceData,
			]);
		}else{
			//TODO: При необходимости надо будет обработать удаление пустых значений
			$result = implode(
				$this->docFieldsGlue,
				(array) $resourceData
			);
		}
		
		return $result;
	}
}