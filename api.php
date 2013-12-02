<?php
date_default_timezone_set('Europe/Moscow');
	define('LINES_IN_PAGE',				3);
	define('LENGHT_LINK_PAGES',			2);
	define('COUNT_FOTO',				3);
	define('WIDTH',						0);
	define('HEIGHT',					100);
	define('FOTO_TABLE',				'etcusersfoto');
	define('FOTO_DIR',					'http://elnyatown.ru/modules/setting/foto/');
	define('FOTO_DIR1',					$_SERVER["DOCUMENT_ROOT"].'modules/setting/foto/');
	define('SIGNATURE',					'Подпись отсутствует');
	//define('PRELOAD_DIR',				'http://elnyatown.ru/modules/pictures/uploads/'); //должна совпадать с M_FOTO_DIR
	define('PRELOAD_DIR',				$_SERVER["DOCUMENT_ROOT"].'modules/pictures/uploads/'); //должна совпадать с M_FOTO_DIR
	define('PRELOAD_DIR1',				'http://elnyatown.ru/modules/pictures/uploads/'); //должна совпадать с M_FOTO_DIR
	define('HIDE_SYSOP',				FALSE);
	define('EVERYONE',					FALSE);
	define('ACCESS_LEVEL',				0);
	define('M_FOTO_DIR',				'http://elnyatown.ru/modules/pictures/uploads/');
	//define('M_FOTO_DIR',				$_SERVER["DOCUMENT_ROOT"].'modules/pictures/uploads/');
	define('M_EVERYONE',				TRUE);
	define('M_FOTO_TABLE',				'etcmoderatefoto');
	define('RPL_IP',					'127.0.0.1');
	define('RPL_PORT',					10010);
	define('RPL_NAMESPACE',				'etc_');
	define('RPL_CHANNEL',				'chat');
	define('STANDART_COLOR_MESSAGE',	'black');
	define('KEEP_ALIVE',				300);
	
