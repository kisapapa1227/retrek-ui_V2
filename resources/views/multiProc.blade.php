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
<div style="display:flex">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id='btb' onclick="window.location.href='/search';" class="sysButton">中止</button>
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
            </form>

            <form action="{{ route('mRet') }}" method="GET" class="mb-3">
                <button id='btb' style="margin-left:2ex" name="mRet" onclick="window.location.href='/search';" class="sysButton">処理継続で戻る</button>
            <input style="display:none" type="text" name="db_type" value="{{$db_type}}">
            <input style="display:none" type="text" name="uid" value="{{$uid}}">
            </form>
</div>
<div>
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
let uid="{{$uid}}";
let pid=-1;
let path="{{asset('images')}}";

h3=document.getElementById('smilesList');

const countUp=()=>{
$(function(){
    $.ajax({
      headers: {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
      },
      url: '/askMaster', // routes/web.php でとび先を設定
      method: 'POST',
      data: {
        'uid': "{{$uid}}",
        'pid': pid,
      },
    }).done(function (data){
//      alert(pid);
//      alert("proc"+proc);
//      alert("list"+list);
      if (data.pid==-2){
	      document.getElementsByName('mRet')[0].click();
      }
      pid=data.pid;
      proc=data.proc;
      list=data.list;
    flushText(data.list,data.proc);
    }).fail(function () {
      console.log('fail');
    });
});
	setTimeout(countUp, 1000);
}

function flushText(l,p) {
let d=new Date();
let nnn=1;
const elem = document.getElementById("proc");

if(p!=""){
let ps=p.split(";");
  elem.innerHTML="探索済みルート数 "+ps[0]+"<br>";
  elem.innerHTML+="<br>"+ps[1]+"<br>経過時間"+ps[2];
//  elem.innerHTML+= " (残り時間 "+ps[3]+")";
  elem.innerHTML+= " ("+ps[3]+")";
}else{
let ls=l.split("\n");
h3.innerHTML="";
for(i=0;i<ls.length;i++){
	h3.innerHTML+="("+String(i+1)+")"+ls[i]+"<br>";
	nnn=nnn+1;
	}
}}
    countUp();
</script>
