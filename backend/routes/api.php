<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TicketController;
use App\Models\Attachment;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function (): void {
    Route::middleware("throttle:auth")->group(function (): void {
        Route::post("/auth/register", [AuthController::class, "register"]);
        Route::post("/auth/login", [AuthController::class, "login"]);
        Route::post("/auth/forgot-password", [
            AuthController::class,
            "forgotPassword",
        ]);
    });
    Route::middleware("auth:sanctum")->group(function (): void {
        Route::get("/me", [AuthController::class, "me"]);
        Route::post("/auth/logout", [AuthController::class, "logout"]);
        Route::get("/dashboard", DashboardController::class);
        Route::get("/tickets/export", [TicketController::class, "export"]);
        Route::apiResource("tickets", TicketController::class);
        Route::post("/tickets/{ticket}/comments", [
            TicketController::class,
            "comment",
        ]);
        Route::post("/tickets/{ticket}/assign", [
            TicketController::class,
            "assign",
        ]);
        Route::post("/tickets/{ticket}/resolve", [
            TicketController::class,
            "resolve",
        ]);
        Route::post("/tickets/{ticket}/close", [
            TicketController::class,
            "close",
        ]);
        Route::get("/attachments/{attachment}/download", [
            TicketController::class,
            "downloadAttachment",
        ]);
    });
});
