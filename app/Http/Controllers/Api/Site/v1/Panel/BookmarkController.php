<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Panel\Bookmark;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        return Bookmark::collection(
            auth()->user()->bookmarks()->with('order')->whereHas('order',function ($q){
                return $q->where('status',Order::IS_CONFIRMED);
            })->paginate(\request('per_page',15))
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => ['required',Rule::exists('orders','id')->where('status',Order::IS_CONFIRMED)]
        ],[
            'order_id' => 'آگهی'
        ]);

        if ($validator->fails()) {
            return response([
                'data' =>  [
                    'message' => $validator->errors()
                ],
                'status' => 'error'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return Bookmark::make(auth()->user()->bookmarks()->create($validator->validated()));
    }

    public function destroy(\App\Models\Bookmark $bookmark)
    {
        if ($bookmark->user_id == auth()->id())  {
            $deleted = $bookmark->delete();

            return response()->json([
                'data' => [
                    'message' => $deleted ? 'success' : 'error'
                ]
            ],$deleted ? 200 : 500);
        }

        return response()->json([
            'data' => [
                'message' => 'access denied'
            ]
        ],403);
    }
}
