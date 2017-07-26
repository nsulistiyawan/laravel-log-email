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
                    $data['errors']['input'] = $this->maskImportantData($app['request']->all());
                    $data['errors']['source'] = 'WEB APPLICATION';
                    $data['errors']['user'] = !empty($app['auth']->user()) ? $app['auth']->user()->username : 'Guest';
                }
                if(is_string($message) == false){
                    $data['errors']['reason'] = $message->getMessage();
                    $data['errors']['file'] = $message->getFile().':'.$message->getLine();
                }
                else{
                    $data['errors']['reason'] = $message;
                }
                Mail::send('logemail::layout', $data, function($message) use ($data, $recipient, $date_formatted){
                    $message->from('contact@fundplaces.com','Error Reporter');
                    $message->to($recipient,'Developer')->subject('Error Happened at '.$date_formatted);
                });
                Log::info('Error report send to '.$recipient);
            }
        }
    }

    private function maskAllString($target) {
        $output = substr_replace($target, str_repeat('*', strlen($target)), 0);
        return $output;
    }

    private function maskImportantData($array){
        if(is_array($array)){
            if(!empty($array['new_key'])){
                $array['new_key'] = maskAllString($array['new_key']);
            }
            if(!empty($array['old_key'])){
                $array['old_key'] = maskAllString($array['old_key']);
            }
            if(!empty($array['for_key'])){
                $array['for_key'] = maskAllString($array['for_key']);
            }
            elseif (!empty($array['key'])){
                $array['key'] = maskAllString($array['key']);
            }
            elseif (!empty($array['pin'])){
                $array['pin'] = maskAllString($array['pin']);
            }
            elseif (!empty($array['password'])){
                $array['password'] = maskAllString($array['password']);
            }
        }
        return $array;
    }



}
