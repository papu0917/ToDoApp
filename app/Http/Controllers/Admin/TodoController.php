<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ToDo;
use App\Category;
use Auth;
use Carbon\Carbon;

class TodoController extends Controller
{
    //
    public function add()
    {
        $categories = Category::all();
        return view('admin.todo.create', ['categories' => $categories]);
    }
    
    public function create(Request $request)
    {
        $this->validate($request, ToDo::$rules);
        $todo = new ToDo;
        $form = $request->all();
        
        unset($form['_token']);
        
        $todo->fill($form);
        $todo->user_id = Auth::id();
        $todo->is_complete = 0;
        $todo->save();
        
        return redirect('admin/todo/create');
    }
    
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
            $toDoQuery = ToDo::where('title', $cond_title);
        } else {
            $toDoQuery = ToDo::where('is_complete', 0);
        }
        
        $order = $request->order;
        if ($order != '') {
            $toDoQuery->orderBy('priority', $order);
        }
        
        $cond_name = $request->cond_name;
        if ($cond_name != '') {
            $toDoQuery->whereHas('category', function($query) use ($cond_name) {
                $query->where('name', $cond_name);
            });
        }
        
        $toDos = $toDoQuery->paginate(5);
        
        return view('admin.todo.index', ['posts' => $toDos, 'cond_title' => $cond_title, 'cond_name' => $cond_name]);
    }
    
    public function edit(Request $request)
    {
        $todo = ToDo::find($request->id);
        if (empty($todo)) {
            abort(404);
        }
        
        $categories = Category::all();
        
        return view('admin.todo.edit', ['todo_form' => $todo, 'categories' => $categories]);
    }
    
    public function update(Request $request)
    {
        $this->validate($request, ToDo::$rules);
        $todo = ToDo::find($request->id);
        $todo_form = $request->all();
        unset($todo_form['_token']);
        
        $todo->fill($todo_form)->save();
        
        return redirect('admin/todo');
    }
    
    public function delete(Request $request)
    {
        $todo = ToDo::find($request->id);
        $todo->delete();
        
        return redirect('admin/todo/');
    }
    
    public function complete(Request $request)
    {
        $todo = ToDo::find($request->id);
        $todo->is_complete = 1;
        $todo->save();
        
        return redirect('admin/todo/completed');
    }
    
    public function completed(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
            $posts = ToDo::where('title', $cond_title)->get();
        } else {
            $posts = ToDo::where('is_complete', 1)->get();
        }
        
        return view('admin.todo.completed', ['posts' => $posts, 'cond_title' => $cond_title]);
    }
    
    public function uncomplete(Request $request)
    {
        $todo = ToDo::find($request->id);
        $todo->is_complete = 0;
        $todo->save();
        
        return redirect('admin/todo/');
    }
}
