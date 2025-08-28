<!DOCTYPE html>
<html lang="ja">
<head>
    <title>Report maker</title>
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
<style>
	.oper{
	display:inline-block;
	color:#343a40;
}
details {
  font: 16px "Open Sans",
    Calibri,
    sans-serif;
}
#btb {
  width:12em;
}

#del,#pdf {
  width:12em;
  padding: 6px 6px;
}

ok {
  width: 620px;
 cursor: pointer;
 list-style: none;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="wrapper">
        <div class="container d-flex justify-content-between align-items-center">
<h1 id="theTitle">レポートを作成する</h1>
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
		<button id="btb" onclick="window.location.href='/search';" class="sysButton">メインメニューに戻る</button>
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
            </form>
    </div>
<div id="mainView"></div>
<div>
<details id="myDetails" onclick="goHere(this)" name="details_head">
<summary> 探索経路の一覧、選択 </summary>
</br>

<details>
<summary> 図の大きさを変える</summary>
</br>
<input type="radio" name="size" class="size" id="pdf1" value="0.2" onchange="setSize(this)">
<label style="width:10px">小さい</label>
<input type="radio" name="size" class="size" id="pdf2" value="0.25" onchange="setSize(this)" checked >
<label style="width:10px">普通</label>
<input type="radio" name="size" class="size" id="pdf3" value="0.4" onchange="setSize(this)">
<label style="width:10px">大きい</label>
<input type="text" value="0.25" id="mySize">
</details>
<div>

<table border="1" id="retTable" align="top">
</table>
</details>
</br>

@php
$opt=array("PDFをダウンロード","PPTXをダウンロード","RetRek情報のダウンロード","探索結果の削除")
@endphp
<input type="radio" name="oper" class="oper" id="pdf" value="1">
 <label style="width:90px" for="pdf">{{$opt[0]}}</label>
<input type="radio" name="oper" class="oper" id="ppt" value="2" checked>
<label style="width:100px" for="ppt">{{$opt[1]}}</label>
<input type="radio" name="oper" class="oper" id="db" value="3">
 <label style="width:90px" for="db">{{$opt[2]}}</label>
<!--
<input type="radio" name="oper" class="oper" id="del" value="4">
 <label style="width:90px" for="del">{{$opt[3]}}</label>
-->
</div>
<div id="proc">
</div>
<div>
<table border="0" align="top">
<tr><td>
<button id='bta' type="button" onclick="proc()"> <div id='ida' style="display:inline-block">実行する</div></button></td><td>
<div id="modal" style="margin-left:20px"></div>
<button id='bta' type="button" onclick="nextPdf()"> <div id='ida' style="display:inline-block">次のpdf</div></button></td><td>
<div id="modal" style="margin-left:20px"></div>
</td></tr>
</table>
</div>

<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>

<form action="{{ route('dropDb') }}" method="POST">
@csrf
<div style="display:none">
	<button type="submit" id="dropDb">
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
        <input type="text" name="oper" value="dropDb" id="dropDbOper">
        <input type="text" name="id" value="" id="dropDbId">
</div>
</form>

<form action="{{ route('syncPdf') }}" method="POST">
@csrf
<div style="display:none">
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
	<button type="submit" id="syncPdf">
        <input type="text" name="options" value="" id="syncOptions">
        <input type="text" name="scale" value="0.25" id="syncScale">
        <input type="text" name="tid" value="" id="syncUid">
        <input type="text" name="given" value="who..." id="given">
        <input type="text" name="from" value="db">
</div>
</form>
    <footer>
      <hr />
      version 2.1 2025/06/24
    </footer>
  </body>
</html>

<script>
let CHK=10
let fname=@json($name);
let table=@json($body);
let modal=@json($modal);
let uid=@json($uid);
let tid=@json($tid);
let db_type=@json($db_type);
let given_filename=@json($filename);
let sT=Date.now();
let i=0;
let smileId=0;
let smileTableId=0;
let path="{{asset('images')}}";
dsr=path+"/smiles/pid";

if ("{{$db_type}}"=="pri"){
	document.getElementById("theTitle").innerHTML+="(個人用データベース)";
}else{
	document.getElementById("theTitle").innerHTML+="(共有データベース)";
}

if ("{{$db_type}}"=="pri"){
        path=path+"/"+uid;
}

if (table==""){
	alert("テーブルにレコードがありません。");
        document.getElementById("btb").click();
}

function chkMess(opt){
	switch(opt){
	case 'pdf':return("pdfを作成中");
	case 'ppt':return("パワーポイントを作成中");
	case '-force':return("pdfを作成中");
	case 'non':return("pdfを準備中");
	case 'askPptx':return("pptxをダウンロード中");
	case 'db':return("ReTRek情報をダウンロード中");
	case 'drop':return("探索履歴の削除中");
	case 'thumbnail':return("分子の画像を作成中");
	default:return('未登録'+opt);
}

}

const sleep = (time) => new Promise((r) => setTimeout(r, time));

function goHere(btn){
	if (btn.open==true){
		return;
	}
	goThere(btn);
}

async function goThere(btn){
	await sleep(100);
	if (btn.open==true){
		location.href="#radio:"+getPrePid();
	}
}

function modal_watcher(){
var fp=path+"/report/readDb"+tid+"normal.log";
var fp2=path+"/report/"+tid+".pdf";
let modal=document.getElementById("modal").innerHTML;

	$.ajax({
		url: fp,
		cache: false,
		async: false,
	}).done(function(data) {
		if (data.includes('mission ok')!=true){
			modal="...";
			setTimeout(modal_watcher,1000);
		}else{
			document.getElementById("proc").innerHTML="";
			const tg=document.getElementById("radio:"+tid);
			exto=given_filename.split(".");
			if (exto.length>1){
				ext=given_filename.split(".")[1];
			}else{
				ext='non';
			}
			if (ext=='pdf' || ext=='pptx' || ext=='txt'){
			const myDetails=document.getElementById("myDetails");
				w1=fp2.split('.pdf');
				w2=given_filename.split('.');
				if (ext=='txt'){
					w3=w1[0]+"db."+w2[1];
				}else{
					w3=w1[0]+"."+w2[1];
				}
//				alert(w3+" as "+given_filename+";"+ext);
				download(w3,given_filename);
				myDetails.open=true;
	      			tg.checked=true;
				setTimeout(function(){location.href="#radio:"+getPrePid()},200);
			}else{
				const win=document.getElementById('forPDF');
				win.src=fp2;
            			win.style.height="700px";
	      			tg.checked=true;
			}
		}
	}).fail(function(data){
		modal="...";
		setTimeout(modal_watcher,1000);
	}
		);

//	filename.innerHTML="<img src='"+dsr+id+".svg' height='150px'>";
}

function is_file(fp){
		var flg=null;
		$.ajax({
			url: fp,
			cache: false,
			async:false
		}).done(function(data) {
			flg=true;
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			flg=false;
		});
		return flg;
}

function closeAndGo(btn){
const myDetails=document.getElementById("myDetails");
const tg=document.getElementById("radio:"+btn.value);
	tg.click();
	myDetails.open=false;
	location.href='#mainView';
}

function showPdf(btn,opt,filename){
const xhr=new XMLHttpRequest();
const xproc=document.getElementById("proc");xproc.innerHTML=chkMess(opt);
const search=document.getElementById("ida");search.style.display='none';
let url="";

url=path+"/report/"+btn.value+".pdf";

if (opt=='non'){
var flg=is_file(url);
       	if(flg==true){
	    const win=document.getElementById('forPDF');
            win.src=url;
            win.style.height="700px";
            xproc.innerHTML='';
            search.style.display='block';
            location.href='#forPDF';
            return;
	}}
      const button=document.getElementById("syncPdf");
      const size=document.getElementById("mySize");
      const options=document.getElementById("syncOptions");
      const scale=document.getElementById("syncScale");
      const myUid=document.getElementById("syncUid");
      const myGiven=document.getElementById("given");
      myUid.value=btn.value;
      scale.value=size.value;

switch(opt){
    case 'non':
	pid=getPid();
	i=pid[0];
	myGiven.value=String(table[fname[7]][i])+" at pid#"+btn.value;
	options.value="-id "+btn.value+" -force -s "+scale.value;
	break;
    case 'pdf':
	myGiven.value=filename;
	options.value="-id "+btn.value+" -force -s "+scale.value;
	break;
    case 'ppt':
	myGiven.value=filename;
	options.value="-id "+btn.value+" -ppt -force -s "+scale.value;
	break;
    case 'db':
	myGiven.value=filename;
	options.value="-id "+btn.value+" -db -force";
	break;
    }

	if (db_type=="pri"){
		options.value+=" -database sList"+uid+".db";
	}

      button.click();
}

function forDownload(id,oper,filename){
mes=chkMess(oper);
const xproc=document.getElementById("proc");xproc.innerHTML=mes;
const search=document.getElementById("ida");search.style.display='none';
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/dbAction', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id': id,
        'oper':oper,
	'options':'true',
      },timeout:5000,
	}).done(function (data){
//	alert(data.pdf+":"+filename);
	if (filename!='non'){
		download(data.pdf,filename);
	}
  		xproc.innerHTML='';
	search.style.display='block';
    }).fail(function () {
	xproc.innerHTML='サーバーが混んでいます。再実行してください';
	search.style.display='block';
    });
}

