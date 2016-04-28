<?php
class apiAnna{
	public static function getSafeContent($fileDirect){
		//block until this file already edited
		$file = fopen($fileDirect, 'r');
		flock($file, LOCK_SH); 
		$txtMsg = file_get_contents($fileDirect);
		flock($file, LOCK_UN); 
		fclose($file);
		
		return $txtMsg;
	}

	public static function checkW5H($text){
		$W5H = array( 
				(object)[
					'siapa',
					'apa',
					'dimana',
					'kapan',
					'mengapa',
					'kenapa',
					'bagaimana'
				],
				(object)[
					'who',
					'what',
					'where',
					'when',
					'why',
					'how'
				]
			);
		
		$textArr = explode(' ', strtolower($text));
		
		$dumpW5H = [];
		foreach($W5H as $row){
			foreach($row as $innerRow){
				for($index = 0; $index<count($textArr);$index++){
					if($textArr[$index] == $innerRow){
						$dumpW5H[] = $innerRow;
					}else if($textArr[$index] == $innerRow.'kah'){
						$dumpW5H[] = $innerRow;
					}
				}
			}
		}
		
		if(count($dumpW5H) == 0){
			$dumpW5H[] = 'notwh5';
		}
		
		return $dumpW5H;
		
	}
	
	public static function createSerializeAnnaData($uid,$wh5,$init){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/public/'.$uid.'/'.$init.'/'.$wh5.'/';
		$fileDirect = public_path().$staticPath.$fileName;
		
		//startCreateFolder
		if (!file_exists(public_path().$staticPath)) {
			functionse::createFolderCustomName($staticPath);
			//put empty text
			file_put_contents($fileDirect, '', LOCK_EX);
		}
		
		
		//data list for temporary sorting
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/temporary/';
		$fileDirect = public_path().$staticPath.$fileName;
		
		//startCreateFolder
		if (!file_exists(public_path().$staticPath)) {
			functionse::createFolderCustomName($staticPath);
			//put empty text
			file_put_contents($fileDirect, '', LOCK_EX);
		}
		
		
		//data list for generate sorting
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/main/'.$init.'/'.$wh5.'/';
		$fileDirect = public_path().$staticPath.$fileName;
		
		//startCreateFolder
		if (!file_exists(public_path().$staticPath)) {
			functionse::createFolderCustomName($staticPath);
			//put empty text
			file_put_contents($fileDirect, '', LOCK_EX);
		}
		
		return true;
	}
	
	public static function createSerializeMainPattern(){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/pattern/';
		$fileDirect = public_path().$staticPath.$fileName;
		
		//startCreateFolder
		if (!file_exists(public_path().$staticPath)) {
			functionse::createFolderCustomName($staticPath);
			//put empty text
			file_put_contents($fileDirect, '', LOCK_EX);
		}
	
		return true;
	}
	
	public static function createSerielizePublicPattern($uid,$wh5,$init){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/pattern/'.$uid.'/'.$init.'/';
		$fileDirect = public_path().$staticPath.$fileName;
	
		//startCreateFolder
		if (!file_exists(public_path().$staticPath)) {
			functionse::createFolderCustomName($staticPath);
			//put empty text
			file_put_contents($fileDirect, '', LOCK_EX);
		}
	
		return true;
	}
	
