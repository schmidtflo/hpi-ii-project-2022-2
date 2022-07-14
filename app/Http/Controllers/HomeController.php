<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(Request $request) {
        $company = Company::search($request->get('company'))->first();
        $articles = $company->getArticles();
        $hrbs = $company->handelsregisterbekanntmachungen()->get();
        return view('home', ['company' => $company, 'articles' => $articles, 'hrbs' => $hrbs]);
    }
}
