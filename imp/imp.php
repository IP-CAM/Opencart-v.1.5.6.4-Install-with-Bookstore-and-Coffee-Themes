<?php
require('inc.php');
/*


НАДО ДОБАВИТЬ ДЕТСКИЕ эНЦИКЛОПЕДИИ

*/
$item='<h2 class="western">';
$files = preg_find('/./', './foto',
  PREG_FIND_RECURSIVE|PREG_FIND_RETURNASSOC |PREG_FIND_SORTMODIFIED);
  
  $img = array_keys($files) ;
  
$text='';
$begin=false;
function gettxt($line,$tag1, $tag2){
		$len1 = strlen($tag1);
		$len2 = strlen($tag2);
		return substr($line, $len1, strlen($line)-$len1-$len2-2);
}
function transliterate($string) {
    $roman = array('-','','-',"Sch","sch",'Yo','Zh','Kh','Ts','Ch','Sh','Yu','ya','yo','zh','kh','ts','ch','sh','yu','ya','A','B','V','G','D','E','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','','Y','','E','a','b','v','g','d','e','z','i','y','k','l','m','n','o','p','r','s','t','u','f','','y','','e');
    $cyrillic = array(' - ','.',' ', "Щ","щ",'Ё','Ж','Х','Ц','Ч','Ш','Ю','я','ё','ж','х','ц','ч','ш','ю','я','А','Б','В','Г','Д','Е','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Ь','Ы','Ъ','Э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','ь','ы','ъ','э');
    //$string = preg_replace('/\s+/', ' ',$string);
	return str_replace($cyrillic, $roman, mb_strtolower(trim($string)));
}


function sql(&$start, $image, $title, $text, $trans){
$sql=<<<EOT
INSERT INTO `oc_product` ( `model`,`quantity`, `stock_status_id`, `image`, `status` ) VALUES
( 'book',1, 5, 'data/$image', 1);

INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`) VALUES
($start, 2, '$title', '$text'), ($start, 1, '$title', '$text');

INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES
($start, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES
($start, 62);

INSERT INTO `oc_url_alias` (`query`, `keyword`) VALUES
('category_id=$start', '$trans');



EOT;
	
	
$start++;	

return $sql;
	
}


$handle = fopen ("boo.html", "r");
$counter = 0;
$header='';
$start = 56;

$img_dir_hndl = opendir('./foto');





while (!feof($handle))
{
  $line = fgets($handle);
  if ($line==''||$line==PHP_EOL) continue;
  
  // or $item2, $item3, etc.
  $pos = strpos($line, $item);
  if ($pos !== false)
  {
    
	if ($header!='' && $text!='') {
		$file=$img[$counter-1];
		$path_parts = pathinfo($file);
		$trans = transliterate($header);
		
		//copy($file, "./foto_new/".$trans);
		//echo "header='$header'  ", $trans ," <br><img src='$file' /><br> text='$text' <br><br>";
		echo sql($start, $trans.".".$path_parts['extension'], $header, $text, $trans);
	}
	
	$header = gettxt($line,$item, '</h2>');
	$begin = true;
	$text ='';
	$counter++;
  } else {
	if ($begin) $text.= $line;
	  
  }
}
//echo "header='$header'   <br><img src='{$img[48]}' /><br> text='$text' <br><br>";
//echo "counter = $counter";
fclose ($handle);
closedir($img_dir_hndl);
?>
