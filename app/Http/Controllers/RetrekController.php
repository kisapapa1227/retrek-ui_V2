<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage; 
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Models\FavoriteRoute;

function chkValidPrms($l){
// Rev 2.2
	$s1=explode(",",$l)[0];
	if (count(explode(",",$l))<10){
		return [$s1,'short'];
	}
	$prc = new Process(['python3',"/var/www/html/ReTReKpy/make_reports/chkSmiles.py", $s1]);
	$prc->run();
	$ret=explode("\n",$prc->getOutput())[0];

	return [$s1,$ret];
}

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

function makeScriptForDrop($id,$uid,$db_type){
    $wk="/var/www/html/ReTReKpy/";
    $sh=$wk.$uid."_drop.sh";
    $agent=$wk."make_reports/readDb.py";

    $fp=fopen($sh,"w");
    fwrite($fp,"#!/bin/sh\n#\n#\n");
    if ($db_type=="com"){
	    fwrite($fp,"python3 ".$agent." -id ".$id." -drop -d ".$wk);
    }else{
	    fwrite($fp,"python3 ".$agent." -id ".$id." -drop -d ".$wk." -database sList".$uid.".db");
    }
    fclose($fp);
    return $sh;
}

function makeScript($sh,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$uid){
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
    fwrite($fp,"exit 1\nfi\n");
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
//	$uid = preg_replace("/[^a-zA-Z0-9]+/u","",$user['email']);
    return view('menu',['user'=>$user,'db_type'=>'com']);
}
    public function singleSearch(Request $request){
        $db_type = $request->input('db_type');
        $uid = $request->input('uid');
	if ($this->getPid($uid)=="non"){
        	return view('singleSearch',['db_type'=>$db_type,'uid'=>$uid]);
	}
	        return view('multiProc',['db_type'=>$db_type,'uid'=>$uid]);
    }
    public function multiSearch(Request $request){
        $db_type = $request->input('db_type');
        $uid = $request->input('uid');

	if ($this->getPid($uid)=="non"){
	        return view('multiSearch',['db_type'=>$db_type,'uid'=>$uid]);
	}

        return view('multiProc', ['uid' => $uid,'db_type'=>$db_type]);
    }

    public function db(Request $request){
        $db_type = $request->input('db_type');
        $uid = $request->input('uid');
	$prm=$this->forDb($db_type,$uid);$prm['modal']='no';$prm['filename']='non';$prm['tid']=0;
        return view('db',$prm);
    }

    public function routeEval(Request $request){
    	$user= Auth::user();
        $uid = $request->input('uid');
        $db_type = $request->input('db_type');
	$tid=$request->input('tid');
	$oper=$request->input('oper');
##
	$ddd="/var/www/html/storage/app/public/";
        $output=$ddd."report/";
	$agent="/var/www/html/ReTReKpy/make_reports/readDb.py";

	switch($oper){
	case 'get_selected':

	$this->log("in get_selected");

	$route="[".$request->input("routes")."]";

	// komai
        $prm=["python3",$agent,"-id",$tid,"-s","1.0","-force","-d",$output,"-route",$route];
## database
            if ($db_type!="com"){
                    array_push($prm,"-database","sList".$uid.".db");
            }
	foreach ($prm as $p){
		$this->log($p);
	}

            $process=new Process($prm);
	    $process->setWorkingDirectory('/var/www/html/ReTReKpy');
	    $ret=$process->run();

	$dst="Retrek_Report_".date("Ymd_Hi").".pdf";

	$this->log($oper."  dst...".$dst);
	$src=$output.$tid.".pdf";

	$stdout=$process->getOutput();

	$lines=explode("\n",$stdout);
	foreach ($lines as $line){
		$this->log("ook ".$line);
	}
	return response()->download($src,$dst);
	    break;
	}
	$prm=$this->forDb($db_type,$uid);

	$ids=$prm['body']['id'];
	$prm['modal']='no';$prm['filename']='non';

	$prm=$prm+$this->statForEval($db_type,$uid,$ids);
	if ($tid==null){
		$prm['tid']=-1;
	}else{
		$prm['tid']=$tid;
	}	

//komai
	$prm['inUse']=$this->isInUse($db_type,$uid);
        return view('routeEvaluation',$prm);
    }

    public function routeEvaluation(Request $request){
    	$user= Auth::user();
        $uid = $request->input('uid');
        $db_type = $request->input('db_type');
	$tid=$request->input('tid');

	$prm=$this->forDb($db_type,$uid);

	$ids=$prm['body']['id'];
	$prm['modal']='no';$prm['filename']='non';

	$prm=$prm+$this->statForEval($db_type,$uid,$ids);

//komai
	$prm['inUse']=$this->isInUse($db_type,$uid);
	$prm['tid']=-1;

        return view('routeEvaluation',$prm);
    }

    public function dbManage(Request $request){
    	$user= Auth::user();
        $uid = $request->input('uid');
        $db_type = $request->input('db_type');
	$prm=$this->forDb($db_type,$uid);$prm['modal']='no';$prm['filename']='non';$prm['tid']=0;
	$prm['inUse']=$this->isInUse($db_type,$uid);
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


    public function mRet(Request $request){
    	$user= Auth::user();
	return view('menu',['user'=>$user,'db_type'=>$request->input('db_type')]);
    }

    public function kRet(Request $request){

	$prc = new Process(['ps','aux']);
	$prc->run();
	$outp=explode("\n",$prc->getOutput());

        $uid = $request->input('uid');
    $this->killIt($outp,"sail","python",$uid);

#    	fclose($fh);
    	$user= Auth::user();
	return view('menu',['user'=>$user,'db_type'=>$request->input('db_type')]);
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
        $tid = $request->input('tid');
        $db_type = $request->input('db_type');
        $uid = $request->input('uid');

	$opr_stat="";

	if ($db_type=="com"){
		$src="/var/www/html/ReTReKpy/sList.db";
	}else{
		$src="/var/www/html/ReTReKpy/sList".$uid.".db";
	}

	switch($op){
	case 'get_all':
		$agent="/var/www/html/ReTReKpy/make_reports/readDb.py";
	 	$ddd="/var/www/html/storage/app/public/";
		$output=$ddd."report/";
		if (is_dir($ddd."report")){
			$process=new Process(["rm","-rf",$output]);
			$ret=$process->run();
		}

		if (file_exists($ddd."report.zip")==true){
			unlink($ddd."report.zip");
		}
			$process=new Process(["mkdir",$output]);
			$ret=$process->run();

	foreach (explode(",",$tid) as $id){
		$prm=["python3",$agent,"-id",$id,"-s","0.25","-force","-d",$output];
		if ($db_type!="com"){
			array_push($prm,"-database","sList".$uid.".db");
		}
		$process=new Process($prm);
        	$process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        	$process->setTimeout(0);
		$ret=$process->run();

		array_push($prm,"-db");
		$process=new Process($prm);
        	$process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
        	$process->setTimeout(0);
		$ret=$process->run();
	}
			foreach (glob($output.'*.log') as $file){
				unlink($file);
			}
			$process=new Process(["zip","-r","report.zip","report"]);
			$process->setWorkingDirectory($ddd);
			$ret=$process->run();
		$dst="Retrek_Report_".date("Ymd_Hi").".zip";
		return response()->download($ddd."report.zip",$dst);
		break;
	case 'get_selected':
		$this->log("get_selected");
		break;
	case 'dropDb':
        	$sh=makeScriptForDrop($tid,$uid,$db_type);
        	$this->easyProcess($sh,"readDb.py","readDb.py");
		break;
	case 'db_load':
		$fn=$request->file('a');$request->file('a')->storeAs('','test.db');
		$dst='/var/www/html/storage/app/test.db';
		if ($this->is_database($dst)===false){
			copy($dst,$src);
		}else{
			$gn=$request->file('a')->getClientOriginalName();
//			$opr_stat=$gn." is not a retrek-ui database file.";
			$opr_stat=$gn." は retrek-ui のデータベースファイルではありません.\nアップロードは行いませんでした。";
		}
		break;
	case 'db_init':
	if ($db_type=="com"){
		foreach (glob("/var/www/html/public/images/report/*.*") as $val){
			unlink($val);
		}
	}else{
		foreach (glob("/var/www/html/public/images/".$uid."/report/*.*") as $val){
			unlink($val);
		}
	}
			unlink($src);
		break;
	case 'db_new':
		$path="/var/www/html/ReTReKpy/";
	$tmp="tmp".$uid.".db";
		$com=["python3", "make_reports/editDb.py","-cp","-ids"];
	foreach (explode(",",$tid) as $id){
		array_push($com,$id);
	}
	$com=array_merge($com,["-src",$src,"-dst",$tmp]);
	$process = new Process($com);
	
	foreach ($com as $c){
		$this->log($c);
	}

if (file_exists($path.$tmp) == true){
	unlink($path.$tmp);
}
        $process->setWorkingDirectory($path); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();

	$lines=explode("\n",$stdout);

	foreach ($lines as $line){
		$this->log($line);
	}
		$dst="Retrek_".date("Ymd_Hi").".db";
		return response()->download($path.$tmp,$dst);
		break;
	case 'db_save':
		$dst="Retrek_".date("Ymd_Hi").".db";
		return response()->download($src,$dst);
		break;
	}

	$prm=$this->forDb($db_type,$uid);$prm['modal']='no';$prm['filename']='non';$prm['tid']=0;

	if ($opr_stat!=""){
		$prm['log']=$opr_stat;
	}
	$prm['inUse']=$this->isInUse($db_type,$uid);
       	return view('dbManage', $prm);
    }

    public function syncPdf(Request $request){
	$tid  =$request->tid;
	$given=$request->given;
	$from =$request->from;
	$db_type =$request->db_type;
	$uid  =$request->uid;
    	$sh=$this->makeNow($request);
	$prm=$this->forDb($db_type,$uid);
	sleep(2);
	$this->easyProcess($sh,"readDb.py","readDb.py"); 

	$prm['modal']='yes';$prm['filename']=$given;$prm['tid']=$tid;

	if ($from=='db'){
	       	return view('db', $prm);
	}
	$prm['inUse']=$this->isInUse($db_type,$uid);
	       	return view('dbManage', $prm);
    }

    function myAddDb($uid,$loop,$db_type){

	$sh="async".$loop.".sh";
	if ($db_type=="com"){
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/addDb.py", "-u",$uid,"-s",$sh,"-d","/var/www/html/public/images/"]);
	}else{
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/addDb.py", "-u",$uid,"-s",$sh,"-d","/var/www/html/public/images/","-database","sList".$uid.".db"]);
	}
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("\n",$stdout);
	foreach ($lines as $line){
		if (strpos($line,"###")){
			$sub=explode(":",explode("###",$line)[0])[1];
		}
	}
	$this->copyPdf($uid,$sub,$db_type);
    }

    public function addDb(Request $request){
       $uid=$request->uid;
       $db_type=$request->db_type;

$path="/var/www/html/";
#komai
	$src="tmp".$uid.".db";
       if ($db_type=="com"){
             $dst="sList.db";
       }else{
             $dst="sList".$uid.".db";
       }
	$process = new Process(["python3", "make_reports/editDb.py","-cp","-ids",1,"-src",$src,"-dst",$dst]);
        $process->setWorkingDirectory($path."ReTReKpy"); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("\n",$stdout);

	foreach ($lines as $line){
		if (strpos($line,"substance")!==false){
			$sub=explode(":",$line)[1];
#			$sub=explode(":",$line);
		}
	}

$substance="the baby";
	$this->copyPdf($uid,$sub,$db_type);
	$param=[
		'uname' => $uid,
		'substance' => $sub,
	];
	return response()->json($param);
	   }

	public function is_db_busy(Request $request){
	}
	public function askMaster(Request $request){
	$uid = $request->uid;
	$pid = $request->pid;
#
# check the list
#
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/jobMaster.py","-ask","-uid",$uid]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("###",$stdout);

	$end=True;
	foreach ($lines as $line){
        	if (false !== strpos($line,'ready') || false !== strpos($line,'Searching')){
			$end=False;
		}
	}
#
# list completed
#
	if ($end){
		return response()->json(['pid'=>-2]);
	}

	$now=explode("pid:",$lines[0])[1];

	if ($now!=$pid){# return progress list
		$list=$lines[1];
		$proc="";
	}else{# return progress time_table
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/jobMaster.py","-ask","-uid",$uid,"-pid",$pid]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getOutput();
	$lines=explode("###",$stdout);
		$list="";
		$proc=$lines[1];
	}

	$param=[
		'pid' => $now,
		'list'=> $list, 
		'proc'=> $proc,
	];
	return response()->json($param);
	}

	public function askProc(Request $request){
$substance=$request->id;
$uid = $request->uid;

$fn="/var/www/html/public/images/".$uid."/".$substance.".txt";
if (file_exists($fn) == false){
        $count=$fn." not_yet";
}else{
        $fh=fopen($fn,"r");
        $count="0";
	$done="0";
	$running="yes";
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
	}

	$pid=$this->getPid($uid);

	$param=[
		'currentRoute' => $count,
		'pdf' => $done,
		'uid' => $uid,
		'pid' => $pid,
	];
	return response()->json($param);
}

    public function tmp()
    {
        return view('resulttmp');
    }
	
    private static function isInUse($db_type,$uid){
        $path="/var/www/html/ReTReKpy/";
        $fn=$path."jobMaster.lock";
if (file_exists($fn) == false){
	return 'not-running';
}
    $fp=fopen($fn,"r");
	$r=True;
	while($line=fgets($fp)){
		if (strpos($line,$uid)!==false){
			$r=False;
		}
	}
	fclose($fp);
	if ($r){
		return 'not-my_job-running';
	}

	$process = new Process(["python3", $path."/make_reports/jobMaster.py","-ask_db_type","-uid",$uid]);
#	$process = new Process(["python3", $path."/make_reports/jobMaster.py","-ask","-uid",$uid]);
        $process->setWorkingDirectory($path); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getErrorOutput();

	$outp=explode("\n",$process->getOutput());
	if($outp[0]==$db_type){
		return 'True';
	}
		return 'not_this_database';
    }

    private static function killIt($outp,$key1,$key2,$key3){

	foreach ($outp as $o){
	if (strpos($o,$key1)!==false 
		&& strpos($o,$key2)!==false
		&& strpos($o,$key3)!==false){
		$prms=explode(" ",$o);
		$l=0;
		foreach ($prms as $i){
			if($i!==""){
				if ($l==1){
				$prc = new Process(['kill',$i]);
				$prc->run();
				}
				$l++;
			}
		}}}
}
    private static function is_database($fn){
	$path="/var/www/html/ReTReKpy/";
	$process = new Process(["python3", $path."make_reports/readDb.py", "-db_list","-database",$fn]);
        $process->setWorkingDirectory($path); // 作業ディレクトリの設定
       	$process->run();
	$stdout=$process->getErrorOutput();
	return strpos($stdout,"sqlite3.DatabaseError");
    }
    private static function log($com){
	$fp=fopen("/var/www/html/public/images/report/step2.txt","a");
	fwrite($fp,$com."\n");
	fclose($fp);
}
    private static function copyPdf($uid,$sub,$db_type){
$fp=fopen("/var/www/html/public/images/report/step1.txt","a");
fwrite($fp,"in copyPdf.....xxx\n");
$path="/var/www/html/";
	#
	# 2025/06/12 データベースに追加する時、作ってあるpdf をimages/report/pid.pdf に移動する。 
	#
if ($db_type=="com"){
$process = new Process(["python3", $path."ReTReKpy/make_reports/readDb.py", "-db_list"]);
}else{
$process = new Process(["python3", $path."ReTReKpy/make_reports/readDb.py", "-db_list","-database","sList".$uid.".db"]);
}
$process->setWorkingDirectory($path.'ReTReKpy'); // 作業ディレクトリの設定
        $process->setTimeout(0);
       	$process->run();
        $routes = $process->getOutput();
	$records=explode("###",$routes);
	$it=$records[count($records)-2];
	$tid=explode("#",$it)[0];
	$ipath=$path."public/images/";
	if ($db_type=="com"){
#fwrite($fp,"<---------------------------------12:".$it."\n");
#fwrite($fp,$ipath.$uid."/report/".$sub.".pdf\n");
#fwrite($fp,$ipath."report/".$tid.".pdf\n");
#fwrite($fp,"<---------------------------------13:".$it."\n");
		$process = new Process(["mv",$ipath.$uid."/report/".$sub.".pdf",$ipath."report/".$tid.".pdf"]);
	}else{
fwrite($fp,"<---------------------------------22:".$it."\n");
		$process = new Process(["mv",$ipath.$uid."/report/".$sub.".pdf",$ipath.$uid."/report/".$tid.".pdf"]);
		
	}
fclose($fp);
       	$process->run();
    }

    private static function getPid($uid){
	$prc = new Process(['ps','aux']);
	$prc->run();
	$outp=explode("\n",$prc->getOutput());
	$pid="non";
	foreach ($outp as $o){
		if (strpos($o,$uid)!==false && strpos($o,"exe.py")!==false){
		$pid=explode(" ",$o)[1];
			$running="yes";
		}
	}
	return $pid;
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

	$fp=fopen("/var/www/html/public/images/report/step1.txt","a");
	foreach ($outp as $o){
		fwrite($fp,$o."\n");

	if (strpos($o,"sail")!==false 
		&& strpos($o,"python")!==false
		&& strpos($o,$py1)!==false
		&& strpos($o,$py2)!==false){
			$ww=21;break;
	}
	}
		sleep(2);
		$ww++;
	}
	fclose($fp);
}

