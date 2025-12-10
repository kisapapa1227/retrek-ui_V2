<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="{{ asset('css/style.css')}}"
        <title>Document</title>
<style>

* {
    box-sizing: border-box;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 40px 0;
    height: 700px;
    text-align: center;
}
.container h1 {
    font-size: 40px;
    transition-duration: 1s;
    transition-timing-function: ease-in-out;
    font-weight: 200;
}

form {
    padding: 5px 0;
    position: relative;
    z-index: 2;
}
#content{
font-size:16px;
color:#000000;
}
.summary .details{
font-size:32px;
color:#ff0000;
	opacity:0.5;
}
.jsonText{
	opacity:0.5;
}

.subMessage {
	font-size: 24px;
	color: #00ff00;
-webkit-text-stroke: 1px black;

}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="newWrapper">
<div class="container">
        <h1>JsonViewer</h1>
<div>
<p id="title">unknown</p>
<p style="display:inline">
<input type="checkBox" name="inView" value="false" id="inView" onChange="proc(this)">
<p id="openOr">
全て開く
</p>
</p>

<div id="content"></div>
</div>
</div>
</div>
</html>

<script>
let openState=false;
let message="";
let json=@json($JSON);

//alert(json);
let obj=JSON.parse(json);


document.getElementById("title").innerHTML=obj.title;

function showNext(prev,obj,xl){

	if (typeof(obj[prev])!='string'){
var ol=document.createElement("details");
var li=document.createElement("summary");
var ta=document.createTextNode(prev);
	ol.id="ourDetails";
	ol.className="ourDetails";
		li.appendChild(ta);
		ol.appendChild(li);
		xl.appendChild(ol);
//		var li=document.createElement("li");
//		var ta=document.createTextNode(prev);
		li.style.textAlign="left";
		li.style.backgroundColor="skyblue";
		li.style.opacity="0.7";
		ol.style.margin="0px 20px";
//		li.style.backgroundColor="transparent";
//	var ol=document.createElement("ol");
		for (key in obj[prev]){
			showNext(key,obj[prev],ol);
		}
	}else{
		var p=document.createElement('p');
		var tx=document.createTextNode(prev+":"+obj[prev]);
		message=message+":"+obj[prev];
		p.appendChild(tx);
		xl.appendChild(p);
//		xl.style.backgroundColor="skyblue";
		p.style.backgroundColor="skyblue";
		p.style.opacity="0.7";
		p.style.textAlign="left";
		p.style.margin="0px 20px";
	}
}

function proc(btn){
var ols=document.getElementsByClassName("ourDetails");
var o=document.getElementById("openOr");

if (openState==false){
	o.innerHTML="全て閉じる";
	openState=true;
}else{
	openState=false;
	o.innerHTML="全て開く";
}

for (var ol of ols){
	ol.open=openState;
}
}

function setDetails(btn){
var xx=document.getElementById("content");
	message="<ol>";
　var ol=document.createElement("ol");
//var ol=document.createElement("details");

	for (var item in obj.body){
		showNext(item,obj.body,ol);
	}
	message=message+"</ol>";
xx.appendChild(ol);
}

setDetails();
</script>
