<?php
Route::resource('{clientId}/website-settings',"WebsiteSettingController",['only' => [
    'edit', 'update'
]]);
Route::resource( '{clientId}/applicationproperties', "ApplicationPropertiesMultiTenantController");
