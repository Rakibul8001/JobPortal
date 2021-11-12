<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Job list
        if(Auth::check()){
            return Job::all();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::check() && Auth::user()->role =='admin'){

            $request->validate([
                'title'=>'required | max:255',
                'description'=>'required|string',
                'status' => 'required'
            ]);

            $job = new Job;
            $job->user_id = Auth::user()->id;
            $job->title = $request->title;
            $job->description = $request->description;
            $job->status = $request->status;
            $filename = $request->thumbnail->store('public/uploads');
            $job->thumbnail = $filename;
            $job->save();

            return response()->json(['success' => true, 'message' => 'Job Post created successfully!', 
                                        'updated_data' => $job], 200);
        }
        return response()->json(['message'=>"Don't have admin access."]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::check()){
            return Job::find($id);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::check() && Auth::user()->role =='admin'){
            $job = Job::find($id);

            if ($request->hasFile('thumbnail')) {
                $logo = $request->thumbnail;
                $fileName = date('Y') . $logo->getClientOriginalName();
                $path = $request->thumbnail->storeAs('thumbnail', $fileName, 'public/uploads');
                $job['thumbnail'] = $path;
            }
            $job->user_id = Auth::user()->id;
            $job->update($request->except('thumbnail'));
        
            return response()->json(['success' => true, 'message' => 'Job Post updated successfully!', 
                               'updated_data' => $job], 200);
        }
        return response()->json(['message'=>"Don't have admin access."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::check() && Auth::user()->role =='admin')
        {
            Job::destroy($id);
            return response()->json(['success' => true, 
                'message' => 'Job Post deleted successfully!'], 200);
        }
        return response()->json(['message'=>"Don't have admin access."]);
    }
}
