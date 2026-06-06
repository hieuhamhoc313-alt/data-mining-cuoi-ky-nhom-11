<?php

namespace App\Http\Controllers;

use App\Services\DataWarehouseService;
use Illuminate\Http\Request;

class DataWarehouseController extends Controller
{
    protected DataWarehouseService $warehouseService;

    public function __construct(DataWarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $summary = $this->warehouseService->getWarehouseSummary();
        $factStats = $summary['fact_table'];
        $dimLocation = $summary['dimension_location'];
        $dimLegal = $summary['dimension_legal'];
        $dimFurniture = $summary['dimension_furniture'];

        return view('warehouse.index', compact(
            'factStats',
            'dimLocation',
            'dimLegal',
            'dimFurniture'
        ));
    }

    public function icebergCube()
    {
        $icebergData = $this->warehouseService->getMultiDimensionalAnalysis();

        return view('iceberg.index', compact('icebergData'));
    }
}