private static function askMakeScript($n,$l,$uid){
	$sh="async".$n.".sh";
        $wk="/var/www/html/ReTReKpy/";
# Rev 2.2
		$ret=chkValidPrms($l);//short or None
	$s=$ret[0];$r=$ret[1];

#$fp=fopen("/var/www/html/public/images/report/step3.txt","a");
#fwrite($fp,"in ask".$n.":".$s.":".$r."\n");
#fclose($fp);

	if ($r=='short'){
    		$fp=fopen($wk.$uid."_".$sh,"w");
    		fwrite($fp,"#!/bin/sh\n#\n#\n#smiles:".$s."\n#abort:false_config\n");
		fclose($fp);
		return;
	}
	if ($r=='None'){
    		$fp=fopen($wk.$uid."_".$sh,"w");
    		fwrite($fp,"#!/bin/sh\n#\n#\n#smiles:".$s."\n#abort:false_smiles\n");
		fclose($fp);
		return;
	}

	list($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p11, $p12)=getPrms($l);
        $p10= csrf_token();

	makeScript($sh,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$uid);
    }

private static function makeNow($request){

    $wk="/var/www/html/ReTReKpy/";

    $sh=$wk."now.sh";
    $fp=fopen($sh,"w");
    fwrite($fp,"#!/bin/sh\n#\n#\n");
    if ($request->db_type=="com"){
    $output="/var/www/html/public/images/report/";
    fwrite($fp,"python3 /var/www/html/ReTReKpy/make_reports/readDb.py ".$request->options." -d ".$output);
    }else{
    $output="/var/www/html/public/images/".$request->uid."/report/";
#    fwrite($fp,"python3 /var/www/html/ReTReKpy/make_reports/readDb.py ".$request->options." -d ".$output." -database sList".$request->uid.".db");
    fwrite($fp,"python3 /var/www/html/ReTReKpy/make_reports/readDb.py ".$request->options." -d ".$output);
    }
    fwrite($fp," 1> /dev/null 2>/dev/null &\n");
    fclose($fp);

    return $sh;
}

