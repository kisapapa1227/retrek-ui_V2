<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <title>Database manager</title>
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
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="wrapper">
	<div class="container">
<h1 id="theTitle">データベースの管理</h1>
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id="btb" onclick="window.location.href='/search';" class="sysButton">メインメニューに戻る</button>
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
            </form>

<div class="mainView"></div>

<table border="1" id="retTable" align="top">
</table>


@php
$opt=array("選択したレコードの削除","データベースの保存","データベースの復元","データベースの初期化","一括ダウンロード")
@endphp
 <button id="del" class="sysButton" onclick="proc(this)">{{$opt[0]}}</button>
 <button id="db_save" class="sysButton" style="width:24ex;" onclick="proc(this)">{{$opt[1]}}</button>
 <button id="db_load" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[2]}}</button>
 <button id="db_init" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[3]}}</button>
 <button id="get_all" class="sysButton" style="width:24ex" onclick="proc(this)">{{$opt[4]}}</button>
                        </div>
<div id="proc">
</div>
<div>
<button class="sysButton" type="button" onclick="goThere(this)">表に戻る</button>
<div id="modal" style="margin-left:20px"></div>
</div>

<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>

<form action="{{ route('dropDb') }}" method="POST" id="dropDbForm" enctype="multipart/form-data">
@csrf
<div style="display:none">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
	<input type="file" name="a" id="loadA" accept=".db">
	<input type="text" name="oper" value="db_load" id="loadDbId">
	<input type="submit" id="loadB">

@isset($log)
	<input type="text" name="log" value="{{$log}}" id="loadStat">
@else
	<input type="text" name="log" value="false" id="loadStat">
@endisset
<div>
</form>

<form action="{{ route('dropDb') }}" method="POST">
@csrf
<div style="display:none">
	<button type="submit" id="dropDb">
        <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
        <input style="display:none" type="text" name="uid" value="{{$uid}}">
        <input type="text" name="oper" value="dropDb" id="dropDbOper">
        <input type="text" name="tid" value="" id="dropDbId">
</div>
</form>

<form action="{{ route('syncPdf') }}" method="POST">
@csrf
<div style="display:none">
	<button type="submit" id="syncPdf">
        <input type="text" name="options" value="" id="syncOptions">
        <input type="text" name="scale" value="0.25" id="syncScale">
        <input type="text" name="tid" value="" id="syncUid">
        <input type="text" name="uid" value="{{$uid}}">
        <input type="text" name="db_type" value="{{$db_type}}">
        <input type="text" name="given" value="who..." id="given">
	<input type="text" name="from" value="dbManage">
</div>
</form>

    <footer>
      <hr />
      version 1.1 2024/11/30
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
let given_filename=@json($filename);
let sT=Date.now();
let i=0;
let smileId=0;
let smileTableId=0;
let path="{{asset('images')}}";

if ("{{$db_type}}"=="pri"){
	path=path+"/"+uid;
}

if ("{{$inUse}}"=='True'){
	alert("このデータベースを利用中のプロセスがあります。このデータベースの初期化、別のデータベースの復元はできません。");
	document.getElementById("db_load").style.display='none';
	document.getElementById("db_init").style.display='none';
}

dsr=path+"/smiles/pid";

if ("{{$db_type}}"=="pri"){
        document.getElementById("theTitle").innerHTML="個人用データベースの管理";
}else{
        document.getElementById("theTitle").innerHTML="共有データベースの管理";
}

let ret=document.getElementById("loadStat").value;

