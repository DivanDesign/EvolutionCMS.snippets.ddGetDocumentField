# (MODX)EvolutionCMS.snippets.ddGetDocumentField

Snippet gets the necessary document fields (and TVs) by its id.


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.60
* [(MODX)EvolutionCMS.snippets.ddTypograph](https://code.divandesign.biz/modx/ddtypograph) >= 2.5 (if typography is required)


## Installation


### Manually


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetDocumentField`.
2. Description: `<b>2.11.1</b> Snippet gets the necessary document fields (and TVs) by its id.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetDocumentField_snippet.php` file from the archive.


#### 2. Elements → Manage Files:

1. Create a new folder `assets/snippets/ddGetDocumentField/`.
2. Extract the archive to the folder (except `ddGetDocumentField_snippet.php`).


### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Install (MODX)EvolutionCMS.snippets.ddGetDocumentField
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDocumentField',
	'type' => 'snippet'
]);
```

* If `ddGetDocumentField` is not exist on your site, `ddInstaller` will just install it.
* If `ddGetDocumentField` is already exist on your site, `ddInstaller` will check it version and update it if needed.


## Parameters description


### Data provider parameters

* `dataProviderParams`
	* Desctription: Parameters to be passed to the provider.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet` or `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `dataProviderParams->resourceId`
	* Desctription: Document identifier.
	* Valid values: `integer`
	* Default value: `$modx->documentIdentifier` (current document)
	
* `dataProviderParams->resourceFields`
	* Desctription: Document field(s) to get separated by commas.  
		If the parameter is empty, the snippet will try to search fields in `outputterParams->tpl` (something like `[+docField+]`).
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `dataProviderParams->resourceFields[i]`
	* Desctription: Fields and their aliases must be separated by `'='` if aliases are required while returning the results (for example: `'pagetitle=title,content=text'`).
	* Valid values:
		* `string` — document field
		* `stringSeparated` — document field and it's alias
	* **Required**
	
* `dataProviderParams->resourceFieldsAlternative`
	* Desctription: Alternative document field(s) to get if the main is empty separated by commas.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `dataProviderParams->resourceFieldsAlternative[i]`
	* Desctription: Document field.
	* Valid values: `string`
	* **Required**


### Output format parameters

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
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet` or `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
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


#### Outputter → String (``&outputter=`string` ``)

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
		
	* Valid values: `object`
	* Default value: —
	
* `outputterParams->docFieldsGlue`
	* Desctription: String for join the fields (if `outputterParams->tpl` is not used).
	* Valid values: `string`
	* Default value: `''`


#### Outputter → JSON (``&outputter=`json` ``)

* `outputterParams->removeEmptyFields`
	* Desctription: Remove resource fields with empty values (`''`) from result.
	* Valid values: `boolean`
	* Default value: `false`


### Other parameters

* `mode`
	* Desctription: Mode.
	* Valid values:
		* `''` — default mode
		* `'ajax'` — `docId` gets from the `$_REQUEST['id']`. Use the `securityFields` param in this case!
	* Default value: `''`
	
* `securityFields`
	* Desctription: The fields for security verification.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) (e. g. `{"template": 15, "published": 1}`)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string) (e. g. `template=15&published=1`)
		* It can also be set as native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet` or `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —


## Examples


### Get the `pagetitle` of current document

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "pagetitle"
	}`
]]
```


### Get the `introtext` of document which ID is `7` and return from chunk

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceId": 7,
		"resourceFields": "introtext"
	}`
	&outputterParams=`{
		"tpl": "testChunk"
	}`
]]
```

`testChunk` code:

```html
<div class="test">[+introtext+]</div>
```


### Get the `longtitle` of a document or `pagetitle` if `longtitle` is empty

```html
<title>[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "longtitle",
		"resourceFieldsAlternative": "pagetitle"
	}`
]]</title>
```


### Get a few phones from TVs and join them with comma

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceId": 7,
		"resourceFields": "phone1,phone2"
	}`
	&outputterParams=`{
		"docFieldsGlue": ", "
	}`
]]
```


### Additional data into result chunk

For example, we are getting something with the Ditto snippet. Into Ditto chunk `result_tpl` we need to get phone number & fax, if phone is not empty or nothing. Chunk code:

```html
<div class="test_row">
	[+content+]
	[[ddGetDocumentField?
		&dataProviderParams=`{
			"resourceId": "[+id+]",
			"resourceFields": "phone"
		}`
		&outputterParams=`{
			"tpl": "test_row_phone",
			"placeholders": {
				"fax": "[+fax+]",
				"someTitle": "Call me!"
			}
		}`
	]]
</div>
```

The `test_row_phone` chunk code:

```html
<p class="phone" title="[+someTitle+]">[+phone+], [+fax+]</p>
```


### Using field aliases while returning the results in the `outputterParams->tpl` chunk

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "pagetitle=title,pub_date=date"
	}`
	&outputterParams=`{
		"tpl": "testChunk"
	}`
]]
```

The `testChunk` chunk code:

```html
<p>[+title+], [+date+]</p>
```


### Using field aliases with JSON format

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "pagetitle=title,introtext=text,content"
	}`
	&outputter=`json`
]]
```

Returns:

```json
{
	"title": "The title of a document",
	"text": "The annotation",
	"content": "The content"
}
```


### Remove resource fields with empty values from result

Let that document `pagetitle` is set and `longtitle` is empty.

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "pagetitle,longtitle"
	}`
	&outputter=`json`
]]
```

Returns:

```json
{
	"pagetitle": "The title of a document",
	"longtitle": ""
}
```

If fields with empty values is no needed, just set `outputterParams->removeEmptyFields` to `true`:

```
[[ddGetDocumentField?
	&dataProviderParams=`{
		"resourceFields": "pagetitle,longtitle"
	}`
	&outputter=`json`,
	&outputterParams=`{
		"removeEmptyFields": true
	}`
]]
```

Returns:

```json
{
	"pagetitle": "The title of a document"
}
```


### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
\DDTools\Snippet::runSnippet([
	'name' => 'ddGetDocumentField',
	'params' => [
		//Can be set as native PHP array
		'dataProviderParams' => [
			'resourceId' => 42,
			'resourceFields' => 'pagetitle,question'
		]
	]
]);
```


## Links

* [Home page](https://code.divandesign.biz/modx/ddgetdocumentfield)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddgetdocumentfield)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />