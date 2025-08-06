<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];  
    public function attendance() {
        return $this->hasMany(Attendance::class);
    }
    
    public function leaves() {
        return $this->hasMany(Leaves::class);
    }
    // protected $fillable = ['name', 'email', 'department', 'phone', 'hire_date', 'salary'];
}
