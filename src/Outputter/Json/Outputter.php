<?php
namespace ddGetDocumentField\Outputter\Json;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	private
		$removeEmptyFields = false
	;
	
	/**
	 * render_main
	 * @version 1.2 (2020-04-25)
	 * 
	 * @return {stringJsonObject}
	 */
	public function render_main($resourceData){
		//Remove resource fields with empty values
		if ($this->removeEmptyFields){
			foreach (
				$resourceData as
				$fieldName =>
				$fieldValue
			){
				if (empty($fieldValue)){
					unset($resourceData->{$fieldName});
				}
			}
		}
		
		return json_encode(
			$resourceData,
			//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode | JSON_UNESCAPED_SLASHES — Не экранировать /
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	}
}