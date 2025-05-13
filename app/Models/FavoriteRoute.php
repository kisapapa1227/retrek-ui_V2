<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteRoute extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'smiles', 'route_id', 'route_num', 'knowledge_weights', 'save_tree',
        'expansion_num', 'cum_prob_mod', 'chem_axon', 'selection_constant', 'time_limit'
    ];

    protected $casts = [
        'knowledge_weights' => 'array',
        'cum_prob_mod' => 'boolean',    
        'chem_axon' => 'boolean',      
        'save_tree' => 'boolean',
        'expansion_num' => 'float', 
        'selection_constant' => 'float',
        'time_limit' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
