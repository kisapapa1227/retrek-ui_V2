<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReTReK Results tmp</title>
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
            <h1>の合成経路</h1>
            <form action="{{ route('user') }}" method="GET" class="mb-3">    
                <button onclick="window.location.href='/search';" class="btn btn-primary back-button">ユーザー検索画面へ戻る</button>
            </form>
        </div>
    </div>

    
    <div class="container">
        <h1 class="mb-4">ReTReK Results</h1>
        <!-- INFO:__main__:<br><br> -->
        <!-- Route 1 -->
        <!--  -->
        <div class="route">
            <div class="route-header" onclick="toggleRoute(this);">
                <h2>Route 1</h2>
                <h5>Reaction route search done.</h5>
            </div>
            <div class="route-body">
                <!--  -->
                <p>Visit frequency to node: 2<br>Total score: 4.006024863570929<br>Node depth: 2 (Starting Material(s))</p>

                <div class="structure-img">
                    
                    <div class="molecule reactant_molecule">
                        <img src="{{ asset('images/route_1_node_2_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="plus">
                        <img src="{{ asset('images/plus.png') }}" alt="plus">
                    </div>

                    <div class="molecule reactant_molecule">
                        <img src="{{ asset('images/route_1_node_2_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="arrow">
                        <img src="{{ asset('images/arrow.png') }}" alt="arrow">
                    </div>

                    <div class="molecule product_molecule">
                        <img src="{{ asset('images/route_1_node_2_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>
                    

                </div>

                <p>Visit frequency to node: 45<br>Total score: 0.18295501443592627<br>Node depth: 1</p>
                <p>Apply reverse reaction rule: [#8-:1].[#6:2]-[#8:3]-[#6:4]>>[#6:2]-[#8:3]</p>
                <p>Reaction applied molecule index: 2</p>
                <div class="structure-img">
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_1_node_1_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_1_node_1_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_1_node_1_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>
                    
                </div>

                <p>Visit frequency to node: 128<br>Total score: 0.06656249999999998<br>Node depth: 0 (Target Molecule)</p>
                <p>Apply reverse reaction rule: [Na+:1].[#6:2]-[#8:3].[#8:4]-[#6:5][Cl:6]>>[#6:2]-[#8:3]-[#6:5]-[#8:4]</p>
                <div class="structure-img">
                    <div class="molecule">
                        <img src="{{ asset('images/route_1_node_0_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>
                </div>

                <div class="route-footer">
                    
                        <form action="{{ route('add') }}" method="POST">
                            @csrf 
                            <input type="hidden" name="smiles" value="{smiles}">
                            <input type="hidden" name="route_id" value="{route_id}">
                            <input type="hidden" name="route_num" value="{route_num}">
                            <input type="hidden" name="knowledge_weights" value="{knowledge_weights}">
                            <input type="hidden" name="save_tree" value="{save_tree}">
                            <input type="hidden" name="expansion_num" value="{expansion_num}">
                            <input type="hidden" name="cum_prob_mod" value="{cum_prob_mod}">
                            <input type="hidden" name="chem_axon" value="{chem_axon}">
                            <input type="hidden" name="selection_constant" value="{selection_constant}">
                            <input type="hidden" name="time_limit" value="{time_limit}">
                            <button type="submit" class="btn btn-primary">お気に入りに追加</button>
                        </form>
                    
                </div>

            </div>
        </div>


        
        <!-- INFO:__main__:<br><br> -->
        <!-- Route 2 -->
        <div class="route" onclick="toggleRoute(this);">
            <div class="route-header">
                <h2>Route 2</h2>
                <!-- INFO:__main__: --><h5>Reaction route search done.</h5>
            </div>
            <div class="route-body">
                <p>Visit frequency to node: 2<br>Total score: 4.005133507074788<br>Node depth: 2 (Starting Material(s))</p>
                <div class="structure-img">
                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_2_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_2_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_2_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_2_mol_3.png') }}" alt="Molecule 3">
                        <p>3</p>
                    </div>
                </div>
                <p>Visit frequency to node: 45<br>Total score: 0.18291880555426082<br>Node depth: 1</p>
                <p>Apply reverse reaction rule: [#8-:1].[#6:2]-[#8:3]-[#6:4]>>[#6:2]-[#8:3]</p>
                <p>Reaction applied molecule index: 1</p>
                <div class="structure-img">
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_1_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_1_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_1_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>

                </div>
                <p>Visit frequency to node: 286<br>Total score: 0.058356643356643575<br>Node depth: 0 (Target Molecule)</p>
                <p>Apply reverse reaction rule: [#6:1]-[#8:2].[#8:3]-[#6:4][Cl:5].[#6:6]-[#6:7]-[#7:8](-[#6:9](-[#6:10])-[#6:11])-[#6:12](-[#6:13])-[#6:14]>>[#6:1]-[#8:2]-[#6:4]-[#8:3]</p>
                <div class="structure-img">
                    <div class="molecule">
                        <img src="{{ asset('images/route_2_node_0_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>
                </div>

                <div class="route-footer">
                    
                        <form action="{{ route('add') }}" method="POST">
                            @csrf 
                            <input type="hidden" name="smiles" value="{smiles}">
                            <input type="hidden" name="route_id" value="{route_id}">
                            <input type="hidden" name="route_num" value="{route_num}">
                            <input type="hidden" name="knowledge_weights" value="{knowledge_weights}">
                            <input type="hidden" name="save_tree" value="{save_tree}">
                            <input type="hidden" name="expansion_num" value="{expansion_num}">
                            <input type="hidden" name="cum_prob_mod" value="{cum_prob_mod}">
                            <input type="hidden" name="chem_axon" value="{chem_axon}">
                            <input type="hidden" name="selection_constant" value="{selection_constant}">
                            <input type="hidden" name="time_limit" value="{time_limit}">
                            <button type="submit" class="btn btn-primary">お気に入りに追加</button>
                        </form>
                </div>
            </div>
        </div>


        <!-- INFO:__main__:<br><br> -->
        <!-- Route 3 -->
        <div class="route" onclick="toggleRoute(this);">
            <div class="route-header">
                <h2>Route 3</h2>
                <!-- INFO:__main__: --><h5>Reaction route search done.</h5>
                <!-- INFO:__main__:<br><br> -->
            </div>
            <div class="route-body">
                <p>Visit frequency to node: 2<br>Total score: 3.5111466029193252<br>Node depth: 3 (Starting Material(s))</p>
                <div class="structure-img">
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_3_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_3_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_3_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_3_mol_3.png') }}" alt="Molecule 3">
                        <p>3</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_3_mol_4.png') }}" alt="Molecule 4">
                        <p>4</p>
                    </div>
                    
                </div>
                <p>Visit frequency to node: 45<br>Total score: 0.16285195975879002<br>Node depth: 2</p>
                <p>Apply reverse reaction rule: [#8-:1].[#6:2]-[#8:3]-[#6:4]>>[#6:2]-[#8:3]</p>
                <p>Reaction applied molecule index: 2</p>
                <div class="structure-img">
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_2_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_2_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_2_mol_2.png') }}" alt="Molecule 2">
                        <p>2</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_2_mol_3.png') }}" alt="Molecule 3">
                        <p>3</p>
                    </div>
                    
                </div>
                <p>Visit frequency to node: 257<br>Total score: 0.03139223834637905<br>Node depth: 1</p>
                <p>Apply reverse reaction rule: [#6:1]-[#8:2].[#8:3]-[#6:4][Cl:5].[#6:6]-[#6:7]-[#7:8](-[#6:9](-[#6:10])-[#6:11])-[#6:12](-[#6:13])-[#6:14]>>[#6:1]-[#8:2]-[#6:4]-[#8:3]</p>
                <div class="structure-img">
                    
                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_1_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>

                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_1_mol_1.png') }}" alt="Molecule 1">
                        <p>1</p>
                    </div>
                    
                </div>
                <p>Visit frequency to node: 810<br>Total score: -0.00339094650205729<br>Node depth: 0 (Target Molecule)</p>
                <p>Apply reverse reaction rule: [#6:1]-[#6:2](-[#8:3])=[O:4].[#6:5]-1-[#6:6]-[#6:7]-[#8:8]-[#6:9]-1>>[#6:1]-[#6:2]-[#8:3]</p>
                <div class="structure-img">
                    <div class="molecule">
                        <img src="{{ asset('images/route_3_node_0_mol_0.png') }}" alt="Molecule 0">
                        <p>0</p>
                    </div>
                </div>

                <div class="route-footer">
                    
                        <form action="{{ route('add') }}" method="POST">
                            @csrf 
                            <input type="hidden" name="smiles" value="{smiles}">
                            <input type="hidden" name="route_id" value="{route_id}">
                            <input type="hidden" name="route_num" value="{route_num}">
                            <input type="hidden" name="knowledge_weights" value="{knowledge_weights}">
                            <input type="hidden" name="save_tree" value="{save_tree}">
                            <input type="hidden" name="expansion_num" value="{expansion_num}">
                            <input type="hidden" name="cum_prob_mod" value="{cum_prob_mod}">
                            <input type="hidden" name="chem_axon" value="{chem_axon}">
                            <input type="hidden" name="selection_constant" value="{selection_constant}">
                            <input type="hidden" name="time_limit" value="{time_limit}">
                            <button type="submit" class="btn btn-primary">お気に入りに追加</button>
                        </form>
                    
                </div>
            </div>
        </div>

    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleRoute(element) {
            $(element).closest('.route').find('.route-body').slideToggle();
        }
    </script>
</body>
</html>


<!-- <img src="http://localhost/images/route_3_node_3_mol_0.png" alt="Molecule"> -->