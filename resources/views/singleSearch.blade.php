<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ReTReK - ユーザー検索画面</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/style.css')}}">
<style>
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

.container{
  padding: 20px 20px;
}

#bta,#btb {
  width: 12em;
}
    </style>
</head>

<body>
<div class="wrapper" style="padding:10px">
    <h1>経路探索(1)</h1>
        <div class="container d-flex justify-content-between align-items-center">
            <form action="{{ route('kRet') }}" method="GET" class="mb-3">
                <button id="btb" onclick="window.location.href='/search';" class="btn btn-primary back-button">メインメニューに戻る</button>
	    <input style="display:none" type="text" name="db_type" id="let_db_type" value="{{$db_type}}">
            </form>
      </div>
	<div class="container">

        <form action="{{ route('exepy') }}" method="POST" class="mb-3">
            @csrf
	    <input style="display:none" type="text" name="db_type" id="db_type" value="{{$db_type}}">
	    <input style="display:none" type="text" name="uid" id="uid" value="{{$uid}}">
            <div class="form-group">
<div id="smiBox">
                <label class="subMessage" for="smiles">SMILES化学式:</label>
                <input type="text" name="smiles" class="form-control" id="smiles" required>
</div>
<div id="subBox">
                <label class="subMessage" for="substance">物質名(空欄なら日付を利用):</label>
                <input type="text" name="substance" class="form-control" id="substance">
</div>
<div>
                    <label for="route" class="subMessage">ルート数:</label>
                        <input type="number" id="route" name="route_num" style="width:100px" class="form-control" required value="100" min="1">
</div>
</div>

</br>
</div>
            <fieldset>
<details>
                <summary class="mb-3">詳細設定</summary>

		@php
		$name=array("Convergent Disconnection Score","Available Substance Score","Ring Disconnection Score","Seletive Transformation Score","Intermediate Score","Template Score");
		$weight=array("5.0","0.5","2.0","2.0","2.0","1.0");
		@endphp
                <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label">knowledgeWeights:</label>
                    <div class="col-sm-8">
                        <div class="row">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="col">
				"{{$name[$i]}}"
                                </div>
                            @endfor
			    </div>
                        <div class="row">

                            @for ($i = 0; $i < 6; $i++)
                                <div class="col">
                                    <input type="number" class="form-control mb-2" name="weights[]" id=weights[{{ $i }}] step="0.1" value="{{$weight[$i]}}" placeholder="{{ $i + 1 }}"  required>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-2">
                    <label for="expansion_num" class="col-sm-4 col-form-label">expansion_num:</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control"  id="expansion_num" name="expansion_num" value="50" required>
                    </div>
                </div>

                <div class="form-group row mb-2">
                    <label for="selection_constant" class="col-sm-4 col-form-label">selection_constant:</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control"  name="selection_constant" id="selection_constant" value="10" required>
                    </div>
                </div>
                <div class="form-group row mb-2" style="display:none">
                    <label for="time_limit" class="col-sm-4 col-form-label">time_limit:</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" name="time_limit" id="time_limit" value="0" required>
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label" for="save_tree">save_tree:</label>
                    <div class="col-sm-8">
                        <div class="form-check">
                            <input type="checkbox" name="save_tree" class="form-check-input" id="save_tree" value="True">
                            <label class="form-check-label" for="save_tree"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label" for="cum_prob_mod">cum_prob_mod:</label>
                    <div class="col-sm-8">
                        <div class="form-check">
                            <input type="checkbox" name="cum_prob_mod" class="form-check-input" id="cum_prob_mod" value="True">
                            <label class="form-check-label" for="cum_prob_mod"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-2">
                    <label class="col-sm-4 col-form-label" for="chem_axon">chem_axon:</label>
                    <div class="col-sm-8">
                        <div class="form-check">
                            <input type="checkbox" name="chem_axon" class="form-check-input" id="chem_axon" value="True">
                            <label class="form-check-label" for="chem_axon"></label>
                        </div>
                    </div>
                </div>
</details>
            </fieldset>
	    <button type="submit" class="btn btn-primary back-button" id="bta">探索</button>
        </form>

<img src="{{('images')}}/template/arrow123.png" style="width:80px;margin-right:20px;margin-left:20px">
<button class="sysButton" id="theHint" style="width:24ex" onclick="toggleDb()"> </button>
<!--
<img src="{{('images')}}/template/arrow123.png" style="width:150px;height:15px">
<span> <---------- </span>
<div style="display:inlinde">
<span id="theHint"><span>
</div>
-->
</br>

</body>
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
</script>
</html>
