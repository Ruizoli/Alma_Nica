<?php
/** Guarda fotos con nombres aleatorios y extensión no ejecutable. */
function guardarFotoProducto(array $archivo): ?string
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('No se pudo subir la foto. Usa JPG, PNG o WebP de hasta 2 MB.');
    }
    $temporal = $archivo['tmp_name'] ?? '';
    if (!is_uploaded_file($temporal) || filesize($temporal) > 2 * 1024 * 1024) {
        throw new InvalidArgumentException('La foto debe ser un archivo de hasta 2 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporal);
    $medidas = @getimagesize($temporal);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true) || !$medidas || ($medidas['mime'] ?? '') !== $mime) {
        throw new InvalidArgumentException('Selecciona una imagen JPG, PNG o WebP válida.');
    }
    if ($medidas[0] > 6000 || $medidas[1] > 6000 || $medidas[0] * $medidas[1] > 12000000) {
        throw new InvalidArgumentException('La imagen es demasiado grande. Usa un máximo de 12 megapíxeles y 6000 píxeles por lado.');
    }
    $directorio = dirname(__DIR__, 2) . '/storage/productos';
    if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
        throw new InvalidArgumentException('No se pudo crear la carpeta de imágenes.');
    }
    $nombre = bin2hex(random_bytes(24)) . '.bin';
    if (!move_uploaded_file($temporal, $directorio . '/' . $nombre)) {
        throw new InvalidArgumentException('No se pudo guardar la imagen.');
    }
    return $nombre;
}

function descartarFotoNueva(?string $nombre): void
{
    if ($nombre && preg_match('/^[a-f0-9]{48}\.bin$/D', $nombre)) {
        $ruta = dirname(__DIR__, 2) . '/storage/productos/' . $nombre;
        if (is_file($ruta)) unlink($ruta);
    }
}

function fotoProducto(?string $archivo): string
{
    return $archivo ? 'imagen.php?archivo=' . rawurlencode($archivo) : 'assets/img/producto-vacio.svg';
}
