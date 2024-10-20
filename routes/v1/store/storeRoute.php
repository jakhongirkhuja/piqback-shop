<?php

use App\Http\Controllers\Api\v1\StoreLatest\DigitalStoreController;
use App\Http\Controllers\Api\v1\StoreLatest\KarzinkaBarcodeController;
use App\Http\Controllers\Api\v1\StoreLatest\PhysicalStoreController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreCategoryController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreProductController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreSellerController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreSellerTGController;
use App\Http\Controllers\Api\v1\StoreLatest\StoreSellerTotalController;
use App\Http\Middleware\CheckStandardAttributes;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    

    Route::prefix('store')->group(function(){

        
        Route::middleware([])->group(function () {
            Route::post('/storeSellerReports',[StoreController::class, 'storeSellerReports']);
            Route::get('/getStoresByType',[StoreController::class, 'getStoresByType'])->withoutMiddleware([CheckStandardAttributes::class]);
        });
        Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
            Route::get('/',[StoreController::class, 'storeList'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::get('/storeOwner',[StoreController::class, 'storeOwner'])->withoutMiddleware([CheckStandardAttributes::class]);
            Route::post('/edit/{store_id}',[StoreController::class, 'storeEditSubmit']);
            Route::post('/delete/{store_id}',[StoreController::class, 'storeDelete']);
            Route::post('/add',[StoreController::class, 'storeSubmit']);
        });
        Route::prefix('category')->group(function(){
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[StoreCategoryController::class, 'categoryList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/edit/{category_id}',[StoreCategoryController::class, 'categoryEditSubmit']);
                Route::post('/delete/{category_id}',[StoreCategoryController::class, 'categoryDelete']);
                Route::post('/add',[StoreCategoryController::class, 'categorySubmit']);
                
            });
        });
        Route::prefix('product')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[StoreProductController::class, 'storeProductList'])->withoutMiddleware([CheckStandardAttributes::class]);
                
                Route::post('/edit/{product_id}',[StoreProductController::class, 'storeProductEditSubmit']);
                Route::post('/delete/{product_id}',[StoreProductController::class, 'storeProductDelete']);
                Route::post('/deleteImage/{product_id}',[StoreProductController::class, 'storeProductImageDelete']);
                Route::post('/add',[StoreProductController::class, 'StoreProductSubmit']);
                Route::post('/buyProduct',[StoreProductController::class, 'buyProduct']);
                Route::get('/buyProduct/show',[StoreProductController::class, 'buyProductShow'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::get('/storeProductCode/{id}',[StoreProductController::class,'storeProductsCode'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::get('/storeProductCodeKorzinka/{id}',[StoreProductController::class,'storeProductsCodeKorzinka'])->withoutMiddleware([CheckStandardAttributes::class]);
                
            });
        });

        Route::prefix('seller')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[StoreSellerController::class, 'storeSellerList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/edit/{seller_id}',[StoreSellerController::class, 'storeSellerEditSubmit']);
                Route::post('/delete/{seller_id}',[StoreSellerController::class, 'storeSellerDelete']);
                Route::post('/add',[StoreSellerController::class, 'storeSellerSubmit']);
                Route::get('/reports',[StoreSellerController::class, 'reports'])->withoutMiddleware([CheckStandardAttributes::class]);
            });
        });
        Route::prefix('storeseller')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[StoreSellerTotalController::class, 'storeSellerTotalList'])->withoutMiddleware([CheckStandardAttributes::class]);
                // Route::post('/edit/{store_id}',[StoreSellerTotalController::class, 'storeSellerTotalEditSubmit']);
                Route::post('/delete/{storeSellerList_id}',[StoreSellerTotalController::class, 'storeSellerTotalDelete']);
                Route::post('/add',[StoreSellerTotalController::class, 'storeSellerSubmit']);
                
            });
        });

        Route::prefix('storesellertg')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[StoreSellerTGController::class, 'storeSellerTGList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/delete/{storeSellerTG_id}',[StoreSellerTGController::class, 'storeSellerTGDelete']);
                Route::post('/add',[StoreSellerTGController::class, 'storeSellerTGSubmit']);
            });
        });


        Route::prefix('physicalstore')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[PhysicalStoreController::class, 'physicalStoreList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/add',[PhysicalStoreController::class, 'physicalStoreSubmit']);
                Route::post('/edit/{store_id}',[PhysicalStoreController::class, 'physicalStoreEditSubmit']);
                Route::post('/delete/{store_id}',[PhysicalStoreController::class, 'physicalStoreDelete']);
                

                Route::get('/products',[PhysicalStoreController::class, 'physicalStoreProductList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/products/add',[PhysicalStoreController::class, 'physicalStoreProductSubmit']);
                Route::post('/products/edit/{product_id}',[PhysicalStoreController::class, 'physicalStoreProductEditSubmit']);
                Route::post('/products/delete/{product_id}',[PhysicalStoreController::class,'physicalStoreProductDelete']);
                Route::post('/products/image/delete/{product_id}',[PhysicalStoreController::class,'physicalStoreProductImageDelete']);
                
            });
        });
        Route::prefix('digitalstore')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[DigitalStoreController::class, 'digitalStoreList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/add',[DigitalStoreController::class, 'digitalStoreSubmit']);
                Route::post('/edit/{store_id}',[DigitalStoreController::class, 'digitalStoreEditSubmit']);
                Route::post('/delete/{store_id}',[DigitalStoreController::class, 'digitalStoreDelete']);
                
                Route::get('/products',[DigitalStoreController::class, 'digitalStoreProductList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/products/add',[DigitalStoreController::class, 'digitalStoreProductSubmit']);
                Route::post('/products/edit/{product_id}',[DigitalStoreController::class, 'digitalStoreProductEditSubmit']);
                Route::post('/products/delete/{product_id}',[DigitalStoreController::class, 'digitalStoreProductDelete']);
                Route::post('/products/image/delete/{product_id}',[DigitalStoreController::class,'digitalStoreProductImageDelete']);
                
                
            });
        });

        Route::prefix('korzinkabarcode')->group(function(){
            //middleware = auth:sanctum;
            Route::middleware(['auth:sanctum','checkStandardAttribute','checkAdminRoleMiddleware'])->group(function () {
                Route::get('/',[KarzinkaBarcodeController::class, 'karzinkaBarcodeList'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::get('/notUsed',[KarzinkaBarcodeController::class, 'karzinkaNotUsedBarcode'])->withoutMiddleware([CheckStandardAttributes::class]);
                Route::post('/add',[KarzinkaBarcodeController::class, 'karzinkaBarcodeSubmit']);
                Route::post('/edit/{karzinka_id}',[KarzinkaBarcodeController::class, 'karzinkaBarcodeEditSubmit']);
                Route::post('/delete/{karzinka_id}',[KarzinkaBarcodeController::class, 'karzinkaBarcodeDelete']);
            });
        });
    });

    
});
