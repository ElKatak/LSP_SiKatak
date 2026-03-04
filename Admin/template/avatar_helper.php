<?php
/**
 * AVATAR HELPER
 * Menghasilkan HTML avatar bergaya Google (inisial + warna unik per nama).
 */

function get_initials(string $nama): string {
    $words = preg_split('/\s+/', trim($nama));
    $init  = '';
    foreach ($words as $w) {
        if ($w !== '') $init .= strtoupper($w[0]);
    }
    return substr($init, 0, 2) ?: '??';
}

function get_avatar_color(string $nama): string {
    $palette = [
        '#EF4444','#F97316','#EAB308','#22C55E',
        '#06B6D4','#3B82F6','#8B5CF6','#EC4899',
        '#14B8A6','#F43F5E',
    ];
    $hash = 0;
    for ($i = 0; $i < strlen($nama); $i++) {
        $hash = ord($nama[$i]) + (($hash << 5) - $hash);
    }
    return $palette[abs($hash) % count($palette)];
}

/**
 * @param string $nama    - nama user
 * @param int    $size    - ukuran px (default 36)
 * @param string $extra   - class tambahan
 */
function render_avatar(string $nama, int $size = 36, string $extra = ''): string {
    $bg      = get_avatar_color($nama);
    $initials = get_initials($nama);
    $font    = round($size * 0.38);
    return sprintf(
        '<div class="xtm-avatar %s" style="width:%dpx;height:%dpx;font-size:%dpx;background:%s;" title="%s">%s</div>',
        htmlspecialchars($extra), $size, $size, $font,
        htmlspecialchars($bg), htmlspecialchars($nama), htmlspecialchars($initials)
    );
}