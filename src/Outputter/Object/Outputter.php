<?php
namespace ddGetDocumentField\Outputter\Object;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	private
		$removeEmptyFields = false,
		$format = 'stringJsonAuto'
	;
	
	/**
	 * render_main
	 * @version 1.3 (2024-07-12)
	 * 
	 * @return {stringJsonObject}
	 */
	public function render_main($resourceData){
		//Remove resource fields with empty values
		if ($this->removeEmptyFields){
			foreach (
				$resourceData
				as $fieldName
				=> $fieldValue
			){
				if ($fieldValue == ''){
					unset($resourceData->{$fieldName});
				}
			}
		}
		
		return \DDTools\ObjectTools::convertType([
			'object' => $resourceData,
			'type' => $this->format,
		]);
	}
}