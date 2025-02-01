<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\TodoList;
use App\Models\TodoElement;

class TodoListController extends Controller
{
    public function index()
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $todoLists = TodoList::where('user_id', $user->id)->with('todoElements')->get();
        if (isset($todoLists)) {
            return response()->json([
                'payload' => compact('todoLists'),
                'error' => false,
                'message' => __('Success'),
            ], 200);
        } else {
            return response()->json([
                'payload' => [],
                'error' => false,
                'message' => __('Error'),
            ], 200);
        }
    }

    public function show($todoListId)
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        } 
        $todoList = TodoList::where('id',$todoListId)->with('todoElements')->first();
 
        if (isset($todoList)) {
            if ($todoList->user_id != $user->id) {
                return response()->json([
                    'payload' => null,
                    'error' => true,
                    'message' => __('Insufficient Permissions'),
                ], 403);
            }

            return response()->json([
                'payload' => compact('todoList'),
                'error' => false,
                'message' => __('Success'),
            ], 200);
        } else {
            return response()->json([
                'payload' => [],
                'error' => false,
                'message' => __('Error'),
            ], 200);
        }
    }
    
    public function store(Request $request)
    {
        $user = JWTAuth::toUser();
  
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['bail', 'required', 'string'],
        ]);
        $validated = $validator->validated();
        if ($validator->fails()) {
            return response()->json([
                'payload' => [],
                'error' => true,
                'message' => __('Validation Error'),
            ], 400);
        }
        

        $todoList = new TodoList();
        $todoList->title = $validated['title'];
        $todoList->user_id = $user->id;

        if ($todoList->save()) {
            return response()->json([
                'payload' => compact('todoList'),
                'error' => false,
                'message' => __('Success'),
            ], 200);
        } else {
            return response()->json([
                'payload' => [],
                'error' => false,
                'message' => __('Error'),
            ], 200);
        }
    }

    public function update(Request $request, $todoListId)
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['bail', 'required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'payload' => ['validation_errors' => $validator->messages()],
                'error' => true,
                'message' => __('Validation Error'),
            ], 400);
        }
        $validated = $validator->validated();

        $todoList = TodoList::find($todoListId);

        if ($todoList->user_id != $user->id) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $todoList->title = $validated['title'];

        if ($todoList->save()) {
            return response()->json([
                'payload' => compact('todoList'),
                'error' => false,
                'message' => __('Success'),
            ], 200);
        } else {
            return response()->json([
                'payload' => [],
                'error' => false,
                'message' => __('Error'),
            ], 200);
        }
    }

    public function destroy($todoListId)
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $todoList = TodoList::find($todoListId);
        if (isset($todoList)) {

            if ($todoList->user_id != $user->id) {
                return response()->json([
                    'payload' => null,
                    'error' => true,
                    'message' => __('Insufficient Permissions'),
                ], 403);
            }
            $todoElements = TodoElement::where('todo_list_id', $todoListId)->get();
            foreach ($todoElements as $todoElement) {
                $todoElement->delete();
            }
            if ($todoList->delete()) {
                return response()->json([
                    'payload' => [],
                    'error' => false,
                    'message' => __('Success'),
                ], 200);
            } else {
                return response()->json([
                    'payload' => [],
                    'error' => false,
                    'message' => __('Error'),
                ], 200);
            }
        } else {
            return response()->json([
                'payload' => [],
                'error' => true,
                'message' => __('Not Found'),
            ], 404);
        }
    }
}
