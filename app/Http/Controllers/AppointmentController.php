<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Mail\AppointmentAdminMail;
use App\Mail\AppointmentuserMail;
use App\Mail\AppointmentDeletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\AppointmentAdminRequest;
use App\Http\Requests\AppointmentUserRequest;
use App\Http\Requests\AppointmentAdminDayTimeRequest;
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
            try{
                $appointments = Appointment::get();
                return response()->json($appointments, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error a la hora de mostrar todas citas.'], 500);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function show(Appointment $appointment)
    {
        try{
            return response()->json($appointment, '200');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error a la hora de obetener la cita'], 404);
        }
    }

    public function getAdminsAppointments(AppointmentAdminRequest $request)
    {
        if (Gate::allows('isAdmin')) {
            try{
                $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.admin_id WHERE admin_id = " . $request->get('admin_id'));
                return response()->json($appointments, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'No se ha podido obtener las citas del administrador'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function getAdminsAppointmentsWithTimeAndDay(AppointmentAdminDayTimeRequest $request)
    {
        try{
            $appointments = DB::select("SELECT appointments.* FROM appointments  WHERE admin_id = ? AND date = ? AND time = ?", [$request->get('admin_id'), $request->get('date'), $request->get('time')] );
            return response()->json($appointments, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se ha podido obtener los datos'], 401);
        }
    }


    public function getUsersAppointments(AppointmentUserRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            try{
                $appointments = DB::select("SELECT appointments.*, users.name, users.surname FROM users INNER JOIN appointments ON users.id = appointments.user_id WHERE user_id = " . $request->get('user_id'));
                return response()->json($appointments, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener las citas del usuario'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
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
            try{
                $appointments = DB::select("SELECT * FROM appointments WHERE date = '" . $request->get('date') . "' AND time = '" . $request->get('time') . "'AND user_id = " . $request->get('user_id'));
                if($appointments != null){
                    return response()->json(['error' => 'Ya tiene cita a esta hora'], 401);
                }
                $appointment = new Appointment();
                $appointment->user()->associate($request->get('user_id'));
                $appointment->admin_id = $request->get('admin_id');
                $appointment->date = $request->get('date');
                $appointment->time = $request->get('time');
                $appointment->save();
                $user = DB::select("SELECT email FROM users WHERE id = " . $request->get('user_id'));
                $admin = DB::select("SELECT email FROM users WHERE id = " . $request->get('admin_id'));
            } catch (\Exception $e){
                return response()->json(['error' => 'Error al crear la cita'], 401);
            }

            try{
                Mail::to($admin[0]->email)->send(new AppointmentAdminMail($appointment));
                Mail::to($user[0]->email)->send(new AppointmentUserMail($appointment));
            } catch(\Exception $e){
                return response()->json(['error' => 'Error al enviar los correos'], 401);
            } finally {
                return response()->json($appointment, 201);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function getDateOfAppointment(Appointment $appointment){
        if (Gate::allows('isAdmin') || Gate::allows('isUsers', $appointment)) {
            try{
                $appointment_date = DB::select("SELECT date, time  FROM appointments WHERE id = " . $appointment->id);
                return response()->json($appointment_date, 200);
            } catch(\Exception $e){
                return response()->json(['error' => 'Error al obtener la fecha de la cita'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
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

            try{
                $appointment->delete();
                $user = DB::select("SELECT email FROM users WHERE id = " . $appointment->user_id);
            } catch(\Exception $e){
                return response()->json(['error' => 'Error a la hora de eliminar la cita'], 401);
            }

            try{
                Mail::to($user[0]->email)->send(new AppointmentDeletedMail($appointment));
            } catch(\Exception $e){
                return response()->json(['error' => 'Error al enviar correo'], 401);
            } finally{
                return response()->json(['success' => 'Cita eliminada'], 200);
            }

        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }
}