	public static function generateInitialNodeToPublicPattern($uid,$wh5,$initial,$textArr){
		$initial = explode('-', $initial);
		$patternName = $initial[0];
		$patternNode = $initial[1];
		$init = substr($patternName, 0, 3);
		
		if(apiAnna::createSerielizePublicPattern($uid,$wh5,$patternNode)){	
			$fileName = 'data.txt';
			$staticPath = '/assets/anna/pattern/'.$uid.'/'.$patternNode.'/';
			$fileDirect = public_path().$staticPath.$fileName;	
			$textMsg = apiAnna::getSafeContent($fileDirect);
			
			$iPublicPatternArray = '';
			if($textMsg != ''){
					$dataRow = unserialize(base64_decode($textMsg));
					
					foreach($dataRow as $key => $value){
						if($key == 'public_pattern'){
							$nodeExist = false;
							foreach($value as $innerKey => $innerValue){
								//untuk orang yang sama mengatakan kata ini
								if($innerKey == 'uid'){
									if($innerValue == $uid){
										$nodeExist = true;
									}
								}
								
								if($innerKey == 'node'){
									if($nodeExist){
										foreach($textArr as $userText){
											//jangan push kecuali parent node
											if($userText != $patternName){
												$wh5Exist = true;
												$initExis = true;
												foreach($innerValue as $innerNode){
													if($innerNode['wh5'] == $wh5){
														$wh5Exist = false;
													}
													
													if( $innerNode['init'] == $userText){
														$initExis = false;
													}
													
													//var_dump($dataRow[$key][$innerKey]);
													//var_dump($userText .'!='. $init);
													//var_dump($innerNode['wh5'] .'!='. $wh5 .' and '. $innerNode['init'] .'!='. $init);
												}
												
												//var_dump($userText .'!='. $patternName);
												
												if($wh5Exist or $initExis){
													array_push($dataRow[$key][$innerKey], [ 'wh5' => $wh5, 'init' => $userText ]);
												}
											}
										}
									}else{
										array_push($dataRow[$key], [ 'uid' => $uid, 'node' => array( [ 'wh5' => $wh5, 'init' =>  $userText ] )]);
									}
								}
							}
						}
					}
					//update text
					$iPublicPatternArray = $dataRow;
					
			}else {
				//3 level of array 1(unique) intial, 2(unique) uid and 3(random) node 
				$iPublicPatternArray = Array();
				$iPublicPatternArray['pattern_name'] = $patternName;
				$iPublicPatternArray['pattern_node'] = $patternNode;
				$iPublicPatternArray['public_pattern'] = [ 'uid' => $uid, 'node' => array( [ 'wh5' => $wh5, 'init' => $init ] )];
			}
			
			$newCommentData = base64_encode(serialize($iPublicPatternArray));
			
			
			//var_dump($iPublicPatternArray);
			
		
			file_put_contents($fileDirect, $newCommentData,  LOCK_EX);
			
		}
		
		return true;
	}
	
	public static function appendInitialNodeToMainPattern($uid,$wh5,$init,$text){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/pattern/';
		$fileDirect = public_path().$staticPath.$fileName;
		$mainPattern =  apiAnna::getSafeContent($fileDirect);
		
		
		$textArr = array_unique(explode(' ',strtolower($text)));
		if($mainPattern != ''){
			
			//append Data if Not exist
			$object = unserialize(base64_decode($mainPattern));
			//for uid
			$uidFalse = true;
			foreach($object['list_uid'] as $row){
				if($row == $uid){
					$uidFalse = false;
					break;
				}
			}
			
			if($uidFalse){
				$object['list_uid'][] =  $uid;
			}
			
			//for pattern
			$objectPattern = $object['list_pattern'];
			$mainPatternArr = array_filter(explode(' ', strtolower($objectPattern)));
			
			$dumpPattern = [];
			foreach($textArr as $row){
				$dumpFalse = true;
				$checkDump = false;
				foreach($mainPatternArr as $innerRow){
					$patternName = explode('-',$innerRow)[0];
					if($row == $patternName){
						$dumpFalse =  false;
						//create public pattern
						apiAnna::generateInitialNodeToPublicPattern($uid,$wh5,$innerRow,$textArr);
					}
				}
				
				if($dumpFalse){
					$dumpPattern[] = $row;;
				}
			}
			
			//jika ada pattern baru
			if(count($dumpPattern) > 0){
				$newPatternArr = [];
				$initial = count($mainPatternArr);
				foreach($dumpPattern as $row){
					$initial +=1;
					$newPatternArr[] = $row.'-'.$initial;
				}
				
				$object['list_pattern'] =  $objectPattern.' '.implode(' ',$newPatternArr);
				
				//generate new pattern
				$appendPatternArr = base64_encode(serialize($object));
				file_put_contents($fileDirect, $appendPatternArr,  LOCK_EX);
			}
			
			return true;
		}else{
			$newPatternArr = array();
			$listPattern = array();
			
			$initial = 0;
			foreach($textArr as $row){
				$initial +=1;
				$listPattern[] = $row.'-'.$initial;
			}
			
			$newPatternArr['list_uid'] = [ $uid ];
			$newPatternArr['list_pattern'] = implode(' ', $listPattern );
			
			//rewrite
			$rewritePatternArr = base64_encode(serialize($newPatternArr));
			file_put_contents($fileDirect, $rewritePatternArr, LOCK_EX);
			return true;	
		}
		
	}

	
	public static function getSuitableNodeFromPublicPattern($uid,$patternNode,$text){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/pattern/'.$uid.'/'.$patternNode.'/';
		$fileDirect = public_path().$staticPath.$fileName;	
		
		if(file_exists(public_path().$staticPath)) {
			$textMsg = apiAnna::getSafeContent($fileDirect);
			
			$object = unserialize(base64_decode($textMsg));
			$textArr =  array_filter(explode(' ',strtolower($text)));	
			
			$matchNum = 0;
			foreach($object['public_pattern'] as $key => $value){
				if($key == 'node'){
					foreach($textArr as $row){
						foreach($value as $nodeValue){
							if($row == $nodeValue['init']){
								$matchNum += 1;
							}
						}
					}
				}
			}
			
			$relationWordsTot = count($object['public_pattern']['node']);
			$wordsDifferentiation = abs($relationWordsTot - $matchNum);
		}else{
			$wordsDifferentiation = 'NaN';
		}
		
		
		
		return array(
			'wordsDifferentiation' => $wordsDifferentiation,
			'uid' => $uid,
			'patternNode' => $patternNode
		);
		
	}
	
