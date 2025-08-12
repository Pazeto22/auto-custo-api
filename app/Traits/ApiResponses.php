<?php

namespace App\Traits;

trait ApiResponses
{
  protected function notFound($message = 'Recurso não encontrado')
  {
    return response()->json([
      'error' => true,
      'message' => $message
    ], 404);
  }

  protected function validationError($errors)
  {
    return response()->json([
      'error' => true,
      'message' => 'Erro de validação',
      'errors' => $errors
    ], 422);
  }

  protected function serverError($message = 'Erro interno do servidor')
  {
    return response()->json([
      'error' => true,
      'message' => $message
    ], 500);
  }

  protected function forbidden($message = 'Acesso negado')
  {
    return response()->json([
      'error' => true,
      'message' => $message
    ], 403);
  }

  protected function unauthorized($message = 'Não autorizado')
  {
    return response()->json([
      'error' => true,
      'message' => $message
    ], 401);
  }

  protected function success($data = null, $message = null, $code = 200)
  {
    $response = [];

    if ($data !== null) {
      $response['data'] = $data;
    }

    if ($message !== null) {
      $response['message'] = $message;
    }

    return response()->json($response, $code);
  }

  protected function created($data = null, $message = 'Recurso criado com sucesso')
  {
    return $this->success($data, $message, 201);
  }
}
