<?php

namespace Canigenus\CommonPhp\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model {
	protected $fillable = [ 
			"name",
			"logo_path",
			"client_id",
			"created_by",
			"updated_by" 
	];
}