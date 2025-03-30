<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Schema(
 *     schema="Employee",
 *     required={"name", "email", "birth_date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
 *     @OA\Property(property="phone", type="string", example="5512345678"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/employees",
     *     tags={"Employees"},
     *     summary="Obtener todos los empleados",
     *     description="Retorna una lista de todos los empleados registrados",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Employee")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontraron empleados")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $employess = Employee::all();
        
        if($employess->isEmpty()) {
            $data = [
                'message' => 'No se encontraron empleados',
                'status' => 200
            ];

            return response()->json($data, 404);
        }

        return response()->json($employess, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/employees",
     *     tags={"Employees"},
     *     summary="Crear un nuevo empleado",
     *     description="Crea un nuevo registro de empleado",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","birth_date"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="5512345678"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado creado",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al crear el empleado")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|digits:10',
            'birth_date' => 'required|date',
        ]);

        if($validate->fails()){
            $data = [
                'message' => 'Error en la validación',
                'errors' => $validate->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $employee = Employee::create($request->all());

        if(!$employee){
            $data = [
                'message' => 'Error al crear el empleado',
                'status' => 500
            ];

            return response()->json($data, 500);
        }

        $data = [
            'message' => 'Empleado creado',
            'employee' => $employee,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     tags={"Employees"},
     *     summary="Obtener un empleado específico",
     *     description="Retorna los datos de un empleado por su ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empleado no encontrado")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $data = [
            'message' => 'Empleado obtenido',
            'employee' => $employee,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{id}",
     *     tags={"Employees"},
     *     summary="Eliminar un empleado",
     *     description="Elimina un empleado por su ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado eliminado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empleado eliminado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $employee->delete();

        $data = [
            'message' => 'Empleado eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/employees/{id}",
     *     tags={"Employees"},
     *     summary="Actualizar un empleado completo",
     *     description="Actualiza todos los campos de un empleado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","birth_date"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="5512345678"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:employees,email,'.$employee->id,
            'phone' => 'nullable|digits:10',
            'birth_date' => 'required|date',
        ]);

        if($validate->fails()){
            $data = [
                'message' => 'Error en la validación',
                'errors' => $validate->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $employee->update($request->all());

        $data = [
            'message' => 'Empleado actualizado',
            'employee' => $employee,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/employees/{id}",
     *     tags={"Employees"},
     *     summary="Actualizar parcialmente un empleado",
     *     description="Actualiza campos específicos de un empleado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del empleado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="5512345678"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function updatePartial(Request $request, $id)
    {
        $employee = Employee::find($id);

        if(!$employee){
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'sometimes|max:255',
            'email' => 'sometimes|email|unique:employees,email,'.$employee->id,
            'phone' => 'sometimes|digits:10',
            'birth_date' => 'sometimes|date',
        ]);

        if($validate->fails()){
            $data = [
                'message' => 'Error en la validación',
                'errors' => $validate->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }

        $employee->update($request->only(array_keys($request->all())));

        $data = [
            'message' => 'Empleado actualizado',
            'employee' => $employee,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
