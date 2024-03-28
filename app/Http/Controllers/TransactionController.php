<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = Transaction::orderBy('time', 'desc')->get();

        if ($transaction->isEmpty()) {
            return response()->json(
                [
                    'message' => 'Transaction not available',
                ],
                200,
            );
        }

        return response()->json(
            [
                'message' => 'List transaction order by time',
                'data' => $transaction,
            ],
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5|max:50',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,income',
        ]);

        if ($validator->fails()) {
            # code...
            return response()->json($validator->errors(), 422);
        }

        try {
            $transaction = Transaction::create($request->all());
            return response()->json([
                "message" => "Transaction successfully created",
                "data" => $transaction
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ])->header("Content-Type", "application/json");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            return response()->json([
                'message' => 'Detail transaction',
                'data' => $transaction,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $transaction = Transaction::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5|max:50',
            'amount' => 'required|numeric',
            'type' => 'required|in:expense,income',
        ]);

        if ($validator->fails()) {
            # code...
            return response()->json($validator->errors(), 422);
        }

        try {
            $transaction->update($request->all());
            return response()->json([
                "message" => "Transaction successfully updated",
                "data" => $transaction
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();

            return response()->json([
                'message' => "Successfully deleted transaction with ID $id"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => "Transaction with ID $id not found"
            ], 404);
        }
    }
}
