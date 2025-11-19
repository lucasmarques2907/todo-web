<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TodoListCollection extends ResourceCollection
{
    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'info' => [
                'current_page' => $paginated['current_page'],
                'last_page'    => $paginated['last_page'],
                'per_page'     => $paginated['per_page'],
                'total_lists'  => $paginated['total'],
            ],
        ];
    }
}
