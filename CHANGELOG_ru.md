# (MODX)EvolutionCMS.snippets.ddGetDocumentField changelog


## Версия 2.12 (2024-07-12)
* \* Параметры → `outputter` → Допустимые значения → `'object'`: Переименован из `'json'` (с обратной совместимостью).
* \+ OutputterObject → Параметры:
	* \+ `outputterParams->format`: Новый параметр. Позволяет:
		* \+ Вернуть результат в виде нативного PHP объекта или массива (удобно при вызове через `\DDTools\Snippet::runSnippet`):
			* \+ `'objectAuto'` — `stdClass` или `array` в зависимости от результата
			* \+ `'objectStdClass'` — `stdClass`
			* \+ `'objectArray'` — `array`
		* \+ Вернуть результат в виде строки:
			* \+ `'stringJsonAuto'` — `stringJsonObject` или `stringJsonArray` в зависимости от результата
			* \+ `'stringJsonObject'`
			* \+ `'stringJsonArray'`
			* \+ `'stringQueryFormatted'` — [Query string](https://en.wikipedia.org/wiki/Query_string)
			* \+ `'stringHtmlAttrs'` — строка HTML-атрибутов (например, `width='100' height='50'`)
* \* `\ddTools::getTpl` используется вместо `$modx->getTpl` (стало чуть меньше багов).
* \* README:
	* \* Примеры: HJSON используется для всех примеров.
	* \+ Ссылки → GitHub.
* \+ Composer.json:
	* \+ `support`.
	* \+ `autoload`.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.60.


## Версия 2.11.1 (2021-12-27)
* \* Внимание! Требуется (MODX)EvolutionCMS.snippets.ddTypograph >= 2.5.
* \* Исправлена ошибка, когда результат сниппета пустой.
* \* `\DDTools\Snippet::runSnippet` используется для запуска ddTypograph без DB и eval.
* \+ README → Установка → Используя (MODX)EvolutionCMS.libraries.ddInstaller.


## Версия 2.11 (2021-03-26)
* \* Внимание! Требуется PHP >= 5.6.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.48.
* \+ Запустить сниппет без DB и eval можно через `\DDTools\Snippet::runSnippet` (см. примеры в README).
* \+ Параметры → `dataProviderParams`, `outputterParams`, `securityFields`: Добавлена поддержка [HJSON](https://hjson.github.io/).
* \+ `\ddGetDocumentField\Snippet`: Новый класс. Весь код сниппета перенесён туда.
* \+ README → Ссылки → Packagist.
* \+ CHANGELOG_ru.


## Версия 2.10.5 (2020-09-29)
* \* `\ddGetDocumentField\DataProvider\Document\DataProvider::get`: Возвращает только существующие поля ресурса.
* \* Рефакторинг подключения (MODX)Evolution.libraries.ddTools.


## Версия 2.10.4 (2020-09-29)
* \* `\ddGetDocumentField\DataProvider\Document\DataProvider::get`: Исправлен неправильный вывод альтернативных полей ресурса.
* \+ README → Ссылки.


## Версия 2.10.3 (2020-07-05)
* \* `\ddGetDocumentField\Input::paramsBackwardCompatibility`: Исправлено логирование в лог событий CMS.


## Версия 2.10.2 (2020-05-11)
* \* Параметры → `removeEmptyFields`: Только пустые строки (`''`) рассматриваются как пустота.
* \* Composer.json:
	* \+ `homepage`.
	* \+ `authors`.
	* \* `require`:
		* \* `dd/evolutioncms-libraries-ddtools`: Переименовано из `dd/modxevo-library-ddtools`.
		* \* `dd/evolutioncms-snippets-ddtypograph`: Переименовано из `dd/modxevo-snippet-ddtypograph`.


## Версия 2.10.1 (2020-04-26)
* \* `\ddGetDocumentField\Outputter\Outputter::render_resourceDataApplyAliases`: Исправлена работа, когда псевдонимы полей пусты или не заданы для некоторых полей.


## Версия 2.10 (2020-04-25)
* \* Внимание! Требуется EvolutionCMS.libraries.ddTools >= 0.32.
* \* Структура сниппета полностью переработана (с обратной совместимостью), см. README.md.
* \* Параметры: Следующие параметры были переименованы (с обратной совместимостью):
	* \* `docId` → `dataProviderParams->resourceId`.
	* \* `docField` → `dataProviderParams->resourceFields`.
	* \* `docFieldAlternative` → `dataProviderParams->resourceFieldsAlternative`.
	* \* `result_outputFormat` → `outputter`.
	* \* `result_typography` → `outputterParams->typography`.
	* \* `result_escapeForJS` → `outputterParams->escapeForJS`.
	* \* `result_URLEncode` → `outputterParams->URLEncode`.
	* \* `result_emptyResult` → `outputterParams->emptyResult`.
	* \* `result_tpl` → `outputterParams->tpl`.
	* \* `result_tpl_placeholders` → `outputterParams->placeholders`.
	* \* `result_docFieldsGlue` → `outputterParams->docFieldsGlue`.
* \+ Параметры → `securityFields`: Также может быть задан как `stringJsonObject`.
* \* `\ddGetDocumentField\Outputter\Json\Outputter`:
	* \+ Добавлена возможность удаления из результата полей ресурса с пустыми значениями (см. `outputterParams->removeEmptyFields`).
	* \* `render_main`: Добавлены флаги `JSON_UNESCAPED_UNICODE` и `JSON_UNESCAPED_SLASHES`.
* \+ README → Примеры.


## Версия 2.9 (2020-04-23)
* \+ Параметры → `result_emptyResult`: Новый параметр. Что будет возвращено, если результат работы сниппета пуст?
* \* Параметры → `docField[i]`: Разделитель между именем поля и псевдонимом изменён с `'::'` на `'='` (с обратной совместимостью).
* \* Улучшен стиль сообщений в логе событий CMS.
* \+ README.
* \+ CHANGELOG.
* \+ Composer.json.


## Версия 2.8 (2018-12-26)
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.20.
* \* Следующие параметры были переименованы (обратная совместимость сохранена):
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
* \+ Параметры → `docField`: Больше не обязателен. Если не передать — сниппет попытается найти поля документов в шаблоне `result_tpl` (будет искать что-то в стиле `[+docField+]`).
* \* Результат работы сниппета будет возвращён в любом случае (пустая строка, если пустой результат).


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>