<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division_id',
        'district_id',
        'upazila_id',
        'union_id',
        'education_type_id',
        'profession_id',
        'religion_id',
        'gender_id',
        'country_id',
        'nid',
        'passport',
        'birth_certificate',
        'full_address',
        'dob',
        'is_autism',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class);
    }

    public function educationType()
    {
        return $this->belongsTo(EducationType::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function union()
    {
        return $this->belongsTo(Union::class);
    }
}
