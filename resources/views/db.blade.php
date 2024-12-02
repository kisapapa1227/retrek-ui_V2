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
</br>
<div>
<br>
<details>
<summary> 探索経路の一覧、選択 </summary>
</br>
<table border="1" id="retTable" align="top">
</table>
</details>
</br>

@php
$opt=array("PDFを表示","PPTXでダウンロード","RetRek情報のダウンロード","探索結果の削除")
@endphp
<input type="radio" name="oper" class="oper" id="pdf" value="1" onchange="showCSV()">
 <label style="width:90px" for="Original">{{$opt[0]}}</label>
<input type="radio" name="oper" class="oper" id="ppt" value="2" checked onchange="showCSV()">
<label style="width:100px" for="Original">{{$opt[1]}}</label>
<input type="radio" name="oper" class="oper" id="db" value="3" onchange="showCSV()">
 <label style="width:90px" for="Original">{{$opt[2]}}</label>
<input type="radio" name="oper" class="oper" id="del" value="4" onchange="showCSV()">
 <label style="width:90px" for="Original">{{$opt[3]}}</label>
                        </div>
</div>
<div id="proc">
</div>
<div>
<div>
<button id='bta' type="button" onclick="proc()"> <div id='ida'>実行する</div></button></br>
</div>
<div>
<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>
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
let loop=CHK;
let pdf=0;
let sT=Date.now();
let exT="∞";
let works=0;
let i=0;
let smileId=0;
let smileTableId=0;
dsr="http://localhost/images/smiles/pid"

function chkMess(opt){
	switch(opt){
	case 'pdf':return("pdfを表示中");
	case '-force':return("pdfを作成中");
	case 'non':return("pdfを切り替え中");
	case 'askPptx':return("pptxをダウンロード中");
	case 'db':return("ReTRek情報をダウンロード中");
	case 'drop':return("探索履歴の削除中");
	case 'thumbnail':return("分子の画像を作成中");
	default:return('未登録'+opt);
}

}

function showPdf(btn,opt){
mes=chkMess(opt);
	xproc=document.getElementById("proc");xproc.innerHTML=mes;
	search=document.getElementById("ida");search.style.display='none';
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/dbAction', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id': btn.value,
        'oper':"askPdf",
	'opt':opt,
      },
      timeout:5000,
    }).done(function (data){
      win=document.getElementById('forPDF');
      win.src=data.pdf;
      win.style.height="700px";
  	xproc.innerHTML='';
	search.style.display='block';
    }).fail(function () {
  	xproc.innerHTML='サーバーが混んでいます。再実行してください';
	search.style.display='block';
    });
}

function forDownload(id,oper,filename){
mes=chkMess(oper);
	xproc=document.getElementById("proc");xproc.innerHTML=mes;
	search=document.getElementById("ida");search.style.display='none';
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/dbAction', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id': id,
        'oper':oper,
	'options':'true',
      },
      timeout:5000,
    }).done(function (data){
	    if (data.pdf=='reload'){
		location.reload();
	    }else if(data.pdf=='none'){
	   setTimeout(()=>{
		filename.innerHTML="<img src='"+dsr+id+".svg' height='150px'>";
	    },"1000");
// nothing to do
	    }else{
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
for (i = 0; i < table[fname[smileId]].length; i++) {
	wk=tbl.rows[i+1].cells[smileTableId];
	img=dsr+table['id'][i]+".svg";
	imageCheck(img,wk,table['id'][i]);
//		wk.innerHTML="<img src='"+img+"' height='150px'>";
}
}

function showAsSmilesStrings(tbl){
//	komai
for (i = 0; i < table[fname[smileId]].length; i++) {
	wk=tbl.rows[i+1].cells[smileTableId];
	wk.innerHTML=table[fname[smileId]][i];
//	wk.style.background='yellow';
//	tbl.rows[i+1].cells[smileTableId].innerHTML="<img src='"+table[fname[smileId]][i]";
}
}

function pushButton(btn){
let tbl = document.getElementById("retTable");
	if (btn.style.background=='transparent'){
		if (btn.name=='smiles'){
//      inp.name = xx;
		btn.style.background='yellow';
	        btn.value = "Click for images";
		//komai
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

function nothing(){
  	document.getElementById("proc").innerHTML=String(works)+" jos is working";
}
function getPid(){
	tgs=document.getElementsByName("id");
	len=tgs.length;
	for (let i=0;i<len;i++){
		bt=tgs[i];
		if (bt.checked){
		return [i,bt.value,bt];
	    }
    }
}

function proc(){
	let pid;
	let tgs;
	let len;
	let rd_btn=document.getElementsByName("oper");
	let checkValue='';
	let filename;

	len=rd_btn.length;
	for(let i=0;i<len;i++){
		if(rd_btn.item(i).checked){
			checkValue=rd_btn.item(i).value;
		}
	}

	switch(parseInt(checkValue)){
	case 1:
		pid=getPid();
		showPdf(pid[2],'-force');
		break;
	case 2:
		pid=getPid();
		i=pid[0];
		filename=String(table[fname[6]][i])+String(table[fname[7]][i]+".pptx");
		forDownload(pid[1],"askPptx",filename);
		break;
	case 3:
		pid=getPid();
		i=pid[0];
		filename=String(table[fname[6]][i])+String(table[fname[7]][i]+".txt");
		forDownload(pid[1],"db",filename);
		break;
	case 4:
		pid=getPid();
		i=pid[0];
		forDownload(pid[1],"drop","non");
		break;
	case 5:
	tgs=document.getElementsByName("pid");
	len=tgs.length;
// komai
	for (let i=0;i<len;i++){
		bt=tgs[i];
		pid=bt.id.split(":")[1]
		if (bt.checked){
		filename=String(table[fname[6]][i])+String(table[fname[7]][i]+".pptx");
		forDownload(pid,"askPptx",filename);
		}
//		alert("what->"+String(i)+bt.id)
	}
		break;
	case 6:
		break;
	}
}
function showCSV(){
	return;
	        search=document.getElementById("ida");
	        oper=document.getElementsByName("oper");
	if (oper[0].checked){
		search.innerHTML='PDF を表示する';
	}else if (oper[1].checked){
		search.innerHTML='ファイルをダウンロードする';
	}else if (oper[2].checked){
		search.innerHTML='ファイルをダウンロードする';
	}else if (oper[3].checked){
		search.innerHTML='削除する';
	}else{
		search.innerHTML='Non';
	}
}

const askPdf=()=>{
$(function(){

let l=[];
  document.getElementById("proc").innerHTML="This "+l;
  return;
    for (let j = 0; j < fname.length; j++) {
	    if (document.getElementById("chk"+String(j)).checked){
		    l.push(j);
	    }
    }
//  document.getElementById("proc").innerHTML=l;
      document.getElementById("proc").innerHTML='作業中';
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/dbAction', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id':"dummy" 
      },
    }).done(function (data){
      count=data.test;
      document.getElementById("proc").innerHTML=data.test.uname;
    }).fail(function () {
      document.getElementById("proc").innerHTML='';
    });
//window.location.reload();
});
}

function makeItTime(s){
	tm=s.slice(0,4)+"年"+s.slice(4,6)+"月"+s.slice(6,8)+"日"+s.slice(8,10)+"時"+s.slice(10,12)+"分";
	return(tm);
}

function flushText(l) {
  const elem = document.getElementById("proc");
  let d=new Date();
//  elem.innerHTML=db.head+"<br>";
  elem.innerHTML=table[fname[3]]+"<br>";
//  elem.innerHTML=table['id']+"<br>";
  if (l==1){
	  elem.innerHTML+="updating.."
  }
    num=3;
  if (count==num){
	  if (pdf=="0"){
	  	elem.innerHTML+="<br>summary report is making...";
	  }
  }else{
  }
}

conv={'date':'日付','uname':'ユーザー名','loop':'検索件数','factors':'検索の重み','options':'検索条件','substance':'物質名'}
function showTable(){
const skip=[1,3,5]
const tbl = document.getElementById("retTable");
const tblBody = document.createElement("tbody");

    let tr = document.createElement("tr");
    let td = document.createElement("td");
//    let inp = document.createTextNode("--");
//    td.appendChild(inp);
    tr.appendChild(td);

    for (let j = 0; j < fname.length; j++) {
if (! skip.includes(j)){
    let td = document.createElement("td");
    let inp = document.createElement('input');
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

    let i;
for (i = 0; i < table[fname[0]].length-1; i++) {
    // 表の行を作成
    let row = document.createElement("tr");
    let td = document.createElement("td");

    let addButton = document.createElement('input');
    addButton.type = 'checkbox';
    //komai
    addButton.setAttribute("name","pid");
    addButton.setAttribute("id","id:"+String(table[fname[0]][i]));
    td.appendChild(addButton);
    row.appendChild(td);

    for (let j = 0; j < fname.length; j++) {
if (! skip.includes(j)){
      let cell = document.createElement("td");
      let cellText;
if (j==0){
      cellText = document.createElement("input");
      cellText.type="radio";
      cellText.name="id";
//      cellText.value=String(i)+":"+table[fname[2]][i];
      cellText.value=table[fname[j]][i];
      cellText.setAttribute('onchange','showPdf(this,"non")');
      if (i==0){
	      cellText.checked=true;
      }
}else{
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
</script>
