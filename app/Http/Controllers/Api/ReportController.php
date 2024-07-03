<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index()
    {

        $reports = Report::orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'List Data Laporan',
            'data' => $reports,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required|integer',
            'description'     => 'required',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'name'      => $request->name,
            'price'     => $request->price,
            'description'     => $request->description,
        ];

        //create report
        $report = Report::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Laporan berhasil ditambahkan',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Laporan gagal ditambahkan",
            ], 400);
        }
    }

    public function show($id)
    {
        // Show Data
        $report = Report::find($id);

        return response()->json([
            'status' => 'Success',
            'message' => 'Data Laporan ditemukan',
            'data' => $report,
        ]);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required|integer',
            'description'     => 'required',
        ], [
            'name.required' => 'Nama harus diisi ngab',
            'price.required' => 'Harga harus diisi ngab',
            'description.required' => 'keterangan harus diisi ngab',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $report = Report::find($id);

        $data = [
            'name'      => $request->name,
            'price'     => $request->price,
            'description'     => $request->description,
        ];

        $report->update($data);

        if ($report) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Laporan berhasil diupdate',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Laporan gagal diupdate',
            ], 400);
        }
    }

    public function destroy($id)
    {
        $report = Report::find($id)->delete();

        if ($report) {
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Laporan gagal dihapus',
            ], 400);
        }
    }

    public function reportsByTime($time)
    {
        $query = Report::query();

        switch ($time) {
            case 'today';
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $query->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $reports = $query->orderBy('id', 'DESC')->get();


        $response = [
            'status' => 'Success',
            'message' => 'List Data Laporan',
            'reports' => $reports
        ];

        if ($reports) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }


    // Laporan Pemasukan

    public function incomeReportByTime($time)
    {

        $queryIncome = Order::query();

        switch ($time) {
            case 'today';
                $queryIncome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryIncome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryIncome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryIncome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $income = $queryIncome->sum('total_price');

        if ($income) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'List Data Laporan Pengeluaran',
                    'data' => $income
                ]
            );
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
                'data' => '0'
            ], 400);
        }
    }


    public function outcomeReportByTime($time)
    {

        $queryOutcome = Report::query();

        switch ($time) {
            case 'today';
                $queryOutcome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryOutcome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $outcome = $queryOutcome->sum('price');


        $response = [
            'status' => 'Success',
            'message' => 'List Data Laporan Pengeluaran',
            'data' => $outcome
        ];

        if ($outcome) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
                'data' => '0'
            ], 400);
        }
    }

    public function revenueReportByTime($time)
    {
        $queryIncome = Order::query();

        switch ($time) {
            case 'today';
                $queryIncome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryIncome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryIncome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryIncome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $income = $queryIncome->sum('total_price');

        $queryOutcome = Report::query();

        switch ($time) {
            case 'today';
                $queryOutcome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryOutcome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $outcome = $queryOutcome->sum('price');

        $revenue = $income - $outcome;


        $response = [
            'status' => 'Success',
            'message' => 'Laporan Keuntungan',
            'income' => $income,
            'outcome' => $outcome,
            'revenue' => "$revenue"
        ];

        if ($revenue) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }
}
