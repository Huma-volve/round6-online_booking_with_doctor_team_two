<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\faqs;
use App\Traits\ApiResponseTrait;
class FaqController extends Controller
{
        use ApiResponseTrait;

    //GET
    public function index()
    {
        $faqs = faqs::orderBy('order', 'asc')->get();

        return $this->successResponse($faqs, 'FAQs retrieved successfully');
    }
//Create 

   public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
            'order'    => 'nullable|integer',
             'status'   => 'nullable|in:active,inactive'
        ]);

        $faq = faqs::create($request->only('question', 'answer', 'order'));

            return $this->successResponse($faq, 'FAQ created successfully', 201);

    }


    //Update

     public function update(Request $request, $id)
    {
        $faq = faqs::findOrFail($id);

        $request->validate([
            'question' => 'sometimes|required|string',
            'answer'   => 'sometimes|required|string',
            'order'    => 'nullable|integer',
 'status'   => 'nullable|in:active,inactive'        ]);

        $faq->update($request->only('question', 'answer', 'order'));

              return $this->successResponse($faq, 'FAQ updated successfully');

    }
    //DEL

      public function destroy($id)
    {
        $faq = faqs::findOrFail($id);
        $faq->delete();

              return $this->successResponse(null, 'FAQ deleted successfully');

    }

}
