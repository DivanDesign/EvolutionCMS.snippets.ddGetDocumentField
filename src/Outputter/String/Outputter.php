<?php
namespace ddGetDocumentField\Outputter\String;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	private
	// TODO: Use $templates instead
		$tpl = '',
		$placeholders = [],
		$docFieldsGlue = ''
	;
	
	protected
		$removeEmptyFields = true
	;
	
	/**
	 * __construct
	 * @version 1.0.3 (2024-08-06)
	 */
	public function __construct($params){
		$params = (object) $params;
		
		if (!empty($params->tpl)){
			$params->tpl = \ddTools::getTpl($params->tpl);
			
			$params->resourceFields = \ddTools::getPlaceholdersFromText([
				'text' => $params->tpl,
			]);
		}
		
		// Call base constructor
		parent::__construct($params);
		
		$this->placeholders = (object) $this->placeholders;
	}
	
	/**
	 * render_main
	 * @version 1.0.4 (2024-08-06)
	 * 
	 * @return {string}
	 */
	protected function render_main($resourceData){
		$result = '';
		
		// Если задан шаблон
		if (!empty($this->tpl)){
			// Если есть дополнительные данные
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
			$result = implode(
				$this->docFieldsGlue,
				(array) $resourceData
			);
		}
		
		return $result;
	}
}