private static function statForEval($db_type,$uid,$tid){
	$pid=str_replace(","," ",$tid);
	array_pop($pid);

if ($db_type=="com"){
	$com=array_merge(["python3", "/var/www/html/ReTReKpy/make_reports/statDb.py", "-ids"],$pid);
	$process = new Process($com);
}else{
	$com =array_merge(["python3", "/var/www/html/ReTReKpy/make_reports/statDb.py", "-database","sList".$uid.".db","-ids"],$pid);
	$process = new Process($com);
}
$process->setWorkingDirectory('/var/www/html/ReTReKpy');
        $process->setTimeout(0);
       	$process->run();
        $routes = $process->getOutput();
	$records=explode("\n",$process->getOutput());

	$prmRoot=array();
	$prms=array();$flag=-1;$key="";
	foreach ($records as $record){

	$fp=fopen("/var/www/html/public/images/report/step2.txt","a");
	fwrite($fp,$record."\n");
	fclose($fp);

		if (strpos($record,"end-forDraw") !== false){
			$prms["draw".$id]=$prm;$flag=-1;
		}
		if (strpos($record,"start-forDraw") !== false){
			$x=explode(" ",$record);
			$prm=array();$id=$x[2];$flag=1;continue;
		}

#
		if (strpos($record,"end-forEval") !== false){
			$prms["record".$id]=$prm;$flag=-1;
		}
		if (strpos($record,"start-forEval") !== false){
			$x=explode(" ",$record);
			$prm=array();$id=$x[2];$flag=0;continue;
		}
#
#	$fp=fopen("/var/www/html/public/images/report/step2.txt","a");
#	fwrite($fp,"->".$x[0]."-".$x[1]." ".$x[2]."\n");
#	fclose($fp);
#
		if ($flag<0){
			continue;
		}
		if($key!=""){
			$prm[$key]=$record;$key="";
		}

		if (strpos($record,"route") !== false){
			$key=explode("\n",$record)[0];
	       	}
#
		for ($i=1;$i<6;$i++){
		if (strpos($record,"key".(string)$i) !== false){
			$key="key".(string)$i;
	       	}}
	}
	$prmRoot['stat']=$prms;
	return $prmRoot;
}

