<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Mail\AppointmentAdminMail;
use App\Mail\AppointmentuserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\AppointmentAdminRequest;
use App\Http\Requests\AppointmentUserRequest;
use Illuminate\Support\Facades\Gate;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('isAdmin')) {
            $appointments = Appointment::get();
            return response()->json($appointments, 200);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    public function getAdminsAppointments(AppointmentAdminRequest $request)
    {
        if (Gate::allows('isAdmin')) {
            $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.admin_id WHERE admin_id = " . $request->get('admin_id'));
            return response()->json($appointments, 200);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    public function getUsersAppointments(AppointmentUserRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.user_id WHERE user_id = " . $request->get('user_id'));
            return response()->json($appointments, 200);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            $appointments = DB::select("SELECT * FROM appointments WHERE date = '" . $request->get('date') . "' AND time = '" . $request->get('time') . "'");
            if ($appointments != null) {
                return response()->json(['error' => 'La cita para esta hora ya existe, por favor seleccione otra hora']);
            }
            $appointment = new Appointment();
            $appointment->user()->associate($request->get('user_id'));
            $appointment->admin_id = $request->get('admin_id');
            $appointment->date = $request->get('date');
            $appointment->time = $request->get('time');
            $appointment->save();
            Mail::to("ataerg.web-designer@outlook.com")->send(new AppointmentAdminMail($appointment));
            Mail::to("ataerg.web-designer@outlook.com")->send(new AppointmentUserMail($appointment));
            return response()->json($appointment, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        if (Gate::allows('isAdmin') || Gate::allows('isUsers', $appointment)) {
            $appointment->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }
}
