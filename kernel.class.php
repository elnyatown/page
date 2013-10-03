<?php

define('DOCUMENT_ROOT',				$_SERVER["DOCUMENT_ROOT"]);
define('TEMP',						$_SERVER["DOCUMENT_ROOT"]."/temp/");
define('THEME',						'default');
define('MODULES',					$_SERVER["DOCUMENT_ROOT"]."/modules/");
define('LINES_IN_PAGE',				2);//количество записей на странице
define('LENGHT_LINK_PAGES',			2);//количество ссылок в любую сторону от выбранной страницы
define('URL',						"http://page.elnyatown.ru");



class Kernel{

//define('DOCUMENT_ROOT',		$_SERVER["DOCUMENT_ROOT"]);
//


function __construct(){//ЗАВЕРШЕНО
/*
	$this->DOCUMENT_ROOT='DOCUMENT_ROOT';
	$this->hostName = "localhost"; 
	$this->userName = "cms"; 
	$this->password = "opendoor"; 
	$this->dbName = "cms";
	$this->connect=mysql_connect($this->hostName,$this->userName,$this->password) OR DIE("Не могу создать соединение ");
	mysql_select_db($this->dbName) or die(mysql_error());
	//mysql_query("SET NAME utf8");
	mysql_query("SET character_set_client='utf8'");
	mysql_query("SET character_set_results='utf8'");
	mysql_query("SET collation_connection='utf8_general_ci' ");
	*/
}

function generateXesh(){return md5(microtime());}//ЗАВЕРШЕНО
function generateTIMESTAMP(){return time();}//ЗАВЕРШЕНО
function generateDATE(){return date("d:m:Y");}//ЗАВЕРШЕНО
function generateTIME(){return date("H:i:s");}//ЗАВЕРШЕНО


//======================отображение для пользователя

function initModules($input){
//print_r($input);

	$this->initModules["tpl"] = new etcTemplate(DOCUMENT_ROOT.'/themes/'.THEME.'/global.template.tpl');
	
	$this->initModules["in"]["getListModules"]["html"]=file_get_contents(DOCUMENT_ROOT.'/themes/'.THEME.'/global.template.tpl');
	
	$this->initModules["out"]["getListModules"]=$this->getListModules($this->initModules["in"]["getListModules"]);

//print_r($this->initModules["out"]["getListModules"]["Modules"]);

$this->initModules["getListModules"]["megreModules"]=array_merge_recursive($input,$this->initModules["out"]["getListModules"]["Modules"]);//сливаем массивы параметров из index и  tpl

//$this->initModules["reverse"]["arrayListModulesTpl"]=array_flip($this->initModules["out"]["getListModules"]["arrayListModulesTpl"]);
//print_r($this->initModules["reverse"]);
//print_r($this->initModules["out"]["getListModules"]["arrayListModulesTpl"]);

	foreach($this->initModules["out"]["getListModules"]["arrayListModules"] as $this->initModules["nameModule"]){
		$this->initModules["nameModule"]=trim($this->initModules["nameModule"]);
		include_once(DOCUMENT_ROOT."/modules/".$this->initModules["nameModule"]."/".$this->initModules["nameModule"].".php");//подключаем исполняемый файл модуля
		$callback=$this->initModules["nameModule"];
		//выполняем основную функцию из файла модуля, и генерируем массив для шаблона
//надо прогнать массив всех модулей
//проверяем есть ли ключ nameModule в массиве arrayListModulesTpl
		if(array_key_exists($this->initModules["nameModule"],$this->initModules["out"]["getListModules"]["arrayListModulesTpl"])){
			$this->initModules["nameModuleTpl"]=$this->initModules["out"]["getListModules"]["arrayListModulesTpl"][$this->initModules["nameModule"]];
		}else{
			$this->initModules["nameModuleTpl"]=$this->initModules["nameModule"];
		}
//если есть то меняем nameModule на значение по ключу

//var_dump($this->initModules["nameModuleTpl"]);
		$this->initModules["assign"][$this->initModules["nameModuleTpl"]]=$callback($input[$this->initModules["nameModule"]]); 
 
	}
return $this->initModules["assign"];
}

function initModulesContent($input){
//передаем массив модулей и контент.
//данная функция должна выполняться в mod_content, чтобы в глобальный модуль уже передался контент с вставленными модулями
	$this->initMC["input"]=$input;
	$this->initMC["timestamp"]=microtime();
	$this->initMC["descFile"]=fopen(TEMP.$this->initMC["timestamp"].".tmp",'w+');
	$this->initMC["input"]["contentTPL"]="<!-- BEGIN: tpl -->".$this->initMC["input"]["content"]."<!-- END: tpl -->";
	$this->initMC["tmpFile"]=fwrite($this->initMC["descFile"],$this->initMC["input"]["contentTPL"]);
	fclose($this->initMC["descFile"]);
	if(count($this->initMC["input"]["listModules"])!=0){
		if($this->initMC["tmpFile"]){//если запись файла прошла успешно
			//инициализируем модули
			foreach($this->initMC["input"]["arrayListModules"] as $this->initMC["name"]){
				$this->initMC["name"]=trim($this->initMC["name"]);
				include_once(DOCUMENT_ROOT."/modules/".$this->initMC["name"]."/".$this->initMC["name"].".php");//подключаем исполняемый файл модуля
				$callback=$this->initMC["name"];
//var_dump($this->initMC["input"]["arrayListModulesTpl"]);
				if(array_key_exists($this->initMC["name"],$this->initMC["input"]["arrayListModulesTpl"])){
					$this->initMC["nameModuleTpl"]=$this->initMC["input"]["arrayListModulesTpl"][$this->initMC["name"]];
				}else{
					$this->initMC["nameModuleTpl"]=$this->initMC["nameModule"];
				}


				//выполняем основную функцию из файла модуля, и генерируем массив для шаблона
				//$this->initMC["assign"][$this->initMC["name"]]=$callback(); //параметры модуля в функцию не передаем, так как пока не можем их вписать в котент
				$this->initMC["assign"][$this->initMC["nameModuleTpl"]]=$callback();
			}	
		}else{//если запись в файл прошла неуспешно, то надо по шаблонам вывести стандартное предупреждение
			foreach($this->initMC["input"]["arrayListModules"] as $this->initMC["name"]){
				$this->initMC["name"]=trim($this->initMC["name"]);
				if(array_key_exists($this->initMC["name"],$this->initMC["input"]["arrayListModulesTpl"])){
					$this->initMC["nameModuleTpl"]=$this->initMC["input"]["arrayListModulesTpl"][$this->initMC["name"]];
				}else{
					$this->initMC["nameModuleTpl"]=$this->initMC["nameModule"];
				}
				//выполняем основную функцию из файла модуля, и генерируем массив для шаблона
				$this->initMC["assign"][$this->initMC["nameModuleTpl"]]="ОШИБКА ПРИ СОЗДАНИИ ВРЕМЕННОГО ФАЙЛА ШАБЛОНА"; //параметры в функцию не передаем, так как пока не можем их вписать в котент
			}
		}
		$this->initMC["tpl"] = new etcTemplate(TEMP.$this->initMC["timestamp"].'.tmp');
		$this->initMC["tpl"]->assign($this->initMC["assign"]);//Ввод данных модуля в шаблон
		$this->initMC["tpl"]->parse("tpl");
		$this->initMC["output"]["content"]=$this->initMC["tpl"]->text("tpl");
		$this->initMC["tpl"]->reset("tpl");
	}else{
		$this->initMC["output"]["content"]=$this->initMC["input"]["content"];
	}
	//удаляем темп файл шаблона 
	unlink(TEMP.$this->initMC["timestamp"].'.tmp');
//возвращаем измененный контент
return $this->initMC["output"];
}



function getListModules($input){//функция выборки модулей из контента (возвращает список модулей )
//В функцию передаем html, startTag, endTag
//dsfgsfg{mod_menu:var1=test1,var2=test2}dfsggssf{mod_menu:var1=test1,var2=test2}cvnbcnhyn
//menu:var1=test1,var2=test2}dfsggssf
//menu:var1=test1,var2=test2}cvnbcnhyn


//{mod_menu}
//Возвращает arrayListModules, Modules[имяМодуля][параметр]=значение
	$this->getLM["input"]=$input;
	$this->getLM["input"]["startTag"]='{mod_';
	$this->getLM["input"]["prefix"]='mod_';
	$this->getLM["input"]["endTag"]='}';
	$this->getLM["startString"]=explode($this->getLM["input"]["startTag"], $this->getLM["input"]["html"]);
	
//var_dump($this->getLM["startString"]);
	
	unset($this->getLM["startString"][0]);//если до модуля есть текст, то просто его удаляем из обработки

	//$this->getLM["startString"]=array_diff($this->getLM["startString"], array(''));

//var_dump($this->getLM["startString"]);
	if (isset($this->getLM["startString"][1])){//если найден хоть один модуль
		foreach($this->getLM["startString"] as $this->getLM["startString"]["value"]){
			$this->getLM["endString"][] = explode($this->getLM["input"]["endTag"], $this->getLM["startString"]["value"]);
		}

		
		
		
		//unset($this->getLM["endString"][1]);
//var_dump($this->getLM["endString"]);

		foreach($this->getLM["endString"] as $this->getLM["endString"]["key"]=>$this->getLM["endString"]["value"]){
			//разбиваем чтобы найти параметры
			$this->getLM["tempStr"]=explode(':',$this->getLM["endString"]["value"][0]);


			$this->getLM["tempName"]=$this->getLM["tempStr"][0];//имя модуля берем мез префикса mod_


			if(isset($this->getLM["tempStr"][1])){
				$this->getLM["strParameters"]=explode(',',$this->getLM["tempStr"][1]);

				foreach($this->getLM["strParameters"] as $this->getLM["strParameters"]["key"]=>$this->getLM["strParameters"]["value"]){
					$this->getLM["parameter"]=explode('=',$this->getLM["strParameters"]["value"]);

					$this->getLM["nameModule"]=$this->getLM["input"]["prefix"].$this->getLM["tempName"];//имя модуля берем с префиксом чтоб везде совпадало
					$this->getLM["Modules"][$this->getLM["nameModule"]][$this->getLM["parameter"][0]]=$this->getLM["parameter"][1];

					//$this->getLM["Modules"][имяМодуля][имяПараметра]=значениеПараметра, зарезервированное имя параметра - modName
				}
			}
			//массив с именами модулей
			$this->getLM["output"]["arrayListModules"][]=$this->getLM["input"]["prefix"].$this->getLM["tempName"];
		}
		$this->getLM["output"]["Modules"]=$this->getLM["Modules"];
	}else{
		//нужна проверка, если нет подключенных модулей (массив пустой), то не запускать initMC()
		$this->getLM["output"]["arrayListModules"]="empty";
		$this->getLM["output"]["Modules"]="empty";
	}

		foreach($this->getLM["Modules"] as $this->getLM["nameModule"] => $this->getLM["arrayVarModule"]){
			$this->getLM["strNameModuleTpl"]=$this->getLM["nameModule"].":";
			foreach($this->getLM["arrayVarModule"] as $this->getLM["varModule"]=>$this->getLM["varModuleValue"]){
				//генерируем правильное имя для tpl
				$this->getLM["strNameModuleTpl"].=$this->getLM["varModule"]."=".$this->getLM["varModuleValue"].",";
			}
			$this->getLM["output"]["arrayListModulesTpl"][$this->getLM["nameModule"]]=substr($this->getLM["strNameModuleTpl"], 0, strlen($this->getLM["strNameModuleTpl"])-1);
		}

return $this->getLM["output"];
unset($this->getLM["output"]);
}


function parseURL($input){
	$this->parseURL["input"]=$input;
	$this->parseURL["str"]= explode("/", $this->parseURL["input"]["REQUEST_URI"]);
	$this->parseURL["url"]["url_alt"]=substr(end($this->parseURL["str"]),0,strrpos(end($this->parseURL["str"]),"."));
return $this->parseURL["url"];
}

function convertURL($input){
	$this->convertURL["input"]=$input;
	$this->query="SELECT * FROM urls WHERE url_alt='{$this->convertURL["input"]["url_alt"]}'";
	$this->sql=mysql_query($this->query) or die(mysql_error());
	$this->convertURL["row"]=mysql_fetch_array($this->sql);
return $this->convertURL["row"];
}


function getModulesSetting($input){
$this->gMS["input"]=$input;
$this->gMS["tpl"] = new etcTemplate(MODULES.$this->gMS["input"]["modName"]."/".$this->gMS["input"]["modName"].'.template.tpl');
	
	$this->gMS["tpl"]->parse("tpl.SETTING.".$this->gMS["input"]["var"]."_".$this->gMS["input"]["parametr"]);
	$this->gMS["vars"]["varValue"]=$this->gMS["tpl"]->text("tpl.SETTING.".$this->gMS["input"]["var"]."_".$this->gMS["input"]["parametr"]);
	$this->gMS["tpl"]->reset("tpl.SETTING.".$this->gMS["input"]["var"]."_".$this->gMS["input"]["parametr"]);

return $this->gMS["vars"]["varValue"];
unset($this->gMS);
}

function getModulesError($input){
$this->getME["input"]=$input;
$this->getME["tpl"] = new etcTemplate(MODULES.$this->getME["input"]["modName"]."/".$this->getME["input"]["modName"].'.template.tpl');

	$this->getME["tpl"]->parse("tpl.SETTING.error_".$this->getME["input"]["errorAlias"]);
	$this->getME["errors"]["error"]=$this->getME["tpl"]->text("tpl.SETTING.error_".$this->getME["input"]["errorAlias"]);
	$this->getME["tpl"]->reset("tpl.SETTING.error_".$this->getME["input"]["errorAlias"]);

return $this->getME["errors"]["error"];
unset($this->getME);
}




//====================================================	



	
//echo	GetBetween("QQQ{mod_1111}}wuret}{mod_TESTMOD}sk{}jlf{mod_TEST2}dgd}fgh{mod_LAST}","{mod_","}");


function CreateCategory($input){//callback создание корневой категории
	$this->CreateCategory["input"]=$input;
	
	if($this->CreateCategory["input"]["reWriteCategoryId"]!="empty"){
		$this->CreateCategory["query"]["TABLE"]="category";
		$this->CreateCategory["query"]["id"]=$this->CreateCategory["input"]["reWriteCategoryId"];
		
		unset($this->CreateCategory["input"]["parentCategoryId"]);
		unset($this->CreateCategory["input"]["parentCategoryDir"]);
		unset($this->CreateCategory["input"]["reWriteCategoryId"]);
		unset($this->CreateCategory["input"]["action"]);
		
		$this->CreateCategory["query"]["SET"]="";
		foreach($this->CreateCategory["input"] as $this->key=>$this->_array){
			$this->CreateCategory["query"]["SET"]=$this->CreateCategory["query"]["SET"].$this->key."='".$this->CreateCategory["input"][$this->key]["value"]."', ";
		}
		$this->CreateCategory["query"]["SET"]=rtrim($this->CreateCategory["query"]["SET"],', ')." ";
		$this->query="UPDATE {$this->CreateCategory["query"]["TABLE"]} SET {$this->CreateCategory["query"]["SET"]} WHERE id='{$this->CreateCategory["query"]["id"]}'";
		echo($this->query);
		mysql_query($this->query) or die(mysql_error());
	
	
	}else{
	$this->CreateCategory["input"]["id_big"]["value"]=$this->generateXesh();
	$this->CreateCategory["input"]["dir"]["value"]=$this->CreateCategory["input"]["parentCategoryDir"]+1;
	$this->CreateCategory["input"]["parent_id"]["value"]=$this->CreateCategory["input"]["parentCategoryId"];
	$this->CreateCategory["input"]["id"]["value"]=$this->CreateCategory["input"]["id_big"]["value"];
	$this->CreateCategory["input"]["timestamp"]["value"]=$this->generateTIMESTAMP();
	$this->CreateCategory["input"]["date"]["value"]=$this->generateDATE();
	$this->CreateCategory["input"]["time"]["value"]=$this->generateTIME();
	$this->CreateCategory["input"]["whos_write"]["value"]="test user";
	$this->CreateCategory["input"]["permission"]["value"]=0000;
	$this->CreateCategory["query"]["TABLE"]="category";
	
	unset($this->CreateCategory["input"]["parentCategoryId"]);
	unset($this->CreateCategory["input"]["parentCategoryDir"]);
	unset($this->CreateCategory["input"]["reWriteCategoryId"]);
	unset($this->CreateCategory["input"]["action"]);
	//формирования запроса на запись в таблицу
	$this->CreateCategory["query"]["INTO"]="";
	$this->CreateCategory["query"]["VALUES"]="";
	foreach($this->CreateCategory["input"] as $this->key=>$this->_array){
		$this->CreateCategory["query"]["INTO"]=$this->CreateCategory["query"]["INTO"].$this->key." , ";
		$this->CreateCategory["query"]["VALUES"]=$this->CreateCategory["query"]["VALUES"]." '".$this->CreateCategory["input"][$this->key]["value"]."' , ";
	}
	$this->CreateCategory["query"]["INTO"]=" (".rtrim($this->CreateCategory["query"]["INTO"],', ').") ";//работает, но проверяет только последний символ на соотв, включая пробел
	$this->CreateCategory["query"]["VALUES"]=" (".rtrim($this->CreateCategory["query"]["VALUES"],', ').") ";
	$this->query="INSERT INTO {$this->CreateCategory["query"]["TABLE"]} {$this->CreateCategory["query"]["INTO"]} VALUES {$this->CreateCategory["query"]["VALUES"]}";
	mysql_query($this->query) or die(mysql_error());
	}
}

function ReWriteCategory($input){
$this->ReWriteCategory["input"]=$input;
	$this->query="SELECT * FROM category WHERE id='{$this->ReWriteCategory["input"]["categoryid"]}'";
	$this->mysql=mysql_query($this->query) or die(mysql_error());
	$this->ReWriteCategory["row"]=mysql_fetch_array($this->mysql);
return $this->ReWriteCategory["row"];

}

function SortCategory($input){
$this->SortCategory["input"]=$input;
	$this->query="UPDATE category SET sort='{$this->SortCategory["input"]["sort"]}' WHERE id='{$this->SortCategory["input"]["id"]}'";
	$this->mysql=mysql_query($this->query) or die(mysql_error());
	
	$this->SortCategory["output"]['module']['replace']=0;
	$this->SortCategory["output"]['form']['replace']=0;
	$this->SortCategory["output"]["_error"]=0;
	$this->SortCategory["output"]["SortCategory"]["erase"]=0;
	$this->SortCategory["output"]["SortCategory"]["error"]=0;
	$this->SortCategory["output"]["SortCategory"]["notice"]=0;
	$this->SortCategory["output"]["SortCategory"]["accept"]=1;
	$this->SortCategory["output"]["SortCategory"]["id"]="sort_".$this->SortCategory["input"]["id"];
	$this->SortCategory["output"]["SortCategory"]["acceptText"][]=$this->SortCategory["input"]["sort"];
	
return $this->SortCategory["output"];
}

function DeleteCategory($input){
	//при удалении категории удаляются и все подкатегории.
	$this->DeleteCategory["input"]=$input;
	//удалили выбранную категорию
	$this->query="DELETE  FROM category WHERE id='{$this->DeleteCategory["input"]["id"]}'";
	$sql=mysql_query($this->query) or die(mysql_error());
	//ищем подкатегории и удаляем их рекурсивно
	$this->query="SELECT * FROM category WHERE parent_id='{$this->DeleteCategory["input"]["id"]}'";
	$mysql_query=mysql_query($this->query) or die(mysql_error());
	while($this->DeleteCategory["row"]=mysql_fetch_array($mysql_query)){
		$this->DeleteCategory(array('id'=>$this->DeleteCategory["row"]["id"]));
	}
}

function CreatePage($input){
	$this->CreatePage["input"]=$input;
	//echo("wwwwwwwwwwwwww");
	if($this->CreatePage["input"]["reWritePageId"]!="empty"){
		$this->CreatePage["query"]["TABLE"]="pages";
		$this->CreatePage["query"]["page_id"]=$this->CreatePage["input"]["reWritePageId"];
		
		unset($this->CreatePage["input"]["reWritePageId"]);
		unset($this->CreatePage["input"]["parentCategoryId"]);
		unset($this->CreatePage["input"]["action"]);
		
		$this->CreatePage["query"]["SET"]="";
		foreach($this->CreatePage["input"] as $this->key=>$this->_array){
		//====проба escape  последовательности
		if($this->key=="page_content"){
			//надо чтобы пост запрос не резал ситемные знаки типа & кодируется в JS 
			$this->CreatePage["input"][$this->key]["value"]=str_replace("(percentSign)","%",$this->CreatePage["input"][$this->key]["value"]);
			//$this->CreatePage["input"][$this->key]["value"]=mysql_real_escape_string($this->CreatePage["input"][$this->key]["value"]); 
			$this->CreatePage["input"][$this->key]["value"]=urldecode($this->CreatePage["input"][$this->key]["value"]);

		}
		//====
		//$this->CreatePage["input"][$this->key]["value"]=preg_replace("/<p>&nbsp;<\/p>/","<br>",$this->CreatePage["input"][$this->key]["value"]);
			$this->CreatePage["query"]["SET"]=$this->CreatePage["query"]["SET"].$this->key."='".$this->CreatePage["input"][$this->key]["value"]."', ";
			//$this->CreatePage["query"]["SET"]=$this->CreatePage["query"]["SET"].$this->key."='".$this->CreatePage["input"][$this->key]["value"]."', ";
		}
		//echo($this->CreatePage["query"]["SET"]);
		$this->CreatePage["query"]["SET"]=rtrim($this->CreatePage["query"]["SET"],', ')." ";
		$this->query="UPDATE {$this->CreatePage["query"]["TABLE"]} SET {$this->CreatePage["query"]["SET"]} WHERE page_id='{$this->CreatePage["query"]["page_id"]}'";
		mysql_query($this->query) or die(mysql_error());
		
		//запись для ЧПУ
		$this->CreatePage["query"]["TABLE"]="urls";
		$this->CreatePage["query"]["SET"]=" url_alt='{$this->CreatePage["input"]["page_alt"]["value"]}', url_id='{$this->CreatePage["query"]["page_id"]}'";
		$this->query="UPDATE {$this->CreatePage["query"]["TABLE"]} SET {$this->CreatePage["query"]["SET"]} WHERE url_id='{$this->CreatePage["query"]["page_id"]}'";
		mysql_query($this->query) or die(mysql_error());
		
	}else{
		//$this->CreatePage["input"]["id_big"]["value"]=$this->generateXesh();
		$this->CreatePage["input"]["page_category_id"]["value"]=$this->CreatePage["input"]["parentCategoryId"];
		$this->CreatePage["input"]["page_id"]["value"]=$this->generateXesh();
		$this->CreatePage["input"]["page_timestamp"]["value"]=$this->generateTIMESTAMP();
		$this->CreatePage["input"]["page_date"]["value"]=$this->generateDATE();
		$this->CreatePage["input"]["page_time"]["value"]=$this->generateTIME();
		//$this->CreatePage["input"]["whos_write"]["value"]="test user";
		//$this->CreatePage["input"]["permission"]["value"]=0000;
		$this->CreatePage["query"]["TABLE"]="pages";
	
		unset($this->CreatePage["input"]["reWritePageId"]);
		unset($this->CreatePage["input"]["parentCategoryId"]);
		unset($this->CreatePage["input"]["action"]);

		//формирования запроса на запись в таблицу
		$this->CreatePage["query"]["INTO"]="";
		$this->CreatePage["query"]["VALUES"]="";
		foreach($this->CreatePage["input"] as $this->key=>$this->_array){
			$this->CreatePage["query"]["INTO"]=$this->CreatePage["query"]["INTO"].$this->key." , ";
			$this->CreatePage["query"]["VALUES"]=$this->CreatePage["query"]["VALUES"]." '".$this->CreatePage["input"][$this->key]["value"]."' , ";
		}
		$this->CreatePage["query"]["INTO"]=" (".rtrim($this->CreatePage["query"]["INTO"],', ').") ";//работает, но проверяет только последний символ на соотв, включая пробел
		$this->CreatePage["query"]["VALUES"]=" (".rtrim($this->CreatePage["query"]["VALUES"],', ').") ";
		$this->query="INSERT INTO {$this->CreatePage["query"]["TABLE"]} {$this->CreatePage["query"]["INTO"]} VALUES {$this->CreatePage["query"]["VALUES"]}";
		mysql_query($this->query) or die(mysql_error());
	
		//запись для ЧПУ
		$this->CreatePage["query"]["TABLE"]="urls";
		$this->CreatePage["query"]["INTO"]="(url_alt, url_id)";
		$this->CreatePage["query"]["VALUES"]="('{$this->CreatePage["input"]["page_alt"]["value"]}','{$this->CreatePage["input"]["page_id"]["value"]}')";
		$this->query="INSERT INTO {$this->CreatePage["query"]["TABLE"]} {$this->CreatePage["query"]["INTO"]} VALUES {$this->CreatePage["query"]["VALUES"]}";
		mysql_query($this->query) or die(mysql_error());
	}
	
	
}
	
function ReWritePage($input){
$this->ReWritePage["input"]=$input;
	$this->query="SELECT * FROM pages WHERE page_id='{$this->ReWritePage["input"]["pageid"]}'";
	$this->mysql=mysql_query($this->query) or die(mysql_error());
	$this->ReWritePage["row"]=mysql_fetch_array($this->mysql);
return $this->ReWritePage["row"];
}
	
function SortPage($input){
$this->SortPage["input"]=$input;
	$this->query="UPDATE pages SET page_sort='{$this->SortPage["input"]["page_sort"]}' WHERE page_id='{$this->SortPage["input"]["pageid"]}'";
	$this->mysql=mysql_query($this->query) or die(mysql_error());
	
	$this->SortPage["output"]['module']['replace']=0;
	$this->SortPage["output"]['form']['replace']=0;
	$this->SortPage["output"]["_error"]=0;
	$this->SortPage["output"]["SortPage"]["erase"]=0;
	$this->SortPage["output"]["SortPage"]["error"]=0;
	$this->SortPage["output"]["SortPage"]["notice"]=0;
	$this->SortPage["output"]["SortPage"]["accept"]=1;
	$this->SortPage["output"]["SortPage"]["id"]="sort_".$this->SortPage["input"]["pageid"];
	$this->SortPage["output"]["SortPage"]["acceptText"][]=$this->SortPage["input"]["page_sort"];
	
return $this->SortPage["output"];
}
	
	
function DeletePage($input){
	$this->DeletePage["input"]=$input;
	//удалили выбранную категорию
	$this->query="DELETE  FROM pages WHERE page_id='{$this->DeletePage["input"]["pageid"]}'";
	mysql_query($this->query) or die(mysql_error());
}
		
function MoveCategory($input){//временно заморожена
	$this->MC["input"]=$input;
	//who_moved - кого перемещают
	//to_move куда перемещают
	//если есть вложенные категории, то выполняем рекурсию смены индекса
	//должна быть рекурсивная функция изменения индекса
	//print_r($input);
	//id-идентификатор категории в которою перемещаем
	//меняем родительский id на id категории в которую перемещаем.
	//меняем dir на dir-1 категории в которую перемещаем.
	$this->MC["dir"]=$this->MC["input"]["dir"]+1;
	$this->query="SELECT * FROM category WHERE parent_id='{$this->MC["input"]["id"]}'";
	$mysql_query=mysql_query($this->query) or die(mysql_error());
	if(mysql_num_rows($mysql_query)!=0){
		while($this->MC["row"]=mysql_fetch_array($mysql_query)){
		echo($this->MC["row"]["name"]."---");
			$this->query="UPDATE category SET parent_id='{$this->MC["input"]["id"]}', dir='{$this->MC["dir"]}' WHERE id='{$this->MC["row"]["id"]}'";
			$sql=mysql_query($this->query) or die(mysql_error());
			$this->MoveCategory(array('id'=>$this->MC["row"]["id"],'dir'=>$this->MC["row"]["dir"]));
		}
	}

}
	
function Cmd($input, $callback=array()){

	$this->System["input"]=$input;
	$this->System["callback"]=$callback;
	foreach($this->System["callback"] as $this->callbackName){
		$callback=$this->callbackName;//иначе название калбека не читается
		if($this->System["input"][$this->callbackName]=="NULL"){
			$this->callbackOutput[$this->callbackName]=$this->$callback();
			return $this->callbackOutput;
		}else{

			$this->callbackOutput[$this->callbackName]=$this->$callback($this->System["input"][$this->callbackName]);
			return $this->callbackOutput;
		}
	}

} 
	
function ViewCategory($input){
static $recursion=FALSE; //надо для проверки первого шага рекурсии
$this->ViewCategory["input"]=$input;
$this->ViewCategory["tpl"] = new etcTemplate('themes/administrator_default/administrator.template.tpl');
	$this->ViewCategory["output"]['module']['replace']=0;
	$this->ViewCategory["output"]['form']['replace']=0;
	$this->ViewCategory["output"]["_error"]=0;
	$this->ViewCategory["output"]["Category"]["erase"]=0;
	$this->ViewCategory["output"]["Category"]["error"]=0;
	$this->ViewCategory["output"]["Category"]["notice"]=0;
	$this->ViewCategory["output"]["Category"]["accept"]=1;
	$this->ViewCategory["output"]["Category"]["id"]="Category";
	
	
	if($recursion===FALSE){
		//генерируем список страниц не входящих не в одну директорию
		$this->pageQuery="SELECT * FROM pages WHERE page_category_id='empty'";
		$this->pageSql=mysql_query($this->pageQuery) or die(mysql_error());
		while($this->ViewCategory["pageRow"]=mysql_fetch_array($this->pageSql)){
			//print_r($this->ViewCategory["pageRow"]);
			//генерация отступов
			$this->ViewCategory["pageTab"]="";
			$this->ViewCategory["tpl"]->assign(array(
				'pageTab'=>$this->ViewCategory["pageTab"],
				'pageid'=>$this->ViewCategory["pageRow"]["page_id"],
				'pagesort'=>$this->ViewCategory["pageRow"]["page_sort"],
				'pageName'=>$this->ViewCategory["pageRow"]["page_name"],
				'THEME'=>"administrator_default"
			));
			$this->ViewCategory["tpl"]->parse('tpl.ListPagesIndex');
			$this->ViewCategory["output"]["Category"]["acceptText"][]=$this->ViewCategory["tpl"]->text('tpl.ListPagesIndex');
			$this->ViewCategory["tpl"]->reset('tpl.ListPagesIndex');
		}
		$recursion=TRUE;
	}
	
	$this->query="SELECT * FROM category WHERE parent_id='{$this->ViewCategory["input"]["id"]}'";
	$sql=mysql_query($this->query) or die(mysql_error());
	
	while($this->ViewCategory["row"]=mysql_fetch_array($sql)){
		$this->ViewCategory["tab"]="";
		for($this->i=0; $this->i<$this->ViewCategory["row"]["dir"]; $this->i++){
			$this->ViewCategory["tpl"]->assign(array(
				'THEME'=>"administrator_default"
			));
			$this->ViewCategory["tpl"]->parse('tpl.folderTab');
			$this->ViewCategory["tab"].=$this->ViewCategory["tpl"]->text('tpl.folderTab');
			$this->ViewCategory["tpl"]->reset('tpl.folderTab');
		}
		$this->ViewCategory["tpl"]->assign(array(
			'folderTabs'=>$this->ViewCategory["tab"],
			'name'=>$this->ViewCategory["row"]["name"],
			'description'=>$this->ViewCategory["row"]["description"],
			'id'=>$this->ViewCategory["row"]["id"],
			'sort'=>$this->ViewCategory["row"]["sort"],
			'dir'=>$this->ViewCategory["row"]["dir"],
			'THEME'=>"administrator_default"
		));
		
		$this->ViewCategory["tpl"]->parse('tpl.category');
		$this->ViewCategory["output"]["Category"]["acceptText"][]=$this->ViewCategory["tpl"]->text('tpl.category');
		$this->ViewCategory["tpl"]->reset('tpl.category');
		
		

		
		//генерируем список страниц в директории.
		$this->pageQuery="SELECT * FROM pages WHERE page_category_id='{$this->ViewCategory["row"]["id"]}'";
		$this->pageSql=mysql_query($this->pageQuery) or die(mysql_error());
		while($this->ViewCategory["pageRow"]=mysql_fetch_array($this->pageSql)){
		//print_r($this->ViewCategory["pageRow"]);

			if($this->ViewCategory["pageRow"]["page_index"]==1){
				//добавление в массив вывода своей переменной
				$this->ViewCategory["tpl"]->parse('tpl.MarkerIndex');
				$this->ViewCategory["pageRow"]["markerIndex"]=$this->ViewCategory["tpl"]->text('tpl.MarkerIndex');
			}else{$this->ViewCategory["pageRow"]["markerIndex"]="";}

			//генерация отступов
			$this->ViewCategory["pageTab"]="";
			for($this->i=0; $this->i<$this->ViewCategory["row"]["dir"]+1; $this->i++){
				$this->ViewCategory["tpl"]->assign(array(
					'THEME'=>"administrator_default"
				));
				$this->ViewCategory["tpl"]->parse('tpl.pageTab');
				$this->ViewCategory["pageTab"].=$this->ViewCategory["tpl"]->text('tpl.pageTab');
				$this->ViewCategory["tpl"]->reset('tpl.pageTab');
			}
			$this->ViewCategory["tpl"]->assign(array(
			'pageTabs'=>$this->ViewCategory["pageTab"],
			'pageid'=>$this->ViewCategory["pageRow"]["page_id"],
			'pagesort'=>$this->ViewCategory["pageRow"]["page_sort"],
			'pageName'=>$this->ViewCategory["pageRow"]["page_name"],
			'markerIndex'=>$this->ViewCategory["pageRow"]["markerIndex"],
			'THEME'=>"administrator_default"
			));
			$this->ViewCategory["tpl"]->parse('tpl.ListPages');
			$this->ViewCategory["output"]["Category"]["acceptText"][]=$this->ViewCategory["tpl"]->text('tpl.ListPages');
			$this->ViewCategory["tpl"]->reset('tpl.ListPages');
		}
		
		$this->ViewCategory(array('id'=>$this->ViewCategory["row"]["id"]));
	}
	
	
	

return $this->ViewCategory["output"];
}

function inputCallback($input, $options='', $parameters=''){//ЗАВЕРШЕНО
//данные передаются в виде inputDATA= $inputArray["name"]=значение
unset ($this->output);
	$this->input=$input;
	//print_r($this->input);
	$this->output["_error"]=0;
	$this->output["module"]["replace"]=0;
	$this->output["form"]["replace"]=0;
	
	$this->inputCallback["modName"]=$this->input["modName"];
	
	unset($this->input["action"]);
	unset($this->input["modName"]);
	
	foreach($this->input as $this->key["id"]=>$this->_array){
		$this->output[$this->key["id"]]["error"]=0;
		$this->output[$this->key["id"]]["notice"]=0;
		$this->output[$this->key["id"]]["accept"]=1;
		$this->output[$this->key["id"]]["acceptText"][]=$this->getModulesError(array('modName'=>$this->inputCallback["modName"],'errorAlias'=>"VALIDLY"));
		$this->output[$this->key["id"]]["id"]=$this->key["id"];
		
		
		switch ($this->input[$this->key["id"]]["typeText"]) {
		//регулярные выражения необходимо усовершенствовать, по мере придумывания корявых логинов пользователями
			case 'fio': $this->template='/([n a-zA-Zа-яА-ЯЁё0-9-@_]+)/u';  break;
			case 'tel': $this->template='/([n a-zA-Zа-яА-ЯЁё0-9-@_]+)/u';  break;
			case 'mail': $this->template='/(^[a-zA-Z0-9-@_.]*$)/u';  break;//надо доработать шаблон
			case 'comment': $this->template='/([n a-zA-Zа-яА-ЯЁё0-9-@_]+)/u';  break;
			case 'text': $this->template='/(^[n a-zA-Zа-яА-ЯЁё0-9-@_,.\/\{\}\[\]\(\)\^\&\*;:\\\]*$)/u';  break;//OK но надо брать строку в 'строка', иначе убрать \
			default: $this->template='/([a-zA-Zа-яА-Я0-9-@_]+)/u';
		} 
		if(!preg_match($this->template,$this->input[$this->key["id"]]["value"]) ){
			//$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_TEMPLATE");
			$this->output[$this->key["id"]]["errorText"][]=$this->getModulesError(array('modName'=>$this->inputCallback["modName"],'errorAlias'=>"TEMPLATE"));
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}
		//вместо имени для вычисления разрешенного количества символов берем значения column, так как оно не меняется, а имя может быть динамическим
		if(strlen(iconv('utf-8', 'windows-1251', $this->input[$this->key["id"]]["value"]))<$this->getModulesSetting(array('modName'=>$this->inputCallback["modName"],'var'=>$this->input[$this->key["id"]]["column"],'parametr'=>'LengthMin'))){
			//$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_MIN");
			$this->output[$this->key["id"]]["errorText"][]=$this->getModulesError(array('modName'=>$this->inputCallback["modName"],'errorAlias'=>"MIN"));
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}
		if(strlen(iconv('utf-8', 'windows-1251', $this->input[$this->key["id"]]["value"]))>$this->getModulesSetting(array('modName'=>$this->inputCallback["modName"],'var'=>$this->input[$this->key["id"]]["column"],'parametr'=>'LengthMax'))){
			//$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_MAX");
			$this->output[$this->key["id"]]["errorText"][]=$this->getModulesError(array('modName'=>$this->inputCallback["modName"],'errorAlias'=>"MAX"));
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}
		if($this->input[$this->key["id"]]["name"]=='pic'){
			if($_SESSION['pic']!=$this->input[$this->key["id"]]["value"]){
				$this->output[$this->key["id"]]["errorText"][]=$this->getModulesError(array('modName'=>$this->inputCallback["modName"],'errorAlias'=>"PIC"));
				$this->output[$this->key["id"]]["error"]=1;
				$this->output["_error"]=1;
			}
		}
		
	$this->output[$this->key["id"]]["value"]=$this->input[$this->key["id"]]["value"];
	$this->output[$this->key["id"]]["name"]=$this->input[$this->key["id"]]["name"];
	$this->output[$this->key["id"]]["table"]=$this->input[$this->key["id"]]["table"];
	$this->output[$this->key["id"]]["column"]=$this->input[$this->key["id"]]["column"];
	}
return $this->output;
unset($this->input);
unset($this->output);
}



function _walk(&$value, $key, $template){//ВСПОМОГАТЕЛЬНАЯ ФУНКЦИЯ ДЛЯ Clean()//ЗАВЕРШЕНО
	$memori="";
	preg_match_all($template,$value,$memori);
	$_TEMP="";
	for ($i=0; $i<count($memori[1]); $i++){$_TEMP=$_TEMP.$memori[1][$i];} 
	$value = htmlspecialchars($_TEMP);
}
function Clean($input){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray=любой массив или строка и тип строки или массива

	$this->Clean["input"]=$input["data"];
	$this->Clean["type"]=$input["type"];
	switch ($this->Clean["type"]) {
		case 'nospace_mixed': $this->Clean["template"]='/([a-zA-Z0-9_.]+)/u';  break;
		case 'mixed': $this->Clean["template"]='/([n a-zA-Z0-9_.]+)/u';  break;
		case 'nospace_string': $this->Clean["template"]='/([a-zA-Z_]+)/u';  break;
		case 'string': $this->Clean["template"]='/([n a-zA-Z_.]+)/u';  break;
		case 'nospace_number': $this->Clean["template"]='/([0-9_]+)/u';  break;
		case 'number': $this->Clean["template"]='/([n 0-9_.]+)/u';  break;
		default: $this->Clean["template"]='/([n a-zA-Z0-9-_.]+)/u'; break;
	}
	if(is_array($this->Clean["input"])){//хер знает работает ли условие (по тесту работает)
		array_walk_recursive($this->Clean["input"],'Kernel::_walk',$this->Clean["template"]);
	return $this->Clean["input"];//возвращает форматированный массив
	}else{
		$this->Clean["memori"]="";
		preg_match_all($this->Clean["template"],$this->Clean["input"],$this->Clean["memori"]);
		$this->Clean["temp"]="";
		for ($i=0; $i<count($this->Clean["memori"][1]); $i++){$this->Clean["temp"]=$this->Clean["temp"].$this->Clean["memori"][1][$i];} 
		$this->Clean["string"]= htmlspecialchars($this->Clean["temp"]);
	return $this->Clean["string"];//возвращается форматированная строка
	}
unset($this->Clean);	
}




function PaginationPage($input){
	$this->PP["input"]=$input;
	
	$this->PP["input"]["LINESinPAGE"]=($this->PP["input"]["LINESinPAGE"]=="" OR empty($this->PP["input"]["LINESinPAGE"]))? LINES_IN_PAGE : $this->PP["input"]["LINESinPAGE"] ;
	
	$this->query=$this->PP["input"]["query"];//строка запроса на выборку всех значений для расчета количества страниц
	$this->PP["sql_dump"]=mysql_query($this->query) or die(mysql_error());
	$this->PP["output"]["LINES"]=mysql_num_rows($this->PP["sql_dump"]);
	
	//расчет страниц
	$this->PP["PAGES"]=ceil($this->PP["output"]["LINES"]/$this->PP["input"]["LINESinPAGE"]);//количество страниц
	$this->PP["output"]["PAGES"]=$this->PP["PAGES"];
	//условия достоверности
	$this->PP["PAGES"]=($this->PP["PAGES"]==0) ? 1 : $this->PP["PAGES"];
	$this->PP["input"]["SORT_PAGE"]=($this->PP["input"]["SORT_PAGE"]>$this->PP["PAGES"]) ? $this->PP["PAGES"] : $this->PP["input"]["SORT_PAGE"];
	$this->PP["input"]["SORT_PAGE"]=($this->PP["input"]["SORT_PAGE"]<1) ? 1 : $this->PP["input"]["SORT_PAGE"];
	
	$this->PP["output"]["SORT_PAGE"]=$this->PP["input"]["SORT_PAGE"];
	$this->PP["output"]["LIMIT_FROM"]=($this->PP["input"]["SORT_PAGE"]-1)*$this->PP["input"]["LINESinPAGE"];//запись с которой начинаем выборку

return $this->PP["output"];
unset($this->PP);
}


function LinePages($input){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray[name]=значение (PAGES,SORT_PAGE)
//опции передаются в виде $inputOptions[option_name]=значение (LINESinPAGE, lengthLinkPAGES)
//$this->LP["tpl"] = new etcTemplate(DOCUMENT_ROOT."/modules/mod_news/mod_news.template.tpl");
$this->LP["input"]=$input;

$this->LP["tpl"] = new etcTemplate(DOCUMENT_ROOT.$this->LP["input"]["template"]);
	//$this->LP["input"]=$this->Clean($input,array('TYPE'=>'mixed'));
	
	//$this->LP["parameters"]=$this->Clean($inputOptions,array('TYPE'=>'mixed'));
	
	//записей на страницу
	$this->LP["input"]["LINESinPAGE"]=($this->LP["input"]["LINESinPAGE"]=="" OR empty($this->LP["input"]["LINESinPAGE"]))? LINES_IN_PAGE : $this->LP["input"]["LINESinPAGE"] ;
	//количество показываемых ссылок страниц в любую сторону от выбранной страницы
	$this->LP["input"]["lengthLinkPAGES"]=($this->LP["input"]["lengthLinkPAGES"]=="" OR empty($this->LP["input"]["lengthLinkPAGES"]))? LENGHT_LINK_PAGES : $this->LP["input"]["lengthLinkPAGES"] ;

			//генерация нумерации страниц
			$this->LP["ListPAGES"]="";
			$this->LP["tpl"]->parse('tpl.firstPAGE');
			$this->LP["firstPAGE"]["html"]=$this->LP["tpl"]->text('tpl.firstPAGE');
			$this->LP["firstPAGE"]=($this->LP["input"]["PAGES"]<=1 OR $this->LP["input"]["SORT_PAGE"]<=1) ? "" : $this->LP["firstPAGE"]["html"];

			$this->LP["tpl"]->assign(array('PAGE'=>$this->LP["input"]["SORT_PAGE"]));
			$this->LP["tpl"]->parse('tpl.previousPAGE');
			$this->LP["previousPAGE"]["html"]=$this->LP["tpl"]->text('tpl.previousPAGE');
			$this->LP["previousPAGE"]=($this->LP["input"]["SORT_PAGE"]<=1) ? "" : $this->LP["previousPAGE"]["html"];

			$this->LP["tpl"]->assign(array('PAGE'=>$this->LP["input"]["SORT_PAGE"]));
			$this->LP["tpl"]->parse('tpl.nextPAGE');
			$this->LP["nextPAGE"]["html"]=$this->LP["tpl"]->text('tpl.nextPAGE');
			$this->LP["nextPAGE"]=($this->LP["input"]["SORT_PAGE"]>=$this->LP["input"]["PAGES"]) ? "" : $this->LP["nextPAGE"]["html"];
			
			$this->LP["tpl"]->assign(array('PAGE'=>$this->LP["input"]["PAGES"]));
			$this->LP["tpl"]->parse('tpl.lastPAGE');
			$this->LP["lastPAGE"]["html"]=$this->LP["tpl"]->text('tpl.lastPAGE');
			$this->LP["lastPAGE"]=($this->LP["input"]["PAGES"]<=1 OR $this->LP["input"]["SORT_PAGE"]>=$this->LP["input"]["PAGES"]) ? "" : $this->LP["lastPAGE"]["html"];
			
			//echo($this->LP["parameters"]["lengthLinkPAGES"]);
			$this->LP["leftBORDER"]=(($this->LP["input"]["SORT_PAGE"]-$this->LP["input"]["lengthLinkPAGES"])<1) ? 1 : $this->LP["input"]["SORT_PAGE"]-$this->LP["input"]["lengthLinkPAGES"];
			$this->LP["rightBORDER"]=(($this->LP["input"]["SORT_PAGE"]+$this->LP["input"]["lengthLinkPAGES"])>$this->LP["input"]["PAGES"]) ? $this->LP["input"]["PAGES"] : ($this->LP["input"]["SORT_PAGE"]+$this->LP["input"]["lengthLinkPAGES"]);

			if($this->LP["input"]["PAGES"]<=1){
				$this->LP["output"]["html"]=$this->LP["output"]["html"]."Страниц:(".$this->LP["input"]["PAGES"].")  ";
			}else{
				for($this->i=$this->LP["leftBORDER"]; $this->i<=$this->LP["rightBORDER"]; $this->i++){
				//echo($this->i);
				$this->LP["parse"]=($this->i==$this->LP["input"]["SORT_PAGE"])? 'tpl.ListPAGES.TEXT' : 'tpl.ListPAGES.LINK';
					$this->LP["tpl"]->assign('PAGE',$this->i);
					$this->LP["tpl"]->parse($this->LP["parse"]);
					$this->LP["ListPAGES"]=$this->LP["ListPAGES"].$this->LP["tpl"]->text($this->LP["parse"]);
					$this->LP["tpl"]->reset($this->LP["parse"]);
				}
				$this->LP["tpl"]->assign(array(
					'PAGES'=>$this->LP["input"]["PAGES"],
					'firstPAGE'=>$this->LP["firstPAGE"],
					'previousPAGE'=>$this->LP["previousPAGE"],
					'ListPAGES'=>$this->LP["ListPAGES"],
					'nextPAGE'=>$this->LP["nextPAGE"],
					'lastPAGE'=>$this->LP["lastPAGE"]
				));
				$this->LP["tpl"]->parse('tpl.PAGINATION');
				$this->LP["output"]["html"]=$this->LP["output"]["html"].$this->LP["tpl"]->text('tpl.PAGINATION');
			}
return ($this->LP["output"]);
unset($this->LP);
}


function Authorization($input){
	$this->Auth["input"]=$input;
	
	$this->query="SELECT psw FROM users WHERE login='{$this->Auth["input"]["login"]["value"]}'";
	$this->mysql=mysql_query($this->query) or die(mysql_error());
	if(mysql_num_rows($this->mysql)==0){
		$this->Auth["output"]["answer"]=0;
		$this->Auth["output"]["psw"]="NON"; 
	}else{
		$this->Auth["row"]=mysql_fetch_array($this->mysql);
		if($this->Auth["input"]["psw"]["value"]==$this->Auth["row"]["psw"]){
			$this->Auth["output"]["answer"]=1;
			$this->Auth["output"]["psw0"]=$this->Auth["row"]["psw"];
			$this->Auth["output"]["psw1"]=$this->Auth["row"]["psw"];
		}else{
			$this->Auth["output"]["answer"]=0;
			$this->Auth["output"]["psw0"]=$this->Auth["row"]["psw"];
			$this->Auth["output"]["psw1"]=$this->Auth["row"]["psw"];
		}
	}
return $this->Auth["output"];
}

function ViewCategorySelect($input){
$this->ViewCategorySelect["input"]=$input;
$this->ViewCategorySelect["tpl"] = new etcTemplate('themes/administrator_default/administrator.template.tpl');
//генерируем список категорий для поля Select
		$this->ViewCategorySelect["tpl"]->parse('tpl.optionSelectCategory');
		$this->ViewCategorySelect["output"]["optionSelectCategory"].=$this->ViewCategory["tpl"]->text('tpl.optionSelectCategory');
		$this->ViewCategorySelect["tpl"]->reset('tpl.optionSelectCategory');

}



}
?>
