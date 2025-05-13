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
form input {
    outline: none;
    border: 1px solid rgba(255, 255, 255, 0.4);
    background-color: rgba(255, 255, 255, 0.2);
    width: 500px;
    border-radius: 3px;
    padding: 10px 15px;
    margin: 0 auto 10px auto;
    display: block;
    text-align: center;
    font-size: 18px;
    transition-duration: 0.25s;
    font-weight: 300;
    color: #fff;
}
form input:hover {
    background-color: rgba(255, 255, 255, 0.4);
}
form input:focus {
    background-color: #fff;
    width: 300px;
    color: #53e3a6;
}
form input::placeholder {
    color: #fff;
}
form button {
    outline: none;
    border: none;
    background-color: #eee;
    width: 600px;
    border-radius: 3px;
    padding: 10px 15px;
    margin: 0 auto 10px auto;
    display: block;
    text-align: left;
    font-size: 18px;
    transition-duration: 0.25s;
    font-weight: 300;
    color: #000000;
    cursor: pointer;
}
form button:hover {
    background-color: #ffeeee;
}
.bg-bubbles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}
.bg-bubbles li {
    position: absolute;
    list-style: none;
    display: block;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.15);
    bottom: -160px;
    animation: square 25s infinite;
    transition-timing-function: linear;
}
.bg-bubbles li:nth-child(1) {
    left: 10%;
}
.bg-bubbles li:nth-child(2) {
    left: 20%;
    width: 80px;
    height: 80px;
    animation-delay: 2s;
    animation-duration: 17s;
}
.bg-bubbles li:nth-child(3) {
    left: 25%;
    animation-delay: 4s;
}
.bg-bubbles li:nth-child(4) {
    left: 40%;
    width: 60px;
    height: 60px;
    animation-duration: 22s;
    background-color: rgba(255, 255, 255, 0.25);
}
.bg-bubbles li:nth-child(5) {
    left: 70%;
}
.bg-bubbles li:nth-child(6) {
    left: 80%;
    width: 120px;
    height: 120px;
    animation-delay: 3s;
    background-color: rgba(255, 255, 255, 0.2);
}
.bg-bubbles li:nth-child(7) {
    left: 32%;
    width: 160px;
    height: 160px;
    animation-delay: 7s;
}
.bg-bubbles li:nth-child(8) {
    left: 55%;
    width: 20px;
    height: 20px;
    animation-delay: 15s;
    animation-duration: 40s;
}
.bg-bubbles li:nth-child(9) {
    left: 25%;
    width: 10px;
    height: 10px;
    animation-delay: 2s;
    animation-duration: 40s;
    background-color: rgba(255, 255, 255, 0.3);
}
.bg-bubbles li:nth-child(10) {
    left: 90%;
    width: 160px;
    height: 160px;
    animation-delay: 11s;
}
@keyframes square {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(-700px) rotate(600deg);
    }
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
	<button type="'submit" id="login-button">{{$mes[0]}}</button>
</form>

<form class="form" action="{{ route('multiSearch') }}" method=GET>
	<button type="'submit" id="login-button">{{$mes[1]}}</button>
</form>

<form class="form" action="{{ route('db') }}" method=GET>
	<button type="'submit" id="login-button">{{$mes[2]}}</button>
</form>

<form class="form" action="{{ route('db') }}" method=GET>
	<button type="'submit" id="login-button">{{$mes[3]}}</button>
</form>

Login user :
{{$user['name']}} on {{$user['email']}}
<div class="inline">
<div class="block">
<a>
<form action="{{ route('myLogout') }}" method=GET>
	<button class="sysButton" type="'submit" id="login-button">logout</button>
</form>
</a>
<a>
<form action="{{ route('profile.edit') }}" method=GET>
	<button class="sysButton" type="'submit" id="login-button">profile_edit</button>
</form>
</a>
</div>
</div>
</div>
</body>
</html>
