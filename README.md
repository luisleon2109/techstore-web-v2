# TechStore POS — Sistema de Punto de Venta
**Versión 2.1 | PHP + MySQL | Desarrollado para XAMPP/WAMP**

---

## ¿Qué es esto?

TechStore POS es un sistema de gestión y punto de venta completo para una tienda de tecnología. Desarrollado en **PHP puro con patrón MVC** y **MySQL** como base de datos, está diseñado específicamente para correr en **XAMPP o WAMP** sin necesidad de instalar ningún framework, composer, ni npm. Solo copiás los archivos y funciona.

El sistema permite gestionar ventas, compras a proveedores, inventario, clientes, catálogo de productos, usuarios del sistema, y generar reportes en PDF y Excel — todo desde una interfaz moderna y responsive con ticket térmico integrado.

---

## ¿Qué incluye?

| Módulo | Descripción |
|--------|-------------|
| **Login con Roles** | 3 niveles de acceso: Administrador, Cajero, Almacén. Cada rol ve solo lo que le corresponde. |
| **Dashboard** | Estadísticas en tiempo real: ventas del día, del mes, alertas de stock bajo y productos agotados. |
| **Punto de Venta (POS)** | Carrito interactivo, búsqueda de productos, selección de cliente, 4 métodos de pago, IVA 13% automático, generación de factura y **ticket térmico para impresora de rollo**. |
| **Historial de Ventas** | Lista completa de ventas con filtro por fechas, detalle por venta, reimprimir ticket y anulación con restauración de stock automática. |
| **Clientes** | CRUD completo con historial de compras por cliente. Campos de CI, NIT y Razón Social para facturación. |
| **Compras** | Órdenes de compra a proveedores con actualización automática de stock e historial de movimientos. |
| **Proveedores** | CRUD completo con NIT, condición de pago, país y historial de órdenes de compra. |
| **Inventario** | Stock actual con alertas visuales, ajustes manuales y registro de todos los movimientos. |
| **Productos** | Alta, edición y baja de productos con stock inicial, precio de costo y precio de venta. |
| **Catálogo** | Gestión de País, Marca, Modelo y Tipo de Producto — la base jerárquica para registrar productos. |
| **Usuarios** | CRUD de usuarios del sistema: crear cajeros y almaceneros, asignar roles, activar/desactivar accesos y cambiar contraseñas. Solo visible para el Administrador. |
| **Reportes** | Ventas por rango de fechas e inventario completo, exportables a **PDF** y **Excel**. |

---

## Estructura de Carpetas

```
techstore/
│
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PosController.php
│   │   ├── VentasController.php       ← historial + anulación
│   │   ├── ComprasController.php
│   │   ├── ClientesController.php
│   │   ├── ProveedoresController.php  ← CRUD proveedores
│   │   ├── InventarioController.php
│   │   ├── ProductosController.php
│   │   ├── CatalogoController.php
│   │   ├── UsuariosController.php     ← gestión de usuarios
│   │   └── ReportesController.php
│   │
│   ├── Models/
│   │   └── UsuarioModel.php
│   │
│   ├── Views/
│   │   ├── auth/login.php
│   │   ├── pos/index.php              ← incluye ticket térmico integrado
│   │   ├── ventas/index.php           ← historial de ventas
│   │   ├── compras/index.php
│   │   ├── clientes/index.php
│   │   ├── proveedores/index.php
│   │   ├── inventario/index.php
│   │   ├── productos/index.php
│   │   ├── catalogo/index.php
│   │   ├── usuarios/index.php
│   │   ├── reportes/index.php
│   │   ├── dashboard.php
│   │   └── partials/                  ← header.php y footer.php compartidos
│   │
│   └── Helpers/
│       ├── Database.php               ← Conexión PDO singleton
│       ├── Auth.php                   ← Sesiones, roles y permisos
│       └── Response.php              ← Respuestas JSON para llamadas AJAX
│
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── components.css
│   └── js/
│       ├── app.js
│       └── modules/
│           ├── pos.js                 ← incluye lógica del ticket térmico
│           ├── ventas.js
│           ├── compras.js
│           ├── clientes.js
│           ├── productos.js
│           ├── proveedores.js
│           └── usuarios.js
│
├── config/
│   └── database.php                   ← ⚠️ EDITAR AQUÍ antes de instalar
│
├── database/
│   ├── techstore.sql                  ← Schema principal + datos de prueba
│   └── nuevas_tablas.sql              ← ⚠️ Ejecutar después del principal
│
├── vendor/
│   └── fpdf/
│       └── fpdf.php
│
└── public/
    ├── index.php                      ← Front Controller + Router
    └── .htaccess                      ← URLs limpias (requiere mod_rewrite)
```

---

## Instalación paso a paso en XAMPP

### Requisitos previos
- XAMPP instalado (versión 7.4 o superior recomendada)
- Apache y MySQL corriendo desde el panel de XAMPP
- Navegador moderno (Chrome, Firefox, Edge)

---

### Paso 1 — Copiar los archivos

