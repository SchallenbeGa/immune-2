<?php

namespace App\Http\Controllers\Htmx;

use App\Models\User;
use App\Models\Computer;
use App\Models\Employee;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticlePostCommentRequest;

class HTMXInventoryController extends Controller
{
    public function showList(){
        $computers = Computer::with('employee')->get();

        return view('home.partials.inventory-preview-light', ['computers' => $computers])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);
    }
    public function showByReference($reference)
    {
        // Rechercher le PC par sa référence avec l'utilisateur associé
        $computer = Computer::with('employee')->where('reference', $reference)->firstOrFail();
        $computer = $computer->append('detailUrl');
        return view('inventory.show', compact('computer'));
    }
    
}