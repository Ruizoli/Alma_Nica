<?php
require_once __DIR__ . '/../../config/roles.php';
function buscarUsuario(PDO $conexion, string $usuario): array|false
{
    $consulta = $conexion->prepare(
        'SELECT u.Id_Usuario, u.Id_Empleado, u.Contrasena, u.Rol, u.Estado,
                e.Nombre, e.Estado AS Empleado_Activo
            FROM Usuario u
            JOIN Empleado e ON e.Id_Empleado = u.Id_Empleado
            WHERE u.Usuario = ?'
    );
    $consulta->execute([$usuario]);
    return $consulta->fetch();
}

function buscarUsuarioActivo(PDO $conexion, int $id): array|false
{
    $consulta = $conexion->prepare(
        'SELECT e.Nombre, u.Rol, u.Id_Empleado, u.Contrasena
            FROM Usuario u
            JOIN Empleado e ON e.Id_Empleado = u.Id_Empleado
            WHERE u.Id_Usuario = ? AND u.Estado = 1 AND e.Estado = 1'
    );
    $consulta->execute([$id]);
    return $consulta->fetch();
}
