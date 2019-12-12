<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Therapy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Schedule extends Model
{
    protected $fillable = ['start_at', 'end_at', 'therapy_id', 'parent_id', 'confirmed', 'person_id', 'doctor_id'];

    protected $dates = [
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'datetime:H:i',
        'end_at' => 'datetime:H:i',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function patient()
    {
        return $this->belongsTo(Individual::class, 'person_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function parent()
    {
        return $this->belongsTo(Schedule::class, 'parent_id');
    }

    public function reschedule()
    {
        return $this->hasOne(Schedule::class, 'parent_id');
    }

    public function therapy()
    {
        return $this->belongsTo(Therapy::class);
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    public static function getSchedulesFromInterval($doctorId, Carbon $from, Carbon $to)
    {
        return Schedule::query()
            ->where('start_at', '>=', $from->startOfDay())
            ->where('end_at', '<=', $to->endOfDay())
            ->where('doctor_id', $doctorId)
            // ->with(['patient', 'patient.person', 'reschedule'])
            ->with(['patient', 'patient.person'])
            ->get();
    }

    public static function getPatientFutureSchedules($personId)
    {
        return self::getFutureSchedules()
            ->where('person_id', $personId)
            ->get();
    }

    public static function getDoctorFutureSchedules($doctorId)
    {
        return self::getFutureSchedules()
            ->where('doctor_id', $doctorId)
            ->get();
    }

    protected static function getFutureSchedules()
    {
        return Schedule::query()
            ->where('deleted_at', null)
            ->where('absence_by', null)
            ->where('start_at', '>', Carbon::now())
            ->orderBy('start_at', 'ASC')
            ->doesntHave('reschedule')
            ->doesntHave('appointment');
    }
}
