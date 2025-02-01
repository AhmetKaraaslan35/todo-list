<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\TodoList;
use App\Models\TodoElement;
use App\Rules\CheckListOwner;
use Illuminate\Support\Facades\Validator;
class TodoElementController extends Controller
{
    public function store(Request $request)
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
            'todo' => ['bail', 'required', 'string'],
            'todo_list_id' => ['bail', 'required', 'exists:todo_lists,id', new CheckListOwner($request->all(),$user)],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'payload' => ['validation_errors' => $validator->messages()],
                'error' => true,
                'message' => __('Validation Error'),
            ], 400);
        }
        $validated = $validator->validated();

        $todoElement = new TodoElement();
        $todoElement->todo = $validated['todo'];
        $todoElement->todo_list_id = $validated['todo_list_id'];
        $todoElement->done = "false";

        if ($todoElement->save()) {
            return response()->json([
                'payload' => compact('todoElement'),
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

    public function update(Request $request, $todoElementId)
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
            'todo' => ['bail', 'sometimes', 'string'],
            'done' => ['bail', 'sometimes', 'in:true,false'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'payload' => ['validation_errors' => $validator->messages()],
                'error' => true,
                'message' => __('Validation Error'),
            ], 400);
        }
        $validated = $validator->validated();

        $todoElement = TodoElement::find($todoElementId);
      //  dd($todoElement);
        $todoList = TodoList::find($todoElement->todo_list_id);

        if ($todoList->user_id != $user->id) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }
        if (isset($validated['todo'])) {
            $todoElement->todo = $validated['todo'];
        }
        if (isset($validated['done'])) {
            $todoElement->done = $validated['done'];
        }

        if ($todoElement->save()) {
            return response()->json([
                'payload' => compact('todoElement'),
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

    public function destroy($todoElementId)
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $todoElement = TodoElement::find($todoElementId);
        if (isset($todoElement)) {


            $todoList = TodoList::find($todoElement->todo_list_id);

            if ($todoList->user_id != $user->id) {
                return response()->json([
                    'payload' => null,
                    'error' => true,
                    'message' => __('Insufficient Permissions'),
                ], 403);
            }

            if ($todoElement->delete()) {
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

    public function show($todoElementId)
    {
        $user = JWTAuth::user();
        if (empty($user)) {
            return response()->json([
                'payload' => null,
                'error' => true,
                'message' => __('Insufficient Permissions'),
            ], 403);
        }

        $todoElement = TodoElement::find($todoElementId);
        $todoList = TodoList::find($todoElement->todo_list_id);
        if (isset($todoElement)) {

            return response()->json([
                'payload' => compact('todoElement'),
                'error' => false,
                'message' => __('Success'),
            ], 200);

            if ($todoList->user_id != $user->id) {
                return response()->json([
                    'payload' => null,
                    'error' => true,
                    'message' => __('Insufficient Permissions'),
                ], 403);
            }
        } else {
            return response()->json([
                'payload' => [],
                'error' => false,
                'message' => __('Error'),
            ], 200);
        }
    }
}