function imageCheck(url,html,pid){
	var newImage=new Image();
	newImage.onload=function(){
		html.innerHTML="<img src='"+url+"' height='150px'>";
	}
	newImage.onerror=function(){
		forDownload(pid,"thumbnail",html);
	}
	newImage.src=url;
}

function showAsSmilesImages(tbl){
let wk,img;
for (var i = 0; i < table[fname[smileId]].length; i++) {
	wk=tbl.rows[i+1].cells[smileTableId];
	img=dsr+table['id'][i]+".svg";
	imageCheck(img,wk,table['id'][i]);
}
}

function showAsSmilesStrings(tbl){
let wk;
for (var i = 0; i < table[fname[smileId]].length; i++) {
	wk=tbl.rows[i+1].cells[smileTableId];
	wk.innerHTML=table[fname[smileId]][i];
}
}

function pushButton(btn){
const tbl = document.getElementById("retTable");

	if (btn.style.background=='transparent'){
		if (btn.name=='smiles'){
		btn.style.background='yellow';
	        btn.value = "Click for images";
		showAsSmilesStrings(tbl);
	}else{
		btn.style.background='red';
	}
	}else{
		if (btn.name=='smiles'){
	        	btn.value = "Click for strings";
			showAsSmilesImages(tbl);
		}
		btn.style.background='transparent';
	}
}

