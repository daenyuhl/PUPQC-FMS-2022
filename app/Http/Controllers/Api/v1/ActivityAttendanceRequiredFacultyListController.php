<?php

//namespace App\Http\Controllers;

// For versioning modified namespace        - Always add it to new controller
namespace App\Http\Controllers\Api\v1;

// For versioning controller        - Always add it to new controller
use App\Http\Controllers\Controller;

use App\Models\ActivityAttendanceRequiredFacultyList;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ActivityAttendanceRequiredFacultyListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only active data
        // return ActivityAttendanceRequiredFacultyList::where('active_status', 'Active')->get();

        // All data
        return ActivityAttendanceRequiredFacultyList::all();

        // Return with relationships
        //return ActivityAttendanceRequiredFacultyList::with('user', 'created_by_user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'attendance_status' => 'required',
            'activity_id' => 'required',
            'faculty_id' => 'required'
        ]);

        return ActivityAttendanceRequiredFacultyList::create($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return ActivityAttendanceRequiredFacultyList::find($id);

        //return ActivityAttendanceRequiredFacultyList::with('user', 'created_by_user')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $faculty_id)
    {
        //
        $ActivityAttendanceRequiredFacultyList = ActivityAttendanceRequiredFacultyList::where('activity_id', 'like', '%'.$id.'%')
        ->where('faculty_id', 'like', '%'.$faculty_id.'%');
        $ActivityAttendanceRequiredFacultyList->update($request->all());

        return $request;
    }

    public function update_status(Request $request, $id)
    {
        //
        $ActivityAttendanceRequiredFacultyList = ActivityAttendanceRequiredFacultyList::find($id);
        $ActivityAttendanceRequiredFacultyList->update($request->all());

        return $ActivityAttendanceRequiredFacultyList;
    }


    public function edit($id)
    {
        // Default
        // return ActivityAttendanceRequiredFacultyList::find($id);

        return ActivityAttendanceRequiredFacultyList::with('user', 'created_by_user')->find($id);
    }

    public function restore(ActivityAttendanceRequiredFacultyList $ActivityAttendance, $id)
    {
        //
        $ActivityAttendance = ActivityAttendanceRequiredFacultyList::onlyTrashed()->where('id', $id)->restore();
        return ActivityAttendanceRequiredFacultyList::find($id);
    }

    public function destroy(ActivityAttendanceRequiredFacultyList $ActivityAttendance, $id)
    {
        //if the model soft deleted
        $ActivityAttendance = ActivityAttendanceRequiredFacultyList::find($id);

        $ActivityAttendance->delete();
        return $ActivityAttendance;
    }

    public function show_soft_deleted($all)
    {
        $ActivityAttendance = ActivityAttendanceRequiredFacultyList::onlyTrashed()->get();

        return $ActivityAttendance;
    }

    public function search($id)
    {

        return ActivityAttendanceRequiredFacultyList::where('activity_id', 'like', '%'.$id.'%')->get();
    }

    public function multi_insert(Request $request)
    {

        $data = $request->all();

        for($i=0; $i < count($data); $i++) {
            ActivityAttendanceRequiredFacultyList::create($data[$i]);
        }

        return [
            'message' => 'Multiple Insert Success.'
        ];
        
    }

    public function get_unrequired_faculty($activity_id)
    {
        $faculties_per_meeting = Faculty::select("*")
        ->whereNotIn('faculties.id', ActivityAttendanceRequiredFacultyList::select("activity_attendance_required_faculty_lists.faculty_id")
        ->rightJoin('faculties', 'faculties.id', '=', 'activity_attendance_required_faculty_lists.faculty_id')
        ->where('activity_attendance_required_faculty_lists.activity_id', $activity_id))
        ->get();

        return $faculties_per_meeting;
    }

    public function search_specific_activity_and_faculty($activity_id, $faculty_id)
    {
        return ActivityAttendanceRequiredFacultyList::where('activity_id', 'like', '%'.$activity_id.'%')
        ->where('faculty_id', 'like', '%'.$faculty_id.'%')
        ->get();
    }

    public function faculty_list_time_out_null($activity_id)
    {
        return ActivityAttendanceRequiredFacultyList::where('activity_id', 'like', '%'.$activity_id.'%')
        ->whereNull('time_out')
        ->whereNull('attendance_status')
        ->get();
    }

    public function get_all_activities_of_specific_categoryANDfaculty($faculty_id, $category)
    {
        $activities = ActivityAttendanceRequiredFacultyList::select(
            'activities.id', 
            'activities.created_at',
            'activities.created_by',
            'activities.updated_at',
            'activities.updated_by',
            'activities.deleted_at',
            'activities.title',
            'activities.location',
            'activities.start_datetime',
            'activities.end_datetime',
            'activities.agenda',
            'activities.description',
            'activities.is_required',
            'activities.status',
            'activities.memorandum_file_directory',
            'activity_attendance_required_faculty_lists.faculty_id',
            'activity_types.category',
            'activity_types.title'
        )
        ->join("activities", "activities.id", "=", "activity_attendance_required_faculty_lists.activity_id")
        ->join("activity_types", "activities.activity_type_id", "=", "activity_types.id")
        ->join("faculties", "activity_attendance_required_faculty_lists.faculty_id", "=", "faculties.id")
        ->where([
            ['activity_attendance_required_faculty_lists.faculty_id','=', $faculty_id],
            ['activity_types.category','=', $category]])
        ->get();

        return $activities;
    }
}
