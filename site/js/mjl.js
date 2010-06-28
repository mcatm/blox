/* mjl.js
 * MITSUE-LINKS JavaScript Library
 * Version 2.0.3
 * Copyright (C) 2008 MITSUE-LINKS
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/*@cc_on eval((function(){var ps="document external self top parent setInterval clearInterval setTimeout clearTimeout".split(" ");var c=[];for(var i=0,l=ps.length,p=null;i<l;i++){p=ps[i];window["_"+p]=window[p];c.push(p+"=_"+p);}return "var "+c.join(",");})()); @*/var MJL={};MJL.version="2.0.3";MJL.inherit=function(C,A){if(undefined!==C.__proto__&&undefined!==C.prototype.__proto__){C.prototype.__proto__=A.prototype;C.__proto__=A;if(A.prototype.isPrototypeOf&&A.prototype.isPrototypeOf(C.prototype)){return C}}C.prototype=new A();try{C.prototype.constructor=C}catch(B){throw Error("can't substitution 'constructor': "+B)}return C};MJL.convArray=function(D){if(D instanceof Array){return D}try{return Array.prototype.slice.call(D)}catch(C){var B=D.length;var A=new Array(B);for(var E=0;E<B;E++){A[E]=D[E]}return A}};MJL.sanitize=(function(){var A=/^[\s\t]+/;var B=/[\s\t]+$/;return function(C){return C.replace(A,"").replace(B,"")}})();MJL.ua=(function(){/*@cc_on @if (@_jscript) var type = "trident"; @else @*/var type=(undefined!==window.opera)?"opera":(undefined!==window.Components)?"gecko":(undefined!==window.defaultstatus)?"webkit":"unknown";/*@end @*/var ret={gecko:false,opera:false,webkit:false,trident:false,unknown:false,quirks:("BackCompat"==document.compatMode),version:("opera"==type)?Number(window.opera.version()):("webkit"==type)?document.evaluate?3:2:("trident"==type)?("undefined"!=typeof document.documentMode)?document.documentMode:("undefined"!=typeof external.SqmEnabled)?7:6:0,activex:("undefined"!=typeof ActiveXObject)};ret[type]=true;return ret})();MJL.convNode=function(E){var B=document.createElement("div");B.innerHTML=E;var C=B.childNodes;var D=C.length;var A=null;if(D<=1){A=C[0]}else{A=document.createDocumentFragment();for(var F=0;F<D;F++){A.appendChild(C[0])}}return A};MJL.getClassNameRegExp=(function(){var A={};return function(B){if(undefined===A[B]){A[B]=new RegExp("(?:^|[\\s\\t]+)"+B+"(?:[\\s\\t]+|$)")}return A[B]}})();MJL.hasClassName=function(B,A){if("string"!=typeof A){return false}return MJL.getClassNameRegExp(A).test(B.className)};MJL.addClassName=function(B,A){if(MJL.hasClassName(B,A)){return }B.className=B.className?B.className+" "+A:A};MJL.removeClassName=function(C,A){if(!MJL.hasClassName(C,A)){return }var B=MJL.sanitize(C.className.replace(MJL.getClassNameRegExp(A)," "));if(B){C.className=B}else{C.removeAttribute("class");C.removeAttribute("className")}};MJL.getElementsByClassName=(function(){if(document.getElementsByClassName){return function(B,A){return MJL.convArray(B.getElementsByClassName(A))}}else{if(document.querySelectorAll){return function(B,A){return MJL.convArray(B.querySelectorAll("."+A))}}else{if(document.evaluate){return function(D,B){var E=document.evaluate('.//*[contains(concat(" ",@class," ")," '+B+' ")]',D,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);var F=E.snapshotLength;var A=new Array(F);for(var C=0;C<F;C++){A[C]=E.snapshotItem(C)}return A}}else{return function(E,C){var B=E.getElementsByTagName("*");var D=B.length;var A=[];for(var F=0;F<D;F++){if(MJL.hasClassName(B[F],C)){A.push(B[F])}}return A}}}}})();MJL.getElementsByChildNodes=(function(){if(document.evaluate){return function(D,G,A){var E=document.evaluate(!G?"./*":(false===A)?"./*[not(self::"+G+")]":"./"+G,D,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);var F=E.snapshotLength;var B=new Array(F);for(var C=0;C<F;C++){B[C]=E.snapshotItem(C)}return B}}else{return function(E,G,A){var C=E.childNodes;var D=C.length;var B=[];if(false!==A){A=true}for(var F=0;F<D;F++){if(1==C[F].nodeType&&(!G||A==(G==C[F].nodeName.toLowerCase()))){B.push(C[F])}}return B}}})();MJL.vp=(function(){var A=MJL.ua.quirks?"body":"documentElement";return{getInnerSize:function(){var B=0;var C=0;if(MJL.ua.webkit&&MJL.ua.version<3){B=window.innerWidth;C=window.innerHeight}else{if(MJL.ua.opera&&MJL.ua.version<9.5){B=document.body.clientWidth;C=document.body.clientHeight}else{B=document[A].clientWidth;C=document[A].clientHeight}}return{width:B,height:C}},getFullSize:function(){return{width:document[A].scrollWidth,height:document[A].scrollHeight}},getScrollPosition:function(){return{x:(window.pageXOffset||document[A].scrollLeft),y:(window.pageYOffset||document[A].scrollTop)}}}})();MJL.event={add:function(F,D,G,A,E){var C=E?G:this._wrapAfterCare(G);var B=C;if(this._origins[D]){B=this._origins[D].add(F,C,A)}else{if(F.addEventListener){F.addEventListener(D,C,A)}else{if(F.attachEvent){F.attachEvent("on"+D,C)}else{B=null}}}if(!E&&null!==B&&"unload"!=D){this._store(F,D,B,A)}return B},remove:function(D,C,E,A){var B=E;if(this._origins[C]){B=this._origins[C].remove(D,E,A)}else{if(D.removeEventListener){D.removeEventListener(C,E,A)}else{if(D.detachEvent){D.detachEvent("on"+C,E)}}}return B},getCurrentNode:function(A){return A.currentTarget?A.currentTarget:A.srcElement?A.srcElement:window.event.srcElement?window.event.srcElement:null},windowLoaded:false,_types:{},_store:function(C,B,D,A){if(undefined===this._types[B]){this._types[B]=[]}this._types[B].push({node:C,listener:D,useCapture:A})},_origins:{},_createStack:function(A){if(undefined===this._origins[A]._callStack){this._origins[A]._callStack={id:0,listeners:{}}}},_getStack:function(A){return this._origins[A]._callStack},_addStack:function(B,C){this._createStack(B);var A=this._getStack(B);var D=A.id;A.listeners[D]=C;A.id++;return D},_removeStack:function(B,C){var A=this._getStack(B);if(undefined===A||undefined===A.listeners[C]){return false}delete A.listeners[C];return true},_runStack:function(C){var A=this._getStack(C);if(undefined===A){return }for(var B in A.listeners){A.listeners[B]()}},_wrapAfterCare:function(A){return function(C){var B=A.apply(A,arguments);if(false===B){if(C.preventDefault){C.preventDefault()}else{C.returnValue=false}if(C.stopPropagation){C.stopPropagation()}else{C.cancelBubble=true}}}}};MJL.event.add(window,"unload",function(){var B=MJL.event._types;for(var C in B){var E=B[C];var D=E.length;for(var A=0;A<D;A++){MJL.event.remove(E[A].node,C,E[A].listener,E[A].useCapture)}}},false,true);MJL.event.add(window,"load",function(){MJL.event.windowLoaded=true},false);MJL.event._origins.fontresize={add:(function(){if(MJL.ua.trident){return function(C,D,B){return MJL.event.add(MJL.style._getUnitElem(),"resize",D,B)}}else{var A=false;return function(C,D,B){var E=MJL.event._addStack("fontresize",D);if(!A){A=true;setInterval(function(){if(MJL.style.isZoomed()){MJL.event._runStack("fontresize")}},1000)}return E}}})(),remove:(function(){if(MJL.ua.trident){return function(B,C,A){return MJL.event.remove(MJL.style._getUnitElem(),"resize",C,A)}}else{return function(B,C,A){return MJL.event._removeStack("fontresize",C)}}})()};MJL.style={getWithTitles:function(){var D=MJL.ua.webkit?this._getList():document.styleSheets;var A=D.length;var B={};for(var C=0;C<A;C++){var E=D[C].title;if(!E){continue}if(undefined==B[E]){B[E]=[]}B[E].push(D[C])}return B},switchAlt:function(E){var C=this.getWithTitles();if(undefined===C[E]){return false}for(var I in C){var A=C[I];var F=A.length;for(var B=0;B<F;B++){A[B].disabled=true}}var D=C[E];var H=D.length;for(var G=0;G<H;G++){D[G].disabled=false}return true},getComputed:(function(){var A={height:{enable:(MJL.ua.trident||(MJL.ua.opera&&MJL.ua.version<9.5)),calc:function(D,G,C){var F=parseInt(this.getComputed(D,"paddingTop"))||0;var E=parseInt(this.getComputed(D,"paddingBottom"))||0;return(D.clientHeight-F-E)+"px"}},fontSize:{enable:(MJL.ua.trident||(MJL.ua.webkit&&MJL.ua.webkit<3)),calc:function(D,E,C){return this._getUnitElem().offsetWidth+"px"}}};if(document.defaultView&&document.defaultView.getComputedStyle){var B=document.defaultView;return function(D,E){var C=B.getComputedStyle(D,null)[E];if(A[E]&&A[E].enable){C=A[E].calc.apply(this,[D,E,C])}return C}}else{if(document.documentElement&&document.documentElement.currentStyle){return function(E,G){var D=E.currentStyle[G];if(A[G]&&A[G].enable){D=A[G].calc.apply(this,[E,G,D])}else{if(!this._cond.px.test(D)&&this._cond.num.test(D)){var F=E.style.left;var C=E.runtimeStyle.left;E.runtimeStyle.left=E.currentStyle.left;E.style.left=D||0;D=E.style.pixelLeft;E.style.left=F;E.runtimeStyle.left=C}}return D}}else{throw Error("not supported getting computed style")}}})(),isZoomed:(function(){if(MJL.ua.opera){var A=0;var C=0;MJL.event.add(window,"load",function(){innerSize=MJL.vp.getInnerSize();C=MJL.style.getComputed(document.body,"fontSize")},false);return function(){var E=MJL.vp.getInnerSize();var D=MJL.style.getComputed(document.body,"fontSize");if(innerSize.width==E.width&&innerSize.height==E.height&&C==D){return false}innerSize=E;C=D;return true}}else{var B=0;MJL.event.add(window,"load",function(){B=MJL.style.getComputed(document.body,"fontSize")},false);return function(){var D=MJL.style.getComputed(document.body,"fontSize");if(B==D){return false}B=D;return true}}})(),_cond:{px:/\d\s*px$/i,num:/^\d/,rel:/(?:^|\s)stylesheet(?:\s|$)/i},_unitElem:null,_getUnitElem:function(){if(null===this._unitElem){var A=document.createElement("div");A.style.display="block";A.style.width="1em";A.style.height="1em";A.style.position="absolute";A.style.top="-999em";A.style.left="-999em";document.body.appendChild(A);this._unitElem=A}return this._unitElem},_getList:function(){var B=document.getElementsByTagName("link");var D=B.length;var C=[];for(var A=0;A<D;A++){if(this._cond.rel.test(B[A].getAttribute("rel"))){C.push(B[A])}}return C}};MJL.style.Switcher=function(){this.parent=null;this.targets=[];var A=this;this.options={collect:A._collectDefault};this._cookie=new MJL.Cookie(this._COOKIE_STATUS.name,this._COOKIE_STATUS.optional);this.setOptions.apply(this,arguments)};MJL.style.Switcher.prototype={setOptions:function(B,A){if(arguments.length<1){return }this.parent=B;if(null!==A&&"object"==typeof A){for(var C in this.options){if(undefined===A[C]){continue}this.options[C]=A[C]}}},create:function(){this.setOptions.apply(this,arguments);this._setTargets();this._setEvent();this._getCookie()},set:function(A){MJL.style.switchAlt(A);this._setCookie(A)},_COOKIE_STATUS:{name:"MJL.style.Switcher",key:"title",optional:{path:"/",fileUnit:false}},_collectDefault:function(E){var F=("a"==E.nodeName.toLowerCase())?[E]:E.getElementsByTagName("a");var G=F.length;var C=[];for(var B=0;B<G;B++){var A=F[B].getAttribute("href");var D=A.lastIndexOf("#");var H=(-1==D)?"":A.substring(D+1);if(H){C.push({node:F[B],title:H})}}return C},_setTargets:function(){this.targets=this.options.collect.call(this,this.parent)},_setEvent:function(){var A=this.targets.length;for(var B=0;B<A;B++){MJL.event.add(this.targets[B].node,"click",this._getEventListener(B),false)}},_getEventListener:function(B){var A=this;return function(){A.set(A.targets[B].title);return false}},_getCookie:function(){var A=this._cookie.get(this._COOKIE_STATUS.key);if(A){this.set(A)}},_setCookie:function(A){this._cookie.set(this._COOKIE_STATUS.key,A)}};MJL.Cookie=function(){this.name="";this.params={path:"",domain:"","max-age":31536000,secure:false};this.options={fileUnit:true,index:"index.html"};this._nameCond={str:"",regexp:null};this.setOptions.apply(this,arguments)};MJL.Cookie.prototype={setOptions:function(B,A){if(null!==A&&"object"==typeof A){for(var D in this.options){if(undefined===A[D]){continue}this.options[D]=A[D]}for(var C in this.params){if(undefined===A[C]){continue}this.params[C]=A[C]}}this.setName(B)},setName:function(A){if(!A||"string"!=typeof A){throw Error("invalid cookie name ("+A+")")}if(this.options.fileUnit){var B=window.location.pathname;if(this._DIR_COND.test(B)&&this.options.index){B+=this.options.index}A+="@"+B}this.name=A;this._nameCond.str=A+"=";this._nameCond.regexp=new RegExp("^"+A+"=")},get:function(A){var B=this._getAll();return("string"==typeof A)?B[A]:B},set:function(D,E){if(undefined===D||undefined===E){return }var B=this._getAll();var A=[];B[D]=E;for(var C in B){if(undefined===B[C]){continue}A.push(encodeURIComponent(C)+":"+encodeURIComponent(B[C]))}if(0<A.length){document.cookie=this.name+"="+A.join(",")+this._getParamStr()}},remove:function(){var A=this.params["max-age"];this.params["max-age"]=0;document.cookie=this.name+"="+this._getParamStr();this.params["max-age"]=A},_DIR_COND:/\/$/i,_DELIMITERS:{item:/\s*;\s*/,value:/\s*\,\s*/,hash:/\s*:\s*/},_param2:{path:{cond:function(A){return A},conv:function(A){return A}},domain:{cond:function(A){return A},conv:function(A){return A}},"max-age":{cond:function(A){return !isNaN(A)},conv:function(A){return A}},secure:{cond:function(A){return A},conv:function(A){return(A?"sequre":"")}}},_getAll:function(){var G=document.cookie;if(!G){return{}}var E={};var C=G.split(this._DELIMITERS.item);var A=C.length;for(var D=0;D<A;D++){if(0==C[D].indexOf(this._nameCond.str)){var H=C[D].replace(this._nameCond.regexp,"").split(this._DELIMITERS.value);var F=H.length;for(var I=0;I<F;I++){var B=H[I].split(this._DELIMITERS.hash);E[decodeURIComponent(B[0])]=decodeURIComponent(B[1])}break}}return E},_getParamStr:function(){var C=[];for(var B in this.params){if(this._param2[B].cond(this.params[B])){C.push(B+"="+this._param2[B].conv(this.params[B]))}}var A=this._getExpiresStr();if(A){C.push(A)}var D=C.join(";");return((""==D)?"":";"+D)},_getExpiresStr:function(){var B=this.params["max-age"];if(isNaN(B)){return""}var A=new Date();A.setTime(A.getTime()+B);return"expires="+A.toGMTString()}};MJL.Rollover=function(){this.targets=[];this.enable="";this.options={disable:"",switchers:{on:{cond:/(\.[^\.]+)$/g,replace:"_o$1"},off:{cond:"",replace:""}}};this.setOptions.apply(this,arguments)};MJL.Rollover.prototype={setOptions:function(B,A){if(arguments.length<1){return }this.enable=B;if(null!==A&&"object"==typeof A){for(var C in this.options){if(undefined===A[C]){continue}this.options[C]=A[C]}}},create:function(){this.setOptions.apply(this,arguments);this._setTargets();this._setEvents()},_TYPES:(function(){function A(C){return function(D){var E=D.getAttribute("src").replace(this.options.switchers[C].cond,this.options.switchers[C].replace);this._addCache(E);return function(){D.setAttribute("src",E)}}}function B(C){return function(G){var I=G.getElementsByTagName("img");var H=I.length;var F=A(C);var E=new Array(H);for(var D=0;D<H;D++){E[0]=F.call(this,I[D])}return function(){for(var J=0;J<H;J++){E[J]()}}}}return{img:{isTarget:function(){return true},getters:{mouseover:A("on"),mouseout:A("off")}},input:{isTarget:function(C){return("image"==C.getAttribute("type"))},getters:{mouseover:A("on"),mouseout:A("off"),focus:A("on"),blur:A("off")}},a:{isTarget:function(C){var D=C.getElementsByTagName("img");return(0<D.length)},getters:{focus:B("on"),blur:B("off")}}}})(),_isEnable:function(A){if(!this.options.disable){return true}do{if(MJL.hasClassName(A,this.options.disable)){return false}if(MJL.hasClassName(A,this.enable)){return true}A=A.parentNode}while(A);return false},_isTarget:function(B){var A=B.nodeName.toLowerCase();if(undefined===this._TYPES[A]){return false}if(this._TYPES[A].isTarget(B)&&this._isEnable(B)){return true}return false},_getElements:function(){var C=[];var A=MJL.getElementsByClassName(document,this.enable);var B=A.length;for(var E=0;E<B;E++){if(this._isTarget(A[E])){C.push(A[E])}for(var H in this._TYPES){var D=A[E].getElementsByTagName(H);var F=D.length;for(var G=0;G<F;G++){if(this._isTarget(D[G])){C.push(D[G])}}}}return C},_setTargets:function(){var B=this._getElements();var D=B.length;for(var G=0;G<D;G++){var C=B[G].nodeName.toLowerCase();var F={element:B[G],events:{}};var E=this._TYPES[C].getters;for(var A in E){F.events[A]=E[A].call(this,B[G])}this.targets.push(F)}},_setEvents:function(){var A=this.targets.length;for(var B=0;B<A;B++){for(var C in this.targets[B].events){MJL.event.add(this.targets[B].element,C,this.targets[B].events[C],false)}}},_addCache:(function(){var A={};return function(B){if(A[B]){return }A[B]=document.createElement("img");A[B].setAttribute("src",B)}})()};MJL.Flash=function(){this.node=null;this.alt=null;this.options={activate:false,version:0,minVerMsg:null};this.params={};this.validCreated=false;this.setOptions.apply(this,arguments)};MJL.Flash.prototype={type:"application/x-shockwave-flash",pluginurl:"http://www.adobe.com/go/getflashplayer",classid:"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000",codebase:"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab",version:(function(){var D="";try{D=navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin.description;D.match(/(\d+)\.(\d+)(?:\s*[r\.](\d+))?(?:\s*[bd](\d+))?$/)}catch(C){try{D=(new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")).GetVariable("$version")}catch(C){try{var B=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");D="6,0,21,0";B.AllowScriptAccess="always";D=B.GetVariable("$version")}catch(C){try{D=(new ActiveXObject("ShockwaveFlash.ShockwaveFlash")).GetVariable("$version")}catch(C){D=""}}}D.match(/(\d+),(\d+),(\d+),(\d+)/)}var A={major:0,minor:0,revision:0,debug:0};if(D){A.major=parseInt(RegExp.$1)||0;A.minor=parseInt(RegExp.$2)||0;A.revision=parseInt(RegExp.$3)||0;A.debug=parseInt(RegExp.$4)||0}return A})(),enable:(function(){try{return !!navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin}catch(A){try{return !!(new ActiveXObject("ShockwaveFlash.ShockwaveFlash"))}catch(A){}}return false})(),compVersion:function(B,D,C,A){B=parseInt(B);D=parseInt(D);C=parseInt(C);A=parseInt(A);return((this.version.major<B)?1:(this.version.major>B)?-1:isNaN(D)?0:(this.version.minor<D)?1:(this.version.minor>D)?-1:isNaN(C)?0:(this.version.revision<C)?1:(this.version.revision>C)?-1:isNaN(A)?0:(this.version.debug<A)?1:(this.version.debug>A)?-1:0)},setOptions:function(B,A){if(arguments.length<1){return }if(1!=B.nodeType||"object"!=B.nodeName.toLowerCase()){throw Error("invalid 'object' element node: "+elem)}this.node=B;this.validCreated=false;if(null!==A&&"object"==typeof A){for(var C in this.options){if(undefined===A[C]){continue}this.options[C]=A[C]}}this._setParams();this._setOptionsByParams()},create:function(){this.setOptions.apply(this,arguments);this._switchNode();this._activate();return this.node},_setParams:function(){var C=MJL.getElementsByChildNodes(this.node,"param");var A=C.length;for(var B=0;B<A;B++){this.params[C[B].getAttribute("name")]=C[B].getAttribute("value")}},_setOptionsByParams:function(){for(var A in this.options){if(undefined===this.params[A]){continue}this.options[A]=this.params[A]}},_switchNode:function(){if(this.enable){if(this.compVersion(this.options.version)<=0){this.validCreated=true;return }this.alt=(this.options.minVerMsg)?this._createMinVerMsg():this._createAlt();var B=this.alt;var A=this.node;this.node=this.alt;setTimeout(function(){A.parentNode.replaceChild(B,A)},0)}},_createMinVerMsg:function(){return("string"==typeof this.options.minVerMsg)?MJL.convNode(this.options.minVerMsg):this.options.minVerMsg},_createAlt:(function(){if(MJL.ua.trident){return function(){return MJL.convNode(this.node.innerHTML)}}else{return function(){var D=document.createDocumentFragment();var A=MJL.getElementsByChildNodes(this.node,"param",false);var B=A.length;for(var C=0;C<B;C++){D.appendChild(A[C].cloneNode(true))}return D}}})(),_activate:function(){if(MJL.ua.activex&&this.options.activate&&this.validCreated){this._setCopyObject();this.node.setAttribute("classid",this.classid)}},_setCopyObject:function(){var E=document.createElement("object");var B=this.node.attributes;var C=B.length;for(var A=0;A<C;A++){if(""==B[A].value||"null"===B[A].value||"type"==B[A].name||"classid"==B[A].name){continue}E.setAttribute(B[A].name,B[A].value)}var D=this.node.childNodes;var F=D.length;for(var G=0;G<F;G++){E.appendChild(D[G].cloneNode(true))}this.node.parentNode.replaceChild(E,this.node);this.node=E}};MJL.Flash.version=MJL.Flash.prototype.version;MJL.Flash.enable=MJL.Flash.prototype.enable;MJL.Flash.compVersion=MJL.Flash.prototype.compVersion;MJL.Window=function(){this.parent=null;this.targets=[];var A=this;this.options={name:"_blank",collect:A._collectDefault};this.params={left:null,top:null,height:null,width:null,menubar:"yes",toolbar:"yes",location:"yes",status:"yes"};this.setOptions.apply(this,arguments)};MJL.Window.prototype={setOptions:function(B,A){if(arguments.length<1){return }this.parent=B;if(null!==A&&"object"==typeof A){for(var D in this.options){if(undefined===A[D]){continue}this.options[D]=A[D]}for(var C in this.params){if(undefined===A[C]){continue}this.params[C]=this._normalizeParam(A[C])}}},create:function(){this.setOptions.apply(this,arguments);this._setTargets();this._setEvents()},open:function(B){var A=window.open(this.targets[B].uri,this.options.name,this._getParamStr());this.targets[B].ref=A?A:null},_IMMUTABLE_PARAMS:{resizable:"yes",scrollbars:"yes"},_NODE2URI:{a:function(A){return A.getAttribute("href")}},_collectDefault:function(A){return(("a"==A.nodeName.toLowerCase())?[A]:A.getElementsByTagName("a"))},_collectHTTP:function(I){var B=[];if("a"==I.nodeName.toLowerCase()){var C=I.getAttribute("href");if(C&&"http"==C.substring(0,4)){B=[I]}}else{if(document.evaluate){var G=document.evaluate('.//a[starts-with(@href, "http")]',I,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);var E=G.snapshotLength;B.length=E;for(var F=0;F<E;F++){B[F]=G.snapshotItem(F)}}else{var A=I.getElementsByTagName("a");var D=A.length;for(var H=0;H<D;H++){var C=A[H].getAttribute("href");if(C&&"http"==C.substring(0,4)){B.push(A[H])}}}}return B},_setTargets:function(){var A=this.options.collect.call(this,this.parent);var B=A.length;for(var C=0;C<B;C++){var D=A[C].nodeName.toLowerCase();this.targets[C]={node:A[C],uri:this._NODE2URI[D](A[C]),ref:null}}},_setEvents:function(){var A=this.targets.length;for(var B=0;B<A;B++){MJL.event.add(this.targets[B].node,"click",this._getEventListener(B),false)}},_getEventListener:function(B){var A=this;return function(){A.open(B);return false}},_getParamStr:function(){var A=[];for(var C in this.params){if(null===this.params[C]){continue}A.push(C+"="+this.params[C])}for(var B in this._IMMUTABLE_PARAMS){A.push(B+"="+this._IMMUTABLE_PARAMS[B])}return A.join(",")},_normalizeParam:function(A){return((true===A)?"yes":(false===A)?"no":A)}};MJL.Window.collectDefault=MJL.Window.prototype._collectDefault;MJL.Window.collectHTTP=MJL.Window.prototype._collectHTTP;MJL.Tab=function(){this.container=null;this.content=null;this.list=null;this.id="";this.activeId="";this.stat=false;this.classes={container:"tabContainer",list:"tabList",panel:"tabPanel",title:"tabTitle",active:"active",stat:"static"};var A=this;this.options={cookie:{},collect:A._collectDefault};this.items={};this.nitems=0;this._cookie=null;this._cookieName="MJL.Tab";this.setOptions.apply(this,arguments)};MJL.Tab.prototype={setOptions:function(B,A){if(arguments.length<1){return }this.content=B;this.id=B.getAttribute("id");if(null!==A&&"object"==typeof A){for(var C in this.options){if(undefined===A[C]){continue}this.options[C]=A[C]}}if(!this.id){this.options.cookie=null}},create:function(){this.setOptions.apply(this,arguments);this._distStatic();this._getContents();this._createContainer();this._createList();this._setEvents();this._createCookie()},replace:function(){var B=document.createComment("");var A=this.content.parentNode;A.replaceChild(B,this.content);for(var C in this.items){MJL.addClassName(this.items[C].panel,this.classes.panel)}if(this.stat){A.replaceChild(this.content,B)}else{this.container.appendChild(this.list);this.container.appendChild(this.content);A.replaceChild(this.container,B)}this.active()},active:function(C){var A=this._getActiveId();var B=this.classes.active;if(C==A){return }else{if(!this._isValidId(C)){C=A}}MJL.removeClassName(this.items[A].list,B);MJL.removeClassName(this.items[A].panel,B);MJL.addClassName(this.items[C].list,B);MJL.addClassName(this.items[C].panel,B);this._setActiveId(C);this._setCookie()},_ID_PREFIX:"MJL_TAB_ITEM_",_URI2ID:/^[^#]*#/,_collectDefault:function(A){return MJL.getElementsByChildNodes(A)},_distStatic:function(){this.stat=MJL.hasClassName(this.content,this.classes.stat);if(this.stat){MJL.removeClassName(this.content,this.classes.stat)}},_isValidId:function(A){return(""!=A&&undefined!==this.items[A])},_getActiveId:function(){var C=this._getActiveIdByMarkup();var A=this._getCookie();if(!this._isValidId(A)){var B=window.location.hash;A=B?B.replace(this._URI2ID,""):"";if(!this._isValidId(A)){A=C;if(!this._isValidId(A)){for(var D in this.items){A=D;break}}}}this._setActiveId(A);this._getActiveId=this._getActiveIdBySelf;return A},_getActiveIdByMarkup:function(){var A="";for(var B in this.items){if(MJL.hasClassName(this.items[B].panel,this.classes.active)){A=B;break}}if(A){MJL.removeClassName(this.items[B].panel,this.classes.active);if(this.stat){MJL.removeClassName(this.items[B].list,this.classes.active)}}return A},_getActiveIdBySelf:function(){return this.activeId},_setActiveId:function(A){if(this._isValidId(A)){this.activeId=A}},_getId:(function(){var A=0;return function(B){var C="";if(this.stat){C=B.getAttribute("id");if(!C){throw Error("invalid id attribute value '"+C+"'")}}else{C=this._ID_PREFIX+A;A++}return C}})(),_getIdByHref:function(B){var A=B.getAttribute("href");if(!A){throw Error("invalid href attribure value '"+A+"'")}var C=A.replace(this._URI2ID,"");if(!this._isValidId(C)){throw Error("invalid reference ID '"+C+"' in '"+A+"'")}return C},_getContents:function(){var A=this.options.collect.call(this,this.content);var B=A.length;for(var C=0;C<B;C++){var D=this._getId(A[C]);if(undefined===this.items[D]){this.items[D]={panel:A[C],title:this._getTitle(A[C]),list:null,event:null}}else{throw Error("overlapping id value '"+D+"'")}}this.nitems=B},_getTitle:function(A){var C="";if(this.stat){}else{var B=MJL.getElementsByClassName(A,this.classes.title);if(B.length<1){throw Error("not found title-use element")}C=B[0]}return C},_cloneTitle:function(E){var C=document.createDocumentFragment();var A=this.items[E].title.childNodes;var B=A.length;for(var D=0;D<B;D++){C.appendChild(A[D].cloneNode(true))}return C},_createContainer:function(){var A=null;if(this.stat){A=this.content.parentNode;while(A&&!MJL.hasClassName(A,this.classes.container)){A=A.parentNode}if(!A){throw Error("not found tab container element")}}else{A=document.createElement("div");MJL.addClassName(A,this.classes.container)}this.container=A},_createList:function(){var D=null;if(this.stat){D=MJL.getElementsByClassName(this.container,this.classes.list)[0];if(!D){throw Error("not found tab list element")}var C=MJL.getElementsByChildNodes(D);var E=C.length;if(E!=this.nitems){throw Error("not equal tab list items ("+this.nitems+") and contents ("+E+")")}for(var G=0;G<E;G++){var B=this._getEventElement(C[G]);var F=this._getIdByHref(B);this.items[F].list=C[G];this.items[F].event=B}}else{D=document.createElement("ul");MJL.addClassName(D,this.classes.list);for(var F in this.items){var A=document.createElement("li");var B=document.createElement("a");B.setAttribute("href","#"+F);D.appendChild(A).appendChild(B).appendChild(this._cloneTitle(F));this.items[F].list=A;this.items[F].event=B}}this.list=D},_getEventElement:function(A){var B=A.getElementsByTagName("a")[0];if(!B){throw Error("not found valid event element")}return B},_setEvents:function(){for(var A in this.items){MJL.event.add(this.items[A].event,"click",this._getEventListener(A),false)}},_getEventListener:function(B){var A=this;return function(){A.active(B);return false}},_createCookie:function(){if(null===this.options.cookie||null!==this._cookie){return }this._cookie=new MJL.Cookie(this._cookieName,this.options.cookie)},_setCookie:function(){if(null===this.options.cookie){return }this._cookie.set(this.id,this._getActiveId())},_getCookie:function(){return((null===this.options.cookie)?"":this._cookie.get(this.id))}};MJL.HeightEqualizer=function(){this.parent=null;this.targets=[];var A=this;this.options={groupBy:0,collect:A._collectDefault,resize:true};this._listeners={resize:null,fontresize:null};this.setOptions.apply(this,arguments)};MJL.HeightEqualizer.prototype={setOptions:function(B,A){if(arguments.length<1){return }this.parent=B;if(null!==A&&"object"==typeof A){for(var C in this.options){if(undefined===A[C]){continue}this.options[C]=A[C]}}},create:function(){this.setOptions.apply(this,arguments);this.targets=this.options.collect.call(this,this.parent);this.set();this._setAutoResize()},set:function(){this.release();var C=this._getHeights();var A=this.targets.length;for(var B=0;B<A;B++){this.targets[B].style.height=C[B]}},release:function(){var A=this.targets.length;for(var B=0;B<A;B++){this.targets[B].style.height=""}},_UNIT:"px",_collectDefault:function(A){return MJL.getElementsByChildNodes(A)},_getHeights:function(E){var C=this.targets.length;var F=new Array(C);for(var D=0;D<C;D++){F[D]=parseInt(MJL.style.getComputed(this.targets[D],"height"))}var B=this.options.groupBy;var A=0;if(B<2||C<=B){A=Math.max.apply(Math,F)+this._UNIT;for(var D=0;D<C;D++){F[D]=A}}else{for(var D=0;D<C;D++){if(0==D%B){A=Math.max.apply(Math,F.slice(D,D+B))+this._UNIT}F[D]=A}}return F},_setAutoResize:function(){if(this.options.resize){var B=this;for(var A in this._listeners){this._listeners[A]=MJL.event.add(window,A,function(){B.set()},false)}}}};MJL.enable={rollover:function(C,B){var A=new MJL.Rollover(C,B);A.create();return A},flash:function(E,C){var A=MJL.getElementsByClassName(document,E);var D=A.length;var B=new Array(D);for(var G=0;G<D;G++){var F=new MJL.Flash(A[G],C);F.create();B.push(F)}return B},window:function(E,C){var A=MJL.getElementsByClassName(document,E);var D=A.length;var B=new Array(D);for(var G=0;G<D;G++){var F=new MJL.Window(A[G],C);F.create();B[G]=F}return B},tab:function(E,C){var A=MJL.getElementsByClassName(document,E);var D=A.length;var B=new Array(D);for(var G=0;G<D;G++){var F=new MJL.Tab(A[G],C);F.create();F.replace();B[G]=F}return B},styleSwitcher:function(E,C){var A=MJL.getElementsByClassName(document,E);var D=A.length;var B=new Array(D);for(var G=0;G<D;G++){var F=new MJL.style.Switcher(A[G],C);F.create();B[G]=F}return B},heightEqualizer:function(E,C){var A=MJL.getElementsByClassName(document,E);var D=A.length;var B=new Array(D);for(var G=0;G<D;G++){var F=new MJL.HeightEqualizer(A[G],C);F.create();B[G]=F}return B}};