	public static function submitPublicMessage($uid,$wh5,$init,$text,$annaComment){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/public/'.$uid.'/'.$init.'/'.$wh5.'/';
		$fileDirect = public_path().$staticPath.$fileName;	
		$textMsg =  apiAnna::getSafeContent($fileDirect);
		//$annaComment = '';
		
		$iCommentArray = Array();
		if($textMsg != ''){
			$dataRow = unserialize(base64_decode($textMsg));
			//ini untuk user yang comment
			//initial new comment 
			$newAnnaComment = true;
			$newComment = false;
			$commentTot = 0;
			foreach($dataRow['user_comment'] as $key => $value){
				foreach($value as $innerKey => $innerValue){
					if($innerKey == 'response'){
						//var_dump(trim($innerValue) .'=='. trim($text));
						if(trim($innerValue) == trim($text)){
							$newComment =  true;
							
						}
						//if(strpos($innerValue, $text) !== false){
						//	$newComment =  true;
						//}
					}else{
						if($newComment){
							foreach($dataRow['user_comment'][$key][$innerKey] as $annaOldComment){
								if(trim($annaComment) == trim($annaOldComment)){
									$newAnnaComment = false;
									break;
								}
							}
							
							
							if($newAnnaComment){
								$dataRow['user_comment'][$key][$innerKey][] = $annaComment;
							}
							
							//set to zero
							$newComment =  false;
							break;
						}else{
							$commentTot += 1;
						}
					}
					
				}
											
				//var_dump($newComment,count($dataRow['user_comment']) .'=='. $commentTot);
				//exit();
				
				if(!$newComment){
					if(count($dataRow['user_comment']) == $commentTot){
						$dataRow['user_comment'][] = [ 'response' => $text, 'anna_comment' => array(  $annaComment ) ] ;
					}
				}
			}
			
		
				//var_dump($dataRow);	
				//exit();
			//update text
			$iCommentArray = base64_encode(serialize($dataRow));
			
		}
		

		if($textMsg == ''){
			$iCommentArray = Array();
			//temporary jika anna gak bisa jawab
			if($annaComment != ''){
				$aComment = $annaComment;
			}else{
				 $aComment = '';
			}
		
			// you know maybe the answer more than one so for better result we should create an array
			$iCommentArray['user_comment'] = array( [ 'response' => $text, 'anna_comment' => array(  $aComment ) ] ) ;
			$newCommentData = base64_encode(serialize($iCommentArray));
			
			
		}else{
			// hanya update comment saja
			
			$newCommentData = $iCommentArray;
		}
		
		//rewrite
		file_put_contents($fileDirect, $newCommentData, LOCK_EX);
			
		return true;	
	}			
	
