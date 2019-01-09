<?php
 header('Content-Type: text/javascript; charset=utf-8; ');
header('Access-Control-Allow-Origin: *');
		$start = microtime(true);

		$l_dict = $_POST['l_dict'];
		$r_dict = $_POST['r_dict'];
		$dict_check = isset($l_dict, $r_dict);
			if (!$dict_check) {
				$l_dict = 'dict';
				$r_dict = 'dict';
			}

		$length =$_POST['length']; 
		$log = $length === null;	
		if ($log) {
			$length = 12;
		}
		$light_password_checked = isset($_POST['light_password_checked']);
		$exclude_sim_symbols = isset($_POST['exclude_sim_symbols']);




	
		function getWord($dict_num = 'dict')																									 //Возврашает слово из словаря
		{	
			$dict = "ozegov_dict/ozegov_$dict_num.txt"; //Имя файла

			$LINE_DELIMITER = "\n"; //Разделитель строк в файле
			$MAX_LINE_LEN = 255 + strlen($LINE_DELIMITER); //Максимальная разумная длина одной строки
			$TRIPLE_BUFFER = $MAX_LINE_LEN * 3; //Берем немного с запасом
			$fileLen = filesize($dict); //Длина файла
			if (empty($fileLen) || $fileLen < $TRIPLE_BUFFER) { //На всякий случай проверим длину файла
			   throw new \ErrorException("Файл слишком мал");
			}    
			$rnd = rand(0, $fileLen - $TRIPLE_BUFFER); //Случайная позиция для чтения
			$handle = fopen($dict, "r"); //Открываем файл для чтения
			fseek($handle, $rnd); //Устанавливаем указатель в случайном месте файла
			$stream = fread($handle, $TRIPLE_BUFFER); //Читаем начиная со случайной позиции
			$lines = explode($LINE_DELIMITER, $stream); //Разбиваем текст на строки
			$lineCount = count($lines); //Считаем количество прочитанных строк
			$randomLine = ""; //Инициализируем нашу случайную строку
			$startAtLine = ($rnd !== 0) ? 1 : 0; //Пропускаем первую строку, так как она может быть обрезана
			for ($i = $startAtLine; $i < $lineCount; $i++) { //Ищем случайную строку
			    if (!empty($lines[$i])) {       //Пропускаем пустые строки
			        $randomLine = $lines[$i]; //Ну и наконец, вот наша случайная строка
			        break;    
			    }
			}

		return $randomLine;  
		}




		function changeRegister($dict_ = 'dict') { //меняет регистр // {для экономии ресурсов можно сделать после генерации getPassword} dict_ - словарь

			$randomLine = getWord($dict_);
			$random_case = rand(1,5);


			if ($random_case == 1) {
				$randomLine = mb_strtolower($randomLine, "UTF-8");  //  "слово"
			}
			if ($random_case == 2) {
				$randomLine = mb_convert_case($randomLine, MB_CASE_TITLE, "UTF-8");						//  "Слово"
			}
			if ($random_case == 3 ) {
				$randomLine = mb_convert_case($randomLine, MB_CASE_UPPER, "UTF-8");						// "СЛОВО"
			}
			if ($randomLine == 4) { 																	// "словО"
				$enc = 'UTF-8';
				$word =  $randomLine;
				$word = preg_replace('/\s+/u', '', $word);
				$word = mb_convert_case($word, MB_CASE_LOWER, "UTF-8");
				$count = mb_strlen($word, $enc); 
				$new_word = mb_substr($word, 0, ($count - 1), $enc);
				$new_word = $new_word . mb_strtoupper(mb_substr($word,($count - 1), 1, $enc), $enc);
				$randomLine = $new_word;
			}

			if ($random_case == 5) {  																	// "СловО"
				$enc = 'UTF-8';
				$word =  $randomLine;
				$word = preg_replace('/\s+/u', '', $word);
				$word = mb_convert_case($word, MB_CASE_TITLE, "UTF-8");
				$count = mb_strlen($word, $enc); 
				$new_word = mb_substr($word, 0, ($count - 1), $enc);
				$new_word = $new_word . mb_strtoupper(mb_substr($word,($count - 1), 1, $enc), $enc);
				$randomLine = $new_word;
			}
				return $randomLine;
		}




																															
																										
		

		function getSymbolOrNumber($not_null = null) { 																						//возврашает цифру, спецсимвол или пустую строку
			
			$Symbols="!@#$%^&*_+";
			$Numbers="1234567890";
			$SymbolsSize=StrLen($Symbols)-1;                                                                                                                                     
			$NumbersSize=StrLen($Numbers)-1;                                                                                                                                     
			$getSymbol=$Symbols[rand(0, $SymbolsSize)];
			$getNumber=$Numbers[rand(0, $NumbersSize)];

			$rand = rand(1, 3);
			if ($not_null == "not_null") {
				$rand = rand(1, 2);
			}
			$p3 = "";
			if ($rand == 1) {
				return $getNumber;
			}
			if ($rand == 2) {
				return $getSymbol;
			}
			if ($rand == 3) {
				return $p3;
			}

		}





		    

		function checkUpperRegister($words) {  //проверка регистра 
			    return (bool) preg_match('~\b[А-ЯЁA-Z]{2,}\b~u', $words);
			}
		function checkLowerRegister($words) {  //проверка регистра 
			    return (bool) preg_match('~\b[а-яёa-z]{2,}\b~u', $words);
			}
		function getPassword($light_password = "off") { 													//Возвращает  валидный пароль
				global $length;
				$glength = $length;	
				global	$l_dict;
				global	$r_dict;			 															
			

		do { //подбор спецсимволов
				$p_1 = getSymbolOrNumber().getSymbolOrNumber();
				$p_2 = getSymbolOrNumber("not_null").getSymbolOrNumber();
				$p_3 = getSymbolOrNumber().getSymbolOrNumber();
				$summa = $p_1.$p_2.$p_3;


					$number = preg_match_all('/[0-9]/u', $summa);
			        $symbol = preg_match_all('/[!@#$%^&*_+]/u', $summa);
			        $check = $symbol >= 1 && $number >= 2;
			        if ($light_password == "on" || $glength <=13) {
			        	$check = $symbol == 1 && $number == 2;
			        }

			} while (!$check);

			
			do {
				do {
				$word_1 = changeRegister($l_dict);
				$word_2 = changeRegister($r_dict);
				$check = checkUpperRegister($word_1) && checkUpperRegister($word_2) || checkLowerRegister($word_1) && checkLowerRegister($word_1);
				} while ($check);

					$summa = $p_1.$word_1.$p_2.$word_2.$p_3;
					$password = preg_replace('/\s+/u', '', $summa); 
					$check_lentgh = mb_strlen($password, 'UTF-8') == $glength;

				} while (!$check_lentgh);

			return $password;
		}






	function translatePassword() {
		global $exclude_sim_symbols;
		global $light_password_checked;
		if ($light_password_checked == true) {
			$light_password_checked = "on";
		}
		else
			$light_password_checked = "off";

		$EngWord = array(
		  'А' => 'F',	'Б' => ',',	'В' => 'D',	'Г' => 'U',	'Д' => 'L',	'Е' => 'T',	'Ё' => '~',	'Ж' => ':',	'З' => 'P',

		  'И' => 'B',	'Й' => 'Q',	'К' => 'R',	'Л' => 'K',	'М' => 'V',	'Н' => 'Y',	'О' => 'J',	'П' => 'G',	'Р' => 'H',

		  'С' => 'C',	'Т' => 'T',	'У' => 'E',	'Ф' => 'A',	'Х' => '{',	'Ц' => 'W',	'Ч' => 'X',	'Ш' => 'I',	'Щ' => 'O',

		  'Ъ' => '}',	'Ы' => 'S',	'Ь' => 'M',	 'Э' => '"',	'Ю' => '>',	'Я' => 'Z',	'а' => 'f',	'б' => ',',	'в' => 'd',

		  'г' => 'u',	'д' => 'l',	'е' => 't',	'ё' => '`',	'ж' => ';',	'з' => 'p',	'и' => 'b',	'й' => 'q',	'к' => 'r',

		  'л' => 'k',	'м' => 'v',	'н' => 'y',	'о' => 'j',	'п' => 'g',	'р' => 'h',	'с' => 'c',	'т' => 'n',	'у' => 'e',

		  'ф' => 'a',	'х' => '[',	'ц' => 'w',	'ч' => 'x',	'ш' => 'i',	'щ' => 'o',	'ъ' => ']',	'ы' => 's',	'ь' => 'm',

		  'э' => "'",	'ю' => '.',	'я' => 'z',
		);

			do {
				$password=getPassword($light_password_checked); //не переведенный пароль
				$newWord = strtr($password, $EngWord); // $newWord - Это переведенный пароль

				if ($exclude_sim_symbols == true) {
					$check = false;
					echo $check;
				}
				else {
				$check = //Проверка на последовательность
				preg_match_all("/il/u", $newWord) && preg_match_all("/li/u", $newWord) &&
				preg_match_all("/1l/u", $newWord) && preg_match_all("/l1/u", $newWord) && 
				preg_match_all("/Il/u", $newWord) && preg_match_all("/lI/u", $newWord) &&
				preg_match_all("/iIl/u", $newWord) && preg_match_all("/Iil/u", $newWord) && 
				preg_match_all("/Ili/u", $newWord) && preg_match_all("/lIi/u", $newWord) && 
				preg_match_all("/liI/u", $newWord) && preg_match_all("/ilI/u", $newWord) && 
				preg_match_all("/oO/u", $newWord) && preg_match_all("/Oo/u", $newWord) && 
				preg_match_all("/oO0/u", $newWord) && preg_match_all("/o0O/u", $newWord) && 
				preg_match_all("/0oO/u", $newWord) && preg_match_all("/0Oo/u", $newWord) && 
				preg_match_all("/Oo0/u", $newWord) && preg_match_all("/O0o/u", $newWord) &&
				preg_match_all("/B8/u", $newWord) && preg_match_all("/G6/u", $newWord) && 
				preg_match_all("/l|/u", $newWord) && preg_match_all("/I1/u", $newWord) &&
				preg_match_all("/1l/u", $newWord) && preg_match_all("/0O/u", $newWord) && 
				preg_match_all("/QD/u", $newWord) && preg_match_all("/OQ/u", $newWord) &&
				preg_match_all("/S5/u", $newWord) && preg_match_all("/Z2/u", $newWord) && 
				preg_match_all("/5S/u", $newWord) && preg_match_all("/2Z/u", $newWord) && 
				preg_match_all("/o0/u", $newWord) && preg_match_all("/0o/u", $newWord) && 
				preg_match_all("/O0/u", $newWord) && preg_match_all("/0O/u", $newWord) && 
				preg_match_all("/8B/u", $newWord) && preg_match_all("/B8/u", $newWord) && 
				preg_match_all("/i1/u", $newWord) && preg_match_all("/1i/u", $newWord) && 
				preg_match_all("/1I/u", $newWord) && preg_match_all("/I1/u", $newWord)&&
				preg_match_all("/8&/u", $newWord) && preg_match_all("/&8/u", $newWord);
				}
			} while ($check);


		
	return $password." "."==>"." ".$newWord;
		
}

$password1 = translatePassword();
$number = preg_match_all('/[0-9]/u', $password1);
$symbol = preg_match_all('/[!@#$%^&*_+]/u', $password1);
$pass_length = mb_strlen($password1, 'UTF-8');


echo "
var password1 = '$password1'
";
/*echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';*/
/*'цифр' $number
'символов' $symbol
'длинна' $pass_length;*/