<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <title>Route evaluation</title>
<style>
	.oper{
	display:inline-block;
	color:#343a40;
}

ok {
  width: 620px;
 cursor: pointer;
 list-style: none;
}

.mainView{
  color:#000000;
  background-color: #ffffff;
}
#btb {
  width:12em;
}

#del,#pdf {
  width:12em;
  padding: 6px 6px;
}
.left {
float:left;
}

strong{
	font-size: 24px;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="wrapper">
	<div class="container">
<h1 id="theTitle">ルートの解析</h1>
        <form action="{{ route('kRet') }}" method="GET" class="mb-3">
        <button id="btb" onclick="window.location.href='/search';" class="sysButton">メインメニューに戻る</button>
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
    </form>

<div id="radioButtons" class="mainView"></div>

<strong>レコードを選択してください</strong>
<table border="1" id="retTable" align="top"></table>
</br>
<strong>選択されているレコードの情報
</strong>
<table border="1" id="recTable" align="top"></table>

</br>
<strong>
合成経路で整理する
</strong>
<table border="0">
<tr>
<td>[異なる合成経路でリストを作る]</td>
<td>[同一の合成経路のみを表示する]</td>
</tr><tr>
<td><select id="pdMenu1"></select></td>
<td><select id="pdMenu2"></select></td>
</tr>
</table>

</br>
<strong>
表示される経路の数
</strong>
<div style="display:flex">
<!--
<div>Step1 &nbsp</div>
-->
<div id="firstMessage"></div>
</div>

<br>

<div style="display:non">
<form id="callShowJson" action="{{route('showJson')}}" method="POST">
@csrf

	<button style="display:none" type="submit" formtarget="_blank" id="show_json">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
</form>
</div>

<div style="display:non">

<form id="xStock" action="{{route('downloadStatics')}}" method="POST">
@csrf
	<button style="display:none" type="submit" id="downloadTable">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
        <input style="display:none" type="text" name="oper" value="get_table">
        <input style="display:none" type="text" name="tid" value="" id="xtid">
        <input style="display:none" type="text" name="item" value="this" id="item">
        <input style="display:none" type="text" name="label" value="" id="xlabel">
</form>

<form id="Stock" action="{{route('routeEval')}}" method="POST">
@csrf
	<button style="display:none" type="submit" id="routeEval">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
        <input style="display:none" type="text" name="oper" value="get_selected" id="oper">
	<input style="display:none" type="text" name="stat" value=stat id="stat">
        <input style="display:none" type="text" name="tid" value="" id="tid">
        <input style="display:none" type="text" name="routes" value="" id="routes">
        <input style="display:none" type="text" name="size" value="1" id="size">
</form>
</div>

<div>
<input type="checkbox" name="inView" value="false" id="inView" onChange="proc(this)">
下に表示する
<input type="checkbox" name="maxView" value="false" id="maxView" onChange="proc(this)">
全経路表示
<input type="checkbox" name="crossCoupling" value="false" id="crossCoupling" onChange="proc(this)">
クロスカップリングのみを表示する。
</div>

<strong>
下記ボタンをクリックしてダウンロードしてください
</strong>
<table border="1" id="routeTable" align="top"></table>
@php
$opt=array("pdfでダウンロード","pptxでダウンロード","表をダウンロードする","図を大きくする","図を小さくする","表の項目を選択する","別ページを開く")
@endphp
 <button id="get_selected" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[0]}}</button>
 <button id="get_pptx" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[1]}}</button>
<button id="get_table" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[2]}}</button>
 <button id="big" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[3]}}</button>
 <button id="small" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[4]}}</button>
<label id="showSize">size=1.0</label>

<br>
<br>

@php
$xitem=array("id","物質名","SMILES","構造式(画像)","要求数","獲得数","探索率","全時間","平均探索時間","合成ステップ数","出現物質数","探索経路の重み","探索条件");
$item=array("id","物質名","SMILES","構造式(画像)","要求数","獲得数","探索率","合成ステップ数","出現物質数","探索経路の重み","探索条件");
@endphp