Copiar toda la carpeta `techstore` dentro de:
```
C:\xampp\htdocs\techstore\
```

Al terminar, la ruta del archivo principal debe ser:
```
C:\xampp\htdocs\techstore\public\index.php
```

---

### Paso 2 — Crear la base de datos principal

1. Abre `http://localhost/phpmyadmin` en tu navegador
2. En el panel izquierdo, clic en **"Nueva"**
3. Escribe `techstore` como nombre y clic en **Crear**
4. Con la base de datos seleccionada, abrí la pestaña **SQL**
5. Abre `database/techstore.sql` con el Bloc de Notas, copiá todo y pegalo en phpMyAdmin
6. Clic en **Ejecutar**

Si todo fue bien deberías ver ~15 tablas creadas.

---

### Paso 3 — Ejecutar el SQL de módulos adicionales

**Este paso es obligatorio.** Sin él no funcionan el ticket térmico, el módulo de Proveedores ni el de Usuarios.

1. Con la BD `techstore` seleccionada, volvé a la pestaña **SQL**
2. Abre `database/nuevas_tablas.sql`, copiá todo y pegalo
3. Clic en **Ejecutar**

Esto crea la tabla `configuracion` con los datos de tu negocio para el ticket (nombre, dirección, teléfono, mensaje de gracias, etc.) y agrega el campo `activo` a la tabla `proveedor`.

---

### Paso 4 — Configurar la conexión

Abre `config/database.php` con cualquier editor y ajustá estos valores:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'techstore');
define('DB_USER', 'root');
define('DB_PASS', '');   // vacía por defecto en XAMPP
define('APP_URL',  'http://localhost/techstore');
```

> Si tu XAMPP tiene contraseña en MySQL, ponela en `DB_PASS`.

---

### Paso 5 — Activar mod_rewrite (URLs limpias)

El sistema usa URLs del tipo `/techstore/public/pos`. Para que funcionen:

1. Abre `C:\xampp\apache\conf\httpd.conf`
2. Buscá esta línea y quitá el `#`:
   ```apache
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Buscá el bloque `<Directory "C:/xampp/htdocs">` y cambiá `AllowOverride None` por `AllowOverride All`
4. Guardá y **reiniciá Apache** desde el panel de XAMPP

**¿No querés tocar la configuración?** Accedé directamente así:
```
http://localhost/techstore/public/index.php
```

---

### Paso 6 — Verificar FPDF

El sistema ya incluye FPDF en `vendor/fpdf/fpdf.php`. No necesitás hacer nada. Si el archivo no estuviera, descargalo desde http://www.fpdf.org/ y colocalo en esa ruta.

---

### Paso 7 — Abrir el sistema

```
http://localhost/techstore/public/
```

Deberías ver la pantalla de login.

---

## Usuarios de prueba

| Nombre | Email | Contraseña | Rol |
|--------|-------|------------|-----|
| Luis Adolfo | admin@techstore.bo | `password` | Administrador |
| María Torres | cajero@techstore.bo | `password` | Cajero |
| Pedro Quispe | almacen@techstore.bo | `password` | Almacén |

> **⚠️ Cambiar las contraseñas después de instalar.** Podés hacerlo directamente desde el módulo **Usuarios** dentro del sistema (solo visible para el Administrador), sin necesidad de tocar phpMyAdmin.

---

## Permisos por rol

| Módulo | Administrador | Cajero | Almacén |
|--------|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | ✅ |
| POS | ✅ | ✅ | ❌ |
| Historial de Ventas | ✅ | ✅ | ❌ |
| Clientes | ✅ | ✅ | ❌ |
| Compras | ✅ | ❌ | ✅ |
| Proveedores | ✅ | ❌ | ✅ |
| Inventario | ✅ | ❌ | ✅ |
| Productos | ✅ | ❌ | ✅ |
| Catálogo | ✅ | ❌ | ❌ |
| Reportes | ✅ | ❌ | ❌ |
| Usuarios | ✅ | ❌ | ❌ |

---

## El ticket térmico

Al confirmar una venta en el POS aparece automáticamente un modal con el ticket completo listo para imprimir. El botón **Imprimir** abre una ventana optimizada para **impresoras de rollo de 80mm**.

El ticket incluye:
- Nombre, dirección, teléfono y NIT del negocio
- Número de venta y número de factura
- Fecha, hora y cajero que realizó la venta
- Nombre, NIT y CI del cliente
- Detalle de productos: cantidad, precio unitario y subtotal
- Subtotal, IVA (13%) y total
- Método de pago utilizado
- Mensaje personalizado de agradecimiento y nota de garantía

También podés **reimprimir cualquier ticket** desde el módulo **Historial de Ventas** seleccionando cualquier venta pasada.

**Para personalizar los datos del negocio** editá la tabla `configuracion` en phpMyAdmin:

| Clave | Descripción |
|-------|-------------|
| `negocio_nombre` | Nombre del local que aparece en el ticket |
| `negocio_slogan` | Slogan o subtítulo |
| `negocio_direccion` | Dirección del local |
| `negocio_telefono` | Teléfono de contacto |
| `negocio_email` | Email de contacto |
| `negocio_nit` | NIT de la empresa |
| `ticket_mensaje` | Mensaje al pie del ticket (ej: ¡Gracias por su compra!) |
| `ticket_garantia` | Nota de garantía al pie |

---

## Datos de prueba incluidos en el SQL

- **14 productos**: iPhone 15 Pro Max, MacBook Pro M3 Max, Samsung S24 Ultra, iPad Pro M4, Sony BRAVIA A95L 65", LG C3 OLED, Sony WH-1000XM5, AirPods Pro 2, DJI Mini 3 Pro, Google Pixel 8 Pro, Apple Watch Ultra 2, Dell XPS 15, entre otros
- **5 proveedores**: TechDistrib Bolivia, GlobalTech Import, Apple Premium Reseller, Samsung Business Center, AV Import Tech
- **6 clientes**: 5 clientes con datos reales + "Consumidor Final" para ventas sin factura
- **7 tipos**: Smartphone, Laptop, Tablet, Televisor, Audio, Accesorio, Drone
- **7 marcas**: Apple, Samsung, Sony, LG, DJI, Google, Dell
- **3 usuarios**: Administrador, Cajero y Almacén listos para usar

---

## Cómo agregar un módulo nuevo

1. Crear `app/Controllers/NuevoController.php`
2. Crear `app/Views/nuevo/index.php`
3. Registrar la ruta en `public/index.php` dentro del array `$routes`:
   ```php
   'nuevo' => ['NuevoController', $action],
   ```
4. Agregar el permiso en `app/Helpers/Auth.php` dentro del array `$perms` para cada rol que lo necesite
5. Agregar el ícono y enlace en `app/Views/partials/header.php`
6. (Opcional) Crear `assets/js/modules/nuevo.js`

---

## Configuraciones frecuentes

**Cambiar el IVA:**
```php
// config/database.php
define('IVA_RATE', 0.13);  // 13% Bolivia — cambiá según tu país
```

**Cambiar la URL base (dominio propio o carpeta diferente):**
```php
// config/database.php
define('APP_URL', 'https://tudominio.com');
```
Y en `public/.htaccess`:
```apache
RewriteBase /                   # para dominio propio
RewriteBase /techstore/public/  # para subcarpeta local
```

---

## Solución de problemas comunes

| Problema | Solución |
|----------|----------|
| Página en blanco | Activar `display_errors = On` en `C:\xampp\php\php.ini` y reiniciar Apache para ver el error exacto |
| Error 404 en todas las URLs | Activar mod_rewrite (Paso 5) |
| `Cannot redeclare clean()` | Verificar que `public/index.php` envuelva la función con `if (!function_exists('clean'))` |
| Error de conexión a BD | Verificar que MySQL corra y que las credenciales en `config/database.php` sean correctas |
| "Class not found" | El nombre del controlador en el router no coincide exactamente con el nombre del archivo PHP |
| PDF no genera | Verificar que `vendor/fpdf/fpdf.php` exista |
| Ticket sin datos del negocio | Verificar que se ejecutó `database/nuevas_tablas.sql` en phpMyAdmin |
| Módulo Usuarios/Proveedores da error | Verificar que se ejecutó `database/nuevas_tablas.sql` y que los archivos de Controllers y Views están en su lugar |
| Fuentes distintas | El sistema usa Google Fonts, se necesita conexión a internet para cargarlas |
| Login no redirige bien | Verificar que `APP_URL` en `config/database.php` NO tenga `/public` al final |

---

## Arquitectura del sistema

```
Navegador (petición HTTP)
        │
        ▼
