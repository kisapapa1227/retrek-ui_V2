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
    height: 600px;
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

</style>
    </head>
    <body>
    <div class="newWrapper">
        <div class="container">
            <h1>Welcome to retrek-ui</h1>
@php
$mes=array("経路探索(1):SMILESを指定して反応経路を探索します。","経路探索(2):条件をファイルから読み込み、複数物質の反応経路を一括して探索します。",
"ファイル変換:保存済みの探索経路をpdf/pptxに変換します。",
"データベース管理:レコードの削除、データベースの保存をします。"
);
@endphp

<form class="form" action="{{ route('singleSearch') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[0]}}</button>
	<input style="display:none" type="text" name="db_type" class="db_type">
	<input style="display:none" type="text" name="uid" class="uid">
</form>

<form class="form" action="{{ route('multiSearch') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[1]}}</button>
	<input style="display:none" type="text" name="db_type" value="com" class="db_type">
	<input style="display:none" type="text" name="uid" class="uid">
</form>

<form class="form" action="{{ route('db') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[2]}}</button>
	<input style="display:none" type="text" name="db_type" value="com" class="db_type">
	<input style="display:none" type="text" name="uid" class="uid">
</form>

<form class="form" action="{{ route('dbManage') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[3]}}</button>
	<input style="display:none" type="text" name="db_type" value="com" class="db_type">
	<input style="display:none" type="text" name="uid" class="uid">
</form>
<div class="subMessage">
User :
{{$user['name']}} on {{$user['email']}}
</div>
<div class="inline">
<div class="block">
<a>
<form action="{{ route('myLogout') }}" method=GET>
	<button class="sysButton" type="submit">logout</button>
</form>
</a>
<a>
<form action="{{ route('profile.edit') }}" method=GET>
	<button class="sysButton" type="submit">profile_edit</button>
</form>
</a>
</div>
</div>

<div class="inline">
<!--
<pre>
データベース      <button class="db_type" type="button" id="db_type" value="com" onClick="toggleDb(this);">共用</buttonm>
</pre>
<button class="db_type" style="height:15px" type="button" id="db_type" value="com" onClick="toggleDb(this);">共用データベースを使う</buttonm>
-->
<button class="sysButton" style="margin-top:30px;font-size:16px;width:28ex" name="db_toggle" type="button" id="db_type" value="com" onClick="toggleDb(this);">共有データベースを使う</buttonm>
</div>
</body>
</html>

<script>
const db=document.getElementById('db_type');
const uid="{{$user['email']}}".replace(/[^a-zA-Z0-9]/g,"");
const tags=document.getElementsByClassName("db_type");
const utags=document.getElementsByClassName("uid");

	db.value="{{$db_type}}";
//alert(db.value+":"+"{{$db_type}}"+":"+uid);

if (db.value != "com"){
	db.textContent="個人用データベースを使う";
}
for (i=0;i<utags.length;i++){
	utags[i].value=uid;
}
for (i=0;i<tags.length;i++){
	tags[i].value=db.value;
}

function toggleDb(dbb){
const tags=document.getElementsByClassName("db_type");

	if (db.value=="com"){
		db.value="pri";
		db.textContent="個人用データベースを使う";
	}else{
		db.value="com";
		db.textContent="共有データベースを使う";
	}
for (i=0;i<tags.length;i++){
	tags[i].value=db.value;
}
}
</script>
