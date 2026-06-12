<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search page results.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $posts = $this->searchService->search($query ?? '');

        return view('frontend.search.index', compact('posts', 'query'));
    }

    /**
     * Search autocomplete suggestions.
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('q');
        $results = $this->searchService->autocomplete($query ?? '');

        return response()->json($results);
    }
}
