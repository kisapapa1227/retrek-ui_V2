<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Models\FavoriteRoute;

function getPrms($l){

        $s1=explode(",",$l);
        $s2=explode("[",$l);
        $ss=$s2[count($s2)-1];
        $s3=explode("]",$ss);
        $s4=explode(",",$s3[1]);

        $p1=$s1[0];
        $p2=$s1[2];
        $p3="[".$s3[0]."]";
        $p4=$s4[4] == '' ? '' : 'True';#savetree
        $p5=$s4[1];#expantion_number
        $p6=$s4[5] == '' ? '' : 'True';#cum_prob_mode
        $p7=$s4[6] == '' ? '' : 'True';#chem_axon
        $p8=$s4[2];#selection_constant
        $p9=$s4[3];#time_limit
        $p11='True';# csv any but 'false'
        $p12=$s1[1];#substance

        return [$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p11,$p12];
} 

function makeScriptForDrop($id){
    $wk="/var/www/html/ReTReKpy/";
    $sh=$wk."drop.sh";
    $fp=fopen($sh,"w");
    fwrite($fp,"#!/bin/sh\n#\n#\n");
    fwrite($fp,"python3 /var/www/html/ReTReKpy/make_reports/readDb.py -id ".$id." -drop -d /var/www/html/public/images");
    fclose($fp);
                return $sh;
}

function makeScript($sh,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12, $uid){
    $user= Auth::user();
    $name = $user['name'];
    $email = $user['email'];
    $wk="/var/www/html/ReTReKpy/";
    $ssh=$uid."_".$sh;
    $fp=fopen($wk.$ssh,"w");
    fwrite($fp,"#!/bin/sh\n#\n#\n");
    fwrite($fp,"#name=".$user['name']."\n");
    fwrite($fp,"#email=".$user['email']."\n");
    fwrite($fp,"r=$(/usr/bin/ps -elf | /usr/bin/grep exe.py | /usr/bin/grep ".$uid." | /usr/bin/grep -v grep)\n");
    fwrite($fp,"if [ ! -z \"\$r\" ]; then\necho \"Process is running, then exit\"\n");
    fwrite($fp,"echo \"this is B...\" >> /var/www/html/public/images/echo.txt\n");
    fwrite($fp,"exit 1\nfi\n");
    fwrite($fp,"echo \"this is C...\" >> /var/www/html/public/images/echo.txt\n");
    fwrite($fp,"/usr/bin/python3 ".$wk."exe.py");
                fwrite($fp," '".$p1."' '".$p2."' '".$p3."' '".$p4."'");
                fwrite($fp," '".$p5."' '".$p6."' '".$p7."' '".$p8."'");
                fwrite($fp," '".$p9."' '".$p10."' '".$p11."' '".$p12."' '".$uid."'");
                fwrite($fp," 1> /dev/null 2>/dev/null &\n");
                fclose($fp);
                return $wk.$ssh;
}

class RetrekController extends Controller
{
    public function dummyEntry(){
    	$user= Auth::user();
    return view('menu',['user'=>$user]);
}
    public function singleSearch(){
        $userId = auth()->id();
        return view('singleSearch');
    }
    public function multiSearch(){
        $userId = auth()->id();
        return view('multiSearch');
    }

    public function dbManage(){
        $userId = auth()->id();
	$prm=$this->forDb();$prm['modal']='no';$prm['uid']=0;$prm['filename']='non';
        return view('dbManage',$prm);
    }
    public function myLogout(){
	Auth::logout();
	return view('entry');
    }
    public function user(){
        $userId = auth()->id();
        return view('user');
    }

    public function kRet(Request $request){

	$prc = new Process(['ps','aux']);
	$prc->run();
	$outp=explode("\n",$prc->getOutput());

	foreach ($outp as &$o){
	if (strpos($o,"sail")!==false 
		&& strpos($o,"python")!==false
		&& strpos($o,"exe.py")!==false){
		$prms=explode(" ",$o);
		$l=0;
		foreach ($prms as &$i){
			if($i!==""){
				if ($l==1){
				$prc = new Process(['kill',$i]);
				$prc->run();
				}
#    	fwrite($fh,$l."(".$i.")");
				$l++;
			}
		}
		}
	}
#    	fclose($fh);
    	$user= Auth::user();
    return view('menu',['user'=>$user]);
}

