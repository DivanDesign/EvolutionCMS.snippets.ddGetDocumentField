<?php
namespace ddGetDocumentField\Outputter\Object;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	private
		$removeEmptyFields = false,
		$format = 'stringJsonAuto'
	;
	
	/**
	 * render_main
	 * @version 1.4 (2024-07-12)
	 * 
	 * @return {stringJsonObject}
	 */
	public function render_main($resourceData){
		//If we need to prepare some fields
		if (
			$this->removeEmptyFields
			|| !\ddTools::isEmpty($this->templates)
		){
			foreach (
				$resourceData
				as $fieldName
				=> $fieldValue
			){
				//Remove resource fields with empty values
				if (
					$this->removeEmptyFields
					&& $fieldValue == ''
				){
					unset($resourceData->{$fieldName});
				//If template for this field is set
				}elseif (
					\DDTools\ObjectTools::isPropExists([
						'object' => $this->templates,
						'propName' => $fieldName
					])
				){
					$resourceData->{$fieldName} = \ddTools::parseText([
						'text' => $this->templates->{$fieldName},
						'data' => \DDTools\ObjectTools::extend([
							'objects' => [
								$resourceData,
								[
									'value' => $fieldValue
								]
							]
						])
					]);
				}
			}
		}
		
		return \DDTools\ObjectTools::convertType([
			'object' => $resourceData,
			'type' => $this->format,
		]);
	}
}