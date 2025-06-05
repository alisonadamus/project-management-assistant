<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PushSubscriptionController extends Controller
{
    /**
     * Створити або оновити push підписку
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'endpoint' => 'required|string|max:500',
                'keys.auth' => 'required|string',
                'keys.p256dh' => 'required|string'
            ]);

            $user = Auth::user();
            
            // Створюємо або оновлюємо підписку
            $user->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth']
            );

            Log::info('Push підписка створена/оновлена', [
                'user_id' => $user->id,
                'endpoint' => substr($validated['endpoint'], 0, 50) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push підписка успішно створена'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Невірні дані підписки',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Помилка створення push підписки', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Помилка створення підписки'
            ], 500);
        }
    }

    /**
     * Видалити push підписку
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'endpoint' => 'required|string'
            ]);

            $user = Auth::user();
            $user->deletePushSubscription($validated['endpoint']);

            Log::info('Push підписка видалена', [
                'user_id' => $user->id,
                'endpoint' => substr($validated['endpoint'], 0, 50) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push підписка успішно видалена'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Невірні дані',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Помилка видалення push підписки', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Помилка видалення підписки'
            ], 500);
        }
    }

    /**
     * Отримати статус push підписки
     */
    public function status(): JsonResponse
    {
        try {
            $user = Auth::user();
            $hasSubscriptions = $user->pushSubscriptions()->exists();

            return response()->json([
                'success' => true,
                'has_subscriptions' => $hasSubscriptions,
                'subscriptions_count' => $user->pushSubscriptions()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Помилка отримання статусу push підписки', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Помилка отримання статусу'
            ], 500);
        }
    }
}
