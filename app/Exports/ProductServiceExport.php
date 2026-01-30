<?php

namespace App\Exports;

use App\Models\ProductService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductServiceExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $data = [];

        if(\Auth::user()->type =='company')
        {
            $data = ProductService::where('created_by' , \Auth::user()->id)->get();
        }
        else{
            $data = ProductService::get();
        } 
        
        if (!empty($data)) {
            foreach ($data as $k => $ProductService) {
                $taxNames = [];
                $taxIds = explode(',', $ProductService->tax_id);
                foreach ($taxIds as $taxId) {
                    $taxName = ProductService::Taxe($taxId); // Use your existing method
                    if (!empty($taxName)) {
                        $taxNames[] = $taxName;
                    }
                }
    
                $unit = ProductService::productserviceunit($ProductService->unit_id);
                $category = ProductService::productcategory($ProductService->category_id);
    
                unset(
                    $ProductService->id,
                    $ProductService->created_by,
                    $ProductService->updated_at,
                    $ProductService->created_at,
                    $ProductService->sale_chartaccount_id,
                    $ProductService->expense_chartaccount_id
                );
    
                // Assign readable values
                $data[$k]["tax_id"] = implode(', ', $taxNames);
                $data[$k]["unit_id"] = $unit;
                $data[$k]["category_id"] = $category;
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "SKU",
            "Sale Price",
            "Purchase Price",
            "Quantity",
            "Tax",
            "Category",
            "Unit",
            "Type",
            "Description",
        ];
    }
}
