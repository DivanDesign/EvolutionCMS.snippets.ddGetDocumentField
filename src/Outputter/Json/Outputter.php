<?php
namespace ddGetDocumentField\Outputter\Json;


class Outputter extends \ddGetDocumentField\Outputter\Outputter {
	/**
	 * render_main
	 * @version 1.0 (2020-04-24)
	 * 
	 * @return {stringJsonObject}
	 */
	public function render_main($resourceData){
		return json_encode($resourceData);
	}
}