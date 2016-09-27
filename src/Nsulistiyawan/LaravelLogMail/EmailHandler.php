<?php
namespace Nsulistiyawan\LaravelLogMail;
use Mail;
use Log;

class EmailHandler
{
    protected $recipients;
    protected $level;

    public function __construct($recipients, $level)
    {
        $this->recipients = $recipients;
        $this->level = $level;
    }

    public function sendEmail($level, $message, $app){
        if(count($this->recipients) > 0 && $this->level == $level){
            foreach ($this->recipients as $recipient){
                $date = new \DateTime();
                $date_formatted = $date->format('d M Y H:i:s');
                $data['title'] = 'Error Happened at '.$date_formatted;
                $data['errors']['datetime'] = $date_formatted;
                $data['errors']['environment'] = $app->environment();
                if ($app->runningInConsole()){
                    $data['errors']['source'] = 'CONSOLE COMMAND';
                }
                else{
                    $data['errors']['accessed url'] = $app['request']->fullUrl();
                    $data['errors']['input'] = $app['request']->all();
                    $data['errors']['source'] = 'WEB APPLICATION';
                    $data['errors']['user'] = !empty($app['auth']->user()) ? $app['auth']->user()->username : 'Guest';
                }
                $data['errors']['reason'] = $message->getMessage();
                $data['errors']['file'] = $message->getFile().':'.$message->getLine();
                Mail::send('logemail::layout', $data, function($message) use ($data, $recipient, $date_formatted){
                    $message->from('contact@fundplaces.com','Error Reporter');
                    $message->to($recipient,'Developer')->subject('Error Happened at '.$date_formatted);
                });
                Log::info('Error report send to '.$recipient);
            }
        }
    }



}