function download(url,filename){
const a = document.createElement('a');

	a.href=url;
	a.download=filename;
	a.click();
}

function getPrePid(){
const tgs=document.getElementsByName("id");

	for (var i=0;i<tgs.length;i++){
		bt=tgs[i];
		if (bt.checked){
		if (i!=0){
			return tgs[i-1].value-1;
		}else{
			return 0;
		}
	    }
    }
}
function getPid(){
const tgs=document.getElementsByName("id");

	for (let i=0;i<tgs.length;i++){
		bt=tgs[i];
		if (bt.checked){
			return [i,bt,bt.value];
	}
    }
}

function nextPdf(){
sel=document.getElementsByName("id");
pid=getPid();
i=pid[0]+1;

//	alert(String(i)+"vs"+String(sel.length));
	if ((i+1)==sel.length){
		i=0;
	}
	sel[i].click();
	showPdf(sel[i],"non","non");
//	alert(i,bt.value)
}

function proc(){
let i,pid;
let rd_btn=document.getElementsByName("oper");
let checkValue='';
let filename;

	for(let i=0;i<rd_btn.length;i++){
		if(rd_btn.item(i).checked){
			checkValue=rd_btn.item(i).value;
		}
	}

	switch(parseInt(checkValue)){
	case 1:
	case 2:
	case 3:
		pid=getPid();
		i=pid[0];
		filename=String(table[fname[6]][i])+String(table[fname[7]][i]);
		if (parseInt(checkValue)==1){
			showPdf(pid[1],'pdf',filename+'.pdf');
		}else if (parseInt(checkValue)==2){
			showPdf(pid[1],'ppt',filename+'.pptx');
		}else{
			showPdf(pid[1],'db',filename+'.txt');
		}
		break;
	case 4:
		pid=getPid();
		document.getElementById("dropDbId").value=pid[1].value;
		document.getElementById("dropDb").click();
		break;
	}
}