	public static function submitMessageBaseFromUidandSuitablePublicPattern($getMainPattern,$uid,$wh5,$text,$annaComment){
		$dataSort = [];
		foreach($getMainPattern as $innerRow){
			//now we need to check the directory is exist
			if(apiAnna::createSerializeAnnaData($uid,$wh5,$innerRow)){
				$dataSort[] = apiAnna::getSuitableNodeFromPublicPattern($uid,$innerRow,$text);
			}
				
			
		}
		
		$smallersNum = [];
		foreach($dataSort as $row){
			$smallersNum[] = $row['wordsDifferentiation'];
		}
		

		//var_dump($dataSort);
		//var_dump($smallersNum);
		//var_dump(array_values($smallersNum)[0]);

		if(count($smallersNum) > 0){
			natsort($smallersNum);
			foreach($dataSort as $row){
				if($row['wordsDifferentiation'] == array_values($smallersNum)[0]){
					$getSuitableNode = $row['patternNode'];
					break;
				}
			}

			if(apiAnna::submitPublicMessage($uid,$wh5,$getSuitableNode,$text,$annaComment)){
				return $annaComment;
			}
		}else{
			return $smallersNum;
		}
	}
	
	
	public static function getSerializeAnnaComment($publicPutternNodeArr,$getMainPattern,$wh5,$text){
		$filterResponse = [];
		foreach($publicPutternNodeArr as $row){
			foreach($getMainPattern as $innerRow){
				$fileName = 'data.txt';
				$staticPath = '/assets/anna/public/'.$row.'/'.$innerRow.'/'.$wh5.'/';
				$fileDirect = public_path().$staticPath.$fileName;
				if(file_exists(public_path().$staticPath)) {
					$textMsg =  apiAnna::getSafeContent($fileDirect);
					$commentResponse = false;
					$iCommentArray = Array();
					if($textMsg != ''){
						$dataRow = unserialize(base64_decode($textMsg));
						foreach($dataRow['user_comment'] as $key => $value){
							foreach($value as $innerKey => $innerValue){
								if($innerKey == 'response'){
									if(trim($innerValue) == trim($text)){
										$commentResponse =  true;
									}	
									//var_dump($text, $innerValue);
									/*
									$textArr = explode(' ', $text);
									$similarTot = 0;
									foreach($textArr as $row){
										if(strpos($innerValue, $row) !== false){
											$similarTot+=1;
										}			
									}
									
									$textArrTot = count($textArr) - $similarTot;
									if($textArrTot >= 0 and $textArrTot <= 1){
										$commentResponse =  true;
									}*/
								}else{
									if($commentResponse){
										foreach($dataRow['user_comment'][$key][$innerKey] as $annaOldComment){
											$filterResponse[] = $annaOldComment;
											break;
										}
										
										$commentResponse =  false;
										break;
									}
								}
							}
						}
						
						if(count($filterResponse) > 0){
							break;
						}
						
					}
				}
			
				if(count($filterResponse) > 0){
					break;
				}
				
			}
		}
		
		if(count($filterResponse) > 0){
			return $filterResponse[0];
		}else{
			return '';
		}
	}
	
