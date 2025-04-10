<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Traits\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    use Helper;


    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'per_page' => ['nullable', 'integer']
        ]);

        if($validator->fails()) {
            return $this->send_response(
                false,
                [],
                $validator->errors(),
                'Validation Error',
                422
            );
        }
        ($request->per_page) ? $per_page = $request->per_page : $per_page = 10;

        $tasks = Task::where('user_id', auth()->user()->id)->paginate($per_page);
        return $this->send_response(
            true,
            $tasks,
            [],
            'Tasks retrieved successfully',
            200
        );
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255']
        ]);

        if($validator->fails()) {
            return $this->send_response(
                false,
                [],
                $validator->errors(),
                'Validation Error',
                422
            );
        }


        $task = Task::create([
            'title' => $request->title,
            'user_id' => auth()->user()->id,
            'status' => 'pending',
        ]);

        return $this->send_response(
            true,
            $task,
            [],
            'Task created successfully',
            200
        );


    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if(!$task) {
            return $this->send_response(
                false,
                [],
                [],
                'Task not found',
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:pending,in-progress,completed']
        ]);

        if($validator->fails()) {
            return $this->send_response(
                false,
                [],
                $validator->errors(),
                'Validation Error',
                422
            );
        }


        $task->update([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return $this->send_response(
            true,
            $task,
            [],
            'Task updated successfully',
            200
        );


    }

    public function destroy(Request $request, $id)
    {
        $task = Task::find($id);
        if(!$task) {
            return $this->send_response(
                false,
                [],
                [],
                'Task not found',
                404
            );
        }
        $task->delete();
        return $this->send_response(
            true,
            [],
            [],
            'Task deleted successfully',
            200
        );

    }


}
