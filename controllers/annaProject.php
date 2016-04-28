<?php

class annaProject extends \BaseController {
	public function mainPanel(){
		return View::make('front/public_view/projectAnna');
	}
	public function process(){
		if(Input::get('responseText') != ''){
			$text = e(api::pluginCustomDigit(Input::get('text')));
			$responseText = e(Input::get('responseText'));
			$annaComment = apiAnna::generateAPIStorage(strtolower($text),$responseText);
		}else{
			$text = e(Input::get('text'));
			if($text == 'imyourxyz'){
				Cookie::queue('daddy',0, 43200);
			}
		
			$encodeText = api::pluginGetCustomDigit($text);
			$annaComment = apiAnna::generateAPIStorage(strtolower($text));
			return View::make('front/public_view/ajax/blog/anna-respond')
							->with('annaComment',$annaComment)
							->with('encodeText',$encodeText);
			
		}
	
		
	}
}