private static function forDb($db_type,$uid){
if ($db_type=="com"){
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-db_list"]);
}else{
	$process = new Process(["python3", "/var/www/html/ReTReKpy/make_reports/readDb.py", "-db_list","-database","sList".$uid.".db"]);
}
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
		$prm['uid']=$uid;
		$prm['db_type']=$db_type;

	return $prm;
}
    public function multiProc(Request $request){
        $csv = $request->input('fromCSV');
	$lines=explode(";",$csv);
        $uid = $request->input('uid');
        $db_type = $request->input('db_type');
	$wk="/var/www/html/ReTReKpy/";

$num=0;
	foreach($lines as $l){
		if (substr($l,0,1)!="#"){
			$num=$num+1;
			$this->askMakeScript($num,$l,$uid);
			}

	}
	$ssh=$wk.$uid."_jobMaster.sh";
	$fp=fopen($ssh,"w");
		fwrite($fp,"#!/bin/sh\n#\n#\npython3 make_reports/jobMaster.py -start -uid ".$uid." -max_loop ".(string)$num." -db_type ".$db_type."\n");
	fclose($fp);
	$this->easyProcess($ssh,"jobMaster.py",$uid);

        return view('multiProc', ['uid' => $uid,'db_type'=>$db_type]);
}

    public function exepy(Request $request){

	    $csv=$request->input('fromCSV');
	if ($csv!=""){# fromCSV
		return $this->multiProc($request);
	}

        $smiles = $request->input('smiles');
	if ($smiles==""){
    		$user= Auth::user();
		return view('menu',['user'=>$user,'db_type'=>$request->input('db_type')]);
	}

        $substance = $request->input('substance');

        $route_num = (int) $request->input('route_num');

        $weights = $request->input('weights');
        $knowledge_weights = json_encode(array_map('floatval', $weights));
        
        $save_tree = $request->input('save_tree');
        $expansion_num = (float) $request->input('expansion_num');
        $cum_prob_mod = $request->input('cum_prob_mod');
        $chem_axon = $request->input('chem_axon');
        $selection_constant = (float) $request->input('selection_constant');
        $time_limit = (float) $request->input('time_limit');
        $db_type = $request->input('db_type');
        $uid = $request->input('uid');

#$fp=fopen("/var/www/html/public/images/report/step1.txt","a");
#fwrite($fp,"db_type in exe.py...\n->".$uid."<-\n");
#if ($csv==""){
#fwrite($fp,"yes\n");
#}
#fclose($fp);

        $csrf_token = csrf_token();

        $sh = makeScript("async.sh",$smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $selection_constant, $time_limit, $csrf_token,$csv,$substance,$uid);

	$this->easyProcess($sh,"exe.py",$uid);

        return view('proc', ['smiles' => $smiles, 'route_num' => $route_num,'substance' => $substance, 'uid' => $uid,'db_type' => $db_type]);
    }
}