	public static function generateSerializeNodeToAnnaComment($uid,$wh5,$text){
		$fileName = 'data.txt';
		$staticPath = '/assets/anna/pattern/';
		$fileDirect = public_path().$staticPath.$fileName;
		$mainPattern =  apiAnna::getSafeContent($fileDirect);
		
		$textArr =  array_filter(explode(' ',strtolower($text)));		
		$object = unserialize(base64_decode($mainPattern));
		
		//for pattern
		$objectPattern = $object['list_pattern'];
		$mainPatternArr = array_filter(explode(' ', strtolower($objectPattern)));

		
		$getMainPattern = [];
		foreach($textArr as $row){
			foreach($mainPatternArr as $innerRow){
				$patternName = explode('-',$innerRow)[0];
				$patternNode = explode('-',$innerRow)[1];
				
				if($row == $patternName){
					$getMainPattern[] = $patternNode;
				}
			}
		}
		
		$publicPutternNodeArr = [];
		$objectUid = $object['list_uid'];
		$check = 0;
		$MainPatternTot = count($getMainPattern);
		foreach($objectUid as $row){
			foreach($getMainPattern as $innerRow){
				$fileName = 'data.txt';
				$staticPath = '/assets/anna/public/'.$row.'/'.$innerRow.'/';
				$fileDirect = public_path().$staticPath.$fileName;
				if(file_exists(public_path().$staticPath)) {
					$check += 1;
				}
			}
			
			//var_dump($MainPatternTot .'=='. $check);
			if($MainPatternTot == $check){
				$publicPutternNodeArr[] = $row;
			}
			
			$check = 0;
		}
		
		//var_dump($publicPutternNodeArr);
		
		
		return array(
			$publicPutternNodeArr,
			$getMainPattern
		);
		
	}
	
	public static function generateMainPattern($uid,$wh5,$init,$text,$responseText){
		if($responseText == null){
			if(apiAnna::createSerializeMainPattern()){
				if(apiAnna::appendInitialNodeToMainPattern($uid,$wh5,$init,$text)){
					$nodeArr = apiAnna::generateSerializeNodeToAnnaComment($uid,$wh5,$text);
					
					$publicPutternNodeArr = $nodeArr[0];
					$getMainPattern = $nodeArr[1];
					$annaComment = apiAnna::getSerializeAnnaComment($publicPutternNodeArr,$getMainPattern,$wh5,$text);
			
					if($annaComment != ''){
						return apiAnna::submitMessageBaseFromUidandSuitablePublicPattern($getMainPattern,$uid,$wh5,$text,$annaComment);
					}else{
						//anna tidak tahu mau jawab apa
						return '';
					}
				}
			}
		}else{
			$nodeArr = apiAnna::generateSerializeNodeToAnnaComment($uid,$wh5,$text);
			$getMainPattern = $nodeArr[1];
			return apiAnna::submitMessageBaseFromUidandSuitablePublicPattern($getMainPattern,$uid,$wh5,$text,$responseText);
		}
	}
	
	public static function getSerializeAnnaData($uid,$wh5,$init,$text,$responseText){
		return apiAnna::generateMainPattern($uid,$wh5,$init,$text,$responseText);
	}
	
	public static function submitMessage($wh5,$text,$responseText){
		
		if(Cookie::get('daddy') !==  null){
			$uid = 0;
		}else{
			$uid = 2;
		}

		
		//get initial for database
		$textString = preg_replace('/\s+/', '', $text);
		$init = substr($textString, 0, 3);
		
		//if wh5 array more than two  then get first of array
		if(count($wh5) > 1){
			$getNumber = 0;
			$dumpWH = [];
			$arrText = explode(' ', strtolower($text));
			foreach($arrText as $row){
				$getNumber += 1;
				foreach($wh5 as $innerRow){
					if($row == $innerRow){
						$dumpWH[] = (object)[
							'number' => $getNumber,
							'wh5' => $innerRow
						];
					}elseif($row == $innerRow.'kah'){
						$dumpWH[] = (object)[
							'number' => $getNumber,
							'wh5' => $innerRow
						];
					}
				}
			}
			
			//sorting and array
			$wh5 = $dumpWH[0] -> wh5;
		}else{
			$wh5 = implode('',$wh5);
		}
		
		
		return  apiAnna::getSerializeAnnaData($uid,$wh5,$init,$text,$responseText);
		
	}
	
	public static function generateAPIStorage($text,$responseText = null){	
		$validateW5H = apiAnna::checkW5H($text);
		return apiAnna::submitMessage($validateW5H,$text,$responseText);
	}
	
}
?>