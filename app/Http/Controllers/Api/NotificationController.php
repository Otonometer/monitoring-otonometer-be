<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponse\ErrorResponse\NotFoundErrorResponse;
use App\Classes\ApiResponse\SuccessResponse\CreatedResponse;
use App\Classes\ApiResponse\SuccessResponse\OKResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\Store as NotificationStoreRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request){
        $notification = Notification::orderByDesc('created_at');

        if ($request->query('take')) {
            $notification = $notification->take($request->query('take'));
        } else {
            $notification = $notification->take(3);
        }

        $notification = $notification->get();

        return (new OKResponse($notification, 1))->toResponse();
    }

    public function store(NotificationStoreRequest $request){
        $data = [
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'link_uri' => $request->link_uri,
        ];

        $image = $request->file('image');
        $imageext = $image->getClientOriginalExtension();
        $imagename = time() . '.' . $imageext;
        $path = 'images/notification';

        $image->move($path, $imagename);

        $data['image_uri'] = $path . '/' . $imagename;

        $book = Notification::create($data);
        return (new CreatedResponse($book))->toResponse();
    }
}
