<?php
namespace myHttp\Actions;

use myHttp\Request;
use myHttp\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}