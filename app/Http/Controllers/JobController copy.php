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
        return Job::all();
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
            ]);

            $data = $request->all();
            if($request->hasFile('thumbnail')){
                $file = $request->file('thumbnail');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('upload/images/'.$filename);
                $data['thumbnail'] = $filename;
            }

            return Job::create($data);
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
        return Job::find($id);
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
        $job = Job::find($id);

        // $request->validate([
        //     'title'=>'required | max:255',
        //     'description'=>'required|string',
        // ]);

        // $data = $request->all();
        $job->title = $request->titile;
        $job->description = $request->description;
        $job->status = $request->status;
        if($request->hasFile('thumbnail')){
            $location = 'upload/images'.$job->thumbnail;
            if(File::exists($location)){
                File::delete($location);
            }

            $file = $request->file('thumbnail');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('upload/images/'.$filename);
            $job->thumbnail = $filename;
        }

        $job->update();

        return $job;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Job::destroy($id);
    }
}
