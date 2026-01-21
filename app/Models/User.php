<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nik',
        'password',
        'departement',
        'jabatan',
        'status',
        'role_id',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Check if user has permission
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->permissions()
            ->where('permission_name', $permissionName)
            ->exists();
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    /**
     * Get default settings structure
     */
    public static function getDefaultSettings()
    {
        return [
            'theme' => [
                'mode' => 'light', // light, dark, auto
                'color_scheme' => 'blue',
            ],
            'language' => [
                'locale' => 'id', // id, en
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
            ],
            'notifications' => [
                'email_enabled' => true,
                'browser_enabled' => true,
                'sound_enabled' => true,
                'desktop_enabled' => false,
            ],
            'display' => [
                'rows_per_page' => 10,
                'compact_mode' => false,
                'animations_enabled' => true,
                'show_tooltips' => true,
            ],
            'accessibility' => [
                'font_size' => 'medium', // small, medium, large, xlarge
                'high_contrast' => false,
                'keyboard_navigation' => true,
                'screen_reader' => false,
            ],
            'layout' => [
                'sidebar_mode' => 'auto', // auto, always_open, always_collapsed
                'density' => 'comfortable', // compact, comfortable, spacious
            ],
            'dashboard' => [
                'default_page' => '/dashboard',
                'show_stats' => true,
                'show_charts' => true,
                'show_recent_activity' => true,
            ],
            'advanced' => [
                'auto_refresh' => false,
                'refresh_interval' => 60, // seconds
                'cache_enabled' => true,
            ],
        ];
    }

    /**
     * Get user settings merged with defaults
     */
    public function getSettings()
    {
        $defaults = self::getDefaultSettings();
        $userSettings = $this->settings ?? [];
        
        return array_replace_recursive($defaults, $userSettings);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting($key, $default = null)
    {
        $settings = $this->getSettings();
        $keys = explode('.', $key);
        
        foreach ($keys as $segment) {
            if (!is_array($settings) || !array_key_exists($segment, $settings)) {
                return $default;
            }
            $settings = $settings[$segment];
        }
        
        return $settings;
    }

    /**
     * Update a specific setting
     */
    public function updateSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $keys = explode('.', $key);
        $current = &$settings;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }
                $current = &$current[$segment];
            }
        }
        
        $this->settings = $settings;
        return $this->save();
    }
}