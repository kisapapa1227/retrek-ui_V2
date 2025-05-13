<!DOCTYPE html>
<html lang="ja">
<head>
    <title>dummy entry</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
  <body>
<form action="{{ route('dummyEntry') }}" method="GET">
@csrf
<div style="display:none">
        <button type="submit" id="dummyEntry">
</div>
</form>
  </body>
</html>

<script>

function download(url,filename){
const a = document.getElementById('dummyEntry');

        a.href=url;
        a.download=filename;
        a.click();
}
download("ok","ok");
</script>
