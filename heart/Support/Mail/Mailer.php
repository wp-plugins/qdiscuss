<?php namespace Qdiscuss\Support\Mail;

use Illuminate\Contracts\Mail\Mailer as IlluminateMailer;
//@todo
class Mailer implements IlluminateMailer
{

	/**
	 * Send a new message when only a raw text part.
	 *
	 * @param  string  $text
	 * @param  \Closure|string  $callback
	 * @return int
	 */
	public function raw($text, $callback)
	{

	}

	/**
	 * Send a new message using a view.
	 *
	 * @param  string|array  $view
	 * @param  array  $data
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public function send($view, array $data, $callback)
	{

	}

	/**
	 * Get the array of failed recipients.
	 *
	 * @return array
	 */
	public function failures()
	{

	}
}