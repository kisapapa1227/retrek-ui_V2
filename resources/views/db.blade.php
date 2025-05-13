<!DOCTYPE html>
<html lang="ja">
<head>
    <title>Database manager</title>
<style>
        .fixed-top {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #fff;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.8);
            padding: 10px 0;
        }
	.oper{
	display:inline-block;
	color:#343a40;
}
details {
  font: 16px "Open Sans",
    Calibri,
    sans-serif;
}
details > summary,#bta, #btb {
  padding: 2px 6px;
  width: 15em;
  background-color: #a9ceec;
  border: none;
  box-shadow: 3px 3px 4px black;
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
    <div class="fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id="btb" onclick="window.location.href='/search';" class="btn btn-primary back-button">ユーザー検索画面へ戻る</button>
            </form>
      </div>
    </div>
<div id="mainView"></br></div>
<div>
</br>
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
<input type="radio" name="oper" class="oper" id="del" value="4">
 <label style="width:90px" for="del">{{$opt[3]}}</label>
                        </div>
</div>
<div id="proc">
</div>
<div>
<table border="0" align="top">
<tr><td>
<button id='bta' type="button" onclick="proc()"> <div id='ida' style="display:inline-block">実行する</div></button></td><td>
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
        <input type="text" name="oper" value="dropDb" id="dropDbOper">
        <input type="text" name="id" value="" id="dropDbId">
</div>
</form>

<form action="{{ route('syncPdf') }}" method="POST">
@csrf
<div style="display:none">
	<button type="submit" id="syncPdf">
        <input type="text" name="options" value="" id="syncOptions">
        <input type="text" name="scale" value="0.25" id="syncScale">
        <input type="text" name="uid" value="" id="syncUid">
        <input type="text" name="given" value="who..." id="given">
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
let thisId=@json($uid);
let given_filename=@json($filename);
let sT=Date.now();
let i=0;
let smileId=0;
let smileTableId=0;
dsr="http://localhost/images/smiles/pid"

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

function modal_wacher(){
var fp="http://localhost/images/report/readDb"+thisId+"normal.log";
var fp2="http://localhost/images/report/"+thisId+".pdf";
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
			const tg=document.getElementById("radio:"+thisId);
			exto=given_filename.split(".");
			if (exto.length>1){
				ext=given_filename.split(".")[1];
			}else{
				ext='non';
			}
			if (ext=='pdf' || ext=='pptx' || ext=='txt'){
			const myDetails=document.getElementById("myDetails");
				w1=fp2.split('.');
				w2=given_filename.split('.');
				if (ext=='txt'){
					w3=w1[0]+"db."+w2[1];
				}else{
					w3=w1[0]+"."+w2[1];
				}
//				alert(w3+" as "+given_filename);
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

function closeAndGo(btn){
const myDetails=document.getElementById("myDetails");
const tg=document.getElementById("radio:"+btn.value);
	tg.click();
	myDetails.open=false;
	location.href='#mainView';
}

function showPdf(btn,opt,filename){
const xhr=new XMLHttpRequest();
const url="http://localhost/images/report/"+btn.value+".pdf";
const xproc=document.getElementById("proc");xproc.innerHTML=chkMess(opt);
const search=document.getElementById("ida");search.style.display='none';

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

conv={'date':'日付','uname':'ユーザー名','loop':'検索件数','factors':'検索の重み','options':'検索条件','substance':'物質名'}
function showTable(){
const skip=[1,3,5]
const tbl = document.getElementById("retTable");
const tblBody = document.createElement("tbody");
let tr,td,inp,xx,addButton;

    tr = document.createElement("tr");
    td = document.createElement("td");
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

for (var i = 0; i < table[fname[0]].length-1; i++) {
    // 表の行を作成
    row = document.createElement("tr");
    td = document.createElement("td");

    addButton = document.createElement('input');
    addButton.type = 'button';
    addButton.setAttribute("name","pid");
    addButton.setAttribute("value",table[fname[0]][i]);
    addButton.setAttribute("onclick","closeAndGo(this)");
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

	setTimeout(modal_wacher,1000);
}else{
	cell=document.getElementById("radio:"+table[fname[0]][0]);
	showPdf(cell,"non","non");
}
</script>
