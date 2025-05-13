<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ReTReK - ユーザー検索画面</title>

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

#bta {
  width: 12em;
}

#template{
  padding: 12px 6px;
  display:flex;
  justify-content: flex-end;
}

#btb,#getfile {
  width: 18em;
}
    </style>
</head>

<body>
    <div class="wrapper">
	<div class="container">
            <h1>経路探索(2)</h1>
        <form action="{{ route('exepy') }}" method="POST" class="mb-3">
            @csrf
            <div class="form-group">
<br>
<div style="display:none">
                <input type="text" name="smiles" id="smiles" value="">
                <input type="text" name="substance" id="substance" value="">
                <input type="number" id="route" name="route_num" class="form-control" required value="100" min="1">
		@php
		$weight=array("5.0","0.5","2.0","2.0","2.0","1.0")
		@endphp
                            @for ($i = 0; $i < 6; $i++)
                                    <input type="number" class="form-control mb-2" name="weights[]" id=weights[{{ $i }}] step="0.1" value="{{$weight[$i]}}" placeholder="{{ $i + 1 }}"  required>
                            @endfor

                  <input type="number" class="form-control"  id="expansion_num" name="expansion_num" value="50" required>
                  <input type="number" class="form-control"  name="selection_constant" id="selection_constant" value="10" required>
                  <input type="number" class="form-control" name="time_limit" id="time_limit" value="0" required>
                  <input type="checkbox" name="save_tree" class="form-check-input" id="save_tree" value="True">
                  <input type="checkbox" name="cum_prob_mod" class="form-check-input" id="cum_prob_mod" value="True">
                  <input type="checkbox" name="chem_axon" class="form-check-input" id="chem_axon" value="True">
		<input type="text" name="cui" value="3">
</div>
<div class="subMessage" id="csvBox">
探索する条件の記述されたCSV
<input type="file" id="getfile">
</br>
<input type="text" name="fromCSV" class="form-control" id="fromCSV" value="id#,smiles,ops,,," style="display:none">
</div>
</br>

<div id='template'>
<button id='btb' type="button" onclick="templateDownload()">テンプレートをダウンロードする</button>
</div>
</br>
            </fieldset>

	    <button type="submit" class="btn btn-primary" id="bta">反応経路の探索</button>
        </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
function templateDownload(){
	const a = document.createElement('a');

        a.href="http://localhost/images/report/template.csv";
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