if (ret!="false"){
	alert(ret);
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

async function goThere(btn){
	await sleep(10);
	location.href="#radio:"+getPrePid();
}

function modal_wacher(){
var fp=path+"/report/readDb"+tid+"normal.log";
var fp2=path+"/report/"+tid+".pdf";

let modal=document.getElementById("modal").innerHTML;

	$.ajax({
		url: fp,
		cache: false,
		async: false,
	}).done(function(data) {
		let mes=data.split("\n");
		modal=mes[mes.length-2];
		if (mes[mes.length-2]!="mission ok"){
			modal="...";
			setTimeout(modal_wacher,1000);
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
				w1=fp2.split('.');
				w2=given_filename.split('.');
				if (ext=='txt'){
					w3=w1[0]+"db."+w2[1];
				}else{
					w3=w1[0]+"."+w2[1];
				}
//				alert(w3+" as "+given_filename);
				download(w3,given_filename);
	      			tg.checked=true;
				setTimeout(function(){location.href="#radio:"+getPrePid()},200);
			}else{
				const win=document.getElementById('forPDF');
				win.src=fp2;
          			win.style.height="700px";
				//window.open(fp2,"mozillaWindow","popup");
	      			tg.checked=true;
			}
		}
	}).fail(function(data){
		modal="...";
		setTimeout(modal_wacher,1000);
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

function showPdf(btn,opt,filename){
const xhr=new XMLHttpRequest();
const url=path+"/report/"+btn.value+".pdf";
const xproc=document.getElementById("proc");xproc.innerHTML=chkMess(opt);

if (opt=='non'){
var flg=is_file(url);
       	if(flg==true){
	    const win=document.getElementById('forPDF');
            win.src=url;
            win.style.height="700px";
            xproc.innerHTML='';
//            location.href='#forPDF';
            return;
	}}

      const button=document.getElementById("syncPdf");
      const options=document.getElementById("syncOptions");
      const scale=document.getElementById("syncScale");
      const myUid=document.getElementById("syncUid");
      const myGiven=document.getElementById("given");
      myUid.value=btn.value;

switch(opt){
    case 'non':
	pid=getPid();
	i=pid[0];
	myGiven.value=String(table[fname[7]][i])+" at pid#"+btn.value;
        options.value="-id "+btn.value+" -force -s "+scale.value;break;
    case 'pdf':
	myGiven.value=filename;
        options.value="-id "+btn.value+" -force -s "+scale.value;break;
    case 'ppt':
	myGiven.value=filename;
        options.value="-id "+btn.value+" -ppt -force -s "+scale.value;break;
    case 'db':
	myGiven.value=filename;
        options.value="-id "+btn.value+" -db -force";break;
    }
      button.click();
}

function forDownload(id,oper,filename){
mes=chkMess(oper);
const xproc=document.getElementById("proc");xproc.innerHTML=mes;
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
    }).fail(function () {
	xproc.innerHTML='サーバーが混んでいます。再実行してください';
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

function pushAll(odd){
let pids=document.getElementsByName("pid");

	for(let i=0;i<pids.length;i++){
		pids.item(i).checked=odd;
		}
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
	}else if (btn.name=="select"){
		pushAll(false);
		btn.style.background='yellow';
	}else{
		btn.style.background='red';
	}
	}else{
		if (btn.name=='smiles'){
	        	btn.value = "Click for strings";
			showAsSmilesImages(tbl);
	}else if (btn.name=="select"){
		pushAll(true);
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
			return tgs[i-1].value-1;
		}
	    }
    }
}
function getPid(){
const tgs=document.getElementsByName("id");

	for (let i=0;i<tgs.length;i++){
		bt=tgs[i];
		if (bt.checked){
			if (i!=0){
				i=i-1;
			}
		return [i,bt,bt.value];
	}
    }
}

function proc(btn){
let i,n;
let pids=document.getElementsByName("pid");
let ask="";

	for(let i=0;i<pids.length;i++){
		if(pids.item(i).checked==true){
			n=pids.item(i).value;
			if (ask==""){
				ask=String(n);
			}else{
				ask=ask+","+String(n);
			}
			
		}
}

switch(btn.id){
    case 'db_load':
	    let r=confirm("今あるデータベースに上書きされます。よろしいですか？");
	    if(r){
	    let a=document.getElementById("loadA");
	    a.addEventListener("input",function(){document.getElementById("loadB").click();});
	    a.click();
	    }
	    break;
    case 'db_init':
	    let x=confirm("初期化すると保存していないデータベースは復元できませんが、よろしいですか？");
	    if (!x){
		    break;
	    }
    case 'db_save':
	document.getElementById("dropDbOper").value=btn.id;
	document.getElementById("dropDb").click();
//        <input type="text" name="oper" value="dropDb" id="dropDbOper">
//        <input type="text" name="id" value="" id="dropDbId">
	    break;
    case 'pdf':
	    break;
    case 'get_all':
	document.getElementById("dropDbOper").value=btn.id;
	document.getElementById("dropDbId").value=ask;
	document.getElementById("dropDb").click();
	    break;
    case 'del':
if(ask==""){
	alert("レコードが選択されていません")
	return;
}
//	alert(ask);
	document.getElementById("dropDbId").value=ask;
	document.getElementById("dropDb").click();
}}

function xproc(){
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
	case 11:
	case 12:
	case 13:
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
	case 14:
		pid=getPid();
		document.getElementById("dropDbId").value=pid[1].value;
		document.getElementById("dropDb").click();
		break;
	}
}

function makeItTime(s){
const tm=s.slice(0,4)+"年"+s.slice(4,6)+"月"+s.slice(6,8)+"日"+s.slice(8,10)+"時"+s.slice(10,12)+"分";
	return(tm);
}

conv={'id':'pdfを表示','date':'日付','uname':'ユーザー名','loop':'検索件数','factors':'検索の重み','options':'検索条件','substance':'物質名'}
function showTable(){
const skip=[1,3,5]
const tbl = document.getElementById("retTable");
const tblBody = document.createElement("tbody");
let tr,td,inp,xx,addButton;

if ("{{$db_type}}"=="pri"){
        skip.push(2);
}

    tr = document.createElement("tr");
    td = document.createElement("td");
//    xx = document.createTextNode("選択");
    xx = document.createElement('input');
    xx.type='button';
    xx.value = "選択";
    xx.name="select";
    xx.setAttribute('onclick','pushButton(this)');
    td.appendChild(xx);
    tr.appendChild(td);

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
//komai


for (var i = 0; i < table[fname[0]].length-1; i++) {
    // 表の行を作成
    row = document.createElement("tr");
    td = document.createElement("td");

    addButton = document.createElement('input');
    addButton.type = 'checkBox';
    addButton.setAttribute("name","pid");
    addButton.setAttribute("value",table[fname[0]][i]);
    td.appendChild(addButton);

    row.appendChild(td);

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

 if(j==0){
    ww=document.createElement('labal');
    ww.innerText=table[fname[0]][i];
    cell.appendChild(ww);
 }
      row.appendChild(cell);
	}}
    tblBody.appendChild(row);
tbl.appendChild(tblBody);
  }
tbl.setAttribute("border", "2");
}

if (table!=""){
showTable();
if (modal=='yes'){
	const mess=given_filename+" is making ... wait...";
	document.getElementById("proc").innerHTML=mess;

	setTimeout(modal_wacher,1000);
}else{
	cell=document.getElementById("radio:"+table[fname[0]][0]);
	showPdf(cell,"non","non");
}

}

</script>
