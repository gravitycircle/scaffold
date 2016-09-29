<?php
function stringPop($string) 
{
    $part1 = substr($string, 0, -1); // get chars upto last
    $part2 = substr($string, -1); // get last char

    return array($part1, $part2);
}

function pc_permute($items, $perms = array()) {
    if (empty($items)) { 
        echo join('', $perms) . "|";
    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             pc_permute($newitems, $newperms);
         }
    }
}

function custom_rand($length)
{      
    $chars = '0123456789abcdef';
    $result = '';
    for ($p = 0; $p < $length; $p++)
    {
        $result .= $chars[mt_rand(0, 15)];
    }
    
    return $result;
}

function random_oddeven($f, $t, $odd) {
	$res = mt_rand($f, $t);
	$oe = 0;
	if($odd) {
		$oe = 1;
	}

	if(($res % 2) == $oe) {
		return $res;
	}
	else{
		if($res == $t) {
			return $res - 1;
		}
		else{
			return $res + 1;
		}
	}
}

function generate($constant = false) {
	

	if(!$constant){
		$key = dechex(date('U', strtotime('+1 minute')));
	}
	else{
		$key = $constant;
	}

	$length = 16 - strlen($key);
	$order = custom_rand(1);
	$reverse = custom_rand(1);


	if(hexdec($reverse) % 2 == 1) {
		$key = strrev($key);
	} 

	$build = array($key,$reverse,custom_rand($length));

	ob_start();
	pc_permute(array(0,1,2));
	$permutations = explode('|', rtrim(ob_get_clean(), '|'));

	$use = 0;
	$out = '';

	if(hexdec($order) > 11) {
		$use = 14 - hexdec($order) + 3;
	}
	else{
		$use = floor(hexdec($order) / 2);
	}

	foreach(str_split($permutations[$use]) as $permute){
		$out .= $build[$permute];
	}

	$checklength = strpos($out, $length.'');
	$lengthbit = 0;

	if($checklength !== false) {
		//found

		if($checklength > 14) {
			$lengthbit .= dechex(random_oddeven(0, 14, false)).''.dechex($length);
		}
		else{
			$lengthbit = dechex(random_oddeven(0, 14, true)).dechex($checklength);
		}
	}
	else{
		$lengthbit .= dechex(random_oddeven(0, 14, false)).''.dechex($length);
	}

	return $out.$lengthbit.$order;
}

function degenerate($key, $constant = false) {
	$float = stringPop($key);
	$decrypt = $float[0];
	$order = (hexdec($float[1]) > 11 ? 14 - hexdec($float[1]) + 3 : floor(hexdec($float[1])/2));
	$lengthbit = '';
	for($i = 0; $i<2; $i++) {
		$float = stringPop($decrypt);
		$decrypt = $float[0];
		$lengthbit .= ''.$float[1];
	}

	$lengthbit = strrev($lengthbit);


	//lengthbit
	$lengthbit = str_split($lengthbit);
	$bitcheck = hexdec($lengthbit[0]) % 2;

	if($bitcheck === 0) {
		$length = hexdec($lengthbit[1]);
	}
	else{
		$tmp = str_split($key);
		$length = $tmp[hexdec($lengthbit[1])];
		unset($tmp);
	}

	$keylength = 16 - $length;

	//echo $decrypt.', '.$length.', '.$order;

	/*
	Key : 0
	Reverse: 1
	Randomhash : 2
	*/

	ob_start();
	pc_permute(array(0,1,2));
	$permutations = explode('|', rtrim(ob_get_clean(), '|'));


	if($permutations[$order] == '012'){
		//length = 8
		$decrypt = substr($key, 0, $keylength);
		$reverse = substr($key, $keylength, 1);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}

		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else if($permutations[$order] == '021'){
		$decrypt = substr($key, 0, $keylength);
		$reverse = substr($key, (intval($keylength)+ intval($length)), 1);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}

		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else if($permutations[$order] == '102'){
		$reverse = substr($key, 0, 1);
		$decrypt = substr($key, 1, $keylength);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}

		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else if($permutations[$order] == '120'){
		$reverse = substr($key, 0, 1);
		$decrypt = substr($key, ($length + 1), $keylength);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}

		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else if($permutations[$order] == '210'){
		$randrem = str_replace(substr($key, 0, $length), '', $key);

		$reverse = substr($randrem, 0, 1);
		$decrypt = substr($randrem, 1, $keylength);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}
		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else if($permutations[$order] == '201'){
		$randrem = str_replace(substr($key, 0, $length), '', $key);

		$decrypt = substr($randrem, 0, $keylength);
		$reverse = substr($randrem, $keylength, 1);

		if(hexdec($reverse) % 2 == 1) {
			$decrypt = strrev($decrypt);
			$reverseq = 'yes';
		} 
		else{
			$reverseq = 'no';
		}
		//return 'Decrypted: '.date('F d, Y h:ia', hexdec($decrypt)).' <br />Length: '.$length.' <br>Order: '.$permutations[$order].' <br />Reverse?: '.$reverseq.', '.$reverse.' <br />Key: '.$key.'<hr />';
	}
	else{
		return false;
	}

	//validate
	if($constant) {
		return $decrypt;
	}
	else{
		$final = hexdec($decrypt) - strtotime('now');
	
		if($final < 0) {
			return false;
		}
		else{
			return true;
		}
	}
}
$out = '';
for($i=0;$i<10;$i++){
 $out .= degenerate(generate());
}
?>