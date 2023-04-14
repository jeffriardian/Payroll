<?php

namespace Modules\Auth\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LevelUserController extends Controller
{

    public function setLevel(Request $request)
    {
        $level = (!empty($request->level)) ? $request->level : 'gudang umum';
        $request->session()->put('user_level', $level);
        $user_level = $request->session()->get('user_level');
        return $user_level;
    }
}