<strong>
ダウンロードする表の項目を設定する。
</strong>
<details>
<summary>設定画面を開く</summary>
<table>
@for ($i=0;$i<11;$i++)
@if ($i%4==0)
<tr>
@endif
<td>
<input type="checkbox" name="items" class="oper" id="item{{$i}}" value={{$i}}>
<label style="width:50px" for="item{{$i}}")>{{$item[$i]}}</label>
</td>
@if ($i%4==3)
<tr>
@endif
@endfor
</table>
</details>
<br>

<strong>
クロスカップリングの情報を表示する。
</strong>

<br>
<button id="showJson" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[6]}}</button>

</div>

    <footer>
      <hr />
      version 2.9 2025/11/19
    </footer>
  </body>
<canvas id="myCanvas" class="canvas" width="4000" height="100"></canvas>

</html>

<script>
let CHK=20
let fname=@json($name);
let table=@json($body);
let modal=@json($modal);
let uid=@json($uid);
let keys=[];
let given_filename=@json($filename);
let sT=Date.now();
let tid=@json($tid);
let smileId=0;
let smileTableId=0;
let path="{{asset('images')}}";
let scale=1.0;
let canvas_height=0;
let canvas_width=2000;
var allRoute=[];
var firstItems=new Object();
var secondItems=new Object();
let ROIs=[];
var smiles;
let stack=[];
let alertShow=true;
let theSmiles=new Object(),allSmiles=new Object();

const skip=[1,3,5];

stat=@json($stat);

if (tid<0){
	tid=table['id'][0];
}

keys=pushAll(tid);
//catSmiles=myForm3(stat["draw"+String(tid)],'sub');
theSmiles=myForm4(stat["draw"+String(tid)]);
allSmiles=makeAllSmiles(theSmiles);

if ("{{$db_type}}"=="pri"){
        path=path+"/"+uid;
}

dsr=path+"/smiles/pid";

if ("{{$db_type}}"=="pri"){
        document.getElementById("theTitle").innerHTML="経路の並び替え(個人用データベース)";
}else{
        document.getElementById("theTitle").innerHTML="経路の並び替え(共有データベース)";
}                                                                               
const sleep = (time) => new Promise((r) => setTimeout(r, time));

function makeAllSmiles(theSmiles){
	let allSmiles=[];let allSmilesPic=[];let count=[];
	let include=[];

	for (var r in theSmiles){
		for (var x in theSmiles[r]){
			if (allSmiles.includes(theSmiles[r][x][0])==false){
				allSmiles.push(theSmiles[r][x][0]);
				allSmilesPic.push(theSmiles[r][x][1]);
			}
		}
	}

	for (var i=0;i<allSmiles.length;i++){
		var xx=[];
//		alert(allSmiles[i]);
	for (var r in theSmiles){
		for (var x in theSmiles[r]){
			if (theSmiles[r][x][0]==allSmiles[i]){
				route=r.split("route")[1];
				if (xx.includes(route)==false){
					xx.push(route);
				}
		}
	}
	}
		include.push(xx);
		count.push(xx.length);
	}
	return [allSmiles,allSmilesPic,count,include];
}

function smilesToButtonN(smiles,s){

for(var i=0;i<smiles.length;i++){
	if (smiles[i]==s){
		return i;
	}
}

return -1;
}

function pushAll(id){
	var keys=[];

keys.push(myForm1(stat['record'+String(id)]['key1']));
keys.push(myForm1(stat['record'+String(id)]['key2']));
keys.push(myForm1(stat['record'+String(id)]['key3']));

var count=0;
for (k in keys[1]){
	count=count+keys[1][k].length;
}
keys.push(count);

return keys;
}

function setItems(org,src){
	org.p1=src.length;
	org.p2=src;
}

function putItems(first,second){
var fm=document.getElementById("firstMessage");
let crossCoupling=document.getElementById('crossCoupling');

if (crossCoupling.checked==true){
var sss=[];
//	alert(keys[2]['crossCoupling']);
for (var r of first.p2){
if (! skip.includes(r)){
	if (keys[2]['crossCoupling'].includes(r)){
		sss.push(r);
	}
}}
	second.p1=sss.length;second.p2=sss;
//alert(sss);
}else{
	second.p1=first.p1;
	second.p2=first.p2;
}
	fm.innerHTML="<-- "+String(second.p1)+" routes in this condition.";
}

