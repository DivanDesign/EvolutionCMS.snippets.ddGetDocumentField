# (MODX)EvolutionCMS.snippets.ddGetDocumentField changelog


## Version 2.10 (2020-04-25)
* \* Attention! EvolutionCMS.libraries.ddTools >= 0.32 is required.
* \* The snippet structure completely revised (with backward compatibility), see README.md.
* \* Snippet: The following parameters were renamed (with backward compatibility):
	* \* `docId` → `dataProviderParams->resourceId`.
	* \* `docField` → `dataProviderParams->resourceFields`.
	* \* `docFieldAlternative` → `dataProviderParams->resourceFieldsAlternative`.
	* \* `result_outputFormat` → `outputter`.
	* \* `result_typography` → `outputterParams->typography`.
	* \* `result_escapeForJS` → `outputterParams->escapeForJS`.
	* \* `result_URLEncode` → `outputterParams->URLEncode`.
	* \* `result_emptyResult` → `outputterParams->emptyResult`.
	* \* `result_tpl` → `outputterParams->tpl`.
	* \* `result_tpl_placeholders` → `outputterParams->placeholders`.
	* \* `result_docFieldsGlue` → `outputterParams->docFieldsGlue`.
* \+ Snippet → Parameters → `securityFields`: Can be set as `stringJsonObject` too.
* \* `\ddGetDocumentField\Outputter\Json\Outputter`:
	* \+ Added an ability to remove resource fields with empty values from result (see `outputterParams->removeEmptyFields`).
	* \* `render_main`: Added the `JSON_UNESCAPED_UNICODE` and `JSON_UNESCAPED_SLASHES` flags.
* \+ README → Examples.


## Version 2.9 (2020-04-23)
* \+ Parameters → `result_emptyResult`. What will be returned if the snippet result is empty?
* \* Parameters → `docField[i]`: Separator between field name and alias changed from `'::'` to `'='` (with backward compatibility).
* \* Improved messages style for CMS event log.
* \+ README.
* \+ CHANGELOG.
* \+ Composer.json.


## Version 2.8 (2018-12-26)
* \* Attention! MODXEvo.library.ddTools >= 0.20 is required.
* \* The following parameters were renamed (with backward compatibility):
	* \* `id` → `docId`.
	* \* `field` → `docField`.
	* \* `alternateField` → `docFieldAlternative`.
	* \* `tpl` → `result_tpl`.
	* \* `placeholders` → `result_tpl_placeholders`.
	* \* `glue` → `result_docFieldsGlue`.
	* \* `outputFormat` → `result_outputFormat`.
	* \* `typographyResult` → `result_typography`.
	* \* `escapeResultForJS` → `result_escapeForJS`.
	* \* `urlencodeResult` → `result_URLEncode`.
* \+ The `docField` parameter is not required anymore. If the parameter is empty, the snippet will try to search fields in `result_tpl` (something like `[+docField+]`).
* \* The snippet result will be returned in anyway (empty string for empty result).


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />
<style>ul{list-style:none;}</style>