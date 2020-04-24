# (MODX)EvolutionCMS.snippets.ddGetDocumentField

Snippet gets the necessary document fields (and TVs) by its id.


## Requires

* PHP >= 5.4
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.20
* [(MODX)EvolutionCMS.snippets.ddTypograph](https://code.divandesign.biz/modx/ddtypograph) >= 2.3 (if typography is required)


## Documentation


### Installation

Elements → Snippets: Create a new snippet with the following data:
1. Snippet name: `ddGetDocumentField`.
2. Description: `<b>2.9</b> Snippet gets the necessary document fields (and TVs) by its id.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetDocumentField_snippet.php` file from the archive.


### Parameters description


#### Data provider parameters

* `docId`
	* Desctription: Document identifier.
	* Valid values: `integer`
	* Default value: `$modx->documentIdentifier` (current document)
	
* `docField`
	* Desctription: Document field(s) to get separated by commas.
		
		If the parameter is empty, the snippet will try to search fields in `result_tpl` (something like `[+docField+]`).
		
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `docField[i]`
	* Desctription: Fields and their aliases must be separated by `'='` if aliases are required while returning the results (for example: `'pagetitle=title,content=text'`).
	* Valid values:
		* `string` — document field
		* `stringSeparated` — document field and it's alias
	* **Required**
	
* `docFieldAlternative`
	* Desctription: Alternate field(s) to get if the main is empty.
	* Valid values: `stringCommaSeparated`
	* Default value: —


#### Output format parameters

* `outputter`
	* Desctription: Format of the output.
	* Valid values:
		* `'string'`
		* `'json'`
	* Default value: `'string'`
	
* `outputterParams`
	* Desctription: Parameters to be passed to the specified outputter.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —
	
* `outputterParams->typography`
	* Desctription: Need to typography result?
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `outputterParams->escapeForJS`
	* Desctription: Need to escape special characters from result?
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `outputterParams->URLEncode`
	* Desctription: Need to URL-encode result string?
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `outputterParams->emptyResult`
	* Desctription: What will be returned if the snippet result is empty?
	* Valid values: `string`
	* Default value: `''`


##### Outputter → String (``&outputter=`string` ``)

* `outputterParams->tpl`
	* Desctription: Chunk to parse result.
		
		Available placeholders:
		* `[+anyNameFromDocFieldParameter+]` — Any document field (or TV).
		* `[+url+]` — Document URL.
		
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `outputterParams->placeholders`
	* Desctription: Additional data has to be passed into `result_tpl`.
		
		Arrays are supported too: `some[a]=one&some[b]=two` => `[+some.a+]`, `[+some.b+]`; `some[]=one&some[]=two` => `[+some.0+]`, `[some.1]`.
		
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) (e. g. `{"pladeholder1": "value1", "pagetitle": "My awesome pagetitle!"}`)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string) (e. g. `pladeholder1=value1&pagetitle=My awesome pagetitle!`)
	* Default value: —
	
* `outputterParams->docFieldsGlue`
	* Desctription: String for join the fields (if `outputterParams->tpl` is not used).
	* Valid values: `string`
	* Default value: `''`


#### Other parameters

* `mode`
	* Desctription: Mode.
	* Valid values:
		* `''` — default mode
		* `'ajax'` — `docId` gets from the `$_REQUEST['id']`. Use the `securityFields` param in this case!
	* Default value: `''`
	
* `securityFields`
	* Desctription: The fields for security verification.
	* Valid values:
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string) (e. g. `template=15&published=1`)
	* Default value: —


## [Home page →](https://code.divandesign.biz/modx/ddgetdocumentfield)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />