<!DOCTYPE html>
<html lang="ja">
<head>
    <title>Procces monitor</title>
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
#bta,#btb {
  padding: 2px 6px;
  width: 12em;
  background-color: #a9ceec;
  color: #000;
  border: none;
  box-shadow: 3px 3px 4px black;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id='btb' onclick="window.location.href='/search';" class="btn btn-primary back-button">ユーザー検索画面へ戻る</button>
            </form>
<div id='ida'>
                <button id='bta' onclick="addDb()" class="btn btn-primary back-button">データベースに追加する</button>
      </div>
<div id='idb'>  </div>
</div>
    </div>
</br>
<div>
</br>
</br>
<h3>Searching "{{$smiles}}"</h3><br>
</div>
<div id="proc">
</div>

<div>
<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>
    <footer>
      <hr />
      last updated 2024 
    </footer>
  </body>
</html>

<script>
let CHK=10
let loop=CHK;
let num={{$route_num}};
let count=0;
let pdf=0;
let sT=Date.now();
let exT="∞";
let elaps_time;

const ida = document.getElementById("ida");
const idb = document.getElementById("idb");
ida.style.visibility='hidden';

const addDb=()=>{
	ida.style.visibility='hidden';
	idb.innerHTML="Saving current search...";
$(function(){
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/addDb', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id': "{{$substance}}",
        'uid': "{{$uid}}",
      },
    }).done(function (data){
      ida.style.visibility='visible';
      idb.innerHTML=data.substance+" is saved.";
	ida.style.display="inline-block";
	idb.style.display="inline-block";
    }).fail(function () {
      ida.style.visibility='visible';
      idb.innerHTML='fail:'+data.substance;
	ida.style.display="inline-block";
	idb.style.display="inline-block";
    });
//window.location.reload();
});
}
const countUp=()=>{
    flushText(loop);
if(loop==0){
    loop=CHK;
$(function(){
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/askProc', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'id': "{{$substance}}",
      },
    }).done(function (data){
      count=data.currentRoute;
      pdf=data.pdf;
      uid=data.uid;
    }).fail(function () {
      console.log('fail');
    });
//window.location.reload();
});
  cT=Date.now();
    if (count!=0){
  	eT=(cT-sT)/count;
	exT=eT*(num-count);
    }
}
    loop--;
    if (pdf==0){
	setTimeout(countUp, 1000);
	ida.style.visibility='hidden';
    }else{
          win=document.getElementById('forPDF');
          win.src="http://localhost/images/"+uid+"/report/{{$substance}}.pdf";
	  win.style.height="700px";
	  ida.style.visibility='visible';
//<embed src="http://localhost/images/report/{substance}.pdf" type="application/pdf" width="100%" height="700px"></embed>'
    }
}

function flushText(l) {
  const elem = document.getElementById("proc");
  let d=new Date();
  let elapse_time,exp_time;

  elem.innerHTML="検索済みルート数 "+count+"/"+num+"<br>";

	  ex=(Date.now()-sT)/1000 >> 0;
	  hr=String(Math.floor(ex/60/60));
	  min=String(Math.floor((ex%3600)/60));
	  sec=String(ex%60);
	  elapse_time=" 経過時間 "+hr+":"+('00'+min).slice(-2)+":"+('00'+sec).slice(-2);
	  elem.innerHTML+="<br>"+d;
	  elem.innerHTML+="<br>"+elapse_time;
  if (count==num){
	  if (pdf=="0"){
	  	elem.innerHTML+="<br>Making report in progress...";
	  }
  }else{
	  if (count==0){
		  line3="";
	  }else{
	  ex=exT/1000 >>0;
	  hr=String(Math.floor(ex/60/60));
	  min=String(Math.floor((ex%3600)/60));
	  sec=String(ex%60);
	  line3=" (残り時間 "+hr+":"+('00'+min).slice(-2)+":"+('00'+sec).slice(-2)+")";
	  }
	  elem.innerHTML+=line3;
  }
}
//    setTimeout(countUp, 1000);
    countUp();

</script>