function proc(btn){
let i,n;
let pids=document.getElementsByName("pid");
var tg=document.getElementById("showSize");
let ask="";

switch(btn.id){
case 'showJson':
	document.getElementById("show_json").click();
	break;
case 'get_table':
var tg=document.getElementById("item");
var ret=""
const chkbox = document.getElementsByName("items");
for (var j=0;j<chkbox.length;j++){
	if (chkbox[j].checked){
		ret=ret+String(j)+",";
	}
}
	tg.value=ret;
	document.getElementById("downloadTable").click();
	break;
case 'get_selected':
	document.getElementById("oper").value="get_selected";
	document.getElementById("tid").value=tid;
	document.getElementById("routes").value=toStr2(secondItems.p2);
	document.getElementById("stat").value=stat;
	document.getElementById("size").value=String(scale);
	document.getElementById("routeEval").click();
	break;
case 'get_pptx':
	document.getElementById("oper").value="get_pptx";
	document.getElementById("tid").value=tid;
	document.getElementById("routes").value=toStr2(secondItems.p2);
	document.getElementById("stat").value=stat;
	document.getElementById("size").value=String(scale);
	document.getElementById("routeEval").click();
	break;
case 'inView':
case 'crossCoupling':
	putItems(firstItems,secondItems);
	putReactions();
	break;
case 'big':
	if (scale<2.0){
		scale*=1.1;putReactions();
	}
        tg.innerHTML="size="+scale.toFixed(3);
	break;
case 'small':
	if (scale>0.4){
		scale/=1.1;putReactions();
	}
        tg.innerHTML="size="+scale.toFixed(3);
	break;
}
}

function extSmiles(src,sss){
var ret=[],w;
var x=src.split(";");

//	ret.push(x[0].split(":")[1]);

	path=x[0].split(":")[1];
	for (var i=1;i<x.length;i++){
		w=x[i].split(":");
		if (w[0]=='drawImage'){
			if (sss!=w[1].split(" ")[7]){
				ret.push([w[1].split(" ")[6],path+w[1].split(" ")[1]]);
			}

		}
	}
	return ret;
}

function myForm4(src){
var smiles=new Object();
var odd=0;
var r;

	for (const s in src){
		if (odd==0){
			r=s;
		}else{
			cat=extSmiles(s,'cat');
			sub=extSmiles(s,'sub');
			smiles[r]=cat.concat(sub);
		}
		odd=1-odd;
	}
return smiles;
}
function myForm3(src,sss){
var smiles=new Object();
var odd=0;
var r;

	for (const s in src){
		if (odd==0){
			r=s;
		}else{
			smiles[r]=extSmiles(s,sss);
		}
		odd=1-odd;
	}
return smiles;
}

function myForm2(src){
let ret=[];
var path="";

xs=src.split(";");

for (const x of xs){
	if (path==""){
		path=x.split(":")[1];
		ret.push(path);
	}else{
		ret.push(x);
}}
	return ret;
}
function myForm1(src){
var key=new Object();

        a=src.split("##");
    for (const e of a) {
	    if (e==""){
		    continue;
	    }
	    x=e.split("#");
            key[x[0]]=toList(x[1]);
    }
        return key;
}

function toStr2(src){
var ret=src[0];
    for (var i = 1; i < src.length; i++){
	    	ret=ret+","+String(src[i]);
    }
return ret;
}

function toStr(src){
var rrr=[];
var ret="";
var ok="";

    for (var i = 0; i < src.length-1; i++){
	    	ret=ret+String(src[i])+",";
		if (ret.length>15){
			rrr.push(ret);
			ret="";
		}
    }
	    	ret=ret+String(src[src.length-1]);

    for (var i = 0; i < rrr.length; i++){
	    ok=ok+rrr[i]+"\n";
    }
	ok=ok+ret;
	return ok;
}

function toList(src){
ret=[];

        a=src.split(",");

    for (let i = 0; i < a.length-1; i++) {
            ret.push(a[i])
    }
        return ret;
}

