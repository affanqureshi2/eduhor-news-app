<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Guardian\GuardianAPI;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    //
    function index(Request $request)
    {

    }
    function list(Request $request)
    {
        $guardian_api = config('app.guardian_key');
        $api = new GuardianAPI($guardian_api);
        // $request->validate([
        //     'search' => 'required'
        // ]);
        $validator = Validator::make($request->all(), [
                'search' => 'required'
            ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        // dd($request->search);
        $response = $api->content()
            ->setQuery($request->search)
            // ->setFromDate(new DateTimeImmutable("01/01/2010"))
            // ->setToDate(new \DateTimeImmutable())
            ->setShowTags("contributor")
            ->setShowFields("thumbnail,short-url,trailText")
            ->setOrderBy("relevance")
            // ->setPageSize(5)
            ->fetch();
        $news = [];
        $i = 0;
        foreach($response->response->results as $result)
        {
            $news[$i]['title'] = $result->webTitle ?? '';
            $news[$i]['source'] = $result->id ?? '';
            $news[$i]['section'] = $result->sectionName ?? '';
            $news[$i]['link'] = $result->webUrl ?? '';
            $news[$i]['publication_date'] = $result->webPublicationDate ?? '';
            if(!empty($result->fields))
            {
                $news[$i]['link'] = ($result->fields->shortUrl ? $result->fields->shortUrl : $result->webUrl) ?? '';
                $news[$i]['image'] = $result->fields->thumbnail ?? '';
                $news[$i]['summary_text'] = $result->fields->trailText ?? '';
            }

            $i++;
        }
        return response()->json(['success' => true, 'news' => $news], 200);
        
        // return $response;
    }

    function single(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        // if(empty($id))
        // {
        //     return response()->json(["message" => 'ID not found','status'=>'404']);
        // }
        $news = self::singleNewsFormat($request->id);
        return response()->json(['success' => true, 'news' => $news], 200);
    }

    public function pin(Request $request)
    {
        $request->validate([
            'news_id' => 'required',
        ]);
    
        $newsId = md5($request->input('news_id'));
        // return response()->json(['success' => true, 'newsData' => $newsId], 200);

        $newsData = self::singleNewsFormat('/'.$request->news_id);
    
        // $pinnedNews = session()->get('pinned_news', []);
        $pinnedNews = Cache::get('pinned_news', []);
        
        if (!isset($pinnedNews[$newsId])) {
            $pinnedNews[$newsId] = $newsData;
    
            // session(['pinned_news' => $pinnedNews]);
            Cache::put('pinned_news', $pinnedNews);
        }
    
        return response()->json(['success' => true, 'newsData' => Cache::get('pinned_news', [])], 200);
    }

    public function unpin(Request $request)
    {
        $request->validate([
            'news_id' => 'required',
        ]);

        $newsId = md5($request->input('news_id'));
    
        // $pinnedNews = session()->get('pinned_news', []);
        $pinnedNews = Cache::get('pinned_news', []);

        if (isset($pinnedNews[$newsId])) {
            unset($pinnedNews[$newsId]);

            Cache::put('pinned_news', $pinnedNews);
        }

        return response()->json(['pinned_news' => Cache::get('pinned_news', [])], 200);
    }

    public function getPinnedNews()
    {
        // $pinnedNews = session()->get('pinned_news', []);
        $pinnedNews = Cache::get('pinned_news', []);

        return response()->json(['pinned_news' => $pinnedNews], 200);
    }
    public function singleNewsFormat($id)
    {
        $guardian_api = config('app.guardian_key');
        $api = new GuardianAPI($guardian_api);
        $response = $api->singleItem()
            ->setId($id)
            ->setShowFields("thumbnail,short-url,trailText")
            ->fetch();
        $news = [];
        if(!empty($response->response->content))
        {
            $content = $response->response->content;
            $news['title'] = $content->webTitle ?? '';
            $news['source'] = $content->id ?? '';
            $news['section'] = $content->sectionName ?? '';
            $news['link'] = $content->webUrl ?? '';
            $news['publication_date'] = $content->webPublicationDate ?? '';
            if(!empty($content->fields))
            {
                $news['link'] = ($content->fields->shortUrl ? $content->fields->shortUrl : $content->webUrl) ?? '';
                $news['image'] = $content->fields->thumbnail ?? '';
                $news['summary_text'] = $content->fields->trailText ?? '';
            }
        }
        return $news;

    }
}