    public function dbAction(Request $request){
        $op = $request->input('oper');
        $id = $request->input('id');
	$output="/var/www/html/public/images/report/";
	$smilesDir="/var/www/html/public/images/smiles/";

	switch($op){
	case 'thumbnail':
	    $process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-id",$id,"-d",$smilesDir,"-thumbnail"]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();

	$param=[
		'pdf' => 'none',
	];
		break;
	case 'drop':
	    $process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-id",$id,"-drop","-d","/var/www/html/public/images"]);
        $process->setWorkingDirectory('/var/www/html/public/images'); // 作業ディレクトリの設定
       	$process->run();

	$param=[
		'pdf' => 'reload',
	];
		break;
	case 'db':
	    $process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-id",$id,"-d",$output,"-db"]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$param=[
		'pdf' => "images/report/".$id."db.txt",
	];
		break;
	case 'askPptx':
	case 'askPdf':
		break;
	}
	return response()->json($param);
    }

   public function dropDb(Request $request){
        $op = $request->input('oper');
        $id = $request->input('id');

        $sh=makeScriptForDrop($id);
        $this->easyProcess($sh,"readDb.py","readDb.py");
        $prm=$this->forDb();$prm['modal']='no';$prm['uid']=0;$prm['filename']='non';
       	return view('dbManage', $prm);
    }

    public function syncPdf(Request $request){
	$uid  =$request->uid;
	$given=$request->given;
    	$sh=$this->makeNow($request->options);
	$prm=$this->forDb();
	sleep(2);
	$this->easyProcess($sh,"readDb.py","readDb.py"); 

	$prm['modal']='yes';$prm['uid']=$uid;$prm['filename']=$given;
       	return view('dbManage', $prm);
    }

    function myAddDb($uid,$loop){

	$sh="async".$loop.".sh";
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/addDb.py", "-u",$uid,"-s",$sh,"-d","/var/www/html/public/images/"]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("\n",$stdout);
	foreach ($lines as $line){
		if (strpos($line,"###")){
			$sub=explode(":",explode("###",$line)[0])[1];
		}
	}
    }

    public function addDb(Request $request){

       $uid=$request->uid;

	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/addDb.py", "-u",$uid,"-d","/var/www/html/public/images/"]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("\n",$stdout);
	foreach ($lines as $line){
		if (strpos($line,"###")){
			$sub=explode(":",explode("###",$line)[0])[1];
		}
	}
	$param=[
		'uname' => $uid,
		'substance' => $sub,
	];
	return response()->json($param);
	   }

    public function askProc(Request $request)
{
$substance=$request->id;
$user= Auth::user();
$uid = preg_replace("/[^a-zA-Z0-9]+/u","",$user['email']);

$fn="/var/www/html/public/images/".$uid."/".$substance.".txt";
if (file_exists($fn) == false){
        $count=$fn." not_yet";
}else{
        $fh=fopen($fn,"r");
        $count="0";
	$done="0";
while(($buf=fgets($fh)) != false){
        if (false !== strpos($buf,'Route:')){
                $num=explode(":",$buf);
                $count=$num[1];
        }
        if (false !== strpos($buf,'reported')){
                $done="1";
        }
}
fclose($fh);
	$param=[
		'currentRoute' => $count,
		'pdf' => $done,
		'uid' => $uid,
	];
    }
	return response()->json($param);
}

    public function tmp()
    {
        return view('resulttmp');
    }

    private static function easyProcess($sh,$py1,$py2){

	$process = new Process(["sh", $sh]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        $process->setTimeout(0);
       	$process->start();
	$ww=0;
	while($ww<20){
	$prc = new Process(['ps','aux']);
	$prc->run();
	$outp=explode("\n",$prc->getOutput());

	foreach ($outp as &$o){
	if (strpos($o,"sail")!==false 
		&& strpos($o,"python")!==false
		&& strpos($o,$py1)!==false
		&& strpos($o,$py2)!==false)
	{
			$ww=21;break;
	}
	}
		sleep(2);
		$ww++;
	}
}

private static function askMakeScript($n,$l){

    	$user= Auth::user();
    	$uid = preg_replace("/[^a-zA-Z0-9]+/u","",$user['email']);
        $userId = auth()->id();

	$sh="async".$n.".sh";

	list($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p11, $p12)=getPrms($l);
        $p10= csrf_token();

	makeScript($sh,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$uid);
    }

private static function makeNow($options){

    $wk="/var/www/html/ReTReKpy/";
    $output="/var/www/html/public/images/report/";
    $sh=$wk."now.sh";
    $fp=fopen($sh,"w");
    fwrite($fp,"#!/bin/sh\n#\n#\n");
    fwrite($fp,"python3 /var/www/html/ReTReKpy/make_reports/readDb.py ".$options." -d ".$output);
    fwrite($fp," 1> /dev/null 2>/dev/null &\n");
    fclose($fp);

    return $sh;
}

private static function forDb(){
$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-db_list"]);
$process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        $process->setTimeout(0);
       	$process->run();
        $routes = $process->getOutput();
	$records=explode("###",$routes);
	$cnt=0;$fieldName=[];$prm=array();$body=array();
	foreach ($records as $record){
		$fields=explode("##",$record);
		if ($cnt==0){
			foreach ($fields as $field){
				$fieldName[]=$field;
			}
		$prm['name']=$fieldName;
		}else{
		$i=0;
		foreach ($fields as $field){
				$body[$fieldName[$i]][]=$field;
				$i+=1;
		}}
		$cnt++;
	}
		$prm['body']=$body;

	return $prm;
}
    public function multiProc(Request $request){

$fp=fopen("/var/www/html/public/images/report/step1.txt","a");
fwrite($fp,"start...\n");
        $csv = $request->input('fromCSV');
	$lines=explode(";",$csv);
fwrite($fp,"fromCSV".$csv."...\n");

if (!array_key_exists("max_loop",$request->input())){
        $user= Auth::user();
        $userId = auth()->id();
        $uid = preg_replace("/[^a-zA-Z0-9]+/u","",$user['email']);

$num=0;
	foreach($lines as $l){
		if (substr($l,0,1)!="#"){
#			fwrite($fp,$l."\n");
			$num=$num+1;
			$this->askMakeScript($num,$l);
		}}
$max_loop=$num;
$loop=1;
}else{
        $max_loop = $request->input('max_loop');
        $loop = $request->input('loop');
        $uid = $request->input('uid');
        $userId = $request->input('user_Id');
fwrite($fp,"else ".$loop."/".$max_loop."\n");

$this->myAddDb($uid,$loop);
	$loop=$loop+1;
}

fwrite($fp,$max_loop." vs ".$loop."\n");
if ($loop>$max_loop){
fwrite($fp,"return 1\n");
fclose($fp);
    $user= Auth::user();
    return view('menu',['user'=>$user]);
}

$wk="/var/www/html/ReTReKpy/";
$ssh=$wk.$uid."_async".$loop.".sh";

$num=0;
foreach($lines as $l){
    if (substr($l,0,1)!="#"){
            $num=$num+1;
fwrite($fp,$num."-".$l);
            if ($num==$loop){
fwrite($fp,$num."xx\n");
	$this->easyProcess($ssh,"exe.py",$uid);
	
        list($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p11, $p12)=getPrms($l);
fwrite($fp,$ssh."\n");
fwrite($fp,$userId."\n");
fwrite($fp,$p1.$p2.$p12.$uid.$max_loop.$loop."\n");
fwrite($fp,"return 2\n");
fclose($fp);
        return view('multiProc', ['smiles' => $p1, 'route_num' => $p2,'substance' => $p12, 'uid' => $uid,'max_loop' => $max_loop, 'loop' => $loop, 'fromCSV'=>$csv, 'userId'=> $userId]);
}}}
fwrite($fp,"return 3\n");
fclose($fp);
    }

    public function exepy(Request $request){

        $smiles = $request->input('smiles');
        $substance = $request->input('substance');

        $route_num = (int) $request->input('route_num');

        $weights = $request->input('weights');
        $knowledge_weights = json_encode(array_map('floatval', $weights));
        
        $save_tree = $request->input('save_tree');
        $expansion_num = (float) $request->input('expansion_num');
        $cum_prob_mod = $request->input('cum_prob_mod');
        $chem_axon = $request->input('chem_axon');
        $ccc = (int) $request->input('cui');
        $csv = $request->input('fromCSV');
        $selection_constant = (float) $request->input('selection_constant');
        $time_limit = (float) $request->input('time_limit');

        $csrf_token = csrf_token();

    if ($ccc==1){
	    $csv='False';
    }elseif($ccc==2){
	    $csv='True';
    }elseif($ccc==3){# fromCSV
	return $this->multiProc($request);
    }elseif($ccc==5){
	    # this is for : void call
        $userId = auth()->id();
        $favoriteRoutes = FavoriteRoute::where('user_id', $userId)->get();
        return view('user', ['favoriteRoutes' => $favoriteRoutes]);
    }elseif($ccc==4){
	    $prm=$this->forDb();$prm['modal']='no';$prm['uid']=0;$prm['filename']='non';
       	return view('dbManage', $prm);
    }
	if ($ccc>1){
    	$user= Auth::user();
    	$uid = preg_replace("/[^a-zA-Z0-9]+/u","",$user['email']);
        $sh = makeScript("async.sh",$smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $selection_constant, $time_limit, $csrf_token,$csv,$substance,$uid);

	$this->easyProcess($sh,"exe.py",$uid);

        	return view('proc', ['smiles' => $smiles, 'route_num' => $route_num,'substance' => $substance, 'uid' => $uid]);
	}

        $process = new Process(["python3", "/var/www/html/ReTReKpy/exe.py", $smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $selection_constant, $time_limit, $csrf_token,$csv, $substance]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        $process->setTimeout(0);
       	$process->run();

        $results_num = [];
        $save_tree = filter_var($save_tree, FILTER_VALIDATE_BOOLEAN);
        $cum_prob_mod = filter_var($cum_prob_mod, FILTER_VALIDATE_BOOLEAN);
        $chem_axon = filter_var($chem_axon, FILTER_VALIDATE_BOOLEAN);

        for ($route_id = 1; $route_id <= $route_num; $route_id++) {
            $count = FavoriteRoute::where('route_id', $route_id)
                ->where('smiles', $smiles)
                ->where('knowledge_weights', $knowledge_weights)
                ->where('save_tree', $save_tree)
                ->where('expansion_num', $expansion_num)
                ->where('cum_prob_mod', $cum_prob_mod)
                ->where('chem_axon', $chem_axon)
                ->where('selection_constant', $selection_constant)
                ->where('time_limit', $time_limit)
                ->count();

            $results_num[$route_id] = $count;
        }

        arsort($results_num);

        // 実行に失敗した場合(失敗の原因の場所の究明)
        if(!$process->isSuccessful())
        {
            throw new ProcessFailedException($process);
        }

        $routes = $process->getOutput();


        $replacements1 = [];
        preg_match_all('/\\d+: (\\/var\\/www\\/html\\/public\\/images\\/[^<]+\\.png)/', $routes, $matches);
        foreach ($matches[0] as $i => $text) {
            $path = str_replace('/var/www/html/public', '', $matches[1][$i]);
            $replacements1[$text] = '<img src="'. asset($path) .'" alt="Molecule">';
        }
        foreach ($replacements1 as $search => $replace) {
            $routes = str_replace($search, $replace, $routes);
        }


        $plus_url = '/plus\.png/';
        $replacement = '<img src="'. asset('images/plus.png') .'" alt="plus">';
        $routes = preg_replace($plus_url, $replacement, $routes);

        $arrow_url = '/arrow\.png/';
        $replacement = '<img src="'. asset('images/arrow.png') .'" alt="arrow">';
        $routes = preg_replace($arrow_url, $replacement, $routes);
        
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($routes, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($doc);
        
        $routeElements = $xpath->query("//div[@class = 'route']");
        foreach ($routeElements as $routeElement) {
            
            $userId = auth()->id();
            $smiles = $routeElement->getAttribute('data-smiles');
            $routeId = (int) $routeElement->getAttribute('data-route-id');
            $routeNum =  (int) $routeElement->getAttribute('data-route-num');
            $knowledgeWeights = json_encode(json_decode($routeElement->getAttribute('data-knowledge-weights')));
            $saveTree = filter_var($routeElement->getAttribute('data-save-tree'), FILTER_VALIDATE_BOOLEAN);
            $expansionNum = (float) $routeElement->getAttribute('data-expansion-num');
            $cumProbMod = filter_var($routeElement->getAttribute('data-cum-prob-mod'), FILTER_VALIDATE_BOOLEAN);
            $chemAxon = filter_var($routeElement->getAttribute('data-chem-axon'), FILTER_VALIDATE_BOOLEAN);
            $selectionConstant = (float) $routeElement->getAttribute('data-selection-constant');
            $timeLimit = (float) $routeElement->getAttribute('data-time-limit');

            $isFavorite = FavoriteRoute::where([
                'user_id' => $userId,
                'smiles' => $smiles,
                'route_id' => $routeId,
                'route_num' => $routeNum,
                'knowledge_weights' => $knowledgeWeights,
                'save_tree' => $saveTree,
                'expansion_num' => $expansionNum,
                'cum_prob_mod' => $cumProbMod,
                'chem_axon' => $chemAxon,
                'selection_constant' => $selectionConstant,
                'time_limit' => $timeLimit
            ])->exists();
    
            if ($isFavorite) {
                $buttonText = '削除';
                $actionRoute =  route('remove');
            }else {
                $buttonText ='追加';
                $actionRoute = route('add');
            }

            $form = $xpath->query(".//form[@class='favorite-form']", $routeElement)->item(0);
            if ($form) {
                $formHtml = $form->ownerDocument->saveHTML($form);
                $form->setAttribute('action', $actionRoute);
                $button = $xpath->query(".//button[contains(@class, 'favorite-button')]", $form)->item(0);
                if ($button) {
                    $button->nodeValue = $buttonText;
                }
            }
        }

        $updatedRoutes = '';
        foreach ($results_num as $route_id => $count) {
            $routeElement = $xpath->query("//div[@class='route' and @data-route-id='$route_id']")->item(0);
            if ($routeElement) {
                $updatedRoutes .= $doc->saveHTML($routeElement);
            }
        }

        
        libxml_use_internal_errors(false);

        return view('results', ['routes' => $updatedRoutes, 'molecule' => $smiles]);
    }


    public function add(Request $request)
    {
        $userId = auth()->id();
        $smiles = $request->input('smiles');
        $routeId = $request->input('route_id');
        $routeNum = $request->input('route_num');
        $knowledgeWeights = $request->input('knowledge_weights');
        $saveTree = filter_var($request->input('save_tree'), FILTER_VALIDATE_BOOLEAN);
        $expansionNum = $request->input('expansion_num');
        $cumProbMod = filter_var($request->input('cum_prob_mod'), FILTER_VALIDATE_BOOLEAN);
        $chemAxon = filter_var($request->input('chem_axon'), FILTER_VALIDATE_BOOLEAN);
        $selectionConstant = $request->input('selection_constant');
        $timeLimit = $request->input('time_limit');


        $exists = FavoriteRoute::where([
            'user_id' => $userId,
            'smiles' => $smiles,
            'route_id' => $routeId,
            'route_num' => $routeNum,
            'knowledge_weights' => $knowledgeWeights,
            'save_tree' => $saveTree,
            'expansion_num' => $expansionNum,
            'cum_prob_mod' => $cumProbMod,
            'chem_axon' => $chemAxon,
            'selection_constant' => $selectionConstant,
            'time_limit' => $timeLimit
        ])->exists();

        if ($exists) {
            return response()->json(['isFavorite' => true, 'message' => 'このルートは既にお気に入りに登録されています。']);
        }


        $favoriteRoute = new FavoriteRoute([
            'user_id' => $userId,
            'smiles' => $smiles,
            'route_num' => $routeNum,
            'route_id' => $routeId,
            'knowledge_weights' => $knowledgeWeights,
            'save_tree' => $saveTree,
            'expansion_num' => $expansionNum,
            'cum_prob_mod' => $cumProbMod,
            'chem_axon' => $chemAxon,
            'selection_constant' => $selectionConstant,
            'time_limit' => $timeLimit
        ]);
        $favoriteRoute->save();

        return response()->json(['isFavorite' => true, 'message' => '新しいルートをお気に入りに追加しました。']);
        
    }



    public function remove(Request $request)
    {
        $userId = auth()->id();
        $saveTree = filter_var($request->input('save_tree'), FILTER_VALIDATE_BOOLEAN);
        $cumProbMod = filter_var($request->input('cum_prob_mod'), FILTER_VALIDATE_BOOLEAN);
        $chemAxon = filter_var($request->input('chem_axon'), FILTER_VALIDATE_BOOLEAN);

        $route = FavoriteRoute::where([
            'user_id' => $userId,
            'smiles' => $request->input('smiles'),
            'route_id' => $request->input('route_id'),
            'route_num' => $request->input('route_num'),
            'knowledge_weights' => $request->input('knowledge_weights'),
            'save_tree' => $saveTree,
            'expansion_num' => $request->input('expansion_num'),
            'cum_prob_mod' => $cumProbMod,
            'chem_axon' => $chemAxon,
            'selection_constant' => $request->input('selection_constant'),
            'time_limit' => $request->input('time_limit')
        ])->first(); 

        if (!$route) {
            return response()->json(['error' => 'No matching route found'], 404);
        }

        $route->delete(); 
        return response()->json(['isFavorite' => false, 'message' => 'Route deleted successfully']);
    }


    public function favorite(Request $request)
    {
        $smiles = $request->input('smiles');

        $selected_route_id = $request->input('route_id'); 

        $route_num = $request->input('route_num');

        $knowledge_weights = json_encode(array_map('floatval', json_decode($request->input('knowledge_weights'))));
        
        $save_tree = $request->input('save_tree');
        $expansion_num = $request->input('expansion_num');
        $cum_prob_mod = $request->input('cum_prob_mod');
        $chem_axon = $request->input('chem_axon');
        $cui = $request->input('cui');
        $selection_constant = $request->input('selection_constant');
        $time_limit = $request->input('time_limit');

        $csrf_token = csrf_token();

        $process = new Process(["python3", "/var/www/html/ReTReKpy/exe.py", $smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $selection_constant, $time_limit, $csrf_token]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        $process->run();
        

        // 実行に失敗した場合(失敗の原因の場所の究明)
        if(!$process->isSuccessful())
        {
            throw new ProcessFailedException($process);
        }

        $routes = $process->getOutput();


        $replacements1 = [];
        preg_match_all('/\\d+: (\\/var\\/www\\/html\\/public\\/images\\/[^<]+\\.png)/', $routes, $matches);
        foreach ($matches[0] as $i => $text) {
            $path = str_replace('/var/www/html/public', '', $matches[1][$i]);
            $replacements1[$text] = '<img src="'. asset($path) .'" alt="Molecule">';
        }
        foreach ($replacements1 as $search => $replace) {
            $routes = str_replace($search, $replace, $routes);
        }


        
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($routes, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($doc);

        
        $routeElements = $xpath->query("//div[@data-route-id='$selected_route_id']");
        
        $selectedRoutes = '';
        foreach ($routeElements as $routeElement) {
    
            $buttonText = 'お気に入りから削除';
            $actionRoute =  route('remove');

            $form = $xpath->query(".//form[@class='favorite-form']", $routeElement)->item(0);
            if ($form) {
                $formHtml = $form->ownerDocument->saveHTML($form);
                
                $form->setAttribute('action', $actionRoute);

                $button = $xpath->query(".//button[contains(@class, 'favorite-button')]", $form)->item(0);
                if ($button) {
                    $button->nodeValue = $buttonText;
                }
            }

            $selectedRoutes .= $doc->saveHTML($routeElement);
        }

        
        libxml_use_internal_errors(false);

        return view('results', ['routes' => $selectedRoutes, 'molecule' => $smiles]);
    
    }

}
