# (MODX)EvolutionCMS.snippets.ddGetDocumentField changelog


## Version 2.13 (2024-07-12)

* \+ OutputterObject → Parameters → `outputterParams->templates`: The new parameters. Allows you to use templates for some fields. Templates will be used before final conversion of results. So you don't need to care about characters escaping for JSON e. g. See README → Examples.


## Version 2.12 (2024-07-12)

* \* Parameters → `outputter` → Valid values → `'object'`: Has been renamed from `'json'` (with backward compatibility).
* \+ OutputterObject → Parameters:
	* \+ `outputterParams->format`: The new parameter. Allows:
		* \+ Return result as a native PHP object or array (it is convenient to call through `\DDTools\Snippet::runSnippet`):
			* \+ `'objectAuto'` — `stdClass` or `array` depends on result object
			* \+ `'objectStdClass'` — `stdClass`
			* \+ `'objectArray'` — `array`
		* \+ Return result as a string:
			* \+ `'stringJsonAuto'` — `stringJsonObject` or `stringJsonArray` depends on result object
			* \+ `'stringJsonObject'`
			* \+ `'stringJsonArray'`
			* \+ `'stringQueryFormatted'` — [Query string](https://en.wikipedia.org/wiki/Query_string)
			* \+ `'stringHtmlAttrs'` — HTML attributes string (e. g. `width='100' height='50'`)
* \* `\ddTools::getTpl` is used instead of `$modx->getTpl` (means a bit less bugs).
* README:
	* \* Examples: HJSON is used for all examples.
	* \+ Links → GitHub.
* \+ Composer.json:
	* \+ `support`.
	* \+ `autoload`.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.60 is required.


## Version 2.11.1 (2021-12-27)

* \* Attention! (MODX)EvolutionCMS.snippets.ddTypograph >= 2.5 is required.
* \* Fixed an error when the snippet result is empty.
* \* `\DDTools\Snippet::runSnippet` is used to run ddTypograph without DB and eval.
* \+ README → Installation → Using (MODX)EvolutionCMS.libraries.ddInstaller.


## Version 2.11 (2021-03-26)

* \* Attention! PHP >= 5.6 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.48 is required.
* \+ You can just call `\DDTools\Snippet::runSnippet` to run the snippet without DB and eval (see README → Examples).
* \+ Parameters → `dataProviderParams`, `outputterParams`, `securityFields`: [HJSON](https://hjson.github.io/) is supported.
+ `\ddGetDocumentField\Snippet`: The new class. All snippet code was moved here.
* \+ README → Links → Packagist.
* \+ CHANGELOG_ru.


## Version 2.10.5 (2020-09-29)

* \* `\ddGetDocumentField\DataProvider\Document\DataProvider::get`: Returns only existing resource fields.
* \* Refactoring (MODX)Evolution.libraries.ddTools including.


## Version 2.10.4 (2020-09-29)

* \* `\ddGetDocumentField\DataProvider\Document\DataProvider::get`: Fixed wrong outputting of alternative resource fields.
* \+ README → Links.


## Version 2.10.3 (2020-07-05)

* \* `\ddGetDocumentField\Input::paramsBackwardCompatibility`: Fixed wrong CMS event logging.


## Version 2.10.2 (2020-05-11)

* \* Snippet → Parameters → `removeEmptyFields`: Only empty strings (`''`) are considered as “empty”.
* \* Composer.json:
	* \+ `homepage`.
	* \+ `authors`.
	* \* `require`:
		* \* `dd/evolutioncms-libraries-ddtools`: Renamed from `dd/modxevo-library-ddtools`.
		* \* `dd/evolutioncms-snippets-ddtypograph`: Renamed from `dd/modxevo-snippet-ddtypograph`.


## Version 2.10.1 (2020-04-26)

* \* `\ddGetDocumentField\Outputter\Outputter::render_resourceDataApplyAliases`: Fixed working when field aliases are empty or not set for some fields.


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


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>