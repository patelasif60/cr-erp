<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\NewsFeed;
use DataTables;

class NewsFeedController extends Controller
{
	public function insertfeed(Request $request){
		
		
		if(!isset($request->exsisting_id)){
			$feed = new NewsFeed;
			$feed->feed_title = $request->get('feed_title');
			$feed->feed_description = $request->get('feed_description');
			$feed->feed_auth_name = $request->get('feed_auth_name');
			$feed->feed_auth_id = $request->get('feed_auth_id');
			$feed->save(); 
			return redirect('/home')->with('success', 'Feed inserted Sucessfully.');
		} else {
			$feed = NewsFeed::find($request->exsisting_id);
			$feed->feed_title = $request->get('feed_title');
			$feed->feed_description = $request->get('feed_description');
			$feed->feed_auth_name = $request->get('feed_auth_name');
			$feed->feed_auth_id = $request->get('feed_auth_id');
			$feed->save();
			return redirect('/home')->with('success', 'Feed Updated..');
		}
    }
	
	public function getnewsfeed(Request $request){
		
		if ($request->ajax()) {
            
			
			
			/*if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                $feedsearch = $request->get('search')['value'];
				//echo $feedsearch;
                $data = DB::table('news_update_feed')
                    ->orWhere('feed_title','LIKE','%'. $feedsearch .'%')
                    ->orWhere('feed_auth_name','LIKE','%'. $feedsearch .'%')
                    ->get();
            } else { */
                $data = NewsFeed::orderBy('created_at','DESC')->get();
            //}			
			
            return Datatables::of($data)		
					->addIndexColumn()
                    ->addColumn('action', function($row){
							$btn = '';
								$btn = '<a href="javascript:void(0)" onclick="editnewsfeed()" id="editnewsfeed" class="edit btn btn-primary btn-sm">Edit New Feed</a>';
                           
                            return $btn;
                    })
					 
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	
	public function editnewsfeed(Request $request){
		$id = $request->id;
		$feed = NewsFeed::find($id);
		return view('cranium.newsfeed.news_feed', ['feed' =>$feed]);
	}
}
