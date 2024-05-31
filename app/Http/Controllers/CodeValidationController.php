<?php

namespace App\Http\Controllers;

use App\Models\codeValidation;
use App\Http\Requests\StorecodeValidationRequest;
use App\Http\Requests\UpdatecodeValidationRequest;

class CodeValidationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorecodeValidationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorecodeValidationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\codeValidation  $codeValidation
     * @return \Illuminate\Http\Response
     */
    public function show(codeValidation $codeValidation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\codeValidation  $codeValidation
     * @return \Illuminate\Http\Response
     */
    public function edit(codeValidation $codeValidation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatecodeValidationRequest  $request
     * @param  \App\Models\codeValidation  $codeValidation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatecodeValidationRequest $request, codeValidation $codeValidation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\codeValidation  $codeValidation
     * @return \Illuminate\Http\Response
     */
    public function destroy(codeValidation $codeValidation)
    {
        //
    }
}
