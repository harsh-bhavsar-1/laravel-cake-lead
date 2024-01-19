<?php
use Illuminate\Support\Facades\Route;
use LaravelCake\Lead\Http\Controllers\LeadGenerateContoller;

Route::get('/test-lead', function(){
    echo 'Hello from the Lead package!';
});

Route::get('lead', [LeadGenerateContoller::class, 'index'])->name('index');
Route::get('lead/create', [LeadGenerateContoller::class, 'create'])->name('create');
Route::post('lead/store', [LeadGenerateContoller::class, 'store'])->name('store');

?>
