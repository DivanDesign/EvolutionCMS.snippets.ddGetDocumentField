<?php
/** 
 * ddGetDocumentField
 * @version 2.11.1 (2021-12-27)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddgetdocumentfield
 * 
 * @copyright 2008–2021 Ronef {@link https://Ronef.ru }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddGetDocumentField',
	'params' => $params
]);
?>