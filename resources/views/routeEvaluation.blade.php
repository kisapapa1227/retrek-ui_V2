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

<table border="1" id="retTable" align="top"></table>
<table border="1" id="recTable" align="top"></table>

<table border="0">
<tr>
<td>骨格の推移</td>
<td>ステップ数</td>
<td>環状構造外の原子数</td>
<td>環状構造の数</td>
<td>環状構造の結合方式</td>
</tr><tr>
<td><select id="pdMenu1"></select></td>
<td><select id="pdMenu2"></select></td>
<td><select id="pdMenu3"></select></td>
<td><select id="pdMenu4"></select></td>
<td><select id="pdMenu5"></select></td>
</tr>
</table>

<div style="display:flex">
<div>Step1 &nbsp</div><div id="firstMessage"></div>
</div>

<div style="display:flex">
<div>Step2 &nbsp</div><div id="secondMessage"></div>
</div>
<br>

<div>
<details>
<summary style="width:64ex">反応で使う物質で選別する。</summary>
<table id="smilesTable"></table>
</details>
</div>

<div style="display:non">
<form id="Stock" action="{{route('routeEval')}}" method="POST">
@csrf
	<button style="display:none" type="submit" id="routeEval">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
        <input style="display:none" type="text" name="oper" value="get_selected">
	<input style="display:none" type="text" name="stat" value=stat id="stat">
        <input style="display:none" type="text" name="tid" value="" id="tid">
        <input style="display:none" type="text" name="routes" value="" id="routes">
</form>
</div>

<div>
<input type="checkbox" name="inView" value="false" id="inView" onChange="proc(this)">
下の枠に反応系を表示する
</div>

<table border="1" id="routeTable" align="top"></table>
@php
$opt=array("pdfでダウンロード","図を大きくする","図を小さくする")
@endphp
 <button id="get_selected" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[0]}}</button>
 <button id="big" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[1]}}</button>
 <button id="small" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[2]}}</button>
</div>

    <footer>
      <hr />
      version 2.5 2028/8/1
    </footer>
  </body>
<canvas id="myCanvas" class="canvas" width="4000" height="100"></canvas>

</html>

<script>
let CHK=10
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

const skip=[1,3,5];

stat=@json($stat);

if (tid<0){
	tid=table['id'][0];
}

keys=pushAll(tid);
theSmiles=myForm3(stat["draw"+String(tid)],'sub');
allSmiles=makeAllSmiles(theSmiles);

if ("{{$db_type}}"=="pri"){
        path=path+"/"+uid;
}

dsr=path+"/smiles/pid";

