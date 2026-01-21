<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        return Inertia::render('Settings', [
            'settings' => $user->getSettings(),
            'defaultSettings' => User::getDefaultSettings(),
        ]);
    }

    /**
     * Get current user settings (API)
     */
    public function show()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'settings' => $user->getSettings(),
        ]);
    }

    /**
     * Update user settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validate the settings structure
        $validated = $request->validate([
            'theme' => 'sometimes|array',
            'theme.mode' => 'sometimes|in:light,dark,auto',
            'theme.color_scheme' => 'sometimes|string',
            
            'language' => 'sometimes|array',
            'language.locale' => 'sometimes|in:id,en',
            'language.timezone' => 'sometimes|string',
            'language.date_format' => 'sometimes|string',
            'language.time_format' => 'sometimes|string',
            
            'notifications' => 'sometimes|array',
            'notifications.email_enabled' => 'sometimes|boolean',
            'notifications.browser_enabled' => 'sometimes|boolean',
            'notifications.sound_enabled' => 'sometimes|boolean',
            'notifications.desktop_enabled' => 'sometimes|boolean',
            
            'display' => 'sometimes|array',
            'display.rows_per_page' => 'sometimes|integer|min:5|max:100',
            'display.compact_mode' => 'sometimes|boolean',
            'display.animations_enabled' => 'sometimes|boolean',
            'display.show_tooltips' => 'sometimes|boolean',
            
            'accessibility' => 'sometimes|array',
            'accessibility.font_size' => 'sometimes|in:small,medium,large,xlarge',
            'accessibility.high_contrast' => 'sometimes|boolean',
            'accessibility.keyboard_navigation' => 'sometimes|boolean',
            'accessibility.screen_reader' => 'sometimes|boolean',
            
            'layout' => 'sometimes|array',
            'layout.sidebar_mode' => 'sometimes|in:auto,always_open,always_collapsed',
            'layout.density' => 'sometimes|in:compact,comfortable,spacious',
            
            'dashboard' => 'sometimes|array',
            'dashboard.default_page' => 'sometimes|string',
            'dashboard.show_stats' => 'sometimes|boolean',
            'dashboard.show_charts' => 'sometimes|boolean',
            'dashboard.show_recent_activity' => 'sometimes|boolean',
            
            'advanced' => 'sometimes|array',
            'advanced.auto_refresh' => 'sometimes|boolean',
            'advanced.refresh_interval' => 'sometimes|integer|min:10|max:3600',
            'advanced.cache_enabled' => 'sometimes|boolean',
        ]);

        // Merge with existing settings
        $currentSettings = $user->settings ?? [];
        $newSettings = array_replace_recursive($currentSettings, $validated);
        
        // Update user settings
        $user->settings = $newSettings;
        $user->save();

        if ($request->header('X-Inertia')) {
            return back()->with('success', 'Settings updated successfully');
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'settings' => $user->getSettings(),
        ]);
    }

    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        $user = Auth::user();
        
        $user->settings = null;
        $user->save();

        if ($request->header('X-Inertia')) {
            return back()->with('success', 'Settings reset to default');
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings reset to default',
            'settings' => $user->getSettings(),
        ]);
    }
}
