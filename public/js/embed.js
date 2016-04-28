
var embedAnna = embedAnna || (function(){
var _args = {}, doc = document, url =['/syndication/'];

var getParent = function (init){
	this.parent =  init.parent
}

getParent.prototype.randomId = function (min,max){
	var parent = this.parent,
		parentId = parent.id;
	return parent.id = parent.id +'_'+ (Math.random() * (max - min) + min);
}

getParent.prototype.createFrame = function(id){
	var parent = doc.getElementById(id),
	frame = doc.createElement('iframe');
	frame.style.width = '100%';
	frame.style.height = '100%';
	frame.scrolling = 'no';
	frame.style.border = '0px';
	
	return {
		'frame' : frame,
		'parent' : parent
	}
}

return {
	init : function(Args) {
		_args = Args;
		// some other initialising
	},
	create : function(idt) {
		var obj = { 
			parent : doc.getElementById('itemLoad'),
			id : {}
		}	
	
		var ins = new getParent(obj);
		obj.id = ins.randomId(100,999)
		data = ins.createFrame(obj.id);
		
		var frame = data.frame,
		parent = data.parent,
		item = data.parent.getAttribute('data-item')
		
		frame.src = embedAnna.syndication()+url[0]+idt+item;
		parent.appendChild(data.frame)
	},
	track :function(){

	},
	syndication:function(){
		return _args[0];
	}
};
}());

embedAnna.init([
		'http://localhost/laravel/toko/toko/public'
]);
