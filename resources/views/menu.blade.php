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
</form>

<form class="form" action="{{ route('multiSearch') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[1]}}</button>
</form>

<form class="form" action="{{ route('db') }}" method=POST>
	<button type="'submit" class="menuButton">{{$mes[2]}}</button>
</form>

<form class="form" action="{{ route('dbManage') }}" method=GET>
	<button type="'submit" class="menuButton">{{$mes[3]}}</button>
</form>
<div class="subMessage">
User :
{{$user['name']}} on {{$user['email']}}
</div>
<div class="inline">
<div class="block">
<a>
<form action="{{ route('myLogout') }}" method=GET>
	<button class="sysButton" type="'submit">logout</button>
</form>
</a>
<a>
<form action="{{ route('profile.edit') }}" method=GET>
	<button class="sysButton" type="'submit">profile_edit</button>
</form>
</a>
</div>
</div>
</div>
</body>
</html>
