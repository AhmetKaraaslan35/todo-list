<?php

namespace App\Rules;

use App\Models\TodoList;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;

class CheckListOwner implements Rule
{
    public $todoListId;
    public $userId;
    public function __construct($request,$user)
    {   
            $this->userId = $user->id; 
            $this->todoListId = $request['todo_list_id'];
       
    }

    public function passes($attribute, $value)
    {

        if (isset($this->requestDepartmentId)) {
            $todoList = TodoList::where('id', $this->todoListId)->first();
            if($todoList->user_id == $this->userId){
                
            }else{
                return true;
            }
        }else{
            return true;
        }
    }

    public function message()
    {
        return 'Todo list user error.';
    }
}
