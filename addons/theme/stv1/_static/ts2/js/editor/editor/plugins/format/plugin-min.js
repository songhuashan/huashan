KISSY.Editor.add("format",function(c){c.addPlugin("format",function(){var g=KISSY.Editor,h=[],d={"\u666e\u901a\u6587\u672c":"p","\u6807\u98981":"h1","\u6807\u98982":"h2","\u6807\u98983":"h3","\u6807\u98984":"h4","\u6807\u98985":"h5","\u6807\u98986":"h6"},i={p:"1em",h1:"2em",h2:"1.5em",h3:"1.17em",h4:"1em",h5:"0.83em",h6:"0.67em"},e={},j=g.Style,b;for(b in d)if(d[b]){e[d[b]]=new j({element:d[b]});h.push({name:b,value:d[b],attrs:{style:"font-size:"+i[d[b]]}})}var k=c.addSelect("font-family",{items:h,title:"\u6807\u9898",width:"100px",mode:g.WYSIWYG_MODE,popUpWidth:"120px",click:function(a){var f=
a.newVal;a=a.prevVal;c.fire("save");if(f!=a)e[f].apply(c.document);else{e.p.apply(c.document);this.btn.set("value","p")}c.fire("save")},selectionChange:function(a){a=a.path;for(var f in e)if(e[f].checkActive(a)){this.btn.set("value",f);break}}});this.destroy=function(){k.destroy()}})},{attach:false});