function makeItTime(s){
const tm=s.slice(0,4)+"年"+s.slice(4,6)+"月"+s.slice(6,8)+"日"+s.slice(8,10)+"時"+s.slice(10,12)+"分";
        return(tm);
}

function myMerge(s1,s2){
let ret=new Object();
for (var s in s1){
	ret[s]=s1[s]
}
for (var s in s2){
	ret[s]=s2[s]
}
return ret;
}
function askRoutes(btn){
const rec = document.getElementById("recTable");
var rowElems=rec.rows;
var cc;

        pid=btn.id.split(":")[1];
        tid=btn.id.split(":")[2];

keys=pushAll(tid);
//the=myForm3(stat["draw"+String(tid)],'cat');
the=myForm4(stat["draw"+String(tid)]);
allSmiles=makeAllSmiles(theSmiles);

        cc=0;
    for (let j = 0; j < fname.length; j++){

if (! skip.includes(j)){
      if (j==6){
        rowElems[1].cells[cc].outerHTML="<td>"+makeItTime(table[fname[j]][pid])+"</td>";
      }else if(j==8){
	      rowElems[1].cells[cc].outerHTML="<td>"+String(keys[3])+"/"+String(table[fname[j]][pid])+"</td>";
      }else{
        rowElems[1].cells[cc].outerHTML="<td>"+table[fname[j]][pid]+"</td>";
      }
        cc=cc+1;
      }}

	addPulldownMenu(keys[0],"pdMenu1");
	addPulldownMenu(keys[1],"pdMenu2");

allRoute=[];
for (var x in keys[1]){
	for (var i=0;i<keys[1][x].length;i++){
		if (allRoute.includes(keys[1][x][i])==false){
			allRoute.push(keys[1][x][i]);
		}
	}
}

setItems(firstItems,allRoute);
putItems(firstItems,secondItems);
}

conv={'id':'id','date':'日付','uname':'ユーザー名','loop':'探索件数','factors':'探索の重み','options':'探索条件','substance':'物質名'}


function procItems(firstItems){
	putItems(firstItems,secondItems);
	putReactions();
}

function fillRoutes(src){
var ret=[];

for (var i=0;i<src.length;i++){
	for (var x of src[i]){
		if (ret.includes(x)==false){
			ret.push(x);
		}
	}
}
return ret;
}

function removeRoutes(sub,src){
	var ret=[];

	for (var x of src){
		if (sub.includes(x)==false){
			ret.push(x);
		}
	}
	return ret;
}

function addRoutes(src,dst){

	for (var x of src){
		if (dst.includes(x)==false){
			dst.push(x);
		}
	}
}

function addPulldownMenu(src,menu,num){
const menu1=document.getElementById(menu);
let option;

while(menu1.firstChild){
	menu1.removeChild(menu1.firstChild);
}

option=document.createElement('option');option.textContent="non";menu1.appendChild(option);
for (var k in src){
	option=document.createElement('option');option.textContent=k;menu1.appendChild(option);
}
menu1.addEventListener('change',(e)=>{
	menuProc(e,menu);
});
}

function loadImage(src){
return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = (e) => reject(e);
    img.src = src;
  });
}

function getDrawLine(path,elm,posit){
var y0,y1;
if (elm==""){
	return 0.0;
}
	x=elm.split(":");
	com=x[0];
	if (com!="line"){
		return 0.0;
	}
	prm=x[1].split(" ");
	y0=parseFloat(prm[2]);y1=parseFloat(prm[4]);
	posit.push([parseFloat(prm[1]),y0,parseFloat(prm[3]),y1]);

	if (y0>y1){
		return y0;
	}
	return y1;
}

function getDrawImage(path,elm,name,posit){
var y0,sy;
if (elm==""){
	return 0.0;
}
	x=elm.split(":");
	com=x[0];
	if (com!="drawImage"){
		return 0.0;
	}
	prm=x[1].split(" ");
	name.push(path+prm[1]);
	y0=parseFloat(prm[3]);sy=parseFloat(prm[5]);
	posit.push([parseFloat(prm[2]),y0,parseFloat(prm[4]),sy,prm[6]]);
	return y0+sy;
}

