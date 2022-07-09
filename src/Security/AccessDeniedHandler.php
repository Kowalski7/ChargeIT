<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        return new Response('<!DOCTYPE html><title>ChargeIT Admin Panel</title><div style="text-align: center"><h1>403 Forbidden</h1><p>You are not authorized to access the admin panel!</p></div>', 403);
    }
}