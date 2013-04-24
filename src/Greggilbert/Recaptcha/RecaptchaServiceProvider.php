<?php namespace Greggilbert\Recaptcha;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

/**
 * Service provider for the Recaptcha class
 * 
 * @author     Greg Gilbert
 * @link       https://github.com/greggilbert

 */
class RecaptchaServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('greggilbert/recaptcha');
		
		$this->addValidator();
		$this->addFormMacro();
	}
	
	/**
	 * Extends Validator to include a recaptcha type
	 */
	public function addValidator()
	{
		$validator = $this->app['Validator'];
		
		$validator::extend('recaptcha', function($attribute, $value, $parameters)
		{
			$challenge = app('Input')->get('recaptcha_challenge_field');
			
			$captcha = new CheckRecaptcha;
			list($passed, $response) = $captcha->check($challenge, $value);
			
			if('true' == trim($passed))
				return true;
			
			return false;
		});
	}
	
	/**
	 * Extends Form to include a recaptcha macro
	 */
	public function addFormMacro()
	{
		$form = $this->app['Form'];
		
		$form::macro('captcha', function()
		{
			$data = array(
				'public_key'	=> $this->app['config']->get('recaptcha::public_key'),
			);
			
			return $this->app['view']->make('recaptcha::captcha', $data);
		});
	}
	

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		
	}

}