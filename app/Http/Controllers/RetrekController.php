<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Models\FavoriteRoute;

class RetrekController extends Controller
{
    public function user()
    {
        $userId = auth()->id();
        $favoriteRoutes = FavoriteRoute::where('user_id', $userId)->get();
        return view('user', ['favoriteRoutes' => $favoriteRoutes]);
    }

    
    public function tmp()
    {
        return view('resulttmp');
    }


    public function exepy(Request $request)
    {
        $smiles = $request->input('smiles');
        $substance = $request->input('substance');

        $route_num = (int) $request->input('route_num');


        $weights = $request->input('weights');
        $knowledge_weights = json_encode(array_map('floatval', $weights));
        
        $save_tree = $request->input('save_tree');
        $expansion_num = (float) $request->input('expansion_num');
        $cum_prob_mod = $request->input('cum_prob_mod');
        $chem_axon = $request->input('chem_axon');
        $ccc = $request->input('cui');
        $csv = $request->input('fromCSV');
        $selection_constant = (float) $request->input('selection_constant');
        $time_limit = (float) $request->input('time_limit');

        $csrf_token = csrf_token();

#	$strout=phpinfo();
	$fh=fopen("/var/www/html/public/images/komai.info","w");
	fwrite($fh,"cut:".$ccc."\n");
	if ($ccc==3){
		$array=explode('#',$csv);
			foreach ($array as &$value){
				fwrite($fh,$value."\n");
			}
		$cui='csv';
		return;
	}else if ($ccc==2){
		fwrite($fh,"iiiiiiiiiiiiiiiiiiiiii:".$ccc."\n");
		$cui='True';
	}else{
		fwrite($fh,"oooooooooooooooooo:".$ccc."\n");
		$cui='False';
	}
	fwrite($fh,"cui:".$cui."\n");
	fwrite($fh,"csv:".$csv."\n");
#	fwrite($fh,$stdout);
	fclose($fh);

        $process = new Process(["python3", "/var/www/html/ReTReKpy/exe.py", $smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $cui, $csv, $selection_constant, $time_limit, $csrf_token, $substance]);
        $process->setWorkingDirectory('/var/www/html/ReTReKpy'); // 作業ディレクトリの設定
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

        $process = new Process(["python3", "/var/www/html/ReTReKpy/exe.py", $smiles, $route_num, $knowledge_weights, $save_tree, $expansion_num, $cum_prob_mod, $chem_axon, $cui, $selection_constant, $time_limit, $csrf_token]);
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
