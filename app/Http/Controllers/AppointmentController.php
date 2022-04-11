<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\MailAppointmentCreatedMail;
use App\Http\Requests\AppointmentRequest;
use App\Mail\AppointmentAdminMail;
use App\Mail\AppointmentuserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointments = Appointment::get();
        return response()->json($appointments, 200);
    }

    public function getAdminsAppointments(Request $request){
        $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.admin_id WHERE admin_id = ".$request->get('admin_id'));
        return response()->json($appointments, 200);
    }

    public function getUsersAppointments(Request $request){
        $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.user_id WHERE user_id = ".$request->get('user_id'));
        return response()->json($appointments, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $appointment = new Appointment();
        $appointment->user()->associate($request->get('user_id'));
        $appointment->admin_id = $request->get('admin_id');
        $appointment->date = $request->get('date');
        $appointment->time = $request->get('time');
        $appointment->save();
        Mail::to("ataerg.web-designer@outlook.com")->send(new AppointmentAdminMail($appointment));
        Mail::to("ataerg.web-designer@outlook.com")->send(new AppointmentUserMail($appointment));
        return response()->json($appointment , 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $comment)
    {
        return response()->json($comment, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(AppointmentRequest $request, Appointment  $comment)
    {
        /*
        $comment->stars = $request->get('stars');
        $comment->content = $request->get('content');
        $comment->save();
        return response()->json($comment, 201);
        */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $comment)
    {
        /*
        if (Gate::denies('update', $comment)) {
            abort(403);
        }
        $comment->delete();
        return response()->json(null, 204);
        */
    }
}
