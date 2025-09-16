<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ReTReK - 複数検索画面</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        .container.d-flex {
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
        }
        .list-group-item {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .list-group-item h5 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #143a40;
        }

#fromCSV{
  padding: 12px 6px;
}

#btb {
  width: 12em;
}
#bta {
  width: 18em;
}

#template{
  padding: 12px 6px;
  display:flex;
  justify-content: flex-end;
}

#getfile {
  width: 20em;
}
    </style>
</head>

<body>
    <div class="wrapper" style="padding:10px">
            <h1>経路探索(2)</h1>
        <div class="container d-flex justify-content-between align-items-center">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id="btb" onclick="window.location.href='/search';" class="btb">メインメニューに戻る</button>
            <input style="display:none" type="text" name="db_type" id="let_db_type" value="{{$db_type}}">
            </form>
      </div>

        <form action="{{ route('exepy') }}" method="POST" class="mb-3">
            @csrf
            <input style="display:none" type="text" name="uid" id="uid" value="{{$uid}}">
            <input style="display:none" type="text" name="db_type" id="db_type" value="{{$db_type}}">
<div class="subMessage" id="csvBox">
探索する条件の記述されたCSV
<input type="file" id="getfile">
</br>
<input type="text" name="fromCSV" class="form-control" id="fromCSV" value="id#,smiles,ops,,," style="display:none">
</div>
</br>

<div id='template'>
<button id='bta' type="button" onclick="templateDownload()">テンプレートをダウンロードする</button>
</div>
</br>
            </fieldset>
	    <button type="submit" class="btn btn-primary" id="btb">探索開始</button>
        </form>
<img src="{{('images')}}/template/arrow123.png" style="width:80px;margin-right:20px;margin-left:20px"/>
<button class="sysButton" id="theHint" style="width:24ex" onclick="toggleDb()"> </button>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>

document.getElementById("uid").value="{{$uid}}";
let dbt=document.getElementById("db_type");
let ldbt=document.getElementById("let_db_type");
dbt.value="{{$db_type}}";
const mes=["データベースの切り替え\nCurrent : 個人用","データベースの切り替え\nCurrent : 共有"];

setDb();

function toggleDb(){
        if (dbt.value=="com"){
                ldbt.value=dbt.value="pri";
        }else{
                ldbt.value=dbt.value="com";
        }
        setDb();
}
function setDb(){
        if (dbt.value=="com"){
                document.getElementById("theHint").innerText=mes[1];
        }else{
                document.getElementById("theHint").innerText=mes[0];
        }
}
var fetch=function(url,inerval){
	$.ajax({
		type: 'GET',
		url: url,
		processData: false,
		contentType: false
}).then(function(res){
	consol.log(res);
	if (res.statu==='Done'){clarInterval(interval);}
});
};

var progress = function(url){
	var interval = setInterval(fetch,1000,url,interval);
};

        $(document).ready(function() {
            $('form.remove-route').on('submit', function(e) {
                e.preventDefault(); // 通常のフォーム送信を停止

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    success: function(data) {
                    if (data.isFavorite) {
                        alert('Error removing route');
                    } else { 
                        form.closest('li.list-group-item').remove();
                        alert(data.message);
                    }
                    },
                    error: function(xhr, status, error) {
                        alert('エラーが発生しました: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
<script>
let path="{{asset('images')}}";
function templateDownload(){
	const a = document.createElement('a');
	alert("テンプレートをダウンロードします。");
        a.href=path+"/template/template.csv";
        a.download="template.csv";
        a.click();
}

const fileInput=document.getElementById("getfile");
	fileInput.addEventListener("change",function(event){
		const file = event.target.files[0];
		readFile(file);
	});
	function readFile(file){
		const reader = new FileReader();
		reader.onload = function (event) {
		step1=event.target.result.split('\n');
		step2=step1[0];
		for (var i =1;i<step1.length-1;i++){
			step2=step2+";"+step1[i];
		}
			document.getElementById("fromCSV").value=step2;
		};
		reader.readAsText(file);
//		reader.readAs(file,'shift-jis');
	};
</script>
</body>
</html>