function setSize(btn){
	document.getElementById("mySize").value=btn.value;
}

function makeItTime(s){
const tm=s.slice(0,4)+"年"+s.slice(4,6)+"月"+s.slice(6,8)+"日"+s.slice(8,10)+"時"+s.slice(10,12)+"分";
	return(tm);
}

conv={'id':'選択','date':'日付','uname':'ユーザー名','loop':'検索件数','factors':'検索の重み','options':'検索条件','substance':'物質名'}
function showTable(){
const skip=[1,3,5]
const tbl = document.getElementById("retTable");
const tblBody = document.createElement("tbody");
let tr,td,inp,xx,addButton;

        skip.push(2);

    tr = document.createElement("tr");
    td = document.createElement("td");
//    xx = document.createTextNode("選択");
//    td.appendChild(xx);
//    tr.appendChild(td);
//

    for (var j = 0; j < fname.length; j++) {
if (!skip.includes(j)){
    td = document.createElement("td");
    inp = document.createElement('input');
    inp.type='button';
    xx=fname[j];
    for(key in conv){
	    if (key==fname[j]){
		    xx=conv[fname[j]];
	    }
    }
      inp.name = xx;
      inp.value = xx;
      inp.setAttribute('onclick','pushButton(this)');
      td.appendChild(inp);
      tr.appendChild(td);
      if (fname[j]=='smiles'){
      inp.value = "Click for images";
	      smileId=j;
	      smileTableId=td.cellIndex;
            }
	}
    }// for index list
    tblBody.appendChild(tr);

let cell,row,cellText;

for (var i = 0; i < table[fname[0]].length-1; i++) {
    // 表の行を作成
    row = document.createElement("tr");
    td = document.createElement("td");

//    addButton = document.createElement('input');
//    addButton.type = 'checkBox';
//    addButton.setAttribute("name","pid");
//    addButton.setAttribute("value",table[fname[0]][i]);
//    td.appendChild(addButton);
//    row.appendChild(td);

    for (let j = 0; j < fname.length; j++) {
if (! skip.includes(j)){
      cell = document.createElement("td");
if (j==0){
      cellText = document.createElement("input");
      cellText.type="radio";
      cellText.name="id";
      cellText.id="radio:"+table[fname[0]][i];
      cellText.value=table[fname[j]][i];
      cellText.setAttribute('onchange','showPdf(this,"non","non")');

      if (i==0){
	      cellText.checked=true;
      }

}else{
let tm;
      if (fname[j]=='date'){
	      tm=makeItTime(table[fname[j]][i]);
	      cellText = document.createTextNode(tm);
      }else{
              cellText = document.createTextNode(table[fname[j]][i]);
      }
}
      cell.appendChild(cellText);
      row.appendChild(cell);
	}
    }
    tblBody.appendChild(row);
tbl.appendChild(tblBody);
  }
tbl.setAttribute("border", "2");
}

showTable();

if (modal=='yes'){
	const mess=given_filename+" is making ... wait...";
	document.getElementById("proc").innerHTML=mess;

	setTimeout(modal_watcher,1000);
}else{
	cell=document.getElementById("radio:"+table[fname[0]][0]);
	showPdf(cell,"non","non");
}

</script>
