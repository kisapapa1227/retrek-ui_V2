<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <title>Procces monitor</title>
<style>
#btb {
  width: 8em;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="wrapper">
	<div class="container">
<h1>進捗表示画面</h1>
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id='btb' onclick="window.location.href='/search';" class="sysButton">処理を中止する</button>
            </form>
<form action="{{ route('multiProc') }}" method="POST">
@csrf
<input type="hidden" id="fromCSV" name="fromCSV" value="{{$fromCSV}}">
<input type="hidden" name="max_loop" value="{{$max_loop}}">
<input type="hidden" name="loop" value="{{$loop}}">
<input type="hidden" name="uid" value="{{$uid}}">
<input type="hidden" name="user_Id" value="{{$userId}}">
<button type="submit" id="multiProc" style="display:none"></button>
</form>
<div>
</br>
<h3 class="message" id="smilesList"></h3>
</div>
<div class="subMessage" id="proc">
</div>

<div>
<embed id="forPDF" type="application/pdf" width="100%" height="0"></embed>
</div>
</body>
</html>

<script>
let CHK=10
let loop=CHK;
let num={{$route_num}};
let targ={{$loop}};
let count=0;
let pdf=0;
let sT=Date.now();
let exT="∞";
let elaps_time;

h3=document.getElementById('smilesList');

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
    }else{
//	  document.getElementById('bta').click();
          win=document.getElementById('forPDF');
          win.src="http://localhost/images/"+uid+"/report/{{$substance}}.pdf";
	  win.style.height="700px";
	  const multiProc=document.getElementById('multiProc');
	  multiProc.click();
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

const lines=document.getElementById('fromCSV').value.split(";");
let nnn=1;
h3.innerHTML="";
	for(i=0;i<lines.length;i++){
		if (lines[i].indexOf('#')==-1){
			col=lines[i].split(',');
			h3.innerHTML+="("+String(nnn)+")"+col[0];
			if (nnn<targ){
				h3.innerHTML+=" done";
			}else if (nnn==targ){
				h3.innerHTML+=" searching";
			}
			h3.innerHTML+="<br>";
			nnn=nnn+1;
		}
	}
    countUp();

</script>
