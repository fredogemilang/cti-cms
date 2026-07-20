<?php

use App\Models\Form;
use App\Models\Setting;
use App\Models\Theme;
use App\Services\ActivityLogger;
use App\Services\ThemeLoader;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ViewErrorBag;

if (! function_exists('activity')) {
    /**
     * Get the central audit log writer.
     *
     * Usage:
     *   activity()->log('page.created', $page, "Created page '{$page->title}'");
     */
    function activity(): ActivityLogger
    {
        return app(ActivityLogger::class);
    }
}

if (! function_exists('translate')) {
    /**
     * Get a translated field value from a model for the current locale.
     * Falls back to the model's default-locale value if the translation is missing.
     */
    function translate(Model $model, string $field, ?string $locale = null): mixed
    {
        if (method_exists($model, 'getTranslation')) {
            return $model->getTranslation($field, $locale);
        }

        return $model->getAttribute($field);
    }
}

if (! function_exists('available_locales')) {
    /**
     * Return the list of locale codes the site supports.
     *
     * @return array<int, string>
     */
    function available_locales(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) Setting::get('available_locales', 'id,en')))));
    }
}

if (! function_exists('setting')) {
    /**
     * Get a CMS setting value (cache-backed).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('admin_path')) {
    /**
     * Get the admin path from config.
     */
    function admin_path(?string $path = null): string
    {
        $adminPath = config('admin.path', 'admin');

        if ($path) {
            return '/'.trim($adminPath, '/').'/'.ltrim($path, '/');
        }

        return '/'.trim($adminPath, '/');
    }
}

if (! function_exists('admin_url')) {
    /**
     * Generate a URL to an admin path.
     *
     * @param  mixed  $parameters
     */
    function admin_url(?string $path = null, $parameters = [], ?bool $secure = null): string
    {
        return url(admin_path($path), $parameters, $secure);
    }
}

if (! function_exists('active_theme')) {
    /**
     * Get the currently active theme.
     */
    function active_theme(): ?Theme
    {
        return app(ThemeLoader::class)->getActiveTheme();
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Get the URL to a theme asset.
     *
     * @param  string  $path  Path relative to theme assets directory
     */
    function theme_asset(string $path): string
    {
        $theme = active_theme();

        if (! $theme) {
            return '';
        }

        // Use Vite::asset if available, otherwise use asset()
        if (class_exists(Vite::class)) {
            try {
                return Vite::asset("themes/{$theme->slug}/assets/{$path}");
            } catch (Exception $e) {
                // Fall back to regular asset if Vite manifest not found
                return asset("themes/{$theme->slug}/assets/{$path}");
            }
        }

        return asset("themes/{$theme->slug}/assets/{$path}");
    }
}

if (! function_exists('theme_view')) {
    /**
     * Render a theme view.
     *
     * @param  string  $view  View name
     * @param  array  $data  Data to pass to view
     */
    function theme_view(string $view, array $data = []): View
    {
        $theme = active_theme();

        if (! $theme) {
            return view($view, $data);
        }

        $themeView = "themes::{$theme->slug}.{$view}";

        if (view()->exists($themeView)) {
            return view($themeView, $data);
        }

        return view($view, $data);
    }
}

if (! function_exists('theme_path')) {
    /**
     * Get the full path to the active theme directory.
     *
     * @param  string|null  $path  Optional path within theme directory
     */
    function theme_path(?string $path = null): string
    {
        $theme = active_theme();

        if (! $theme) {
            return '';
        }

        $basePath = base_path("themes/{$theme->slug}");

        if ($path) {
            return $basePath.'/'.ltrim($path, '/');
        }

        return $basePath;
    }
}

if (! function_exists('render_theme_form')) {
    /**
     * Render the form assigned to the active theme's placeholder.
     */
    function render_theme_form(string $placeholder): string
    {
        $theme = active_theme();
        if (! $theme) {
            return '';
        }

        $assignments = setting("theme_{$theme->slug}_form_assignments", []);
        $formId = $assignments[$placeholder] ?? null;

        if (! $formId) {
            return '';
        }

        $form = Form::where('id', $formId)
            ->where('is_active', true)
            ->with('fields')
            ->first();

        if (! $form) {
            return '';
        }

        $html = '';

        // Flash message handling
        if (session('success')) {
            $html .= '<div id="form-success-'.$form->slug.'" class="alert alert-success alert-dismissible fade show mb-4 elementor-message elementor-message-success" role="alert">';
            $html .= '<strong>Success!</strong> '.e(session('success'));
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= '</div>';
        }

        if (session('form_success_message')) {
            $html .= '<div id="form-success-'.$form->slug.'" class="alert alert-success alert-dismissible fade show mb-4 elementor-message elementor-message-success" role="alert">';
            $html .= '<strong>Success!</strong> '.e(session('form_success_message'));
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= '</div>';
        }

        $errors = session('errors');
        if ($errors instanceof ViewErrorBag && $errors->any()) {
            $html .= '<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">';
            $html .= '<strong>Please fix the following errors:</strong>';
            $html .= '<ul class="mb-0 mt-2">';
            foreach ($errors->all() as $error) {
                $html .= '<li>'.e($error).'</li>';
            }
            $html .= '</ul>';
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= '</div>';
        }

        $html .= $form->renderForm(['class' => 'needs-validation form-dynamic', 'novalidate' => true]);

        return $html;
    }
}