public/.htaccess  ──── redirige todo a index.php
        │
        ▼
public/index.php  ──── parsea la URL, determina módulo y acción
        │
        ▼
app/Controllers/[Módulo]Controller.php
        │   - verifica sesión y permisos (Auth::require / requireRole)
        │   - ejecuta la lógica de negocio
        │   - consulta la BD con PDO
        │
        ├── (petición normal)   → include app/Views/[módulo]/index.php → HTML
        └── (petición AJAX)     → Response::success() / error() → JSON
```

**¿Por qué PHP puro y no Laravel?**
Porque está hecho para correr en XAMPP sin instalar nada. Sin composer, sin npm, sin migraciones. Copiás los archivos, importás los dos SQL y listo.

---

## Tecnologías utilizadas

| Capa | Tecnología |
|------|------------|
| Backend | PHP 7.4+ sin frameworks |
| Base de datos | MySQL 5.7+ / MariaDB 10.3+ |
| Consultas | PDO con prepared statements (seguro contra SQL injection) |
| Frontend | HTML5 + CSS3 + JavaScript vanilla (sin jQuery) |
| Tipografía | Google Fonts — Syne + DM Sans |
| PDF | FPDF (sin dependencias externas) |
| Excel | Exportación TSV/XLS nativa |
| Servidor | Apache + mod_rewrite |

---

*TechStore POS v2.1 — PHP MVC para XAMPP*
