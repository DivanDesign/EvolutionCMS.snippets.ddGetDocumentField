# (MODX)EvolutionCMS.snippets.ddGetDocumentField changelog


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