if ("{{$db_type}}"=="pri"){
        document.getElementById("theTitle").innerHTML="ルート解析(個人用データベース)";
}else{
        document.getElementById("theTitle").innerHTML="ルート解析(共有データベース)";
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

function pushAll(id){
	var keys=[];

keys.push(myForm1(stat['record'+String(id)]['key1']));
keys.push(myForm1(stat['record'+String(id)]['key2']));
keys.push(myForm1(stat['record'+String(id)]['key3']));
keys.push(myForm1(stat['record'+String(id)]['key4']));
keys.push(myForm1(stat['record'+String(id)]['key5']));

let canvas=document.getElementById('myCanvas');

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
var sm=document.getElementById("secondMessage");

fm.innerHTML="<-- "+String(first.p1)+" routes in this condition.";
sm.innerHTML="<-- "+String(second.p1)+" routes are selected.";

}

function proc(btn){
let i,n;
let pids=document.getElementsByName("pid");
let ask="";

switch(btn.id){
case 'get_selected':
	document.getElementById("tid").value=tid;
	document.getElementById("routes").value=toStr2(secondItems.p2);
	document.getElementById("stat").value=stat;
	document.getElementById("routeEval").click();
	break;
case 'inView':
	putReactions();
	break;
case 'big':
	if (scale<2.0){
		scale*=1.1;putReactions();
	}
	break;
case 'small':
	if (scale>0.4){
		scale/=1.1;putReactions();
	}
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

function askRoutes(btn){
const rec = document.getElementById("recTable");
var rowElems=rec.rows;
var cc;

        pid=btn.id.split(":")[1];
        tid=btn.id.split(":")[2];

keys=pushAll(tid);
theSmiles=myForm3(stat["draw"+String(tid)],'sub');
allSmiles=makeAllSmiles(theSmiles);

        cc=0;
    for (let j = 0; j < fname.length; j++){

if (! skip.includes(j)){
      if (j==6){
        rowElems[1].cells[cc].outerHTML="<td>"+makeItTime(table[fname[j]][pid])+"</td>";
      }else if(j==8){
	      rowElems[1].cells[cc].outerHTML="<td>"+String(keys[5])+"/"+String(table[fname[j]][pid])+"</td>";
      }else{
        rowElems[1].cells[cc].outerHTML="<td>"+table[fname[j]][pid]+"</td>";
      }
        cc=cc+1;
      }}

	addPulldownMenu(keys[0],"pdMenu1");
	addPulldownMenu(keys[1],"pdMenu2");
	addPulldownMenu(keys[2],"pdMenu3");
	addPulldownMenu(keys[3],"pdMenu4");
	addPulldownMenu(keys[4],"pdMenu5");

	showSmilesTable(allSmiles);
}

conv={'id':'id','date':'日付','uname':'ユーザー名','loop':'検索件数','factors':'検索の重み','options':'検索条件','substance':'物質名'}

function showSmilesTable(allSmiles){
	var smiles=allSmiles[0];
	var images=allSmiles[1];
	var count=allSmiles[2];
	var include=allSmiles[3];
const tbl = document.getElementById("smilesTable");

while(tbl.firstChild){
	tbl.removeChild(tbl.firstChild);
}
const tblBody = document.createElement("tbody");
tblBody.id="myTbody";

tr=document.createElement("tr");

th=document.createElement("th");
label=document.createTextNode('--');
th.appendChild(label);tr.appendChild(th);

th=document.createElement("th");
label=document.createTextNode('SMILES');
th.appendChild(label);
th.style.setProperty("width","200px");
th.style.setProperty("text-align","center");
tr.appendChild(th);

th=document.createElement("th");
label=document.createTextNode('ルート数');
th.appendChild(label);tr.appendChild(th);
th.style.setProperty("width","80px");
//th.style.setProperty("margin","0 auto");

th=document.createElement("th");
th.width="30px";

btn=document.createElement('input');
btn.type="button";btn.id="theButton";btn.name="theButton";btn.value="含む";
btn.setAttribute('onclick','pushButton(this)');
th.appendChild(btn);tr.appendChild(th);

th=document.createElement("th");
div=document.createElement("div");
div.style.setProperty("display","flex");
label=document.createElement("div");
label.id="showRouteDiv";
label.innerHTML="隠す";
div.append(label);

btn=document.createElement('input');
btn.type="checkbox";btn.id="showRoute";btn.name="showRoute";btn.value="no";
btn.setAttribute('onclick','pushButton(this)');
div.append(btn);
th.appendChild(div);tr.appendChild(th);


	tblBody.appendChild(tr);

for (var i=0;i<smiles.length;i++){
tr=document.createElement("tr");
	td=document.createElement("td");
var newImage=new Image();
	newImage.src="{{asset('images')}}"+images[i].split("images")[1];
	newImage.height="100";
	td.appendChild(newImage);
	tr.appendChild(td);

	td=document.createElement("td");
	label=document.createTextNode(smiles[i]);
	td.appendChild(label);
	tr.appendChild(td);
	
	td=document.createElement("td");
	label=document.createTextNode(String(count[i]));
	td.appendChild(label);
//	td.style.setProperty("margin","0 auto");
	td.style.setProperty("text-align","center");
	tr.appendChild(td);

td=document.createElement("td");
btn=document.createElement("input");
btn.type="checkbox";btn.id="radioSmile"+String(i);btn.name="cBox";
btn.setAttribute('onclick','secondSelect(this)');
td.appendChild(btn);tr.appendChild(td);

	td=document.createElement("td");
	label=document.createTextNode(toStr(include[i]));

	td.appendChild(label);
	tr.appendChild(td);

	tblBody.appendChild(tr);
}
	tbl.appendChild(tblBody);
	tbl.setAttribute("border","2");
}

function pushButton(btn){
if (btn.type=="button"){
	if(btn.value=='含む'){
		btn.value='除く';
	}else{
		btn.value='含む';
	}
	procItems(firstItems,secondItems);
}
//komai
if (btn.type=="checkbox"){
const div=document.getElementById("showRouteDiv");
	if(btn.value=='no'){
		btn.value='yes';
		div.innerHTML='表示する';
		hideShowRoutes(true);
	}else{
		btn.value='no';
		div.innerHTML='隠す';
		hideShowRoutes(false);
	}
}

}
function hideShowRoutes(type){
const tbl = document.getElementById("smilesTable");
var include=allSmiles[3];

for (var i=1;i<tbl.rows.length;i++){
	cell=tbl.rows[i].cells[4];
	if (type){
while(cell.firstChild){
	cell.removeChild(cell.firstChild);
}
	}else{
	label=document.createTextNode(toStr(include[i-1]));
	cell.appendChild(label);
	}
}
}

function secondSelect(btn){
	procItems(firstItems,secondItems);
}

function procItems(firstItems,secondItems){
const tgs=document.getElementsByName("cBox");
const select=document.getElementById("firstSelect");
const button=document.getElementById("theButton");
const output=document.getElementById("finalSelect");

flag=false;
for (var tg of tgs){
	if (tg.checked==true){
		flag=true;
	}
}
// no selection means "all routes pass"
if (flag==false){
	secondItems.p1=firstItems.p1;
	secondItems.p2=firstItems.p2;
	putItems(firstItems,secondItems);
	return
}

var filter=[];
var dst=[];

for (var tg of tgs){
	if (tg.checked==true){
		i=parseInt(tg.id.split('radioSmile')[1]);
		for (var j=0;j<allSmiles[3][i].length;j++){
			x=allSmiles[3][i][j];
			filter.push(x);
		}}
}

//alert("filter"+filter); //ok
var ret=[];
//alert(firstItems.p2.length);
for (var i=0;i<firstItems.p2.length;i++){
	x=firstItems.p2[i];
	if(button.value!='含む'){
	if (filter.includes(x)==false){
		ret.push(x);
	}
}else{
	if (filter.includes(x)==true){
		ret.push(x);
//	alert(String(x)+"in"+toStr2(filter));
	}
}
}
	secondItems.p1=ret.length;
	secondItems.p2=ret;
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
	posit.push([parseFloat(prm[2]),y0,parseFloat(prm[4]),sy]);
	return y0+sy;
}

function putElement(name,posit,py,type){
let canvas=document.getElementById('myCanvas');
let ctx=canvas.getContext('2d');
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
	ctx.drawImage(img,(posit[i][0]+img.width*0.05)*scale,(py+posit[i][1])*scale,img.width*0.4*scale,img.height*0.4*scale);
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

for (var i=1;i<6;i++){
	tg="pdMenu"+String(i);
	if (tg!=menu){
		w=document.getElementById(tg);
		w.options[0].selected=true;
	}else{
		key=keys[i-1];
	}
}
	theKey=event.currentTarget.value;

if(theKey=='non'){
	setItems(firstItems,allRoute);
	procItems(firstItems,secondItems);
	putItems(firstItems,secondItems);
}else{
	setItems(firstItems,key[theKey]);
	procItems(firstItems,secondItems);
	putItems(firstItems,secondItems);
}

putReactions();

}

function getLength(){
let py=0.0,ppy=0.0;
const routes=secondItems.p2;

for (n in routes){
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
}
	return py
}

function putReactions(){
let canvas=document.getElementById('myCanvas');
let inView=document.getElementById('inView');
let ctx=canvas.getContext('2d');
let ppy=0,ty;
const routes=secondItems.p2;
ctx.font='20px Robot medium';


if (inView.checked==false){
	return;
}


ctx.clearRect(0,0,canvas_width*scale,canvas_height);

py=getLength();
canvas_height=(py+10)*scale;
canvas.setAttribute("height",canvas_height);
canvas.setAttribute("width",canvas_width*scale);

py=0

let name=[];let posit=[];
for (n in routes){
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

function showTable(){
const sec1=document.createTextNode("レコードを選択してください");
const theDiv=document.getElementById("radioButtons");
const tbl = document.getElementById("retTable");
const rec = document.getElementById("recTable");
const tblBody = document.createElement("tbody");
const recBody = document.createElement("tbody");
var count;

theDiv.appendChild(sec1);
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
    for(key in conv){
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
        cellText = document.createTextNode(String(keys[5])+"/"+String(table[fname[j]][0]));
      }else{
        cellText = document.createTextNode(table[fname[j]][0]);
      }
      cell.appendChild(cellText);
      tr.appendChild(cell);
    }}
    recBody.appendChild(tr);
rec.appendChild(recBody);
}

if (table!=""){
	showTable();
}
	showSmilesTable(allSmiles);

addPulldownMenu(keys[0],"pdMenu1");
addPulldownMenu(keys[1],"pdMenu2");
addPulldownMenu(keys[2],"pdMenu3");
addPulldownMenu(keys[3],"pdMenu4");
addPulldownMenu(keys[4],"pdMenu5");

for (var x in keys[1]){
	for (var i=0;i<keys[1][x].length;i++){
		allRoute.push(keys[1][x][i]);
	}
}
setItems(firstItems,allRoute);
setItems(secondItems,allRoute);
putItems(firstItems,secondItems);

</script>
