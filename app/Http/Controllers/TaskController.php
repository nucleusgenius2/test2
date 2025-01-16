<?php


namespace App\Http\Controllers;


use App\Models\Task;

use App\Traits\StructuredResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class TaskController
{
    use StructuredResponse;

    public int $perPageFrontend = 15;

    public function index(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'page' => 'integer|min:1',
        ]);

        if ($validated->fails()) {
            $this->text = $validated->errors();
        } else {
            $data = $validated->valid();

            $task = Task::orderBy('id', 'desc')->paginate($this->perPageFrontend, ['*'], 'page', $data['page'] ?? 1);

            if (count($task) > 0) {
                $this->status = 'success';
                $this->code = 200;
                $this->json = $task;
            } else {
                $this->code = 404;
                $this->text = 'Запрашиваемой страницы не существует';
            }

        }

        return $this->responseJsonApi();
    }


    public function store(Request $request): JsonResponse
    {

        $validated = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:255',
        ]);

        if ($validated->fails()) {
            $this->text = $validated->errors();
        } else {
            $data = $validated->valid();

            $response = Task::create([
                'title' => $data['title'],
            ]);

            if ($response) {
                $this->status = 'success';
                $this->code = 200;
            }
        }

        return $this->responseJsonApi();
    }


    public function update(int $id): JsonResponse
    {
        $validated = Validator::make(['id' => $id], [
            'id' => 'integer|min:1',
        ]);

        if ($validated->fails()) {
            $this->text = $validated->errors();
        } else {
            $data = $validated->valid();

            $task = Task::where('id', '=', $data['id'])->update([
                'is_completed' => 1,
            ]);

            if ($task) {
                $this->status = 'success';
                $this->code = 200;
                $this->text = 'Запись создана';
            }
            else {
                $this->text = 'Запрашиваемой записи не существует';
                $this->code = 404;
            }

        }

        return $this->responseJsonApi();
    }


    function destroy(int $id)
    {
        $validated = Validator::make(['id' => $id], [
            'id' => 'integer|min:1',
        ]);

        if ($validated->fails()) {
            $this->text = $validated->errors();
        } else {
            $data = $validated->valid();

            $task = Task::where('id',  $data['id'])->delete();
            if ($task) {
                $this->status = 'success';
                $this->code = 200;
            } else {
                $this->text = 'Запрашиваемой записи не существует';
                $this->code = 404;
            }

        }

        return $this->responseJsonApi();
    }
}
