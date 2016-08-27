<?php
namespace Canigenus\CommonPhp\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationProperties extends Model{
	
	 protected $fillable = ['key', 'value','created_by', 'updated_by'];
}