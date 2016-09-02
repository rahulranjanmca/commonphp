<?php
namespace  Canigenus\CommonPhp\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class CommonServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->app->router->group(['namespace' => 'Canigenus\CommonPhp\Controllers'],
				function(){
					
					$value = config('app.canigenus_multitenant');
					
					info('Showing user profile for user: '.$value);
					if($value==true)
					{
					 require __DIR__.'/../Http/multitenant_routes.php';
					}
					else{
						require __DIR__.'/../Http/routes.php';
					}
				});
	}
	
	public function register() {
	/* 	$this->app->bind(
				'App\Http\Services\ProductService' ,        // Assuming you used these
				'App\Http\Serviceses\ProductRepositoryEloquentUserRepository' // namespaces 
				); */
	}
	
}