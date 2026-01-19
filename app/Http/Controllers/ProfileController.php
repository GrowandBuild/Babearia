<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        \Log::info('Profile update method called!');
        \Log::info('Request data: ' . json_encode($request->all()));
        
        $user = $request->user();
        $user->fill($request->validated());

        // Salvar configuração de agenda
        if ($request->has('mostrar_agenda_comprometida')) {
            $user->mostrar_agenda_comprometida = true;
        } else {
            $user->mostrar_agenda_comprometida = false;
        }

        // Remover avatar se solicitado
        if ($request->has('remove_avatar')) {
            // Deletar avatar antigo
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
            
            // Se tiver profissional vinculado, remover também
            if ($user->profissional) {
                $user->profissional->update(['avatar' => null]);
            }
        }
        // Upload avatar se fornecido
        elseif ($request->hasFile('avatar')) {
            \Log::info('Upload de avatar iniciado', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'file_name' => $request->file('avatar')->getClientOriginalName(),
                'file_size' => $request->file('avatar')->getSize(),
                'file_type' => $request->file('avatar')->getMimeType()
            ]);
            
            // Deletar avatar antigo
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
                \Log::info('Avatar antigo deletado: ' . $user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            
            \Log::info('Avatar salvo com sucesso', [
                'avatar_path' => $avatarPath,
                'full_path' => storage_path('app/public/' . $avatarPath),
                'file_exists' => file_exists(storage_path('app/public/' . $avatarPath))
            ]);
            
            // Se tiver profissional vinculado, atualizar também
            if ($user->profissional) {
                $user->profissional->update(['avatar' => $avatarPath]);
            }
        }

        // --- Site logo and identidade visual ---
        // Remover logo se solicitado
        if ($request->has('remove_site_logo')) {
            if (\App\Models\Setting::get('site.logo')) {
                $old = \App\Models\Setting::get('site.logo');
                if (\Storage::disk('public')->exists($old)) {
                    \Storage::disk('public')->delete($old);
                }
            }
            \App\Models\Setting::set('site.logo', null);
        }

        // Upload da logo do site
        if ($request->hasFile('site_logo')) {
            if (\App\Models\Setting::get('site.logo')) {
                $old = \App\Models\Setting::get('site.logo');
                if (\Storage::disk('public')->exists($old)) {
                    \Storage::disk('public')->delete($old);
                }
            }
            $logoPath = $request->file('site_logo')->store('site', 'public');
            \App\Models\Setting::set('site.logo', $logoPath);
        }

        // --- Banner do Agendamento ---
        // Debug para verificar se está recebendo o arquivo
        \Log::info('Arquivos recebidos:', $request->allFiles());
        
        // Remover banner se solicitado
        if ($request->has('remove_site_banner')) {
            \Log::info('Removendo banner solicitado');
            if (\App\Models\Setting::get('site.banner')) {
                $old = \App\Models\Setting::get('site.banner');
                if (\Storage::disk('public')->exists($old)) {
                    \Storage::disk('public')->delete($old);
                }
            }
            \App\Models\Setting::set('site.banner', null);
        }

        // Upload do banner do agendamento
        if ($request->hasFile('site_banner')) {
            \Log::info('Banner recebido para upload');
            if (\App\Models\Setting::get('site.banner')) {
                $old = \App\Models\Setting::get('site.banner');
                \Log::info('Removendo banner antigo: ' . $old);
                if (Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }
            $bannerPath = $request->file('site_banner')->store('site', 'public');
            \App\Models\Setting::set('site.banner', $bannerPath);
            \Log::info('Banner salvo em: ' . $bannerPath);
        }

        // Processar banner URL
        if ($request->filled('site_banner_url')) {
            \App\Models\Setting::set('site.banner_url', $request->input('site_banner_url'));
            \Log::info('Banner URL salvo: ' . $request->input('site_banner_url'));
        }

        // Remover banner URL se solicitado
        if ($request->has('remove_site_banner_url')) {
            \App\Models\Setting::forget('site.banner_url');
            \Log::info('Banner URL removido');
        } else {
            \Log::info('Nenhum banner recebido');
        }

        // Cores da identidade
        if ($request->filled('primary_color')) {
            $val = $request->input('primary_color');
            \App\Models\Setting::set('brand.primary', $val);
        }
        if ($request->filled('secondary_color')) {
            $val = $request->input('secondary_color');
            \App\Models\Setting::set('brand.secondary', $val);
        }
        if ($request->filled('tertiary_color')) {
            $val = $request->input('tertiary_color');
            \App\Models\Setting::set('brand.tertiary', $val);
        }
        // Text colors (light/dark) for content readability
        if ($request->filled('text_color_light')) {
            \App\Models\Setting::set('brand.text_light', $request->input('text_color_light'));
        }
        if ($request->filled('text_color_dark')) {
            \App\Models\Setting::set('brand.text_dark', $request->input('text_color_dark'));
        }
        // Expanded palette
        if ($request->filled('accent_color')) {
            \App\Models\Setting::set('brand.accent', $request->input('accent_color'));
        }
        if ($request->filled('bg_color')) {
            \App\Models\Setting::set('brand.bg', $request->input('bg_color'));
        }
        if ($request->filled('surface_color')) {
            \App\Models\Setting::set('brand.surface', $request->input('surface_color'));
        }
        if ($request->filled('border_color')) {
            \App\Models\Setting::set('brand.border', $request->input('border_color'));
        }
        if ($request->filled('muted_color')) {
            \App\Models\Setting::set('brand.muted', $request->input('muted_color'));
        }
        if ($request->filled('success_color')) {
            \App\Models\Setting::set('brand.success', $request->input('success_color'));
        }
        if ($request->filled('warning_color')) {
            \App\Models\Setting::set('brand.warning', $request->input('warning_color'));
        }
        if ($request->filled('danger_color')) {
            \App\Models\Setting::set('brand.danger', $request->input('danger_color'));
        }
        if ($request->filled('info_color')) {
            \App\Models\Setting::set('brand.info', $request->input('info_color'));
        }
        if ($request->filled('text_on_primary')) {
            \App\Models\Setting::set('brand.on_primary', $request->input('text_on_primary'));
        }
        if ($request->filled('text_on_secondary')) {
            \App\Models\Setting::set('brand.on_secondary', $request->input('text_on_secondary'));
        }
        // Header and button colors
        if ($request->filled('header_bg')) {
            \App\Models\Setting::set('brand.header_bg', $request->input('header_bg'));
        }
        if ($request->filled('header_text')) {
            \App\Models\Setting::set('brand.header_text', $request->input('header_text'));
        }

        if ($request->filled('btn_primary_bg')) {
            \App\Models\Setting::set('brand.btn_primary_bg', $request->input('btn_primary_bg'));
        }
        if ($request->filled('btn_primary_text')) {
            \App\Models\Setting::set('brand.btn_primary_text', $request->input('btn_primary_text'));
        }
        if ($request->filled('btn_primary_hover')) {
            \App\Models\Setting::set('brand.btn_primary_hover', $request->input('btn_primary_hover'));
        }

        if ($request->filled('btn_secondary_bg')) {
            \App\Models\Setting::set('brand.btn_secondary_bg', $request->input('btn_secondary_bg'));
        }
        if ($request->filled('btn_secondary_text')) {
            \App\Models\Setting::set('brand.btn_secondary_text', $request->input('btn_secondary_text'));
        }
        if ($request->filled('btn_secondary_hover')) {
            \App\Models\Setting::set('brand.btn_secondary_hover', $request->input('btn_secondary_hover'));
        }

        // Solo mode: trabalho sozinho - só atualizar se for enviado
        if ($request->has('solo_mode')) {
            \App\Models\Setting::set('site.solo_mode', $request->boolean('solo_mode'));
        }

        // Public schedule mode: mostrar agenda pública aos clientes - só atualizar se for enviado
        if ($request->has('public_schedule_mode')) {
            \App\Models\Setting::set('site.public_schedule_mode', $request->boolean('public_schedule_mode'));
        }

        // Dark mode: tema escuro - só atualizar se for enviado
        if ($request->has('dark_mode')) {
            \App\Models\Setting::set('site.dark_mode', $request->boolean('dark_mode'));
        }

        // --- Company Settings ---
        // Company Name
        if ($request->has('company_name')) {
            $companyName = trim($request->input('company_name'));
            if (!empty($companyName)) {
                \App\Models\Setting::set('company_name', $companyName);
            }
        }

        // --- Client Appearance Settings ---
        // Client primary color
        if ($request->filled('client_primary_color')) {
            $color = $request->input('client_primary_color');
            if (!str_starts_with($color, '#')) {
                $color = '#' . $color;
            }
            \App\Models\Setting::set('client.primary_color', $color);
        }

        // Client modal theme
        if ($request->has('client_modal_theme')) {
            \App\Models\Setting::set('client.modal_theme', $request->input('client_modal_theme'));
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