function putElement(name,posit,py,type){
let ctx=canvas.getContext('2d');
let x,y;
ctx.font='20px Robot medium';

	switch(type){
	case 0:
	Promise.all(
		name.map(function(src){
			return new Promise(function(resolve,reject){
				let img=new Image();
				img.onload=function(){resolve(img);}
				img.onerro=function(e){reject(e);}
				img.src=src
			});
		})
		).then(function(imgs){
			imgs.forEach(function(img,i){
	var i=getNum(img.src,name);
	x=(posit[i][0]+img.width*0.05)*scale;y=(py+posit[i][1])*scale;
	wx=img.width*0.4*scale;wy=img.height*0.4*scale;
	ctx.drawImage(img,x,y,wx,wy);
	ROIs.push([x,y,wx,wy,posit[i][4]])
		});
		}).catch(function(e){
//		document.getElementById("message").innerHTML+=e;
	});
		break;
	case 1:// for line
		for (var i=0;i<posit.length;i++){
			ctx.beginPath();
			ctx.moveTo(posit[i][0]*scale,(py+posit[i][1])*scale);
			ctx.lineTo(posit[i][2]*scale,(py+posit[i][3])*scale);
			ctx.strokeStyle="#afafaf";
			ctx.stroke();
		}
		break;
	}
}

function getNum(path,name){
	for (var i=0;i<name.length;i++){
		if (path.includes(name[i])){
			return i;
		}
	}
	return 0;
}
function menuProc(event,menu){
var w,key;

for (var i=1;i<3;i++){
	tg="pdMenu"+String(i);
	if (tg!=menu){
		w=document.getElementById(tg);
		w.options[0].selected=true;
	}else{
		key=keys[i-1];
	}
}
	theKey=event.currentTarget.value;

if (theKey=='non'){
	setItems(firstItems,allRoute);
}else{
	setItems(firstItems,key[theKey]);
}
	procItems(firstItems);

putReactions();

}

function getLength(){
let py=0.0,ppy=0.0;
const routes=firstItems.p2;
let maxView=document.getElementById('maxView');

let cnt=0;
for (var n in routes){
	route=routes[n];
	ret=myForm2(stat["draw"+String(tid)]["route"+String(route)]);

	ppy=0;
	for (var i=1;i<ret.length;i++){
		var x1=ret[i].split(",");
		var x2=x1[0].split(":");
		if (x2[0]=='drawImage'){
		var prm=x2[1].split(" ");
			ty=parseFloat(prm[3])+parseFloat(prm[5]);
		}else if (x2[0]=='line'){
		var prm=x2[1].split(" ");
			y0=parseFloat(prm[2]);y1=parseFloat(prm[4]);

			if (y0>y1){
				ty=y0;
			}else{
				ty=y1;
			}
		}else{
			continue;
		}
		if (ty>ppy){ppy=ty;}
	}
	py=py+ppy;

	if (cnt>CHK && maxView.checked==false){
		return py;
	}
	cnt=cnt+1;
}
	return py;
}

function putReactions(){
let inView=document.getElementById('inView');
let maxView=document.getElementById('maxView');
let ctx=canvas.getContext('2d');
let ppy=0,ty;
const routes=secondItems.p2;
ctx.font='20px Robot medium';

ROIs=[];

if (inView.checked==false){
	return;
}

ctx.clearRect(0,0,canvas_width*scale,canvas_height);

py=getLength();
canvas_height=(py+10)*scale;
canvas.setAttribute("height",canvas_height);
canvas.setAttribute("width",canvas_width*scale);

py=0

let cnt=0;let name=[];let posit=[];
for (var n in routes){
	if (cnt>CHK && maxView.checked==false){
		if (alertShow){
		alert("表示する反応が多すぎるため、最初の"+String(CHK)+"反応のみを表示します");
		alertShow=false;
		}
		return;
	}
	cnt=cnt+1;
	ppy=0;
	name=[];posit=[];
	route=routes[n];
	ret=myForm2(stat["draw"+String(tid)]["route"+String(route)]);

	for (var i=1;i<ret.length;i++){
		ty=getDrawImage(ret[0],ret[i],name,posit);
		if (ty>ppy){ppy=ty;}
	}
		putElement(name,posit,py,0);
posit=[];
	for (var i=1;i<ret.length;i++){
		ty=getDrawLine(ret[0],ret[i],posit);
		if (ty>ppy){ppy=ty;}
	}
		putElement(name,posit,py,1);

	ctx.fillStyle='#ffffff';
	ctx.fillText("Route"+route,10*scale,(py+50)*scale);
	ctx.stroke();

		ctx.beginPath();
		ctx.moveTo(0,(py+ppy)*scale);
		ctx.lineTo(1000,(py+ppy)*scale);
		ctx.strokeStyle="#00ffff";
		ctx.stroke();

	py=py+ppy;
}
//ctx.strokeRect(10,10,10,50);
}

