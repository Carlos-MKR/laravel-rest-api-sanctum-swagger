<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Employees API",
 *      description="API para gestion de empleados",
 *      @OA\Contact(
 *          email="saavedracarlos0066@gmail.com",
 *          name="Carlos Mankar"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Servidor API"
 * )
 * 
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


abstract class Controller
{
    //
}
