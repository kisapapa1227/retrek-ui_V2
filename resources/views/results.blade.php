<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReTReK Results</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
     <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            padding-top: 20px;
            background-color: #f5f5f5;
        }
        .fixed-top {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #fff;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 0;
        }
        .route {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .route-header{
            cursor: pointer;
        }
        .structure-img {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 5px;
        }
        .structure-img img {
            width: 100%;
            max-width: 100px;
            height: auto;
            justify-content: center;
        }
        .route-body {
            padding-top: 10px;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; 

        }
        .molecule {
            display: inline-block;
            text-align: center;
            margin: 0 4px;
            width: 100px;
        }
        .molecule img {
            width: 100%;
            height: auto;
        }
        .molecule p {
            margin-top: 5px; 
        }

        .plus, .arrow {
            display: inline-block;
            text-align: center;
            align-items: center;
            width: 30px; 
            margin: 0 2px;
        }
        .plus img, .arrow img {
            width: 100%;
            height: auto;
            vertical-align: middle;
            margin-top: -35px;
        }

        .route-footer {
            display: flex;
            justify-content: flex-end; 
            margin-top: 20px; 
        }
        .back-button {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="mb-4" style="color: #17a2b8;">{{ $molecule }}の合成経路</h2>
            <form action="{{ route('user') }}" method="GET" class="mb-3">    
                <button onclick="window.location.href='/search';" class="btn btn-primary back-button">ユーザー検索画面へ戻る</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h1 class="mb-4">ReTReK Results</h1>
        
            
            {!! nl2br($routes) !!}   
        
    </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleRoute(element) {
            $(element).closest('.route').find('.route-body').slideToggle();
        }

        $(document).ready(function() {
            $('form.favorite-form').on('submit', function(e) {
                e.preventDefault(); // 通常のフォーム送信を停止

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), 
                    success: function(data) {
                        alert(data.message);
                        if(data.isFavorite) {
                            form.find('.favorite-button').text('削除');
                            form.attr('action', "{{ route('remove') }}");
                        } else {
                            form.find('.favorite-button').text('追加');
                            form.attr('action', "{{ route('add') }}");
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('エラーが発生しました: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
       


</body>
</html>

    