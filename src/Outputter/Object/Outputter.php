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
		
		return $resourceData;
	}
	
	/**
	 * render_finish
	 * @version 1.0 (2024-07-13)
	 * 
	 * @param $result {string|\stdClass|arrayAssociative}
	 * 
	 * @return {string|\stdClass|arrayAssociative}
	 */
	protected function render_finish($result){
		$result = \DDTools\ObjectTools::convertType([
			'object' => $result,
			'type' => $this->format,
		]);
		
		return parent::render_finish($result);
	}
}