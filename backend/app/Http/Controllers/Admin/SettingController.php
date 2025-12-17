<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Get all settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        $group = $request->input('group');

        $query = Setting::language($language);

        if ($group) {
            $query->group($group);
        }

        $settings = $query->get();

        // Group by group name
        $grouped = $settings->groupBy('group')->map(function ($items) {
            return $items->mapWithKeys(function ($item) {
                return [$item->key => [
                    'id' => $item->id,
                    'value' => $item->getTypedValue(),
                    'type' => $item->type,
                ]];
            });
        });

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Get a specific setting by key.
     *
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($key)
    {
        $language = request()->input('language', 'en');
        
        $setting = Setting::language($language)->key($key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->getTypedValue(),
                'type' => $setting->type,
                'group' => $setting->group,
            ]
        ]);
    }

    /**
     * Update settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $language = $request->input('language', 'en');
        $settings = $request->input('settings', []);

        if (empty($settings)) {
            return response()->json([
                'success' => false,
                'message' => 'No settings provided'
            ], 422);
        }

        try {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    [
                        'language' => $language,
                        'key' => $key
                    ],
                    [
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'type' => $this->determineType($value),
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Determine the type of a value.
     *
     * @param  mixed  $value
     * @return string
     */
    private function determineType($value)
    {
        if (is_array($value)) {
            return 'json';
        } elseif (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } else {
            return strlen($value) > 255 ? 'textarea' : 'text';
        }
    }
}
