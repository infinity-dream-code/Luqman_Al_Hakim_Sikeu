<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CyberKey;
use App\Support\PersistentLogin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class SsoLoginController extends Controller
{
    private const AUTH_CONNECTION = 'mysql';

    public function login(Request $request): RedirectResponse
    {
        if (! class_exists(CyberKey::class)) {
            Log::error('SSO login: CyberKey model tidak ditemukan di server');

            return redirect()->route('login')->withErrors([
                'username' => 'Konfigurasi SSO belum lengkap di server (model CyberKey). Hubungi admin.',
            ]);
        }

        try {
            $token = (string) $request->query('token', '');

            if ($token === '') {
                return redirect()->route('login')->withErrors([
                    'username' => 'Token SSO tidak valid.',
                ]);
            }

            [$payload, $rejectReason] = $this->verifyToken($token);

            if ($payload === null) {
                Log::warning('SSO login rejected', ['reason' => $rejectReason]);

                return redirect()->route('login')->withErrors([
                    'username' => 'Sesi SSO tidak valid atau sudah kedaluwarsa. Silakan login kembali dari portal.',
                ]);
            }

            $username = trim((string) ($payload['sub'] ?? ''));

            if ($username === '') {
                return redirect()->route('login')->withErrors([
                    'username' => 'Token SSO tidak berisi data pengguna.',
                ]);
            }

            $user = CyberKey::on(self::AUTH_CONNECTION)
                ->whereRaw('LOWER(TRIM(users)) = ?', [strtolower($username)])
                ->first();

            if (! $user) {
                Log::warning('SSO login rejected: user not found', ['username' => $username]);

                return redirect()->route('login')->withErrors([
                    'username' => 'Akun tidak ditemukan.',
                ]);
            }

            Auth::guard('web')->login($user, false);
            PersistentLogin::queue($user);

            return redirect('/admin');
        } catch (Throwable $e) {
            Log::error('SSO login exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $message = config('app.debug')
                ? 'SSO error: '.$e->getMessage().' ('.class_basename($e).')'
                : 'Terjadi kesalahan saat proses SSO. Silakan login manual.';

            return redirect()->route('login')->withErrors([
                'username' => $message,
            ]);
        }
    }

  /**
   * @return array{0: ?array, 1: ?string}
   */
    private function verifyToken(string $token): array
    {
        $secret = config('services.portal_sso.secret');
        if (empty($secret)) {
            return [null, 'secret_not_configured'];
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return [null, 'malformed_token'];
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $signingInput = $headerEncoded.'.'.$payloadEncoded;
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $signingInput, $secret, true)
        );

        if (strlen($expectedSignature) !== strlen($signatureEncoded)) {
            return [null, 'signature_mismatch'];
        }

        if (! hash_equals($expectedSignature, $signatureEncoded)) {
            return [null, 'signature_mismatch'];
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        if (! is_array($payload)) {
            return [null, 'invalid_payload'];
        }

        $now = time();
        if (($payload['exp'] ?? 0) < $now) {
            return [null, 'token_expired'];
        }

        if (($payload['iat'] ?? 0) > ($now + 600)) {
            return [null, 'iat_in_future'];
        }

        return [$payload, null];
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
