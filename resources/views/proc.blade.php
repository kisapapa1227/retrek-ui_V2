<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <title>Procces monitor</title>
<style>
#bta {
  width:14em;
}
#btb {
  width:12em;
}

</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="wrapper">
	<div class="container">
<h1>進捗表示画面</h1>
		<div class="inline">
		<div class="block">
<a>
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
            <button id='btb' onclick="window.location.href='/search';" class="sysButton">処理を中止する</button>
            </form>
</a>
<a>
	<div id='ida'>
        <button id='bta' onclick="addDb()" class="sysButton">データベースに追加する</button>
	</div>
	<div id='idb'>  </div>
</a>
		</div>
		</div>
<div>
<h3 class="message">Searching "{{$smiles}}"</h3><br>
</div>
<div  class="subMessage" id="proc">
</div>
</div>
<div>
<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>
</div>
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
const btb = document.getElementById("btb");
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
	  btb.innerHTML="メインメニューに戻る";
//<embed src="http://localhost/images/report/{substance}.pdf" type="application/pdf" width="100%" height="700px"></embed>'
    }
}

function flushText(l) {
  const elem = document.getElementById("proc");
  let d=new Date();
  let elapse_time,exp_time;

  elem.innerHTML="探索済みルート数 "+count+"/"+num+"<br>";

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
