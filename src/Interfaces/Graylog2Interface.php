<?php
namespace AbsoluteSoftware\Graylog2\Interfaces;

use Illuminate\Http\Request;

interface Graylog2Interface
{
    public function alert($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function critical($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function error($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function warning($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function notice($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function info($shortMessage, Request $request, $exception, $facility, $timestamp);
    public function debug($shortMessage, Request $request, $exception, $facility, $timestamp);
}