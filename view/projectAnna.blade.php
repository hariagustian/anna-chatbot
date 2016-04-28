<!doctype html>

<html>
<head>


<style>
html,body {
	font-family: sans-serif; 
	font-size: 13px;  
	color:#444; 
	padding:0px;
	margin:0px;
	max-width:600px;
	max-height:600px;
	margin:20px auto;
	

}

p{
	margin:0px
}
.mt10{
	margin-top:10px !important
}

.box{
	height:100%;
	border:1px solid #D1D1D1;
	border-radius:4px;
	overflow:hidden;
	position:relative;
	padding-bottom:100px;
	background:#F4F4F4 url(../assets-frn/img/Chat-Pattern.png);
	box-sizing:border-box
}
header{
	height:20px;
	background:#54A9A9;
	padding:7px 20px;
	color:#fff;
	font-weight:bold
}

.box-body{
	padding:20px;
	max-height: 300px;
    overflow-y: auto;
}
.chat{
	width:100%;
	overflow:auto;
	margin-bottom:10px;
	display:block;
	
}

.body-msg-left:before{
	content:"";
	background:transparent url(../assets-frn/img/left.png) no-repeat center;
	left: -20px;
	top:0px;
	width:30px;
	height:20px;
	position:absolute;
}

.body-msg-left{
	padding:9px;
	background:#fff;
	float:left;
	margin-left:20px;
	border-radius:4px;
	position:relative;
	
}

.body-msg{
	max-width:80%;
}

.body-msg span{
	font-weight:bold;

}

.body-msg-right:before{
	content:"";
	background:transparent url(../assets-frn/img/right.png) no-repeat center;
	right: -20px;
	top:0px;
	width:30px;
	height:20px;
	position:absolute;
	text-align:left;
}

.body-msg-right{
	padding:9px;
	background:#fff;
	float:right;
	text-align:right;
	margin-right:20px;
	border-radius:4px;
	position:relative
}



.box-footer{
	position:absolute;
	bottom:0px;
	left:0px;
	overflow:hidden;
	width:100%;
	-webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
	padding:10px
}
input{
	box-sizing: border-box;
}

.disabled{
	background:#eee !important;
	cursor:not-allowed;
	color:#928D8D !important;
}

input[type='text'] {
	background:#E2E2E2;
	height:35px;
	border:0px;
	padding:3px 5px;
	outline:none;
	width:100%;
	color: inherit;
    font: inherit;
    margin: 0;
	text-rendering: auto;
    color: initial;
    letter-spacing: normal;
    word-spacing: normal;
    text-transform: none;
    text-indent: 0px;
    text-shadow: none;
    display: inline-block;
    text-align: start;
    margin: 0em 0em 0em 0em;
    font: 13.3333px Arial;
	border-radius:3px
  
}

</style>	

</head>
<body>
	<div class="box">
		<header>
			<div>
				<span>Start convertation with Anna</span>
			</div>
		</header>
		<div class="box-body" id="parent-chat">

		</div>
		<footer>
			<div class="box-footer">
				<input type="text" />
			</div>
		</footer>
	</div>

</body>
<script>
	var doc = document,
		parent = doc.getElementsByTagName('footer')[0],
		inputBox = parent.getElementsByTagName('input')[0];
		obj = { 
			commentBox : inputBox,
			parentChat:doc.getElementById('parent-chat'),
			base : '{{url()}}',
			xyz:'{{csrf_token()}}',
			charsetJson : 'application/json;charset=UTF-8'
		}	
	
	var annaPrnt = function (init){
		this._obj =  init;
	}
	
	annaPrnt.prototype.event = function (element,method){
		if (element.attachEvent){
		  return element.attachEvent('onkeypress', method);
		}else{
		  return element.addEventListener('keypress', method, false);
		}
	}
	
	annaPrnt.prototype.encodeStr = function(rawStr){
		return encodedStr = rawStr.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
		   return '&#'+i.charCodeAt(0)+';';
		})
	}
	
	annaPrnt.prototype.appendConversation = function(obj){
		var parentElement = this._obj.parentChat;
		if(!!obj.text){
			var data = obj.text,
				userName = obj.uName,
				html = '<div class="body-msg body-msg-right"> <span>'+userName+'</span> <p>'+data+'</p> </div>',
				element = doc.createElement('div');
				
				element.setAttribute("class", "chat")
				element.innerHTML = html;
				parentElement.appendChild(element);	
		}else{
				var element = doc.createElement('div');
				
				element.setAttribute("class", "chat")
				element.innerHTML = obj;
				parentElement.appendChild(element);	
		}
		
		
		parentElement.scrollTop = parentElement.scrollHeight
	}
	
	annaPrnt.prototype.sendConversation = function (url,getData,handler){
		var charsetJson = this._obj.charsetJson,
			xyz = this._obj.xyz,
			data = false,
			self = function(){}
			
			window.XMLHttpRequest ? 
				self.data = new XMLHttpRequest() :
				self.data = new ActiveXObject("Microsoft.XMLHTTP"); 
				
			self.data.callback = handler
			self.data.arguments = Array.prototype.slice.call(arguments, 2)
			self.data.onerror = function(e) {
				alert('error')
			}	
			self.data.timeout = 23000;
			
			self.data.open('POST', url, true);
			self.data.setRequestHeader('X-CSRF-TOKEN', xyz)
			self.data.setRequestHeader("Content-Type", charsetJson);	
			self.data.send(JSON.stringify(getData));
			self.data.ontimeout = function () {
				alert('ops, request time out')
			}
			
			self.data.onload = function () {						
				self.data.callback.apply(this, this.arguments);
			};
		
	}
	
	annaPrnt.prototype.respondConversation = function(){
		var anna = new annaPrnt(obj),
			parentElement = this._obj.parentChat,
			inputElement = parentElement.getElementsByTagName('input');
			if(inputElement.length > 0){
				for(var index=0; index<inputElement.length;index++){
					var elClone = inputElement[index].cloneNode(true);
						inputElement[index].parentNode.replaceChild(elClone, inputElement[index]);
						anna.event(elClone,function(e){
							if (e.keyCode == 13) {
								var data = this.getAttribute('set-value'),
									activeEl = this,
									url = obj.base+'/xyz',
									dataResponse = {
											text: data,
											responseText:activeEl.value,
											uName:'User'
										}
										
									activeEl.disabled = true,
									activeEl.className = 'disabled';
									activeEl.value = 'Thank You :)';
									anna.sendConversation(url,dataResponse,function(){	
										var Pp = activeEl.parentNode,
											TagP = Pp.getElementsByTagName('p')[0];
										    TagP.innerHTML = 'Thank You :)';
											activeEl.remove()
									})
							}
						})
				}
			}
	}
	
	var anna = new annaPrnt(obj);
	anna.event(obj.commentBox,function(e){
		if (e.keyCode == 13) {
			var activeEl = this;
			var url = obj.base+'/xyz',
				data = {
						text: anna.encodeStr(obj.commentBox.value),
						uName:'User'
					}
					
			activeEl.disabled = true,
			activeEl.className = 'disabled';
			activeEl.value = 'wating respond from anna ...';
			
			anna.appendConversation(data)
			anna.sendConversation(url,data,function(){	
				var data = this.responseText;
				anna.appendConversation(data)
				anna.respondConversation()
				
				activeEl.disabled = false,
				activeEl.className = '',
				activeEl.value = '';
				
			})
		}
	})

</script>
</html>