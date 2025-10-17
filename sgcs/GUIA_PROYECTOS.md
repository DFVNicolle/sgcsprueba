# 📚 Guía de Uso: Creación de Proyectos

## 🎯 Funcionalidad Implementada

Sistema de creación de proyectos con **flujo de 2 pasos** y protección contra pérdida de datos.

---

## 🚀 Cómo Usar

### **Paso 1: Acceder al Formulario**
1. Desde el **Dashboard**, haz clic en el botón **"+ Nuevo Proyecto"**
2. Serás redirigido a `/proyectos/crear`

### **Paso 2: Información del Proyecto**
Completa los siguientes campos:

- **Código** (Opcional): Código único del proyecto (ej: `PROJ-2024-001`)
- **Nombre** (Requerido): Nombre descriptivo del proyecto
- **Descripción** (Opcional): Detalles del objetivo y alcance
- **Metodología** (Requerido): Selecciona entre:
  - Ágil (Scrum, Kanban)
  - Cascada (Waterfall)
  - Híbrida

**Botones:**
- **Cancelar**: Vuelve al dashboard sin guardar
- **Continuar al Paso 2**: Guarda los datos en sesión y avanza

---

### **Paso 3: Asignar Roles y Miembros**

#### 🔍 **Búsqueda de Usuarios**
- Escribe en el campo de búsqueda para filtrar por:
  - **Nombre completo**
  - **Correo electrónico**
- Haz clic en el usuario deseado para seleccionarlo
- El usuario seleccionado aparecerá en un badge azul

#### ➕ **Agregar Miembros**
1. Busca y selecciona un usuario
2. Selecciona el rol del usuario en el proyecto
3. Haz clic en **"+ Agregar Miembro"** para añadir más usuarios

#### ❌ **Eliminar Miembros**
- Haz clic en el botón ❌ rojo al lado derecho del miembro
- O usa el botón ❌ en el badge del usuario seleccionado para cambiar la selección

#### ✅ **Finalizar**
- Debes tener **al menos 1 miembro** para poder guardar
- Haz clic en **"Crear Proyecto"**
- Confirma la acción en el diálogo

**Botones:**
- **Cancelar Proceso**: Limpia la sesión y vuelve al dashboard (con confirmación)
- **Crear Proyecto**: Guarda el proyecto con todos los miembros

---

## 🔒 **Características de Seguridad**

### **1. Transacciones de Base de Datos**
- Si algo falla, **todo se revierte** automáticamente
- Garantiza integridad de datos

### **2. Gestión de Sesión**
- Los datos del Paso 1 se guardan en **sesión**
- Si sales sin completar:
  - Los datos permanecen temporalmente
  - Puedes volver a `/proyectos/crear/paso-2` para continuar
  - O cancela para limpiar la sesión

### **3. Validaciones**
- **Backend (Laravel)**:
  - Código único (si se proporciona)
  - Nombre requerido
  - Metodología válida
  - Al menos 1 miembro
  - Usuario y rol válidos por cada miembro

- **Frontend (JavaScript)**:
  - Impide envío sin miembros
  - Valida usuario seleccionado
  - Confirmación antes de guardar

---

## 📁 **Archivos Modificados/Creados**

### **Modelos** (`app/Models/`)
- ✅ `Proyecto.php` - Modelo principal
- ✅ `Rol.php` - Roles de usuarios
- ✅ `Equipo.php` - Equipos de proyecto
- ✅ `Usuario.php` - Relaciones agregadas

### **Controlador** (`app/Http/Controllers/gestionProyectos/`)
- ✅ `ProyectoController.php`
  - `create()` - Formulario Paso 1
  - `storeStep1()` - Guardar y avanzar al Paso 2
  - `assignRoles()` - Mostrar Paso 2 (GET)
  - `store()` - Guardar proyecto completo
  - `cancel()` - Cancelar proceso

### **Vistas** (`resources/views/gestionProyectos/`)
- ✅ `create.blade.php` - Formulario Paso 1
- ✅ `assign-roles.blade.php` - Formulario Paso 2 con búsqueda

### **Rutas** (`routes/web.php`)
```php
/proyectos/crear              [GET]  → Paso 1
/proyectos/crear/paso-1       [POST] → Procesar Paso 1
/proyectos/crear/paso-2       [GET]  → Paso 2 (requiere sesión)
/proyectos/guardar            [POST] → Crear proyecto
/proyectos/cancelar           [GET]  → Cancelar proceso
```

---

## 🐛 **Solución de Problemas**

### **Error: "Method Not Allowed GET /proyectos/crear/paso-1"**
✅ **Solucionado**: Ahora puedes acceder al Paso 2 con GET en `/proyectos/crear/paso-2`

### **No se veían los usuarios al hacer clic en "Agregar Miembro"**
✅ **Solucionado**: Implementado sistema de búsqueda interactiva con dropdown

### **El botón "Agregar Miembro" no hacía nada**
✅ **Solucionado**: Funcionalidad JavaScript actualizada y mejorada

---

## ✅ **Checklist de Testing**

- [ ] Crear proyecto desde el dashboard
- [ ] Validar campos requeridos en Paso 1
- [ ] Avanzar al Paso 2 correctamente
- [ ] Buscar usuarios por nombre
- [ ] Buscar usuarios por correo
- [ ] Seleccionar usuario y ver badge azul
- [ ] Agregar múltiples miembros
- [ ] Eliminar un miembro
- [ ] Intentar guardar sin miembros (debe mostrar alerta)
- [ ] Cancelar y verificar limpieza de sesión
- [ ] Crear proyecto exitosamente
- [ ] Verificar mensaje de éxito en dashboard
- [ ] Verificar que el proyecto se guardó en BD

---

## 📊 **Estructura de Base de Datos**

### **Tabla: `proyectos`**
- `id` (UUID)
- `codigo` (único, nullable)
- `nombre`
- `descripcion`
- `metodologia` (agil/cascada/hibrida)
- `creado_en`, `actualizado_en`

### **Tabla: `usuarios_roles`**
- `id`
- `usuario_id` (FK → usuarios)
- `rol_id` (FK → roles)
- `proyecto_id` (FK → proyectos, nullable)
- Índice único: `usuario_id + rol_id + proyecto_id`

---

## 🎨 **Estilo Visual**

- **Tema**: Blanco/Negro (consistente con el dashboard)
- **Stepper**: Progreso visual de 2 pasos
- **Búsqueda**: Dropdown interactivo con hover
- **Validación**: Mensajes claros en rojo
- **Confirmación**: Diálogos antes de acciones críticas

---

## 🔄 **Próximas Mejoras Sugeridas**

1. **Vista de Listado de Proyectos** (`/proyectos`)
2. **Vista de Detalles** (`/proyectos/{id}`)
3. **Edición de Proyectos** (`/proyectos/{id}/editar`)
4. **Asignación de Equipos** (Paso 3 opcional)
5. **Validación de usuarios duplicados** en el mismo proyecto
6. **Filtros avanzados** en la búsqueda de usuarios (por rol, departamento, etc.)

---

## 📞 **Soporte**

Si encuentras algún problema:
1. Verifica que las migraciones estén ejecutadas: `php artisan migrate`
2. Verifica que existan usuarios y roles en la BD
3. Limpia la caché: `php artisan cache:clear`
4. Limpia las vistas compiladas: `php artisan view:clear`

---

**¡Listo para usar!** 🎉