function myChk(a,b){
	for (var i=0;i<a.length;i++){
		if (a[i]==b) return true;
	}

	return false;
}

function showTable(){
const sec1=document.createTextNode("レコードを選択してください");
const theDiv=document.getElementById("radioButtons");
const tbl = document.getElementById("retTable");
const rec = document.getElementById("recTable");
const tblBody = document.createElement("tbody");
const recBody = document.createElement("tbody");
const chkbox = document.getElementsByName("items");
var count;

const default_check=[1,2,3,5,7];
    for (var j = 0; j < chkbox.length; j++) {
	    if (myChk(default_check,chkbox[j].value)){
		    chkbox[j].checked=true;
	    }
    }
//theDiv.appendChild(sec1);
theDiv.style.color="white";
theDiv.style.backgroundColor="transparent";
theDiv.style.padding="10px";

    tr = document.createElement("tr");
        j=6;count=0;
for (var i = 0; i < table[fname[0]].length-1; i++) {
      td = document.createElement("td");
      cellText = document.createElement("input");
      cellText.type="radio";
      cellText.name="id";
//      tm=makeItTime(table[fname[6]][i]);
      tm=table[fname[6]][i];
      label=table[fname[0]][i]+":"+tm;
      cellText.id="radio:"+String(i)+":"+table[fname[0]][i];
      cellText.value=label;
      cellText.setAttribute('onchange','askRoutes(this)');
      label=document.createTextNode(label);
      td.appendChild(cellText);
            td.appendChild(label);
      if (count==5){
        tblBody.appendChild(tr);
        tr = document.createElement("tr");
        count=0
      }
      if (table[fname[0]][i]==tid){
        cellText.checked=true;
      }
      count=count+1;
      tr.appendChild(td);
       }
    tr.appendChild(td);
    tblBody.appendChild(tr);
tbl.appendChild(tblBody);

    tr = document.createElement("tr");
    for (var j = 0; j < fname.length; j++) {
if (!skip.includes(j)){
    td = document.createElement("td");
    xx=fname[j];
    for(var key in conv){
            if (key==fname[j]){
                    xx=conv[fname[j]];
            }
    }
    label=document.createTextNode(xx);
    td.appendChild(label);
    tr.appendChild(td);
        }
    }// for index list
    recBody.appendChild(tr);

    tr = document.createElement("tr");
    for (let j = 0; j < fname.length; j++) {
if (! skip.includes(j)){
      cell = document.createElement("td");
      if (j==6){
        cellText = document.createTextNode(makeItTime(table[fname[j]][0]));
      }else if (j==8){
        cellText = document.createTextNode(String(keys[3])+"/"+String(table[fname[j]][0]));
      }else{
        cellText = document.createTextNode(table[fname[j]][0]);
      }
      cell.appendChild(cellText);
      tr.appendChild(cell);
    }}
    recBody.appendChild(tr);
rec.appendChild(recBody);
}

// main loop
let canvas=document.getElementById('myCanvas');

if (table!=""){
	showTable();
}

addPulldownMenu(keys[0],"pdMenu1");
addPulldownMenu(keys[1],"pdMenu2");

for (var x in keys[1]){
	for (var i=0;i<keys[1][x].length;i++){
		if (allRoute.includes(keys[1][x][i])==false){
		allRoute.push(keys[1][x][i]);
		}
	}
}

	setItems(firstItems,allRoute);
	putItems(firstItems,secondItems);

</script>
