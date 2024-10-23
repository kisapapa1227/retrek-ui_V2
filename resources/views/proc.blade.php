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
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
    <div class="fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button onclick="window.location.href='/search';" class="btn btn-primary back-button">ユーザー検索画面へ戻る</button>
            </form>
      </div>
    </div>
</br>
<div>
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
          win=document.getElementById('forPDF');
          win.src="http://localhost/images/report/{{$substance}}.pdf";
	  win.style.height="700px";
//<embed src="http://localhost/images/report/{substance}.pdf" type="application/pdf" width="100%" height="700px"></embed>'
    }
}

function flushText(l) {
  const elem = document.getElementById("proc");
  let d=new Date();
  elem.innerHTML="Route "+count+"/"+num+"<br>"+d;
  if (l==1){
	  elem.innerHTML+="updating.."
  }
  if (count==num){
	  if (pdf=="0"){
	  	elem.innerHTML+="<br>summary report is making...";
	  }
  }else{
	  if (count==0){
		  elem.innerHTML+="<br>Remaining --:--:--";
	  }else{
	  ex=parseInt(exT/1000);
	  hr=String(Math.floor(ex/60/60));
	  min=String(Math.floor((ex%3600)/60));
	  sec=String(ex%60);
	  elem.innerHTML+="<br>Remaining "+hr+":"+('00'+min).slice(-2)+":"+('00'+sec).slice(-2);
	  }
  }
}
//    setTimeout(countUp, 1000);
    countUp();

</script>