class ETC {
//=========================================================================================================
//подключение к БД
function __construct(){//ЗАВЕРШЕНО
	$this->DOCUMENT_ROOT=$_SERVER["DOCUMENT_ROOT"];
	$this->hostName = "localhost"; 
	$this->userName = "chat"; 
	$this->password = "opendoor"; 
	$this->dbName = "chat";
	$this->connect=mysql_connect($this->hostName,$this->userName,$this->password) OR DIE("Не могу создать соединение ");
	mysql_select_db($this->dbName) or die(mysql_error());
	//mysql_query("SET NAME utf8");
	mysql_query("SET character_set_client='utf8'");
	mysql_query("SET character_set_results='utf8'");
	mysql_query("SET collation_connection='utf8_general_ci' ");
}

function _walk(&$value, $key, $template){//ВСПОМОГАТЕЛЬНАЯ ФУНКЦИЯ ДЛЯ Clean()//ЗАВЕРШЕНО
	$memori="";
	preg_match_all($template,$value,$memori);
	$_TEMP="";
	for ($i=0; $i<count($memori[1]); $i++){$_TEMP=$_TEMP.$memori[1][$i];} 
	$value = htmlspecialchars($_TEMP);
}
public function Clean($in=array(),$opt=array(),$prm=array()){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray=любой массив или строка
//опции передаются в виде $inputOptions[option_name]=значение (TYPE)
	$this->Clean->in=$in;
	$this->Clean->opt=$opt;
	$this->Clean->prm=$prm;
	switch ($this->Clean->opt["type"]) {
		case 'nospace_mixed': $this->Clean->tpl='/([a-zA-Z0-9_]+)/u';  break;
		case 'mixed': $this->Clean->tpl='/([n a-zA-Zа-яА-Я0-9_.]+)/u';  break;
		case 'nospace_string': $this->Clean->tpl='/([a-zA-Z_]+)/u';  break;
		case 'string': $this->Clean->tpl='/([n a-zA-Z_.]+)/u';  break;
		case 'nospace_number': $this->Clean->tpl='/([0-9_]+)/u';  break;
		case 'number': $this->Clean->tpl='/([n 0-9_.]+)/u';  break;
		default: $this->Clean->tpl='/([n a-zA-Z0-9-_.]+)/u'; break;
	}
	if(is_array($this->Clean->in)){//хер знает работает ли условие (по тесту работает)
		array_walk_recursive($this->Clean->in,'ETC::_walk',$this->Clean->tpl);
	return $this->Clean->in;//возвращает форматированный массив
	}else{
		$this->Clean->memory="";
		preg_match_all($this->Clean->tpl,$this->Clean->in,$this->Clean->memory);
		$this->Clean->temp="";
		for ($i=0; $i<count($this->Clean->memory[1]); $i++){$this->Clean->temp=$this->Clean->temp.$this->Clean->memory[1][$i];} 
		$this->Clean->str= htmlspecialchars($this->Clean->str);
	return $this->Clean->str;//возвращается форматированная строка
	}
unset($this->Clean);	
}

function gXeshLogin($Login){//ЗАВЕРШЕНО
//будет хеш сумма логина в нижнем регистре что бы не было похожих логинов
//преобразование в win-1251 для русских букв (без него хеш русских больших и маленьких букв отличается)
	$this->gXL=$Login;
return md5(mb_strtolower(iconv('utf-8', 'windows-1251', $this->gXL)));
}

function gXesh(){return md5(microtime());}//ЗАВЕРШЕНО
function gTIMESTAMP(){return time();}//ЗАВЕРШЕНО
function gDATE(){return date("d:m:Y");}//ЗАВЕРШЕНО
function gTIME(){return date("H:i:s");}//ЗАВЕРШЕНО
function gSID(){return session_id();}//ЗАВЕРШЕНО


public function gCookie($in=array(), $opt=array(), $prm=array()){//вроде пока не используется
	$this->gCookie->in=$in;
	$this->gCookie->out["decrypt"] = mcrypt_decrypt(MCRYPT_CAST_256, $this->gCookie->in["key"], $_COOKIE[$this->gCookie->in["name"]],MCRYPT_MODE_CFB, $_COOKIE["iv"]);
return $this->gCookie->out["decrypt"];
unset($this->gCookie);
}

public function cookieAccess($in=array(), $opt=array(), $prm=array()){
	$this->access=$this->gCookie(array('key'=>$_SESSION["ssid"],'name'=>'Auth'));
	if($this->access!="TRUE"){die("  ACCESS DENIED: NOT AUTHORIZATION");}
}

function Access($in=array(), $opt=array(), $prm=array('aLevel'=>ACCESS_LEVEL)){//проверка не доделана
//$this->Access->tpl = new etcTemplate('template.tpl');
	$this->Access->in=$in;
	$this->Access->opt=$opt;
	$this->Access->prm=$prm;

	$this->Access->access="FALSE";

	if($this->Access->opt["module"]=="ExternalModule" AND $this->Access->in["uid"]!='' AND $this->Access->in["ssid"]!='' AND $_SESSION["Auth"]!='FALSE'){
		//моя хитрая проверка
		$this->query="SELECT uid,ssid FROM etcusersglobal WHERE uid='{$this->Access->in["uid"]}' AND ssid='{$this->Access->in["ssid"]}'";
		$this->Access["sql"]=mysql_query($this->query) or die(mysql_error());
		//$this->Access["global"]=mysql_fetch_array($this->Access["sql"]);
		if(mysql_num_rows($this->Access["sql"]) != 0){
			$this->Access["User"]=$this->getUsers($this->Access["input"]["uid"]);
			if($this->Access["User"]["setting"]["int_permission"]>=$this->Access["options"]["AccessLevel"]){
				$this->Access["access"]=TRUE;
			}else{die("ACCESS DENIED_int");}
		}else{die("ACCESS DENIED_NO_MATH_uid-ssid");}
	}//else{die("ACCESS DENIED_EMPTY");}
	
	//блок доступа на базовою страницу чата
	if($this->Access["options"]["module"]=="CHAT" AND $this->Access["input"]["uid"]!='' AND $this->Access["input"]["ssid"]!='' AND $_SESSION["Authorization1"]!='FALSE'){
		$this->query="SELECT uid,ssid FROM etcusersglobal WHERE uid='{$this->Access["input"]["uid"]}' AND ssid='{$this->Access["input"]["ssid"]}'";
		$this->Access["sql"]=mysql_query($this->query) or die(mysql_error());
		$this->Access["global"]=mysql_fetch_array($this->Access["sql"]);
		if(mysql_num_rows($this->Access["sql"]) != 0){
			//проверка на бан
			$this->query="SELECT * FROM etcusersblocked WHERE uid='{$this->Access["input"]["uid"]}'";
			$this->Access["sql"]=mysql_query($this->query) or die(mysql_error());
			if(mysql_num_rows($this->Access["sql"]) == 0){
				$this->Access["access"]=TRUE;
			}else{
				//$this->Access["blocked"]=mysql_fetch_array($this->Access["sql"]);
				//$this->Access["blocked"]["intTime"]=($this->Access["blocked"]["timestamp_out"]-$this->Access["blocked"]["timestamp_in"])/3600;
				//$this->Access["tpl"]->assign(array(
					//'UID'=>$this->Access["blocked"]["uid"],
					//'LOGIN'=>$this->Access["blocked"]["login"],
					//'BLOCKED_FOR'=>$this->Access["blocked"]["intTime"],
					//'STARTING_DATE'=>$this->Access["blocked"]["date"],
					//'STARTING_TIME'=>$this->Access["blocked"]["time"],
					//'REASON'=>$this->Access["blocked"]["reason"]
				//));
				
				//$this->Access["tpl"]->parse('tpl.BLOCKED');
				//$this->Access["tpl"]->out('tpl.BLOCKED');
				////echo("Пользователь ".$this->ACCESS["_BLOCKED"]["login"]." заблокирован на ".$this->ACCESS["_BLOCKED"]["intTime"]."час(а,ов)  начиная с ".$this->ACCESS["_BLOCKED"]["date"]."-".$this->ACCESS["_BLOCKED"]["time"]."  по причине:".$this->ACCESS["_BLOCKED"]["reason"]);
				//echo("<p style='text-align:center;color:red'><b>ACCESS DENIED1</b></p>");
				header("Location: http://elnyatown.ru/modules/blocked/blocked.php?uid={$this->Access["input"]["uid"]}");
				die("");
			}
		}else{die("  ACCESS DENIED2");}
	}
	//В последнем IF функции должен стоять DIE
	if($this->Access["access"]!=TRUE){die("  ACCESS DENIED_ALL");}
unset($this->Access);
}

function gresImage($file,$opt=array('y'=>0)) {//
//по умолчанию resмаштабируем по х
$this->gresImage->tpl = new etcTemplate(DOCUMENT_ROOT.THEMES.DS.THEME.DS.'opt.tpl');
	$this->gresImage->FILE=$file;
	$this->gresImage->opt=$opt;
	$this->gresImage->image=getimagesize ($this->gresImage->FILE); 
	$this->gresImage->width=$this->gresImage->image[0]; 
	$this->gresImage->height=$this->gresImage->image[1];

	

	if($this->gresImage->opt["y"]==0 $this->gresImage->opt["x"]!=0){
		$this->gresImage->y=$this->gresImage->["x"]*($this->gresImage->width/$this->gresImage->height);
//надо сгенерировать ссылку в шаблоне themes/def/opt.tpl
	}
	if($this->gresImage->opt["x"]==0 $this->gresImage->opt["y"]!=0){
		$this->gresImage->x=$this->gresImage->opt["y"]*($this->gresImage->width/$this->gresImage->height);
	}
	//if($this->ImageSize->opt["x"]!=0 AND $this->ImageSize->opt["y"]!=0){
		//просто возвращаем ссылку
	//}
	$this->gresImage->tpl->assign(array(
		'FILE'=>$this->gresImage->FILE,
		'width'=>$this->gresImage->x,
		'height'=>$this->gresImage->y
	));	
	$this->gresImage->tpl->parse('tpl.resImage');
	$this->gresImage->resImage=$this->ImageSize->tpl->text('tpl.resImage');
	$this->gresImage->tpl->reset('tpl.resImage');

return $this->gresImage->resImage;
unset($this->gresImage);
}

public function Lang($keywords){//ЗАВЕРШЕНО ОЧИЩЕНО
	$this->Lang["keywords"]=$this->Clean($keywords,array('type'=>'nospase_mixed'));
	$this->query = "SELECT descriptions FROM etclang WHERE keywords='{$this->Lang["keywords"]}' ";
	$this->Lang["sql"]=mysql_query($this->query);
	$this->Lang["_LANG"]=mysql_fetch_array($this->Lang["sql"]); 
return $this->Lang["_LANG"]['descriptions']; 
unset($this->Lang);
}

function Letters($options, $parameters){//ЗАВЕРШЕНО ОЧИЩЕНО
//опции передаются в виде $inputOptions[option_name]=значение (name, border)
	$this->options=$this->Clean($options,array('type'=>'nospase_mixed'));
	$this->parameters=$this->Clean($parameters,array('type'=>'nospase_mixed'));
	
	//определяет максимальное и минимальное количество символов вводимых в поле
	$this->options["border"]=mb_strtolower($this->options["border"]);
	switch($this->options["border"]){
		case 'min': $this->query = "SELECT min FROM etcsettingletters WHERE keywords='{$this->parameters["name"]}' "; break;
		case 'max': $this->query = "SELECT max FROM etcsettingletters WHERE keywords='{$this->parameters["name"]}' "; break;
		default: $this->query = "SELECT max FROM etcsettingletters WHERE keywords='{$this->parameters["name"]}' "; break;
	}
	$this->DB["sql"]=mysql_query($this->query);
	$this->DB["SQLANS_settingletters"]=mysql_fetch_array($this->DB["sql"]);
return $this->DB["SQLANS_settingletters"][$this->options["border"]];
unset($this->options);
unset($this->parameters);
unset($this->DB);
}

function PaginationPages($input, $parameters=array('LINESinPAGE'=>LINES_IN_PAGE)){
//в input будем писать зам запрос на выборку, запрос на все страницы (или из запроса на выборку автоматом сфомируем запрос на страцицы)
	$this->input=$input;
		
	$this->PP["parameters"]["LINESinPAGE"]=($this->PP["parameters"]["LINESinPAGE"]=="" OR empty($this->PP["parameters"]["LINESinPAGE"]))? LINES_IN_PAGE : $this->PP["parameters"]["LINESinPAGE"] ;

	//$this->query=" SELECT uid FROM etcusersglobal WHERE BINARY login LIKE '{$this->input["SORT_LETTER"]}%' ";
	$this->query=$this->input["queryAll"];
	$this->PP["sql_dump"]=mysql_query($this->query) or die(mysql_error());
	$this->PP["output"]["LINES"]=mysql_num_rows($this->PP["sql_dump"]);

	//расчет страниц
	$this->PP["output"]["PAGES"]=ceil($this->PP["output"]["LINES"]/$this->PP["parameters"]["LINESinPAGE"]);
	//условия достоверности
	$this->PP["PAGES"]=($this->PP["PAGES"]==0) ? 1 : $this->PP["PAGES"];
	$this->input["SORT_PAGE"]=($this->input["SORT_PAGE"]>$this->PP["PAGES"]) ? $this->PP["PAGES"] : $this->input["SORT_PAGE"];
	$this->input["SORT_PAGE"]=($this->input["SORT_PAGE"]<1) ? 1 : $this->input["SORT_PAGE"];
	
	$this->PP["LIMIT_FROM"]=($this->input["SORT_PAGE"]-1)*$this->PP["parameters"]["LINESinPAGE"];

}

function LinePages($input, $parameters=array('LINESinPAGE'=>LINES_IN_PAGE,'lengthLinkPAGES'=>LENGHT_LINK_PAGES)){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray[name]=значение (PAGES,SORT_PAGE)
//опции передаются в виде $inputOptions[option_name]=значение (LINESinPAGE, lengthLinkPAGES)
$this->LP["tpl"] = new etcTemplate('template.tpl');
	$this->LP["input"]=$this->Clean($input,array('TYPE'=>'mixed'));
	$this->LP["parameters"]=$this->Clean($inputOptions,array('TYPE'=>'mixed'));
	
	//записей на страницу
	$this->LP["parameters"]["LINESinPAGE"]=($this->LP["parameters"]["LINESinPAGE"]=="" OR empty($this->LP["parameters"]["LINESinPAGE"]))? LINES_IN_PAGE : $this->LP["parameters"]["LINESinPAGE"] ;
	//количество показываемых ссылок страниц в любую сторону от выбранной страницы
	$this->LP["parameters"]["lengthLinkPAGES"]=($this->LP["parameters"]["lengthLinkPAGES"]=="" OR empty($this->LP["parameters"]["lengthLinkPAGES"]))? LENGHT_LINK_PAGES : $this->LP["parameters"]["lengthLinkPAGES"] ;

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
			$this->LP["leftBORDER"]=(($this->LP["input"]["SORT_PAGE"]-$this->LP["parameters"]["lengthLinkPAGES"])<1) ? 1 : $this->LP["input"]["SORT_PAGE"]-$this->LP["parameters"]["lengthLinkPAGES"];
			$this->LP["rightBORDER"]=(($this->LP["input"]["SORT_PAGE"]+$this->LP["parameters"]["lengthLinkPAGES"])>$this->LP["input"]["PAGES"]) ? $this->LP["input"]["PAGES"] : ($this->LP["input"]["SORT_PAGE"]+$this->LP["parameters"]["lengthLinkPAGES"]);

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
					'PAGE'=>$this->LP["input"]["SORT_PAGE"],
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
return ($this->LP["output"]["html"]);
unset($this->LP);
}

function Online($input, $options, $parameters=array()){//ЗАВЕРШЕНО

$this->Online["input"]=$input;
$this->Online["options"]=$options;
$this->Online["parameters"]=$parameters;

	if($this->Online["options"]["action"]=="Add"){//ЗАВЕРШЕНО
		$this->User=$this->getUsers($this->Online["input"]["uid"]);
		//print_r($this->User["global"]);
		$this->query="SELECT uid FROM etcusersblocked WHERE uid='{$this->User["global"]["uid"]}' ";
		$this->Online["sql"]=mysql_query($this->query) or die(mysql_error());
		if(mysql_num_rows($this->Online["sql"]) == 0){

			//пишем в таблицу онлайн что пользовательзашел или обновляем время, попутно удаляя старых
			$this->query="DELETE FROM etcusersonline WHERE timestamp<{$this->generateTIMESTAMP()}-300";
			mysql_query($this->query) or die(mysql_error());
			$this->query="SELECT * FROM etcusersonline WHERE uid='{$this->User["global"]["uid"]}'";
			$this->Online["sql"]=mysql_query($this->query) or die(mysql_error());
			if(mysql_num_rows($this->Online["sql"]) == 0){
				//пишем новую строку
				$this->Online["INTO"]="(ip,uid, ssid, int_permission, timestamp)";
				$this->Online["VALUES"]="('{$_SERVER["REMOTE_ADDR"]}','{$this->User["global"]["uid"]}','{$this->User["global"]["ssid"]}','{$this->User["setting"]["int_permission"]}','{$this->generateTIMESTAMP()}')";
				$this->query="INSERT INTO etcusersonline {$this->Online["INTO"]} VALUES {$this->Online["VALUES"]}";
				mysql_query($this->query) or die(mysql_error());
			}else{
				//обновляем время
				$this->Online["SET"]="ssid='{$this->User["global"]["ssid"]}', int_permission='{$this->User["setting"]["int_permission"]}', timestamp='{$this->generateTIMESTAMP()}'";
				$this->query="UPDATE etcusersonline SET {$this->Online["SET"]} WHERE uid='{$this->User["global"]["uid"]}'";
				mysql_query($this->query) or die(mysql_error());
			}
		}
	}
	if($this->Online["options"]["action"]=="Delete"){//ЗАВЕРШЕНО
		$this->query="DELETE FROM etcusersonline WHERE uid='{$this->Online["input"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		unset($_SESSION);
		$_SESSION["Authorization1"]='FALSE';
		//куки удаляются как только прошел редирект на главную страницу
	}
}

function getUsers($object,  $options=array('EveryOne'=>EVERYONE),  $parameters=array('count'=>COUNT_FOTO,'width'=>WIDTH,'height'=>HEIGHT,'fotoTABLE'=>FOTO_TABLE,'fotoDIR'=>FOTO_DIR)){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray[]=значение
//объект передается в виде $inputObject=значение (uid)
//опции передаются в виде $inputOptions[option_name]=значение (count,fotoDIR,width,height,fotoTABLE)
$this->getUsers["tpl"] = new etcTemplate('template.tpl');
unset($this->getUsers["temp"]);

	$this->getUsers["object"]=$this->Clean($object,array('type'=>'nospace_mixed'));
	//echo("UID=".$this->getUsers["object"]);
	$this->getUsers["options"]=$options;
	$this->getUsers["parameters"]=$parameters;
	
	//если массив опций пустой, то надо брать опции по умолчанию (желательно всять из базы данных, но можно определить сдесь напрямую)
	$this->getUsers["parameters"]["count"]=($this->getUsers["parameters"]["count"]=="")? 2 : $this->getUsers["parameters"]["count"] ;
	$this->getUsers["parameters"]["fotoDIR"]=($this->getUsers["parameters"]["fotoDIR"]=="")? FOTO_DIR : $this->getUsers["parameters"]["fotoDIR"] ;
	$this->getUsers["parameters"]["width"]=($this->getUsers["parameters"]["width"]=="")? 0 : $this->getUsers["parameters"]["width"] ;
	$this->getUsers["parameters"]["height"]=($this->getUsers["parameters"]["height"]=="")? 100 : $this->getUsers["parameters"]["height"] ;
	$this->getUsers["parameters"]["fotoTABLE"]=($this->getUsers["parameters"]["fotoTABLE"]=="")? FOTO_TABLE : $this->getUsers["parameters"]["fotoTABLE"] ;
	$this->getUsers["options"]["EveryOne"]=(!$this->getUsers["options"]["EveryOne"])? EVERYONE : $this->getUsers["options"]["EveryOne"];
	//надо сделать если uid пустой, то ставить empty
	$this->query="SELECT uid,login,ssid FROM etcusersglobal WHERE uid='{$this->getUsers["object"]}'";
	$this->getUsers["sql"]=mysql_query($this->query) or die(mysql_error());
	while($this->getUsers["global"]=mysql_fetch_array($this->getUsers["sql"])){
		$this->getUsers["temp"]["global"]=$this->getUsers["global"];
	}
	$this->query="SELECT * FROM etcuserssetting WHERE uid='{$this->getUsers["object"]}'";
	$this->getUsers["sql"]=mysql_query($this->query) or die(mysql_error());
	while($this->getUsers["setting"]=mysql_fetch_array($this->getUsers["sql"])){
		$this->getUsers["temp"]["setting"]=$this->getUsers["setting"];
	}
	$this->query="SELECT * FROM etcusersinfo WHERE uid='{$this->getUsers["object"]}'";
	$this->getUsers["sql"]=mysql_query($this->query) or die(mysql_error());
	while($this->getUsers["info"]=mysql_fetch_array($this->getUsers["sql"])){
		$this->getUsers["temp"]["info"]=$this->getUsers["info"];
	}
	$this->query="SELECT * FROM etcusersstatistic WHERE uid='{$this->getUsers["object"]}'";
	$this->getUsers["sql"]=mysql_query($this->query) or die(mysql_error());
	while($this->getUsers["statistic"]=mysql_fetch_array($this->getUsers["sql"])){
		$this->getUsers["temp"]["statistic"]=$this->getUsers["statistic"];
	}
//print_r($this->getUsers["parameters"]);
	$this->getUsers["WHERE"]=($this->getUsers["options"]["EveryOne"]===TRUE)? "" : "WHERE uid='{$this->getUsers["object"]}'" ;
	$this->query="SELECT * FROM {$this->getUsers["parameters"]["fotoTABLE"]} {$this->getUsers["WHERE"]}";
	$this->getUsers["sql"]=mysql_query($this->query) or die(mysql_error());
	if(mysql_num_rows($this->getUsers["sql"]) == 0){
		$this->getUsers["tpl"]->assign(array(
			'NOTICE'=>$this->LANG("EMPTY_OBJECT")
		));
		$this->getUsers["tpl"]->parse('tpl.NOTICE');
		$this->getUsers["temp"]["foto"]=$this->getUsers["tpl"]->text('tpl.NOTICE');
		$this->getUsers["tpl"]->reset('tpl.NOTICE');
	}else{
		$this->getUsers["temp"]["text"]="";
		$this->getUsers["partDiv"]="";
		$this->i=0;
		$this->j=0;
		while ($this->getUsers["image"]=mysql_fetch_array($this->getUsers["sql"])) {
			$this->j++;
			$this->getUsers["tpl"]->assign(array(
				'DIR'=>$this->getUsers["parameters"]["fotoDIR"],
				'FILE'=>$this->getUsers["image"]["file"],
				'FILENAME'=>$this->getUsers["image"]["filename"],
				'TITLE'=>$this->getUsers["image"]["signature"],
				'SIZE'=>$this->ImageSize($this->getUsers["parameters"]["fotoDIR"].$this->getUsers["image"]["file"],$this->getUsers["parameters"]["width"],$this->getUsers["parameters"]["height"])
			));
			$this->getUsers["tpl"]->parse('tpl.FOTO');
			$this->getUsers["partDiv"]=$this->getUsers["partDiv"].$this->getUsers["tpl"]->text('tpl.FOTO');
			$this->getUsers["tpl"]->reset('tpl.FOTO');
			//"<div class=\"float-left\">
			//<INPUT TYPE=CHECKBOX NAME='SELECT[]' value='".$this->View["IMAGE"]["file"]."'  >
			//<a class=\"fancybox\" data-fancybox-group=\"gallery\" href=\"../setting/foto/".$this->View["IMAGE"]["file"]."\" title=\"".$this->View["IMAGE"]["signature"]."\" >
			//<img src=\"../setting/foto/".$this->View["IMAGE"]["file"]."\" ".$this->ImageSize("../setting/foto/".$this->View["IMAGE"]["file"],0,40)." border=\"0\">
			//</a></div>";
			$this->i++;
			if($this->i==$this->getUsers["parameters"]["count"]){
				$this->i=0; 
				$this->getUsers["tpl"]->assign(array(
					'stringFOTO'=>$this->getUsers["partDiv"]
				));
				$this->getUsers["tpl"]->parse('tpl.stringFOTO');
				$this->getUsers["html"]=$this->getUsers["html"].$this->getUsers["tpl"]->text('tpl.stringFOTO');
				$this->getUsers["tpl"]->reset('tpl.stringFOTO');
				$this->getUsers["partDiv"]="";
			}
			if(mysql_num_rows($this->getUsers["sql"])==$this->j AND $this->i!=$this->getUsers["parameters"]["count"]){
				$this->getUsers["tpl"]->assign(array(
					'ENDstringFOTO'=>$this->getUsers["partDiv"]
				));
				$this->getUsers["tpl"]->parse('tpl.ENDstringFOTO');
				$this->getUsers["html"]=$this->getUsers["html"].$this->getUsers["tpl"]->text('tpl.ENDstringFOTO');
				$this->getUsers["tpl"]->reset('tpl.ENDstringFOTO');
				//$this->View["text"]="<tr><td valign=\"bottom\">".$this->View["text"]."</td></tr>";
			}
		
			//формируем поле изменения для подписи
			$this->getUsers["tpl"]->parse('tpl.SIGNATURE');
			
		
		}
		$this->getUsers["tpl"]->assign(array(
			'blockFOTO'=>$this->getUsers["html"]
		));
		$this->getUsers["tpl"]->parse('tpl.blockFOTO');
		$this->getUsers["html"]=$this->getUsers["tpl"]->text('tpl.blockFOTO');
		$this->getUsers["tpl"]->reset('tpl.blockFOTO');
		
		$this->getUsers["html"]=$this->getUsers["html"].$this->getUsers["tpl"]->text('tpl.SIGNATURE');
		$this->getUsers["tpl"]->reset('tpl.SIGNATURE');
		
		//$this->View["output"]["text"]="<table width=\"100%\" >".$this->View["output"]["text"]."</table>";
		$this->getUsers["temp"]["foto"]=$this->getUsers["html"];
	}
return $this->getUsers["temp"];
unset($this->getUsers);
}

function Files($inputFile, $options, $parameters=array('signature'=>SIGNATURE,'preloadDIR'=>PRELOAD_DIR)){//ЗАВЕРШЕНО
//данные передаются в виде $inputFle[]=Выбранный файл для загрузки
//действие передается в виде $inputAction=значение 
//опции передаются в виде $inputOptions[option_name]=значение 
$this->DF["options"]=$this->Clean($options,array('TYPE'=>'mixed'));
$this->DF["parameters"]=$parameters;

//умолчания
$this->DF["parameters"]["signature"]=($this->DF["parameters"]["signature"]=="")? SIGNATURE : $this->DF["parameters"]["signature"] ;
$this->DF["parameters"]["preloadDIR"]=($this->DF["parameters"]["preloadDIR"]=="")? PRELOAD_DIR : $this->DF["parameters"]["preloadDIR"] ;

	$this->output["_error"]=0;
	$this->output["upload"]["error"]=0;

	$this->output["upload"]["notice"]=0;
	
	$this->output["upload"]["accept"]=1;
	//$this->output["upload"]["acceptText"][]=$this->lang("SAVE_OK");
	$this->output["upload"]["id"]="upload";
	$this->output["upload"]["erase"]=1;
	$this->output["upload"]["eraseid"]='picture_signature';

	$this->_FILES['preload']=$inputFile;
	$this->_FILES['preload']['name']=basename($this->_FILES['preload']['name']);
	//проверка подписи к файлу будет производится в upload.php
	if($this->DF["options"]["action"]=="TestPreload"){
		$this->DF["user"]=$this->getUsers($this->DF["parameters"]["uid"]);
		if($this->DF["user"]["setting"]["int_foto"]>0){
			if($this->_FILES['preload']['name']!="" || $this->_FILES['preload']['size']!=0){
				if($this->_FILES['preload']['size']<=$this->Letters(array('border'=>'MAX'),array('name'=>"upload"))){
					//if($this->_FILES['preload']['type']=="image/gif" || $this->_FILES['preload']['type']=="image/pjpeg"){
					
					$this->output["upload"]["acceptText"][]=$this->lang("SAVE_OK");
				
					//}else{$this->output["_error"]=1; $this->output["upload"]["notice"]=1; $this->output["upload"]["notice"]["noticeText"][]="ERROR_EXTENSION";}//не разрешенные расширения
				}else{$this->output["_error"]=1; $this->output["upload"]["erase"]=0; $this->output["upload"]["notice"]=1; $this->output["upload"]["notice"]["noticeText"][]=$this->lang("ERROR_LETTER_MAX");}//превышен максимальный размер файла
			}else{$this->output["_error"]=1; $this->output["upload"]["erase"]=0; $this->output["upload"]["notice"]=1;  $this->output["upload"]["notice"]["noticeText"][]=$this->lang("ERROR_EMPTY_FILE");}//попытка загрузить пустой файл
		}else{$this->output["_error"]=1; $this->output["upload"]["erase"]=0; $this->output["upload"]["notice"]=1;$this->output["upload"]["noticeText"][]=$this->lang("ERROR_INT_FOTO");}//нельзя загрузить фото, так как превышено количество
		
	return $this->output;
	unset($this->output);
	}
	
	if($this->DF["options"]["action"]=="Preload"){
	//надо записать в базу данные по предзагруженной фото
		$this->FILE_TEMP['time']=(double)microtime(true);
		$this->FILE_TEMP['name']=$this->_FILES['preload']['name'];
		$this->FILE_TEMP['size']=$this->_FILES['preload']['size'];
		$this->FILE_TEMP['texttype']=strrchr($this->_FILES['preload']['name'],'.');//Находит последнюю точку и выводит ее и все что после нее
		$this->FILE_TEMP['uid']="image_".$this->DF["parameters"]["uid"]."_".$this->FILE_TEMP['time'].$this->FILE_TEMP['texttype'];//генерируется в виде picture_uid_timestamp(microtime)
		if(move_uploaded_file($this->_FILES['preload']["tmp_name"],$this->DF["parameters"]["preloadDIR"].$this->FILE_TEMP['uid'])){
			//$this->DF["output"]["text"]=$this->lang("SAVE_OK");//Файл загружен
			//записываем данные о загруженном файле в таблицу премодерации.
			$this->DF["INTO"]="(uid, file, signature)";
			$this->DF["VALUES"]="('{$this->DF["parameters"]["uid"]}', '{$this->FILE_TEMP['uid']}', '{$this->DF["parameters"]["signature"]}')";
			$this->query="INSERT INTO etcmoderatefoto {$this->DF["INTO"]} VALUES {$this->DF["VALUES"]}";
			mysql_query($this->query) or die(mysql_error());
			//записываем новое значение счетчика фотографий (потом надо поменять + на -)
			$this->query="UPDATE etcuserssetting SET int_foto=int_foto-1 WHERE uid='{$this->DF["parameters"]["uid"]}'";
			mysql_query($this->query) or die(mysql_error());
			//return $this->result;
			//надо изменить значение счетчика фотографий в настройках (надо показывать сколько фоток осталось, чтобы понять что удалили так как если ого добявят и так будет видно фото после модерирования)
		}
	return $this->output;
	}
}

function View($input, $options, $parameters=array('HideSysOp'=>HIDE_SYSOP,'LINESinPAGE'=>LINES_IN_PAGE,'lengthLinkPAGES'=>LENGHT_LINK_PAGES)){//ЗАВЕРШЕНО
//данные передаются в виде $inputDATA[sort]=значение
//объект передается в виде $inputObject=значение
//действие передается в виде $inputAction=значение 
//опции передаются в виде $inputOptions[option_name]=значение (LINESinPAGE, lengthLinkPAGES)нужны для пагинатора страниц
$this->View["tpl"] = new etcTemplate('template.tpl');
	//$this->input=$this->Clean($input,array('TYPE'=>'mixed'));//при очистке удаляются русские буквы???
	$this->input=$input;
	$this->View["options"]=$options;
	
	
	$this->View["parameters"]=$parameters;
	//разрешение на вывод тех у кого разрешений больше чем у того кто просматривает
	$this->View["parameters"]["HideSysOp"]=(!$this->View["parameters"]["HideSysOp"])? FALSE : $this->View["parameters"]["HideSysOp"] ;
	//записей на страницу
	$this->View["parameters"]["LINESinPAGE"]=($this->View["parameters"]["LINESinPAGE"]=="" OR empty($this->View["parameters"]["LINESinPAGE"]))? LINES_IN_PAGE : $this->View["parameters"]["LINESinPAGE"] ;
	//количество показываемых ссылок страниц в любую сторону от выбранной страницы
	$this->View["parameters"]["lengthLinkPAGES"]=($this->View["parameters"]["lengthLinkPAGES"]=="" OR empty($this->View["parameters"]["lengthLinkPAGES"]))? LENGHT_LINK_PAGES : $this->View["parameters"]["lengthLinkPAGES"] ;
	
	if($this->View["options"]["action"]=="ListOnline"){//время отсутствия пока 300сек //ЗАВЕРШЕНО
		$this->query="DELETE FROM etcusersonline WHERE timestamp<{$this->generateTIMESTAMP()}-300";
		mysql_query($this->query) or die(mysql_error());
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		$this->output["ListOnline"]["error"]=0;
		$this->output["ListOnline"]["notice"]=0;
		$this->output["ListOnline"]["accept"]=1;
		$this->output["ListOnline"]["id"]="ListOnline";
		
		$this->View["my"]=$this->getUsers($_SESSION["uid"]);//берем данные для того кому выводим список
		
		$this->query="SELECT * FROM etcusersonline ";
		$this->View["sql"]=mysql_query($this->query) or die(mysql_error());
		$this->View["countList"]=0;
		while ($this->View["online"]=mysql_fetch_array($this->View["sql"])) {
			$this->View["User"]=$this->getUsers($this->View["online"]["uid"]);
			//print_r($this->View["User"]);
			if($this->View["parameters"]["HideSysOp"]===TRUE AND $this->View["User"]["setting"]["int_permission"]>=$this->View["my"]["setting"]["int_permission"]){continue;}
			//надо сделать таблице setting в которой будет сопоставлен логин и картинка и при проверке, если совпадения есть выводить картинку
			if($this->View["User"]["setting"]["permission_status"]==1){
				$this->View["User"]["status"]=$this->View["User"]["info"]["status"];
			}else{$this->View["User"]["status"]="";}
			$this->View["tpl"]->assign(array(
				'UID'=>$this->View["User"]["global"]["uid"],
				'LOGIN'=>$this->View["User"]["global"]["login"],
				'COLOR'=>$this->View["User"]["setting"]["color"],
				'STATUS'=>$this->View["User"]["status"],
				'int_PERMISSION'=>$this->View["User"]["setting"]["int_permission"],
			));
			$this->View["tpl"]->parse('tpl.ONLINE');
			/* $this->View["output"]["text"]=$this->View["output"]["text"].
			"<div>
				<span onClick=\"Select('".$this->View["_GLOBAL"]["login"]."','".$this->View["_GLOBAL"]["uid"]."','PRIVATE');\">
				PR1
				</span>
				<span class=\"TextBig\" onClick=\"Select('".$this->View["_GLOBAL"]["login"]."','".$this->View["_GLOBAL"]["uid"]."','EMPTY');\">
					<font color=\"".$this->View["_SETTING"]["color"]."\">".$this->View["_GLOBAL"]["login"]."</font>
				</span>
				<a href=\"../info/index.php?uid=".$this->View["_GLOBAL"]["uid"]."\" target=\"_blank\">INF</a>
			</div>"; */
			$this->View["countList"]++;
			//echo($this->View["countList"]."<br>");
		}
		if($this->View["countList"]!=0){
			$this->output["ListOnline"]["acceptText"][]=$this->View["tpl"]->text('tpl.ONLINE');
			$this->View["tpl"]->reset('tpl.ONLINE');
		}else{
			$this->output["ListOnline"]["notice"]=1;
			$this->output["ListOnline"]["noticeText"][]=$this->Lang("EMPTY_OBJECT");
		}
	return $this->output;
	}
	
	if($this->View["options"]["action"]=="ListUsers"){//ЗАВЕРШЕНО
		//unset($this->input["action"]);
		//print_r($this->input);
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		$this->output["ListUsers"]["error"]=0;
		$this->output["ListUsers"]["notice"]=0;
		$this->output["ListUsers"]["accept"]=1;
		$this->output["ListUsers"]["id"]="ListUsers";
		//print_r($this->input);
		//все что после BINARY регистрозависимо
		$this->query=" SELECT uid FROM etcusersglobal WHERE BINARY login LIKE '{$this->input["SORT_LETTER"]}%' ";
		$this->View["sql_dump"]=mysql_query($this->query) or die(mysql_error());
		$this->View["LINES"]=mysql_num_rows($this->View["sql_dump"]);
		//расчет страниц
		$this->View["PAGES"]=ceil($this->View["LINES"]/$this->View["parameters"]["LINESinPAGE"]);
		//условия достоверности
		$this->View["PAGES"]=($this->View["PAGES"]==0) ? 1 : $this->View["PAGES"];
		$this->input["SORT_PAGE"]=($this->input["SORT_PAGE"]>$this->View["PAGES"]) ? $this->View["PAGES"] : $this->input["SORT_PAGE"];
		$this->input["SORT_PAGE"]=($this->input["SORT_PAGE"]<1) ? 1 : $this->input["SORT_PAGE"];
		
		$this->View["LIMIT_FROM"]=($this->input["SORT_PAGE"]-1)*$this->View["parameters"]["LINESinPAGE"];
		
		if($this->View["LINES"] == 0){
			$this->output["ListUsers"]["notice"]=1;
			$this->output["ListUsers"]["noticeText"][]=$this->Lang("EMPTY_OBJECT");
		}else{
			$this->query=" SELECT uid,login FROM etcusersglobal WHERE BINARY login LIKE '{$this->input["SORT_LETTER"]}%' ORDER BY lower({$this->input["SORT_ORDERBY"]}) {$this->input["SORT_DECS"]} LIMIT {$this->View["LIMIT_FROM"]},{$this->View["parameters"]["LINESinPAGE"]}";              
			$this->View["sql_LIST"]=mysql_query($this->query) or die(mysql_error());
			while ($this->View["LIST"]=mysql_fetch_array($this->View["sql_LIST"])) {
				$this->View["user"]=$this->getUsers($this->View["LIST"]["uid"]);
				$this->View["tpl"]->assign(array(
					'UID'=>$this->View["user"]["global"]["uid"],
					'COLOR'=>$this->View["user"]["setting"]["color"],
					'int_PERMISSION'=>$this->View["user"]["setting"]["int_permission"],
					'LOGIN'=>$this->View["user"]["global"]["login"]
				));
				$this->View["tpl"]->parse('tpl.USERS');
			}
			$this->output["ListUsers"]["acceptText"][0]=$this->View["tpl"]->text('tpl.USERS');
			$this->output["ListUsers"]["acceptText"][0]=$this->output["ListUsers"]["acceptText"][0].$this->LinePages(array("SORT_PAGE"=>$this->input["SORT_PAGE"],"PAGES"=>$this->View["PAGES"]),array('LINESinPAGE'=>$this->View["parameters"]["LINESinPAGE"],'lengthLinkPAGES'=>$this->View["parameters"]["lengthLinkPAGES"]));
		}
	return $this->output;
	unset($this->output);
	}

	if($this->View["options"]["action"]=="intFoto"){//ЗАВЕРШЕНО
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		$this->output["intFoto"]["error"]=0;
		$this->output["intFoto"]["notice"]=0;
		$this->output["intFoto"]["id"]="intFoto";
		$this->View["user"]=$this->getUsers($this->input["uid"]);
		$this->output["intFoto"]["accept"]=1;
		$this->output["intFoto"]["acceptText"][]=$this->View["user"]["setting"]["int_foto"];
	return $this->output;
	unset($this->output);
	}

	if($this->View["options"]["action"]=="ListFoto"){//ЗАВЕРШЕНО
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		$this->output["ListFoto"]["error"]=0;
		$this->output["ListFoto"]["notice"]=0;
		$this->output["ListFoto"]["id"]="ListFoto";
		
		$this->View["options"]["EveryOne"]=(!$this->View["options"]["EveryOne"])? EVERYONE : $this->View["options"]["EveryOne"] ;
	//как то неправильно переносятся перем из функции в функцю (с точки зрения удобства)
		if($this->View["options"]["EveryOne"]===TRUE){
			$this->View["user"]=$this->getUsers('empty',array('EveryOne'=>TRUE),array('fotoTABLE'=>M_FOTO_TABLE,'fotoDIR'=>M_FOTO_DIR));
		}else{
			$this->View["user"]=$this->getUsers($this->input["uid"]);
		}
		$this->output["ListFoto"]["accept"]=1;
		$this->output["ListFoto"]["acceptText"][]=$this->View["user"]["foto"];
	return $this->output;
	unset($this->output);
	}
	
	if($this->View["options"]["action"]=="TextBlocked"){
		//проверка на бан
		$this->query="DELETE FROM etcusersblocked WHERE timestamp_out<{$this->generateTIMESTAMP()}";
		mysql_query($this->query) or die(mysql_error());
				$this->query="SELECT * FROM etcusersblocked WHERE uid='{$this->input["uid"]}'";
				$this->View["sql"]=mysql_query($this->query) or die(mysql_error());
				if((int)mysql_num_rows($this->View["sql"])!=0 ){
					$this->View["blocked"]=mysql_fetch_array($this->View["sql"]);
					$this->View["blocked"]["intTime"]=($this->View["blocked"]["timestamp_out"]-$this->View["blocked"]["timestamp_in"])/3600;
					$this->View["tpl"]->assign(array(
						'UID'=>$this->View["blocked"]["uid"],
						'LOGIN'=>$this->View["blocked"]["login"],
						'BLOCKED_FOR'=>$this->View["blocked"]["intTime"],
						'STARTING_DATE'=>$this->View["blocked"]["date"],
						'STARTING_TIME'=>$this->View["blocked"]["time"],
						'REASON'=>$this->View["blocked"]["reason"]
					));
					$this->View["tpl"]->parse('tpl.BLOCKED');
					$this->View["output"]["num_rows"]=1;
					$this->View["output"]["text"]=$this->View["tpl"]->text('tpl.BLOCKED');
				}else{$this->View["output"]["num_rows"]=0;}
	return $this->View["output"];
	}
	
unset($this->View);
}

function inputInTheField($input, $options='', $parameters=''){//ЗАВЕРШЕНО
//данные передаются в виде inputDATA= $inputArray["name"]=значение
	$this->input=$input;
	$this->output["_error"]=0;
	$this->output["module"]["accept"]=0;
	$this->output["form"]["accept"]=0;
	unset($this->input["action"]);
	
	foreach($this->input as $this->key["id"]=>$this->_array){
		$this->output[$this->key["id"]]["error"]=0;
		$this->output[$this->key["id"]]["notice"]=0;
		$this->output[$this->key["id"]]["accept"]=1;
		$this->output[$this->key["id"]]["acceptText"][]=$this->lang("VALIDLY");
		$this->output[$this->key["id"]]["id"]=$this->key["id"];
		
		
		switch ($this->input[$this->key["id"]]["typeText"]) {
		//регулярные выражения необходимо усовершенствовать, по мере придумывания корявых логинов пользователями
			case 'login': $this->template='/([n a-zA-Zа-яА-ЯЁё0-9-@_]+)/u';  break;
			case 'psw': $this->template='/^([a-zA-Zа-яА-ЯЁё0-9-@_]|\\s)+$/uis';  break; //так же используется для confirm_psw
			case 'pic': $this->template='/(^[0-9]*$)/u';  break;//OK
			case 'email': $this->template='/(^[a-zA-Z0-9-@_.-]*$)/u';  break;//OK
			case 'town': $this->template='/(^[n a-zA-Zа-яА-ЯЁё0-9-@_-]*$)/u';  break;//OK
			case 'url': $this->template='/(^[a-zA-Zа-яА-Я0-9-@_-:.\/\\\]*$)/u';  break;//OK
			case 'icq': $this->template='/(^[0-9]*$)/u';  break;//OK
			case 'text': $this->template='/(^[n a-zA-Zа-яА-ЯЁё0-9-@_,.\/\{\}\[\]\(\)\^\&\*;:\\\]*$)/u';  break;//OK но надо брать строку в 'строка', иначе убрать \
			case 'status': $this->template='/(^[n a-zA-Zа-яА-ЯЁё0-9-@_]*$)/u';  break;//OK должно быть одно слово без пробелов
			case 'color': $this->template='/(^#[0-9aAbBcCdDeEfF]{6}$)/u';  break;//OK
			case 'font': $this->template='/(^[n a-zA-Z]*$)/u';  break;//OK
			case 'font_size': $this->template='/(^[0-9]*$)/u';  break;//OK
			default: $this->template='/([a-zA-Zа-яА-Я0-9-@_]+)/u';
		} 
		
		if($this->input[$this->key["id"]]["name"]=="picture_signature" AND $this->input[$this->key["id"]]["value"]==""){
			$this->input[$this->key["id"]]["value"]="by default";
		
		}
		
		if(!preg_match($this->template,$this->input[$this->key["id"]]["value"])){
			$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_TEMPLATE");
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}
		//вместо имени для вычисления разрешенного количества символов берем значения column, так как оно не меняется, а имя может быть динамическим
		if(strlen(iconv('utf-8', 'windows-1251', $this->input[$this->key["id"]]["value"]))<$this->Letters(array('border'=>'MIN'),array('name'=>$this->input[$this->key["id"]]["column"]))){
			$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_MIN");
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}
		if(strlen(iconv('utf-8', 'windows-1251', $this->input[$this->key["id"]]["value"]))>$this->Letters(array('border'=>'MAX'),array('name'=>$this->input[$this->key["id"]]["column"]))){
			$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LETTER_MAX");
			$this->output[$this->key["id"]]["error"]=1;
			$this->output["_error"]=1;
		}

		if($this->input[$this->key["id"]]["name"]=='confirm_psw'){
			if($this->input["psw"]["value"]!=$this->input[$this->key["id"]]["value"]){
				$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_CONFIRM_PSW");
				$this->output[$this->key["id"]]["error"]=1;
				$this->output["_error"]=1;
			}
		}

		if($this->input[$this->key["id"]]["name"]=='pic'){
			if($_SESSION['pic']!=$this->input[$this->key["id"]]["value"]){
				$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_PIC");
				$this->output[$this->key["id"]]["error"]=1;
				$this->output["_error"]=1;
			}
		}
		if($this->input[$this->key["id"]]["name"]=='login' ){
			if ( !preg_match("/^([a-zA-Z0-9]|\\s)+$/uis",$this->input[$this->key["id"]]["value"]) AND !preg_match("/^((?<![a-zA-Z])[а-яA-Я0-9]|\\s)+$/uis",$this->input[$this->key["id"]]["value"])  AND $this->input[$this->key["id"]]["value"]!="") {
				$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_RU-EN");
				$this->output[$this->key["id"]]["error"]=1;
				$this->output["_error"]=1;
			}  
			$this->input[$this->key["id"]]["value"]=htmlspecialchars($this->input[$this->key["id"]]["value"]);
			$this->input[$this->key["id"]]["value"]=mysql_escape_string($this->input[$this->key["id"]]["value"]);
			if($this->input[$this->key["id"]]["module"]!='authorization'){//проверка существования логина по базе не проверяем при авторизации
				$this->xeshLogin=$this->generateXeshLogin($this->input[$this->key["id"]]["value"]);
				//echo($this->xeshLogin);
				$this->query = "SELECT login FROM etcusersglobal WHERE uid='{$this->xeshLogin}' ";
				$this->DB["sql"]=mysql_query($this->query);
				if(mysql_num_rows($this->DB["sql"]) != 0){
					$this->output[$this->key["id"]]["errorText"][]=$this->lang("ERROR_LOGIN_REGISTERED");
					$this->output[$this->key["id"]]["error"]=1;
					$this->output["_error"]=1;
				}
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

function Permission($inputDATA, $inputObject, $inputOptions){//пока не трогаем, может будет не по радио, а по селект
//данные передаются в виде $inputArray=значение
//объект передается в виде $inputObject=значение (TEXT,RADIO)
//опции передаются в виде $inputOptions[option_name]=значение (active,permission)
	$this->PERMISSION["inputDATA"]=$this->Clean($inputDATA,array('TYPE'=>'mixed'));
	$this->PERMISSION["inputObject"]=$this->Clean($inputObject,array('TYPE'=>'mixed'));
	$this->PERMISSION["inputOptions"]=$this->Clean($inputOptions,array('TYPE'=>'mixed'));
	
	//блок который прячет данные в информации о пользователе
	if($this->PERMISSION["inputObject"]=="TEXT"){
		return($this->PERMISSION["inputOptions"]["permission"]==0) ? $this->LANG('USER_HIDE_TEXT') : $this->PERMISSION["inputDATA"];
	}
	//блок выбора радио в настройках у пользователя
	if($this->PERMISSION["inputObject"]=="RADIO" AND $this->PERMISSION["inputOptions"]["active"]==0){
		return($this->PERMISSION["inputDATA"]==0)? "CHECKED" : "";
	}
	if($this->PERMISSION["inputObject"]=="RADIO" AND $this->PERMISSION["inputOptions"]["active"]==1){
		return($this->PERMISSION["inputDATA"]==1)? "CHECKED" : "";
	}
unset($this->PERMISSION);
}

function HideControl($input, $options, $parameters){//ЗАВЕРШЕНО
$this->HCL["tpl"] = new etcTemplate('template.tpl');
	$this->input=$input;
	$this->HCL["options"]=$options;
	$this->HCL["parameters"]=$parameters;
	
	if($this->HCL["options"]["type"]=="TEXT"){
		$this->HCL["text"]=($this->HCL["parameters"]["permission"]==0) ? $this->LANG('USER_HIDE_TEXT') : $this->input;
		$this->HCL["tplBlock"]=($this->HCL["parameters"]["permission"]==0) ? 'HIDE_TEXT' : 'OPEN_TEXT';
		$this->HCL["tpl"]->assign(array(
			'TEXT'=>$this->HCL["text"]
		));
		$this->HCL["tpl"]->parse('tpl.'.$this->HCL["tplBlock"]);
		$this->output=$this->HCL["tpl"]->text('tpl.'.$this->HCL["tplBlock"]);
		$this->HCL["tpl"]->reset('tpl.'.$this->HCL["tplBlock"]);
	return $this->output;
	unset($this->output);
	}

}

function Users($input, $options, $parameters=array('fotoDIR'=>FOTO_DIR1)){//ЗАВЕРШЕНО
$this->Users["tpl"] = new etcTemplate('template.tpl'); 
//$this->Users["tpl"] = new etcTemplate(DOCUMENT_ROOT.THEMES.DS.THEME.DS.VAR_TEMPLATE);
//данные передаются в виде $inputArray["table_name"]["column_name"]=значение
//объект передается в виде $inputObject=значение
//действие передается в виде $inputAction=значение
//опции передаются в виде $inputOptions[option_name]=значение(fotoDIR)
	$this->input=$input;
	$this->inputUsers=$input;
	$this->Users["options"]=$this->Clean($options,array('TYPE'=>'mixed'));
	$this->Users["parameters"]=$parameters;
	//умолчания
	$this->Users["parameters"]["fotoDIR"]=($this->Users["parameters"]["fotoDIR"]=="" OR !isset($this->Users["parameters"]["fotoDIR"]))? FOTO_DIR1 : $this->Users["parameters"]["fotoDIR"] ;
	
	if($this->Users["options"]["action"]=="AddAccount"){//ЗАВЕРШЕНО

		//добовляем массив из вне к массиву по умолчанию, который пишется в базу (стандартные значение переопределяются из массива из вне)
		$this->_define["info"]["uid"]="empty";
		$this->_define["info"]["date"]=$this->generateDATE();
		$this->_define["info"]["time"]=$this->generateTIME();
		$this->_define["info"]["email"]="by default";
		$this->_define["info"]["icq"]="by default";
		$this->_define["info"]["jabber"]="by default";
		$this->_define["info"]["hp"]="by default";
		$this->_define["info"]["country"]="by default";
		$this->_define["info"]["town"]="by default";
		$this->_define["info"]["about"]="by default";
		$this->_define["info"]["status"]="by default";
		$this->_define["info"]["signature"]="by default";
		$this->_define["setting"]["uid"]="empty";
		$this->_define["setting"]["color"]="grey";
		$this->_define["setting"]["color_in"]="black";
		$this->_define["setting"]["color_out"]="black";
		$this->_define["setting"]["text_type"]="areal";
		$this->_define["setting"]["text_size"]=12;
		$this->_define["setting"]["permission_email"]=0;
		$this->_define["setting"]["permission_icq"]=0;
		$this->_define["setting"]["permission_jabber"]=0;
		$this->_define["setting"]["permission_status"]=0;
		$this->_define["setting"]["permission_signature"]=0;
		$this->_define["setting"]["users_group"]="group_0000";
		//0000-таких семь штук для гость-админ, разработчик, 0000-одна дополнит,все, читать писать, читать,
		$this->_define["setting"]["int_permission"]=0000000000000000000000000000;
		$this->_define["setting"]["int_foto"]=1;
		$this->_define["statistic"]["uid"]="empty";
		$this->_define["statistic"]["lastvisit_timestamp"]=0;
		$this->_define["statistic"]["lastvisit_date"]=0;
		$this->_define["statistic"]["lastvisit_time"]=0;
		$this->_define["statistic"]["sum_time"]=0;
		$this->_define["statistic"]["sum_messages"]=0;

		//print_r($this->input);
		foreach($this->input as $this->Users["prefix"] => $this->Users["array"]){
			foreach($this->Users["array"] as $this->Users["key"] => $this->Users["value"]){
				if($this->Users["key"]=="pic"){unset($this->input[$this->Users["prefix"]][$this->Users["key"]]);
				}else{
					$this->_define[$this->Users["prefix"]][$this->Users["key"]]=$this->Users["value"];//перезапись значений по умолчанию
					$this->_redefine[$this->Users["prefix"]][$this->Users["key"]]=$this->Users["value"];//создание массива со значениями отличными от стандартных
				}
			}
		}
		//записываем уиды (обязательно после инициализации входящего массива)
		$this->_define["info"]["uid"]=$this->_define["global"]["uid"];
		$this->_define["setting"]["uid"]=$this->_define["global"]["uid"];
		$this->_define["statistic"]["uid"]=$this->_define["global"]["uid"];

		$this->Users["prefix_T"]="etcusers";
		$this->Users["INTO"]="";
		$this->Users["VALUES"]="";
		
		//print_r($this->_define);
		//формируем запрос для записи данных в таблицы
		foreach($this->_define as $this->Users["prefix"] => $this->Users["_array"]){
			$this->Users["table"]=$this->Users["prefix_T"].$this->Users["prefix"];
			foreach($this->Users["_array"] as $this->Users["column"] => $this->Users["value"]){
				$this->Users["INTO"]=$this->Users["INTO"].$this->Users["column"].",";
				$this->Users["VALUES"]=$this->Users["VALUES"]."'".$this->Users["value"]."',";
			}
			$this->Users["INTO"]="(".rtrim($this->Users["INTO"],',').")";//надо проверить работает ли rtrim
			$this->Users["VALUES"]="(".rtrim($this->Users["VALUES"],',').")";
			//записываем данные в таблицу
			$this->query="INSERT INTO {$this->Users["table"]} {$this->Users["INTO"]} VALUES {$this->Users["VALUES"]}";
			$this->Users["sql"]=mysql_query($this->query) or die(mysql_error());
			//обнуляем запросы
			$this->Users["INTO"]="";
			$this->Users["VALUES"]="";
		}
		$this->output["module"]["replace"]=0;
		$this->output["form"]["replace"]=1;
		$this->output["form"]["module"]="module_registration";
		$this->Users["tpl"]->assign(array(
				'acceptText'=>$this->LANG("REGISTRATION_OK")
			));
		$this->Users["tpl"]->parse('tpl.regOK');
		$this->output["form"]["acceptText"]=$this->Users["tpl"]->text('tpl.regOK');
		$this->Users["tpl"]->reset('tpl.regOK');
		//$this->output["form"]["acceptText"]=$this->LANG("REGISTRATION_OK");
		
	
 

	//-------==============--------запись на форум нового пользователя-----=============----------
define("FM_BOARDSTATS",		"../../forum/data/boardstats.php");
function _Read($filename,$newfile = TRUE) {
        		if (!file_exists($filename)/* && $newfile === TRUE*/) {
					return array();
					//fclose(fopen($filename,'a+'));
        			//@chmod($filename,$this->exbb['ch_files']);
        		}
        		$fp = @fopen($filename,'r') or die('Could not read from the file <b>'.$filename.'</b>');
        		//$this->_Flock($fp,$filename);
				flock($fp, 1);
        		$filesize = filesize($filename);
        		$filesize = ($filesize ===0) ? 1:$filesize-8;
        		fseek($fp,8);
        		$str = fread($fp,$filesize);
				flock($fp, 3);
        		fclose($fp);
        		return (!empty($str))?unserialize($str):array();
}
function _Read2Write(&$fp,$filename,$newfile = TRUE) {
				if (!file_exists($filename)/* && $newfile === TRUE*/) {
					@fclose(@fopen($filename,'a+'));
					//@chmod($filename,$this->exbb['ch_files']);
				}
				$fp = @fopen($filename,'a+') or die('Could not write in the file <b>'.$filename.'</b>');
				//$this->_Flock($fp,$filename,LOCK_EX);
				flock($fp, /*1*/2);
				$filesize = filesize($filename);
				$filesize = ($filesize ===0) ? 1:$filesize-8;
				fseek($fp,8);
				$str = fread($fp,$filesize);
				//flock($fp, 2);
				$_FilePointers[$fp] = $fp;
				return (!empty($str))?unserialize($str):array();
}
function _Write(&$fp,$arr) {
				fseek ($fp,0);
				ftruncate ($fp,0);
				fwrite($fp,'<?die;?>'.serialize($arr));
				fflush($fp);
				flock($fp,3);
				fclose($fp);
				unset($arr,$_FilePointers[$fp]);
				return;
}
function _BOARDSTATS() {
				$_Stats = _Read(FM_BOARDSTATS);
}
function _SAVE_STATS($array) {
				$stats = _Read2Write($fp_stats,FM_BOARDSTATS,FALSE);
				foreach ($array as $key => $value) {
						switch ($value[1]) {
							case -1: 	$stats[$key] = $stats[$key] - $value[0];
										break;
							case 0: 	$stats[$key] = $value[0];
										break;
							case 1: 	$stats[$key] = $stats[$key] + $value[0];
										break;
						}
				}
				_Write($fp_stats,$stats);
				return;
}
function _LowerCase($var) {
				//return (_RuLocale === FALSE) ? _strtolower($var):strtolower($var);
				return strtolower($var);
}

$user				= array();
	$user['id']			= 0;
	$user['status']		= 'me';
	$user['name']		= iconv('utf-8', 'windows-1251', $this->_define["global"]["login"]);
	$user['pass']		= md5(iconv('utf-8', 'windows-1251', $this->_define["global"]["psw"]));
	$user['mail']		= iconv('utf-8', 'windows-1251', $this->_define["info"]["email"]);
	$user['title']		= '';
	$user['posts']		= 0;
	$user['joined']		= time();
	$user['ip']			= "127.0.0.1";
	$user['showemail']	= FALSE;
	$user['www'] 		= iconv('utf-8', 'windows-1251', $this->_define["info"]["hp"]);
	$user['icq']		= iconv('utf-8', 'windows-1251', $this->_define["info"]["icq"]);
	$user['aim']		= '';
	$user['location']	= iconv('utf-8', 'windows-1251', $this->_define["info"]["town"]);
	$user['interests']	= iconv('utf-8', 'windows-1251', $this->_define["info"]["about"]);
	$user['sig']		= '';
	$user['sig_on']		= TRUE;//FALSE;
	$user['lang']		= 'russian';
	$user['skin']		= 'InvisionExBB';
	$user['timedif']	= 0;
	$user['avatar']		= 'noavatar.gif';
	$user['upload']		= FALSE;
	$user['visible'] 	= FALSE;
	$user['new_pm'] 	= FALSE;
	$user['sendnewpm'] 	= FALSE;
    $user['posts2page'] = 10;
	$user['topics2page']= 15;
	$user['last_visit'] = 0;

$allusers =_Read2Write($fp_allusers,"../../forum/data/users.php");
// foreach ($allusers as $u_id=>$info) {
	// echo($info['n']."-".$info['m']."-".$info['p']."<br>");
					
// }
			ksort($allusers, SORT_NUMERIC);
			end($allusers);
			$id = key($allusers) + 1;
_BOARDSTATS();
			//$_Stats = _Read("data/boardstats.php");
			$id = ($_Stats['last_id'] === $id) ? $id + 1:$id;
			$allusers[$id]['n'] = strtolower($user['name']);
			$allusers[$id]['m'] = $user['mail'];
			$allusers[$id]['p'] = 0;
			$allusers[$id]['h'] = $uid;
			_Write($fp_allusers,$allusers);
			unset($allusers);
           	$user['id']			= $id;
           	//$user['pass']		= md5($user['pass']);
           	//$user['last_visit'] = $fm->_Nowtime;


			_Read2Write($fp_user,'../../forum/members/'.$id.'.php');
	    	_Write($fp_user,$user); 	

_SAVE_STATS(array ('totalmembers' => array(1, 1),'lastreg' => array($user['name'], 0),'last_id' => array($id, 0)));

//--------------------------============================-------------------------------- 

		
		
		
	return $this->output;
	}

	if($this->Users["options"]["action"]=="DeleteAccount"){//ЗАВЕРШЕНО
	
		$this->query="DELETE FROM etcusersglobal WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		$this->query="DELETE FROM etcusersinfo WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		$this->query="DELETE FROM etcuserssetting WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		$this->query="DELETE FROM etcusersstatistic WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		
		$this->query="SELECT file FROM etcusersfoto WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		while($this->Users["foto"]=mysql_fetch_array($this->getUsers["sql"])){
			unlink($this->Users["parameters"]["fotoDIR"].$this->Users["foto"]["image"]);
		}
		$this->query="DELETE FROM etcusersfoto WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		
		$this->query="DELETE FROM etcusersblocked WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
		$this->query="DELETE FROM etcmoderatefoto WHERE uid='{$this->input["global"]["uid"]}'";
		mysql_query($this->query) or die(mysql_error());
	
	}
	
	if($this->Users["options"]["action"]=="DeleteFoto"){//ЗАВЕРШЕНО
		if(is_array($this->input["foto"])){
			foreach($this->input["foto"] as $this->Users["image"]){
			//echo($this->Users["parameters"]["fotoDIR"]."----<br>");
				unlink($this->Users["parameters"]["fotoDIR"].$this->Users["image"]);
				$this->query="DELETE FROM etcusersfoto WHERE file='{$this->Users["image"]}' AND uid='{$this->input["uid"]}'";
				mysql_query($this->query) or die(mysql_error());
				$this->query="UPDATE etcuserssetting SET int_foto=int_foto+1 WHERE uid='{$this->input["uid"]}' ";
				mysql_query($this->query) or die(mysql_error());
			}
		}
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		$this->output["ListFoto"]["error"]=0;
		$this->output["ListFoto"]["notice"]=0;
		$this->output["ListFoto"]["id"]="ListFoto";
		$this->Users["user"]=$this->getUsers($this->input["uid"]);
		$this->output["ListFoto"]["accept"]=1;
		$this->output["ListFoto"]["acceptText"][]=$this->Users["user"]["foto"];
	return $this->output;
	}
	
	if($this->Users["options"]["action"]=="TestAuthorization"){//ЗАВЕРШЕНО
	//надо дописать проверку если пользователь заблокирован (проверка по IP и логину) и вывести сообщение
	unset($this->error);//не делаем массив так как ошибки глобальные по всему классу
		$this->output["_error"]=0;
		$this->output["module"]["replace"]=0;
		$this->output["form"]["replace"]=0;
		foreach($this->inputUsers as $this->key["id"]=>$this->_array){
		//echo($this->inputUsers[$this->key["id"]]["name"]."=PPP");
			$this->output[$this->key["id"]]["error"]=0;
			$this->output[$this->key["id"]]["notice"]=0;
			$this->output[$this->key["id"]]["accept"]=1;
			$this->output[$this->key["id"]]["acceptText"][0]=$this->lang("VALIDLY");
			$this->output[$this->key["id"]]["id"]=$this->key["id"];
			
			$this->Users["prefix_T"]="etcusers";
			$this->Users["prefix"]="global";
			$this->Users["table"]=$this->Users["prefix_T"].$this->Users["prefix"];
		
			if($this->inputUsers[$this->key["id"]]["name"]=='login'){$this->Users["User"]=$this->getUsers($this->generateXeshLogin($this->inputUsers[$this->key["id"]]["value"]));}
			if($this->inputUsers[$this->key["id"]]["name"]=='login'){
				//echo("<--".$this->inputUsers[$this->key["id"]]["name"]."-->");
				//проверка на бан
				/* $this->query="SELECT * FROM etcusersblocked WHERE uid='{$this->Users["User"]["global"]["uid"]}'";
				$this->Users["sql"]=mysql_query($this->query) or die(mysql_error());
				if((int)mysql_num_rows($this->Users["sql"])!=0 ){
					$this->Users["blocked"]=mysql_fetch_array($this->Users["sql"]);
					$this->Users["blocked"]["intTime"]=($this->Users["blocked"]["timestamp_out"]-$this->Users["blocked"]["timestamp_in"])/3600;
					$this->Users["tpl"]->assign(array(
						'UID'=>$this->Users["blocked"]["uid"],
						'LOGIN'=>$this->Users["blocked"]["login"],
						'BLOCKED_FOR'=>$this->Users["blocked"]["intTime"],
						'STARTING_DATE'=>$this->Users["blocked"]["date"],
						'STARTING_TIME'=>$this->Users["blocked"]["time"],
						'REASON'=>$this->Users["blocked"]["reason"]
					));
					$this->Users["tpl"]->parse('tpl.BLOCKED');
					//$this->Users["tpl"]->reset('tpl.BLOCKED');
					//echo("Пользователь ".$this->ACCESS["_BLOCKED"]["login"]." заблокирован на ".$this->ACCESS["_BLOCKED"]["intTime"]."час(а,ов)  начиная с ".$this->ACCESS["_BLOCKED"]["date"]."-".$this->ACCESS["_BLOCKED"]["time"]."  по причине:".$this->ACCESS["_BLOCKED"]["reason"]);
					$this->output["_error"]=1;
					$this->output["module"]["replace"]=1;
					$this->output["module"]["location"]='http://elnyatown.ru/modules/blocked/index.php?uid='.$this->Users["User"]["global"]["uid"];
					$this->output["form"]["acceptText"]=$this->Users["tpl"]->text('tpl.BLOCKED'); */
					$this->blocked=$this->View(array('uid'=>$this->Users["User"]["global"]["uid"]),array('action'=>'TextBlocked'));
					if($this->blocked["num_rows"]!=0){
						$this->output["_error"]=1;
						$this->output["module"]["replace"]=1;
						$this->output["module"]["location"]='http://elnyatown.ru/modules/blocked/blocked.php?uid='.$this->Users["User"]["global"]["uid"];
						//$this->output["form"]["acceptText"]=$this->blocked["text"];
					}
					//echo("<--".$this->inputUsers[$this->key["id"]]["name"]."-->");
				//}
			}
			//все остальное проверяется если нет бана
			//echo("<-".$this->inputUsers[$this->key["id"]]["value"]."->");
			
			if($this->inputUsers[$this->key["id"]]["name"]=='login' AND $this->output["_error"]==0){
			//echo($this->Users["User"]["global"]["uid"]);
				$this->query="SELECT login FROM {$this->Users["table"]} WHERE uid='{$this->Users["User"]["global"]["uid"]}'";
				$this->Users["sql"]=mysql_query($this->query) or die(mysql_error());
				if(mysql_num_rows($this->Users["sql"]) == 0){
					$this->output["_error"]=1;
					$this->output[$this->key["id"]]["notice"]=1;
					$this->output[$this->key["id"]]["noticeText"][]=$this->lang("ERROR_LOGIN_NOT_FOUND");
				}
			}
			if($this->inputUsers[$this->key["id"]]["name"]=='psw' AND $this->output["_error"]==0){
				$this->query="SELECT psw FROM {$this->Users["table"]} WHERE uid='{$this->Users["User"]["global"]["uid"]}'";
				$this->Users["sql"]=mysql_query($this->query) or die(mysql_error());
				$this->Users["global"]=mysql_fetch_array($this->Users["sql"]);
				if($this->Users["global"]["psw"]!=$this->inputUsers[$this->key["id"]]["value"]){
					$this->output["_error"]=1;
					$this->output[$this->key["id"]]["notice"]=1;
					$this->output[$this->key["id"]]["noticeText"][]=$this->lang("ERROR_PASSWORD_NOT_MATCH");
				}
			}
		}
	return $this->output;
	unset($this->output);
	}
	
	if($this->Users["options"]["action"]=="Authorization"){//ЗАВЕРШЕНО
		unset($this->_redefine);
		$this->_redefine["global"]["uid"]=$this->generateXeshLogin($this->input["login"]["value"]);
		$this->_redefine["global"]["login"]=$this->input["login"]["value"];
		$this->_redefine["global"]["ssid"]=$this->input["login"]["ssid"];//берется из handler
		$this->_redefine["statistic"]["lastvisit_timestamp"]=$this->generateTIMESTAMP();
		$this->_redefine["statistic"]["lastvisit_date"]=$this->generateDATE();
		$this->_redefine["statistic"]["lastvisit_time"]=$this->generateTIME();
		$this->Users["prefix_T"]="etcusers";
		foreach($this->_redefine as $this->Users["prefix"] => $this->Users["_array"]){
			$this->Users["table"]="";
			$this->SET="";
			$this->Users["table"]=$this->Users["prefix_T"].$this->Users["prefix"];
			foreach($this->Users["_array"] as $this->Users["column"] => $this->Users["value"]){
				$this->SET=$this->SET." ".$this->Users["column"]."='".$this->Users["value"]."',";
			}
			$this->SET{strlen($this->SET)-1} = " ";//обрезает последнюю ","
			$this->query="UPDATE {$this->Users["table"]} SET {$this->SET} WHERE uid='{$this->_redefine["global"]["uid"]}'";
			mysql_query($this->query) or die(mysql_error());
		}
		//пишем в online
		$this->Online(array('uid'=>$this->_redefine["global"]["uid"]),array('action'=>'Add'));
		//пишем в сеесию необходимые данные
		$_SESSION["uid"]=$this->_redefine["global"]["uid"];
		$_SESSION["ssid"]=$this->_redefine["global"]["ssid"];
		$_SESSION["login"]=$this->_redefine["global"]["login"];
		$_SESSION["Authorization"]='TRUE';
		$_SESSION["Authorization1"]='TRUE';
		//пишем в куки необходимые данные
		// создаем вектор начального состояния для шифрования
		$this->iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB), MCRYPT_RAND);
		$this->key = $this->_redefine["global"]["ssid"]; // ключ для расшифрования
		$this->Authorization = "TRUE";
		$this->crypt["Authorization"] = mcrypt_encrypt(MCRYPT_CAST_256, $this->key, $this->Authorization, MCRYPT_MODE_CFB, $this->iv);
		//$this->crypt["group"] = mcrypt_encrypt(MCRYPT_CAST_256, $this->key, $this->Authorization, MCRYPT_MODE_CFB, $this->iv);
		setcookie("Authorization", $this->crypt["Authorization"]);
		setcookie("iv", $this->iv);
		setcookie("uid", $this->_redefine["global"]["uid"]);
		
		$this->Cmd(array('uid'=>$this->_redefine["global"]["uid"]),array('action'=>'UserIn'));//сообщение о входе в чат нового пользователя
		$this->output["module"]["replace"]=1;
		$this->output["module"]["location"]='modules/chat/index.php';

	return $this->output;
	unset($this->output);
	}
	
	if($this->Users["options"]["action"]=="SaveSetting"){//ЗАВЕРШЕНО
		$this->output=Array();
		$this->output["_error"]=0;
		$this->output["module"]["accept"]=0;
		$this->output["form"]["accept"]=0;
		
		$this->input=$input;
		unset($this->input["_error"]);
		unset($this->input["module"]);
		unset($this->input["form"]);
		$this->key["prefix"]="etcusers";
		foreach($this->input as $this->key["id"] => $this->_array){
			$this->output[$this->key["id"]]["error"]=0;
			$this->output[$this->key["id"]]["notice"]=0;
			$this->output[$this->key["id"]]["accept"]=1;
			unset($this->output[$this->key["id"]]["acceptText"]);
			$this->output[$this->key["id"]]["acceptText"][]=$this->lang("SAVE_OK");
		
			$this->output[$this->key["id"]]["id"]=$this->key["id"];
			if($this->input[$this->key["id"]]["table"]=="setting" OR $this->input[$this->key["id"]]["table"]=="info" OR $this->input[$this->key["id"]]["table"]=="foto"){//запрос в базу генерируется только для таблицы setting и info (исключается перезапись остальных данных)
				$this->table="";
				$this->SET="";
				$this->table=$this->key["prefix"].$this->input[$this->key["id"]]["table"];
				$this->SET=" ".$this->input[$this->key["id"]]["column"]."='".$this->input[$this->key["id"]]["value"]."'";
				$this->WHERE=($this->input[$this->key["id"]]["table"]=="foto")?  "uid='{$this->input[$this->key["id"]]["uid"]}' AND filename='{$this->input[$this->key["id"]]["name"]}'" :  "uid='{$this->input[$this->key["id"]]["uid"]}'" ;

			}
		}
		//$this->query="UPDATE {$this->table} SET {$this->SET} WHERE uid='{$this->input[$this->key["id"]]["uid"]}'";
		$this->query="UPDATE {$this->table} SET {$this->SET} WHERE {$this->WHERE}";
		//echo($this->query);
		mysql_query($this->query) or die(mysql_error());//сдесь можно добавить ошибку в наш массив и показать пользователю что не сохранилось
		//$this->output[$this->key["id"]]["name"]=$this->input[$this->key["id"]]["name"];
		$this->output[$this->key["id"]]["erase"]=1;
		$this->output[$this->key["id"]]["value"]=$this->input[$this->key["id"]]["value"];
	return $this->output;
	unset($this->input);
	unset($this->output);
	}
	
}

function SysOp($input, $options, $parameters=array()){//ЗАВЕРШЕНО
//данные передаются в виде $inputArray["table_name"]["column_name"]=значение
//объект передается в виде $inputObject=значение
//действие передается в виде $inputAction=значение 
//опции передаются в виде $inputOptions[option_name]=значение (LINESinPAGE, lengthLinkPAGES)
$this->SysOp["tpl"] = new etcTemplate('template.tpl');
	unset($input['action']);//сдесь action приходит от JS
	$this->input=$input;
	//$this->M["options"]=$this->CLEAN($options,array('TYPE'=>'mixed'));
	//$this->M["parameters"]=$this->CLEAN($parameters,array('TYPE'=>'mixed'));
	$this->SysOp["options"]=$options;
	$this->SysOp["parameters"]=$parameters;
	
	if($this->SysOp["options"]["action"]=="BlockedUser"){//ЗАВЕРШЕНО
		unset($this->input["action"]);
		if(is_array($this->input)){
			foreach($this->input as $this->key["uid"] => $this->_array){
				$this->SysOp["user"]=$this->getUsers($this->key["uid"]);//берем данные для выделенного пользователя
				$this->SysOp["moderator"]=$this->getUsers($_SESSION["uid"]);//берем данные для модератора
				
				//ОШИБКА не пишет в базу удаленных при активном условии
				//if($this->SysOp["user"]["setting"]["int_permission"]>=$this->SysOp["moderator"]["setting"]["int_permission"]){continue;}
				//если пользователь удален уже(гонки) просто пропускаем удаление
				$this->query="SELECT * FROM etcusersblocked WHERE uid='{$this->SysOp["user"]["global"]["uid"]}'";
				$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
				if(mysql_num_rows($this->SysOp["sql"]) != 0){continue;}//условие надо, если несколько модераторов в одно время попытаютя удалить одного пользователя
				
				$this->query="SELECT * FROM etcusersonline WHERE uid='{$this->SysOp["user"]["global"]["uid"]}'";
				$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
				$this->SysOp["online"]=mysql_fetch_array($this->SysOp["sql"]);
				$this->SysOp["online"]["timestamp_out"]=$this->generateTIMESTAMP()+(3600*$this->input[$this->key["uid"]]["hour"]);
				$this->SysOp["INTO"]="(ip, uid, login, date, time, timestamp_in, timestamp_out, moderator, int_permission, reason)";
				$this->SysOp["VALUES"]="('".$this->SysOp["online"]["ip"]."', '".$this->SysOp["online"]["uid"]."', '".$this->SysOp["user"]["global"]["login"]."', '".$this->generateDATE()."', '".$this->generateTIME()."',
				'".$this->generateTIMESTAMP()."', '".$this->SysOp["online"]["timestamp_out"]."', '".$this->SysOp["moderator"]["global"]["uid"]."', '".$this->SysOp["moderator"]["setting"]["int_permission"]."', '".$this->input[$this->key["uid"]]["reason"]."')";
				$this->query="INSERT INTO etcusersblocked ".$this->SysOp["INTO"]." VALUES ".$this->SysOp["VALUES"];
				mysql_query($this->query) or die(mysql_error());
				
				//определяем переменные для формирования сообщения в Cmd
				$this->SysOp["send"]["uid"]=$this->SysOp["user"]["global"]["uid"];
				$this->SysOp["send"]["ssid"]=$this->SysOp["user"]["global"]["ssid"];
				$this->SysOp["send"]["login"]=$this->SysOp["user"]["global"]["login"];
				$this->SysOp["send"]["color"]=$this->SysOp["user"]["setting"]["color"];
				$this->SysOp["send"]["m_login"]=$this->SysOp["moderator"]["global"]["login"];
				$this->SysOp["send"]["m_color"]=$this->SysOp["moderator"]["setting"]["color"];
				$this->SysOp["send"]["reason"]=$this->input[$this->key["uid"]]["reason"];

				$this->Cmd($this->SysOp["send"],array('action'=>'BlockedUser'));
				//удаляем из онлайна может быть так, что после удаления функция online опять запишет пользователя как активного
				$this->Online(array('uid'=>$this->SysOp["user"]["global"]["uid"]),array('action'=>'Delete'));
			}
		}
	return $this->View('empty',array('action'=>'ListOnline'),array('HideSysOp'=>TRUE));
	}
	
	if($this->SysOp["options"]["action"]=="AllowFoto"){//ЗАВЕРШЕНО
		if(is_array($this->input["file"])){
			foreach($this->input["file"] as $this->SysOp["key"] => $this->SysOp["file"]){
				if(copy(PRELOAD_DIR1.$this->SysOp["file"],FOTO_DIR1.$this->SysOp["file"])){
					if(unlink(PRELOAD_DIR.$this->SysOp["file"])){
						//пишем в базу изменения
						$this->query="SELECT * FROM etcmoderatefoto WHERE file='{$this->SysOp["file"]}'";
						$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
						$this->SysOp["foto"]=mysql_fetch_array($this->SysOp["sql"]);
						$this->query="DELETE FROM etcmoderatefoto WHERE file='{$this->SysOp["file"]}'";
						$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
						list($this->SysOp["name"],$this->SysOp["microtime"],$this->SysOp["exp"])=explode(".",$this->SysOp["file"]);
						$this->SysOp["filename"]=$this->SysOp["name"].$this->SysOp["microtime"];
						$this->SysOp["INTO"]="(uid, filename, microtime, exp, file, signature)";
						$this->SysOp["VALUES"]="('{$this->SysOp["foto"]["uid"]}', '{$this->SysOp["filename"]}', '{$this->SysOp["microtime"]}', '{$this->SysOp["exp"]}', '{$this->SysOp["file"]}', '{$this->SysOp["foto"]["signature"]}')";
						$this->query="INSERT INTO etcusersfoto {$this->SysOp["INTO"]} VALUES {$this->SysOp["VALUES"]}";
						$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
					}
				}
			}
		}
	return $this->View(array('uid'=>'empty'),array('action'=>'ListFoto','EveryOne'=>TRUE));
	unset($this->output);
	}
	
	if($this->SysOp["options"]["action"]=="DennyFoto"){//ЗАВЕРШЕНО
		//print_r($this->Moderator["inputDATA"]["SELECT"]);
		if(is_array($this->input["file"])){
			foreach($this->input["file"] as $this->SysOp["key"] => $this->SysOp["file"]){
				unlink(PRELOAD_DIR.$this->SysOp["file"]);
				$this->query="SELECT * FROM etcmoderatefoto WHERE file='{$this->SysOp["file"]}'";
				$this->SysOp["sql"]=mysql_query($this->query) or die(mysql_error());
				$this->SysOp["foto"]=mysql_fetch_array($this->SysOp["sql"]);
				$this->query="DELETE FROM etcmoderatefoto WHERE file='{$this->SysOp["file"]}'";
				mysql_query($this->query) or die(mysql_error());
				$this->query="UPDATE etcuserssetting SET int_foto=int_foto+1 WHERE uid='{$this->SysOp["foto"]["uid"]}'";
				mysql_query($this->query) or die(mysql_error());
			}
		}
	return $this->View(array('uid'=>'empty'),array('action'=>'ListFoto','EveryOne'=>TRUE));
	unset($this->output);
	}
unset($this->SysOp);
}

function Cmd($input,$options,$parameters=array()){//ЗАВЕРШЕНО
	//$this->data=$this->CLEAN($data,'nospace_mixed','');
	$this->Cmd["tpl"] = new etcTemplate('template.tpl');
	//$this->Cmd["tpl"] = new etcTemplate(DOCUMENT_ROOT.THEMES.DS.THEME.DS.VAR_TEMPLATE);
$this->input=$input;
//print_r($this->data);
	$this->options=$options;
	$this->parameters=$parameters;
	//взять все ссид и запихнуть в масстив
	$this->query="SELECT * FROM etcusersonline ";
	$this->Cmd["sql"]=mysql_query($this->query) or die(mysql_error());
	while ($this->Cmd["ONLINE"]=mysql_fetch_array($this->Cmd["sql"])) {
		$this->Cmd["ssids"][]=$this->Cmd["ONLINE"]["ssid"];
	}

	if($this->options["action"]=="Ping"){
		//пинг чтобы не закрывалась сессия, отправляется при запросе кто онлайн
		$_RPL = new Dklab_Realplexor(RPL_IP, RPL_PORT, RPL_NAMESPACE);
		$this->Cmd["message"]="Ping";
		$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"], array($_SESSION["ssid"]));
	}
	
	if($this->options["action"]=="UserIn"){//ЗАВЕРШЕНО
		$_RPL = new Dklab_Realplexor(RPL_IP, RPL_PORT, RPL_NAMESPACE);
		$this->Cmd["User"]=$this->getUsers($this->input["uid"]);
		$this->Cmd["tpl"]->assign(array(
			'TIME'=>$this->generateTIME(),
			'COLOR'=>$this->Cmd["User"]["setting"]["color"],
			'LOGIN'=>$this->Cmd["User"]["global"]["login"]
		));
		$this->Cmd["tpl"]->parse('tpl.MessageUserIn');
		$this->Cmd["message"]=$this->Cmd["tpl"]->text('tpl.MessageUserIn');
		$this->Cmd["tpl"]->reset('tpl.MessageUserIn');
		$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"], $this->Cmd["ssids"]);
	}
	
	if($this->options["action"]=="BlockedUser"){//ЗАВЕРШЕНО
		$_RPL = new Dklab_Realplexor(RPL_IP, RPL_PORT, RPL_NAMESPACE);
		$_RPL->send(array(RPL_CHANNEL), 'LocksUser',array($this->input["ssid"]));//команда пользователю что он заблокирован (заменяет окно с чатом на страницу user_blocked.php)
		$this->Cmd["tpl"]->assign(array(
			'TIME'=>$this->generateTIME(),
			'UID'=>$this->input["uid"],
			'LOGIN'=>$this->input["login"],
			'COLOR'=>$this->input["color"],
			'M_LOGIN'=>$this->input["m_login"],
			'M_COLOR'=>$this->input["m_color"],
			'REASON'=>$this->input["reason"]
		));
		$this->Cmd["tpl"]->parse('tpl.MessageUserDeleted');
		$this->Cmd["message"]=$this->Cmd["tpl"]->text('tpl.MessageUserDeleted');
		$this->Cmd["tpl"]->reset('tpl.MessageUserDeleted');
		
		$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"], $this->Cmd["ssids"]);
	}

	if($this->options["action"]=="UserOut"){//ЗАВЕРШЕНО
		$_RPL = new Dklab_Realplexor(RPL_IP, RPL_PORT, RPL_NAMESPACE);
		$this->Cmd["User"]=$this->getUsers($this->input["uid"]);
		$this->Cmd["tpl"]->assign(array(
			'TIME'=>$this->generateTIME(),
			'COLOR'=>$this->Cmd["User"]["setting"]["color"],
			'LOGIN'=>$this->Cmd["User"]["global"]["login"]
		));
		$this->Cmd["tpl"]->parse('tpl.MessageUserOut');
		$this->Cmd["message"]=$this->Cmd["tpl"]->text('tpl.MessageUserOut');
		$this->Cmd["tpl"]->reset('tpl.MessageUserOut');
		$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"], $this->Cmd["ssids"]);
	}
	
	if($this->options["action"]=="SendMessage"){//просто сказать всем //ЗАВЕРШЕНО
		$_RPL = new Dklab_Realplexor(RPL_IP, RPL_PORT, RPL_NAMESPACE);
		//подключаем парсер
		$this->input["message"]=ETCparser::run($this->input["message"]);
		$this->Cmd["User_whoSend"]=$this->getUsers($this->input["whoSend"]);//берем настройки пользователя кто написал сообщение
		if($this->input["noprivate"]=="EMPTY" AND $this->input["private"]=="EMPTY"){//сказать просто в чат
			$this->myssid=array_search($_SESSION["ssid"],$this->Cmd["ssids"]);//находим ключ своего сид
			unset($this->Cmd["ssids"][$this->myssid]);//удаляем из массива свой сид
			$this->Cmd["User"]=$this->getUsers($_SESSION["uid"]);//берем настройки пользователя
			$this->Cmd["tpl"]->assign(array(
				'TIME'=>$this->generateTIME(),
				'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
				'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
				'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
				'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_out"],
				'MESSAGE'=>$this->input["message"]
			));
			$this->Cmd["tpl"]->parse('tpl.MESSAGE');
			$this->Cmd["messageMe"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
			$this->Cmd["tpl"]->reset('tpl.MESSAGE');
			$_RPL->send(array(RPL_CHANNEL), $this->Cmd["messageMe"],array($_SESSION["ssid"]));//отдаем сообщение только себе своего цвета

			if(count($this->Cmd["ssids"])>0){
				$this->Cmd["tpl"]->assign(array(
					'TIME'=>$this->generateTIME(),
					'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
					'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
					'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
					'COLOR_MESSAGE'=>'black',//пока черный берем как цвет по умолчанию, когда отправляем всем
					'MESSAGE'=>$this->input["message"]
				));
				$this->Cmd["tpl"]->parse('tpl.MESSAGE');
				$this->Cmd["message"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
				$this->Cmd["tpl"]->reset('tpl.MESSAGE');
				$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"],$this->Cmd["ssids"]);//всем отдаем сообщение стандартного цвета
				echo("Всем");
			}
			echo("себе");
		}

		if($this->input["noprivate"]!="EMPTY" AND $this->input["private"]=="EMPTY"){//сказать кому нибудь без привата //ЗАВЕРШЕНО
			$this->myssid=array_search($_SESSION["ssid"],$this->Cmd["ssids"]);//находим ключ своего сид
			unset($this->Cmd["ssids"][$this->myssid]);//удаляем из массива свой сид
			$this->Cmd["User"]=$this->getUsers($_SESSION["uid"]);
			//$this->Cmd["User_whoSend"]=$this->getUsers($this->input["whoSend"]);//берем настройки пользователя кто написал сообщение
			$this->Cmd["tpl"]->assign(array(
				'TIME'=>$this->generateTIME(),
				'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
				'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
				'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
				'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_out"],
				//'MESSAGE'=>$this->input["message"].$this->Cmd["User"]["setting"]["color_out"]
				'MESSAGE'=>$this->input["message"]
			));
			$this->Cmd["tpl"]->parse('tpl.MESSAGE');
			$this->Cmd["messageMe"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
			$this->Cmd["tpl"]->reset('tpl.MESSAGE');
			$_RPL->send(array(RPL_CHANNEL), $this->Cmd["messageMe"],array($_SESSION["ssid"]));//отдаем сообщение только себе своего цвета

			$this->Cmd["User"]=$this->getUsers($this->input["noprivate"]);
			
			$this->userssid=array_search($this->Cmd["User"]["global"]["ssid"],$this->Cmd["ssids"]);//находим ключ сид кому пишем
			unset($this->Cmd["ssids"][$this->userssid]);//удаляем из массива сид кому пишем
			
			$this->Cmd["tpl"]->assign(array(
				'TIME'=>$this->generateTIME(),
				'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
				'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
				'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
				'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_in"],
				//'MESSAGE'=>$this->input["message"].$this->Cmd["User"]["setting"]["color_in"]
				'MESSAGE'=>$this->input["message"]
			));
			$this->Cmd["tpl"]->parse('tpl.MESSAGE');
			$this->Cmd["messageTo"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
			$this->Cmd["tpl"]->reset('tpl.MESSAGE');
			$_RPL->send(array(RPL_CHANNEL), $this->Cmd["messageTo"],array($this->Cmd["User"]["global"]["ssid"]));//отдаем сообщение только пользователю его цвета

			if(count($this->Cmd["ssids"])>0){
				$this->Cmd["tpl"]->assign(array(
					'TIME'=>$this->generateTIME(),
					'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
					'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
					'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
					'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_out"],
					'MESSAGE'=>$this->input["message"]
				));
				$this->Cmd["tpl"]->parse('tpl.MESSAGE');
				$this->Cmd["message"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
				$this->Cmd["tpl"]->reset('tpl.MESSAGE');
				$_RPL->send(array(RPL_CHANNEL), $this->Cmd["message"],$this->Cmd["ssids"]);//всем отдаем сообщение стандартного цвета
				echo("Всем");
			}
		}
	
		if($this->input["noprivate"]!="EMPTY" AND $this->input["private"]!="EMPTY"){//сказать кому нибудь в приват //ЗАВЕРШЕНО
			
			$this->Cmd["tpl"]->parse('tpl.wordPrivate');
			$this->Cmd["wordPrivate"]=$this->Cmd["tpl"]->text('tpl.wordPrivate');
			$this->Cmd["tpl"]->reset('tpl.wordPrivate');
			
			$this->Cmd["User"]=$this->getUsers($_SESSION["uid"]);
			//$this->Cmd["User_whoSend"]=$this->getUsers($this->input["whoSend"]);//берем настройки пользователя кто написал сообщение
			$this->Cmd["tpl"]->assign(array(
				'TIME'=>$this->generateTIME(),
				'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
				'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
				'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
				'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_out"],
				'MESSAGE'=>$this->Cmd["wordPrivate"].$this->input["message"]
			));
			$this->Cmd["tpl"]->parse('tpl.MESSAGE');
			$this->Cmd["messageMe"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
			$this->Cmd["tpl"]->reset('tpl.MESSAGE');
			$_RPL->send(array(RPL_CHANNEL), $this->Cmd["messageMe"],array($_SESSION["ssid"]));//отдаем сообщение только себе своего цвета

			$this->Cmd["User"]=$this->getUsers($this->input["private"]);
			$this->Cmd["tpl"]->assign(array(
				'TIME'=>$this->generateTIME(),
				'LOGIN'=>$this->Cmd["User_whoSend"]["global"]["login"],
				'UID'=>$this->Cmd["User_whoSend"]["global"]["uid"],
				'COLOR'=>$this->Cmd["User_whoSend"]["setting"]["color"],
				'COLOR_MESSAGE'=>$this->Cmd["User"]["setting"]["color_in"],
				'MESSAGE'=>$this->Cmd["wordPrivate"].$this->input["message"]
			));
			$this->Cmd["tpl"]->parse('tpl.MESSAGE');
			$this->Cmd["messageTo"]=$this->Cmd["tpl"]->text('tpl.MESSAGE');
			$this->Cmd["tpl"]->reset('tpl.MESSAGE');
			$_RPL->send(array(RPL_CHANNEL), $this->Cmd["messageTo"],array($this->Cmd["User"]["global"]["ssid"]));//отдаем сообщение только пользователю его цвета
		}
	}
}


/* function System($input, $callback=array()){
	$this->Sys["input"]=$input;
	$this->Sys["callback"]=$callback;
	foreach($this->Sys["callback"] as $this->callbackName){
		$callback=$this->callbackName;
		$this->$callback($this->Sys["input"][$this->callbackName]);
	}
} 
 */

function _mysql_close(){
	mysql_close($this->connect);
}
}
?>
