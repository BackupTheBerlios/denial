<?php

function to_html($text){
	return addslashes($text);
}

function to_raw($text)
{
	return stripslashes($text);
}

function urlify($text){

	return preg_replace("/\b((?:[A-Za-z]{3,}:\/\/)?(?:\w+:\w+@)?[-\w]+(?:\.[-\w]+)*(?:\.[A-Za-z]{2,3})+(?::\d{1,5})?(?:\/\S+)*\/?)\b/","<a href=\"$1\" target=\"blank\">$1</a>",$text);

}

function text_in($text){
	return addslashes(htmlspecialchars($text));
}

function text_out($text){
	return stripslashes($text);
}

function ip_cut($ip){
	if(preg_match("/^(((([1-9])|([1-9][\d])|((1[\d]{2})|(2[0-4][\d])|(25[0-4])))(\.(([\d])|([1-9][\d])|((1[\d]{2})|(2[0-4][\d])|(25[0-4])))){3})|(0(\.0){3}))$/",$ip)){
		list($part1,$part2,$part3,$part4) = explode(".",$ip);
		return $part1.".".$part2.".".$part3.".*";
	}else{
		$last = strrpos($ip,".");
		$first = strpos($ip,".");
		$newIp = substr(substr($ip,0,$last),$first);
		if(!empty($newIp)){
			return '*'.$newIp.'.*';
		}else{
			return $ip;
		}
	}
}


?>
