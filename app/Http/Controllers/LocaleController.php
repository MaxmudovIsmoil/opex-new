<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function lang(string $lang)
    {
        App::setLocale($lang);
        Session::put('locale', $lang);
        $userId = Auth::id();
        User::findOrfail($userId)->update(['language' => $lang]);
        return redirect()->back();
    }
}
