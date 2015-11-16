<?php
namespace AbsoluteSoftware\Graylog2;

use AbsoluteSoftware\Graylog2\Interfaces\Graylog2Interface;
use Gelf\Message;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;

class Graylog2 implements Graylog2Interface
{
    protected $connections;
    protected $app;
    protected $publisher;
    protected $lastMessage;

    public function __construct()
    {
        $this->app['host'] = config('graylog2.app.host');
        $this->app['machine'] = config('graylog2.app.machine');
        $this->app['version'] = config('graylog2.app.version');

        $this->lastMessage = null;
        if(config('graylog2.log.type') == 'graylog2') {
            $this->publisher = new Publisher();

            $this->connections = config('graylog2.connections');
            foreach ($this->connections as $protocol => $con) {
                switch ($con['driver']):
                    case 'udp':
                        $transport = new UdpTransport($con['host'], $con['port'], UdpTransport::CHUNK_SIZE_LAN);
                        $this->publisher->addTransport($transport);
                        break;
                endswitch;
            }
        }
    }

    public function alert($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::ALERT, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function critical($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::CRITICAL, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function error($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::ERROR, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function warning($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::WARNING, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function notice($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::NOTICE, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function info($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::INFO, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function debug($shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $this->write(LogLevel::DEBUG, $shortMessage, $request, $exception, $facility, $timestamp, $fullMessage);
    }

    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    protected function write($level, $shortMessage, Request $request = null, $exception = null, $facility = null, $timestamp = null, $fullMessage = null)
    {
        $message = new Message();
        $message
            ->setHost($this->app['host'])
            ->setAdditional('app_machine', $this->app['machine'])
            ->setAdditional('app_version', $this->app['version'])
            ->setLevel($level)
            ->setShortMessage($shortMessage)
            ->setTimestamp((is_null($timestamp) ? time() : $timestamp))
        ;

        if(!is_null($request)) {
            $message
                ->setAdditional('request_url', $request->url())
                ->setAdditional('request_method', $request->method())
            ;
            if(config('graylog2.log.inputs.do')) {
                $message->setAdditional('request_inputs', json_encode($request->except(config('graylog2.log.inputs.except'))));
            }
        }

        if(!is_null($exception)) {
            $message
                ->setFullMessage($exception->getMessage())
                ->setFile($exception->getFile())
                ->setLine($exception->getLine())
                ->setAdditional('exception_code', $exception->getCode())
                ->setAdditional('exception_trace', $exception->getTraceAsString())
            ;
        }

        if(!is_null($fullMessage)) {
            $message->setFullMessage($fullMessage);
        }

        if(!is_null($facility)) {
            $message->setFacility($facility);
        }

        $this->lastMessage = $message;

        switch(config('graylog2.log.type')) {
            case 'graylog2':
                $this->publisher->publish($message);
                break;
            case 'file':
                $file = 'logs'.DIRECTORY_SEPARATOR.'graylog2.log';
                $message = $message->toArray();
                $log = [];
                $log[] = "\n";
                $log[] = '['.date('Y-m-d H:i:s', $message['timestamp']).']';
                $log[] = strtoupper($level).' in '.$message['file'].' at L'.$message['line'].':';
                $log[] = isset($message['facility']) ? "\n" . 'COMPONENT: ' . $message['facility'] : '';
                $log[] = "\r\n" .'SHORT_MESSAGE: ' . $message['short_message'];
                $log[] = isset($message['full_message']) ? "\n" . 'FULL_MESSAGE: ' . $message['full_message'] : '';
                $log[] = isset($message['_exception_trace']) ? "\n" . 'STRACKTRACE: ' . "\n" . $message['_exception_trace'] : '';
                $log = implode(" ", $log);

                if(!\Storage::disk('local')->exists($file)) {
                    \Storage::disk('local')->put($file, trim($log));
                } else {
                    \Storage::disk('local')->append($file, $log);
                }
                break;
        }

    }
}