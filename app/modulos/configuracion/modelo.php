<?php
function obtenerPerfil(PDO $db, int $id): array
{
    $q=$db->prepare('SELECT u.Usuario,e.Nombre FROM Usuario u JOIN Empleado e ON e.Id_Empleado=u.Id_Empleado WHERE u.Id_Usuario=?');
    $q->execute([$id]);
    return $q->fetch() ?: [];
}

function guardarPerfil(PDO $db, int $id, string $usuario, string $nombre): void
{
    $usuario=trim($usuario); $nombre=trim($nombre);
    if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/D',$usuario)) throw new InvalidArgumentException('El usuario debe tener de 3 a 50 letras, números, puntos, guiones o guiones bajos.');
    if ($nombre==='' || mb_strlen($nombre)>100) throw new InvalidArgumentException('Escribe un nombre de hasta 100 caracteres.');
    $db->beginTransaction();
    try {
        $q=$db->prepare('SELECT Id_Empleado FROM Usuario WHERE Id_Usuario=? FOR UPDATE'); $q->execute([$id]); $empleado=$q->fetchColumn();
        if (!$empleado) throw new InvalidArgumentException('La cuenta ya no está disponible.');
        $q=$db->prepare('UPDATE Usuario SET Usuario=? WHERE Id_Usuario=?'); $q->execute([$usuario,$id]);
        $q=$db->prepare('UPDATE Empleado SET Nombre=? WHERE Id_Empleado=?'); $q->execute([$nombre,$empleado]);
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
}
