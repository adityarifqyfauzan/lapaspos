<?php

namespace App\Http\Controllers\Cashier\Category;

use App\Http\Context\Category\CategoryContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CategoryListController extends Controller
{
    protected CategoryContextInterface $category_context;

    function __construct(CategoryContextInterface $category_context)
    {
        $this->category_context = $category_context;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {

            $resp = $this->category_context->getBy($request);
            return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
