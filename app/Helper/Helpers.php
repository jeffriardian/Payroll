<?php

if (! function_exists('getAuthDescription')) {
    function getAuthDescription($data)
    {
        $employee = getEmployeeFullName($data->employee_nik);
        $group = getAuthGroup($data->user_group_id);
        return "<b>".$employee."</b> login sebagai <b>".$group."</b>";
    }
}

if (! function_exists('getAuthGroup')) {
    function getAuthGroup($id)
    {
        $model = new Modules\Auth\Entities\UserGroup;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getAuthLevel')) {
    function getAuthLevel()
    {
        $model = new Modules\Auth\Entities\UserGroup;
        $data  = $model::where('id',Auth::user()->user_group_id)->first();
        return !empty($data) ? $data->slug : '';
    }
}

if (! function_exists('getServiceCategoryName')) {
    function getServiceCategoryName($id)
    {
        $model = new Modules\PurchaseOrder\Entities\ServiceCategory;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getMachineDetail')) {
    function getMachineDetail($id)
    {
        $model = new Modules\General\Entities\Machine;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name." ".$data->serial_number." ".$data->capacity  : '';
    }
}

if (! function_exists('getItemDetail')) {
    function getItemDetail($code)
    {
        $model = new Modules\Stock\Entities\Item;
        $data  = $model::where('code',$code)->first();
        return !empty($data) ? $data->code." - ".$data->detail : '';
    }
}

if (! function_exists('getGoodsReleaseStatusName')) {
    function getGoodsReleaseStatusName($id)
    {
        $model = new Modules\PublicWarehouse\Entities\GoodsReleaseStatus;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getCategoryName')) {
    function getCategoryName($id)
    {
        $model = new Modules\Stock\Entities\ItemCategory;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getSupplierName')) {
    function getSupplierName($code)
    {
        $model = new Modules\Supplier\Entities\Supplier;
        $data  = $model::where('code',$code)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getUnitName')) {
    function getUnitName($id)
    {
        $model = new Modules\General\Entities\Unit;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->symbol : '';
    }
}

if (! function_exists('getUnitConversionName')) {
    function getUnitConversionName($id)
    {
        $model = new Modules\Stock\Entities\ItemUnitConversion;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? getUnitName($data->unit_id) : '';
    }
}

if (! function_exists('getBrandName')) {
    function getBrandName($id)
    {
        $model = new Modules\Stock\Entities\Brand;
        $data  = $model::where('id',$id)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getWorkingAreaCode')) {
    function getWorkingAreaCode($nik)
    {
        $model = new Modules\Employee\Entities\WorkingArea;
        $workingArea = $model::query()
            ->where('employee_nik',$nik)
            ->where('is_current_working_area',1)
            ->first();

        return (!empty($workingArea)) ? $workingArea->company_working_area_code : "";
    }
}

if (! function_exists('getWorkingAreaName')) {
    function getWorkingAreaName($code)
    {
        $model = new Modules\Company\Entities\WorkingArea;
        $data  = $model::where('code',$code)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getCompanyPositionName')) {
    function getCompanyPositionName($code)
    {
        $model = new Modules\Company\Entities\Position;
        $data  = $model::where('code',$code)->first();
        return !empty($data) ? $data->name : '';
    }
}

if (! function_exists('getEmployeeFullName')) {
    function getEmployeeFullName($nik)
    {
        $model = new Modules\Employee\Entities\Identity;
        $data  = $model::where('employee_nik',$nik)->first();
        return !empty($data) ? $data->first_name." ".$data->last_name : '';
    }
}

if (! function_exists('getEmployeeDepartement')) {
    function getEmployeeDepartement($nik)
    {
        $working_area = "";
        $ModuleEmployeeWorkingArea = new Modules\Employee\Entities\WorkingArea;
        $ModuleCompanyWorkingArea  = new Modules\Company\Entities\WorkingArea;
        $EmployeeWorkingArea       = $ModuleEmployeeWorkingArea::where('employee_nik',$nik)->first();
        if (!empty($EmployeeWorkingArea)) {
            $CompanyWorkingArea  = $ModuleCompanyWorkingArea::where('code',$EmployeeWorkingArea->company_working_area_code)->first();
            $working_area        = !empty($CompanyWorkingArea) ? $CompanyWorkingArea->name : '';
        }

        return $working_area;
    }
}

if (! function_exists('getEmployeeDescription')) {
    function getEmployeeDescription($nik)
    {
        $employee_desc = $nik;
        $ModuleEmployeeWorkingArea = new Modules\Employee\Entities\WorkingArea;
        $ModuleEmployeeIdentity = new Modules\Employee\Entities\Identity;
        $ModuleCompanyWorkingArea = new Modules\Company\Entities\WorkingArea;

        $EmployeeWorkingArea  = $ModuleEmployeeWorkingArea::where('employee_nik',$nik)->first();

        if (!empty($EmployeeWorkingArea)) {
            $EmployeeIdentity  = $ModuleEmployeeIdentity::where('employee_nik',$EmployeeWorkingArea->employee_nik)->first();
            $full_name = !empty($EmployeeIdentity) ? $EmployeeIdentity->first_name." ".$EmployeeIdentity->last_name : '';
            $employee_desc .= !empty($employee_desc) ? " - ".$full_name : '';
        }

        if (!empty($EmployeeWorkingArea)) {
            $CompanyWorkingArea  = $ModuleCompanyWorkingArea::where('code',$EmployeeWorkingArea->company_working_area_code)->first();
            $working_area = !empty($CompanyWorkingArea) ? !empty($CompanyWorkingArea->name) ? "(".$CompanyWorkingArea->name.")" : '' : '';
            $employee_desc .= !empty($employee_desc) ? " ".$working_area : '';
        }
        return $employee_desc;
    }
}

if (! function_exists('getEmployeesByWorkingArea')) {
    function getEmployeesByWorkingArea($code)
    {
        $module = new Modules\Employee\Entities\WorkingArea;
        $data = $module::leftJoin('company_working_areas', 'company_working_areas.code', '=', 'employee_working_areas.company_working_area_code')
        ->leftJoin('employee_identities','employee_identities.employee_nik', '=', 'employee_working_areas.employee_nik')
        ->select("employee_working_areas.employee_nik as id",DB::raw("CONCAT(employee_working_areas.employee_nik,'-',employee_identities.first_name,' ',employee_identities.last_name,' (',company_working_areas.name,')') as text"))
        ->where('is_current_working_area',1)
        ->where('company_working_area_code',$code)
        ->get();

        return $data;
    }
}

if (! function_exists('getPOGrandTotal')) {
    function getPOGrandTotal($code)
    {
        $module = new Modules\PurchaseOrder\Entities\PurchaseOrderItems;
        $data = $module::query()
            ->select(DB::raw('sum(quantity*price) as total'))
            ->where('purchasing_purchase_order_code',$code)
            ->first();
        return $data->total;

    }
}

if (! function_exists('getItemAvgPrice')) {
    function getItemAvgPrice($code)
    {
        if (!empty($code)) {
            $PurchaseOrderItems = new Modules\PurchaseOrder\Entities\PurchaseOrderItems;
            $data2 = $PurchaseOrderItems::query()
                ->select('price','created_at')
                ->where('item_code',$code)
                ->where('price','>',0)
                ->orderBy('created_at','desc')
                ->skip(0)
                ->take(5)
                ->get();

                $PurchaseOrderDirectDetail = new Modules\PurchaseOrder\Entities\PurchaseOrderDirectDetail;
                $data = $PurchaseOrderDirectDetail::query()
                ->select('price','created_at')
                ->where('item_code',$code)
                ->where('price','>',0)
                ->orderBy('created_at','desc')
                ->skip(0)
                ->take(5)
                ->get();

            $collection =  collect([$data2, $data]);
            $collapsed  = $collection->collapse();
            $collapsed->all();
            $avg_price = $collapsed->sortByDesc('created_at')->take(5)->avg('price');
            return ($avg_price>0) ? $avg_price : 0;
        }
        return 0;
    }
}
