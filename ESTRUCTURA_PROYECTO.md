# ESTRUCTURA DEL PROYECTO - DOCUMENTACIÓN PARA REPLICAR

## 🎯 INFORMACIÓN GENERAL

### Tipo de Proyecto
- **Framework**: Symfony 6.1
- **Lenguaje**: PHP 8.1+
- **Base de Datos**: PostgreSQL (con Docker)
- **Autenticación**: JWT (JSON Web Tokens) con Lexik JWT Bundle
- **Frontend**: Twig Templates + Bootstrap + jQuery
- **Arquitectura**: MVC con capa BLL (Business Logic Layer)

### Tecnologías Principales
```json
{
  "Backend": "Symfony 6.1",
  "ORM": "Doctrine ORM 2.19",
  "Autenticación": "Lexik JWT Authentication Bundle 2.18",
  "Validación": "Symfony Validator",
  "Formularios": "Symfony Forms",
  "Email": "Symfony Mailer + Google Mailer",
  "Testing": "PHPUnit 9.5",
  "Contenedores": "Docker Compose (PostgreSQL)"
}
```

---

## 📁 ESTRUCTURA DE CARPETAS COMPLETA

```
proyecto/
├── bin/                          # Ejecutables de consola
│   ├── console                   # CLI de Symfony
│   └── phpunit                   # Tests
│
├── config/                       # Configuración completa
│   ├── bundles.php              # Bundles registrados
│   ├── routes.yaml              # Rutas principales
│   ├── services.yaml            # Contenedor de servicios
│   ├── packages/                # Configuración de paquetes
│   │   ├── security.yaml        # 🔐 IMPORTANTE: Firewalls JWT
│   │   ├── doctrine.yaml        # Base de datos
│   │   ├── lexik_jwt_authentication.yaml
│   │   ├── framework.yaml
│   │   ├── twig.yaml
│   │   ├── validator.yaml
│   │   └── [otros paquetes]
│   ├── routes/                  # Rutas adicionales
│   └── jwt/                     # Claves JWT (private.pem, public.pem)
│
├── migrations/                   # Migraciones de BD (Doctrine)
│   ├── Version20251212142354.php
│   └── [múltiples versiones]
│
├── public/                      # Directorio público (Document Root)
│   ├── index.php               # Front Controller
│   ├── bootstrap/              # Bootstrap 3/4
│   ├── css/                    # Estilos personalizados
│   │   ├── style.css
│   │   └── magnific-popup.css
│   ├── js/                     # JavaScript
│   │   ├── jquery.min.js
│   │   ├── script.js
│   │   ├── scrollreveal.min.js
│   │   └── jquery.magnific-popup.min.js
│   ├── font-awesome/           # Iconos
│   └── images/                 # Imágenes del sitio
│       ├── index/              # Imágenes homepage
│       │   ├── portfolio/      # Portfolio
│       │   └── gallery/        # Galería
│       ├── clients/            # Logos clientes
│       ├── blog/               # Blog
│       └── [más carpetas]
│
├── src/                         # 🔥 CÓDIGO PRINCIPAL
│   ├── Kernel.php              # Kernel de Symfony
│   │
│   ├── Entity/                 # 🗃️ ENTIDADES DOCTRINE
│   │   ├── User.php            # Usuario (con seguridad)
│   │   ├── Imagen.php          # Imagen principal
│   │   ├── Categoria.php       # Categoría de imágenes
│   │   └── Asociado.php        # Asociados/Clientes
│   │
│   ├── Repository/             # 📚 REPOSITORIOS DOCTRINE
│   │   ├── UserRepository.php
│   │   ├── ImagenRepository.php
│   │   ├── CategoriaRepository.php
│   │   └── AsociadoRepository.php
│   │
│   ├── Controller/             # 🎮 CONTROLADORES WEB
│   │   ├── ProyectoController.php      # Páginas principales (/, /about, /blog, /contact)
│   │   ├── ImagenController.php        # CRUD de imágenes (formularios)
│   │   ├── ImagenController1.php       # Vista de imágenes
│   │   ├── LoginController.php         # Login web
│   │   ├── RegistrationController.php  # Registro
│   │   ├── DefaultController.php       # Controlador por defecto
│   │   └── API/                        # 🌐 API REST
│   │       ├── BaseApiController.php   # Controlador base API
│   │       ├── ImagenApiController.php # CRUD Imágenes API
│   │       └── UsuarioApiController.php # Registro y perfil API
│   │
│   ├── BLL/                    # 💼 BUSINESS LOGIC LAYER
│   │   ├── BaseBLL.php         # Lógica base (común)
│   │   ├── ImagenBLL.php       # Lógica de negocio imágenes
│   │   └── UsuarioBLL.php      # Lógica de negocio usuarios
│   │
│   ├── Form/                   # 📝 FORMULARIOS SYMFONY
│   │   └── [Tipos de formularios]
│   │
│   └── Security/               # 🔒 SEGURIDAD
│       └── [Voters, Authenticators]
│
├── templates/                   # 🎨 PLANTILLAS TWIG
│   ├── base.html.twig          # Plantilla base (layout)
│   ├── index.view.html.twig    # Homepage
│   ├── about.view.html.twig    # About
│   ├── blog.view.html.twig     # Blog
│   ├── contact.view.html.twig  # Contacto
│   ├── galeria.html.twig       # Galería
│   ├── parts/                  # Componentes parciales
│   │   ├── navegacion.part.html.twig
│   │   ├── imagenIndex.part.html.twig
│   │   └── nombres.part.html.twig
│   ├── imagen/                 # Plantillas CRUD imágenes
│   ├── login/                  # Login forms
│   └── registration/           # Registro forms
│
├── tests/                       # 🧪 TESTS (PHPUnit)
│   └── bootstrap.php
│
├── var/                         # Archivos temporales
│   ├── cache/                  # Caché de Symfony
│   └── log/                    # Logs
│
├── vendor/                      # Dependencias Composer
│
├── compose.yaml                 # Docker Compose (PostgreSQL)
├── compose.override.yaml
├── composer.json               # Dependencias PHP
├── phpunit.xml.dist            # Configuración PHPUnit
├── cursosym.sql                # 💾 Dump de base de datos
└── test.http                   # Tests HTTP (REST Client)
```

---

## 🗂️ ENTIDADES Y MODELO DE DATOS

### 1. **Entity/User.php**
```php
- Implementa: UserInterface, PasswordAuthenticatedUserInterface
- Campos:
  * id (int, autoincremental)
  * username (string 180, unique)
  * email (string 180)
  * password (string, hasheado)
  * roles (array) - ROLE_USER, ROLE_ADMIN
  * isVerified (boolean)
  * imagenes (OneToMany con Imagen)
```

### 2. **Entity/Imagen.php**
```php
- Campos:
  * id (int)
  * nombre (string 255) - Nombre del archivo
  * descripcion (string 255, nullable)
  * numLikes (int)
  * numVisualizaciones (int)
  * numDownloads (int)
  * fecha (DateTime, nullable)
  * categoria (ManyToOne con Categoria) - NO nullable
  * usuario (ManyToOne con User) - NO nullable
  
- Constantes:
  * RUTA_IMAGENES_PORTFOLIO = 'images/index/portfolio/'
  * RUTA_IMAGENES_GALERIA = 'images/index/gallery/'
  * RUTA_IMAGENES_CLIENTES = 'images/clients/'
  
- Métodos:
  * getUrlPortfolio()
  * getUrlGaleria()
  * getUrlClientes()
```

### 3. **Entity/Categoria.php**
```php
- Campos:
  * id (int)
  * nombre (string 100)
  * imagenes (OneToMany con Imagen)
```

### 4. **Entity/Asociado.php**
```php
- Campos:
  * id (int)
  * nombre (string 255)
  * logo (string 255)
  * descripcion (string 255)
```

---

## 🎮 CONTROLADORES Y RUTAS

### A) CONTROLADORES WEB (HTML/Twig)

#### **ProyectoController.php** - Páginas principales
```
Rutas:
- GET  /                → index()        - Homepage con imágenes
- GET  /about           → about()        - About con clientes
- GET  /blog            → blog()         - Blog con artículos
- GET  /contact         → contact()      - Contacto
```

#### **ImagenController.php** - CRUD Imágenes (Formularios)
```
Prefijo: /imagen

Rutas:
- GET     /                      → app_imagen_index (lista)
- GET     /orden/{ordenacion}    → app_imagen_index_ordenado
- GET     /new                   → app_imagen_new (formulario)
- POST    /new                   → app_imagen_new (crear)
- POST    /busqueda              → app_imagen_index_busqueda
- GET     /{id}                  → app_imagen_show
- GET     /edit/{id}             → app_imagen_edit
- POST    /edit/{id}             → app_imagen_edit
- POST    /delete/{id}           → app_imagen_delete
- DELETE  /{id}                  → app_imagen_delete_json
```

#### **ImagenController1.php** - Vista de imágenes
```
Rutas:
- GET  /imagen             → app_imagen (vista)
- GET  /imagen/{id}        → sym_imagen_show (detalle)
- GET  /galeria            → app_imagen_galeria
```

#### **LoginController.php**
```
Rutas:
- GET   /login      → app_login
- GET   /logout     → app_logout
```

#### **RegistrationController.php**
```
Rutas:
- GET   /register        → app_register
- POST  /register        → app_register (crear usuario)
- GET   /verify/email    → app_verify_email
```

---

### B) CONTROLADORES API REST (JSON)

#### **API/UsuarioApiController.php**
```
Prefijo: /api

Rutas:
- POST   /auth/register         → Registro de usuario
- PATCH  /profile/password      → Cambiar contraseña
```

#### **API/ImagenApiController.php**
```
Prefijo: /api

Rutas:
- GET    /prueba                           → Prueba API
- POST   /imagenesapinueva                 → Crear imagen
- GET    /imagenesapi                      → Listar todas
- GET    /imagenesapi/ordenadas/{order}    → Listar ordenadas
- GET    /imagenesapi/{id}                 → Obtener una
- PUT    /imagenesapi/{id}                 → Actualizar
- DELETE /imagenesapi/{id}                 → Eliminar

Parámetros de filtrado:
- ?descripcion=texto
- ?fechaInicial=yyyy-mm-dd
- ?fechaFinal=yyyy-mm-dd
```

---

## 💼 CAPA BLL (BUSINESS LOGIC LAYER)

### **BaseBLL.php**
Clase base con métodos comunes para toda la lógica de negocio:
- Acceso a EntityManager
- Acceso a RequestStack
- Acceso a Security (usuario actual)

### **ImagenBLL.php**
Lógica de negocio para imágenes:
```php
Métodos:
- nueva(array $data): Imagen
- actualizaImagen(Imagen $imagen, array $data): Imagen
- delete(Imagen $imagen): void
- toArray(Imagen $imagen): array
- getImagenes(string $order, $descripcion, $fechaInicial, $fechaFinal): array
- getImagenesConOrdenacion(?string $ordenacion): array
- checkAccessToImagen(Imagen $imagen): void
```

### **UsuarioBLL.php**
Lógica de negocio para usuarios:
```php
Métodos:
- registra(array $data): User
- cambiaPassword(array $data): void
- getCurrentUser(): User
```

---

## 🔐 SEGURIDAD Y AUTENTICACIÓN

### Configuración JWT (config/packages/security.yaml)

#### **Firewalls**:
```yaml
1. dev: Sin seguridad para profiler
2. login: 
   - Pattern: ^/api/auth/login
   - Tipo: json_login
   - Genera token JWT
   
3. api:
   - Pattern: ^/api
   - Protegido con JWT
   - Stateless: true
   
4. main:
   - Aplicación web normal
   - Provider: app_user_provider (Entity User)
```

#### **Access Control**:
```yaml
- /api/auth/login        → PUBLIC_ACCESS
- /api/auth/register     → PUBLIC_ACCESS
- /api/*                 → IS_AUTHENTICATED_FULLY (requiere JWT)
```

#### **Roles**:
```yaml
- ROLE_USER  (usuario normal)
- ROLE_ADMIN (administrador, hereda ROLE_USER)
```

---

## 🎨 FRONTEND - PLANTILLAS TWIG

### Estructura de Templates

#### **base.html.twig** (Layout Principal)
```twig
Bloques:
1. {% block title %} - Título de página
2. {% block stylesheets %} - CSS
3. {% block navegacion %} - Incluye parts/navegacion.part.html.twig
4. {% block principal %} - Contenido principal
5. {% block footer %} - Pie de página
6. {% block javascripts %} - JS
```

#### **Templates de Páginas**:
- `index.view.html.twig` - Homepage con carousel y galería por categorías
- `about.view.html.twig` - About con logos de clientes
- `blog.view.html.twig` - Blog con artículos
- `contact.view.html.twig` - Formulario de contacto
- `galeria.html.twig` - Galería de imágenes completa

#### **Componentes Parciales (parts/)**:
- `navegacion.part.html.twig` - Barra de navegación
- `imagenIndex.part.html.twig` - Card de imagen para homepage
- `nombres.part.html.twig` - Componente de nombres

#### **Assets Frontend**:
```
Bootstrap 3/4:
- CSS: /bootstrap/css/bootstrap.min.css
- JS: /bootstrap/js/bootstrap.min.js

Custom:
- CSS: /css/style.css
- CSS: /css/magnific-popup.css

jQuery:
- /js/jquery.min.js
- /js/jquery.magnific-popup.min.js
- /js/scrollreveal.min.js
- /js/script.js
- /js/delete.js

Font Awesome:
- /font-awesome/css/font-awesome.min.css
```

---

## 🐳 DOCKER Y BASE DE DATOS

### compose.yaml
```yaml
services:
  database:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: app (o el nombre configurado)
      POSTGRES_USER: app
      POSTGRES_PASSWORD: !ChangeMe!
    volumes:
      - database_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"
```

### Migraciones
```bash
# Crear migración
php bin/console make:migration

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Ver estado
php bin/console doctrine:migrations:status
```

---

## 📋 DEPENDENCIAS COMPOSER (composer.json)

### Dependencias Principales
```json
{
  "php": ">=8.1",
  "symfony/framework-bundle": "6.1.*",
  "symfony/console": "6.1.*",
  "symfony/twig-bundle": "6.1.*",
  "symfony/form": "6.1.*",
  "symfony/validator": "6.1.*",
  "symfony/security-bundle": "6.1.*",
  "symfony/mailer": "6.1.*",
  "symfony/asset": "6.1.*",
  
  "doctrine/orm": "2.19",
  "doctrine/dbal": "3.8",
  "doctrine/doctrine-bundle": "^2.13",
  "doctrine/doctrine-migrations-bundle": "^3.7",
  
  "lexik/jwt-authentication-bundle": "^2.18",
  "lcobucci/jwt": "^4.0",
  
  "sensio/framework-extra-bundle": "^6.2",
  "symfonycasts/verify-email-bundle": "^1.18",
  "twig/extra-bundle": "^2.12|^3.0"
}
```

### Dependencias de Desarrollo
```json
{
  "symfony/maker-bundle": "^1.50",
  "symfony/debug-bundle": "6.1.*",
  "symfony/web-profiler-bundle": "6.1.*",
  "symfony/phpunit-bridge": "^8.0",
  "phpunit/phpunit": "^9.5"
}
```

---

## 🔧 CONFIGURACIÓN IMPORTANTE

### .env (Variables de entorno)
```env
APP_ENV=dev
APP_SECRET=tu_secret_key

DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=tu_passphrase

MAILER_DSN=gmail://usuario:password@default
```

### Generar Claves JWT
```bash
php bin/console lexik:jwt:generate-keypair
```

Esto crea:
- `config/jwt/private.pem`
- `config/jwt/public.pem`

---

## 🚀 FLUJO DE TRABAJO API REST

### 1. Registro de Usuario
```http
POST /api/auth/register
Content-Type: application/json

{
  "username": "usuario",
  "email": "email@ejemplo.com",
  "password": "contraseña"
}
```

### 2. Login (Obtener Token)
```http
POST /api/auth/login_check
Content-Type: application/json

{
  "username": "usuario",
  "password": "contraseña"
}

Respuesta:
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### 3. Usar API con Token
```http
GET /api/imagenesapi
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

### 4. Crear Imagen
```http
POST /api/imagenesapinueva
Authorization: Bearer [token]
Content-Type: application/json

{
  "nombre": "imagen.jpg",
  "descripcion": "Descripción",
  "categoria": 1
}
```

---

## 📊 OPERACIONES CRUD TÍPICAS

### Imágenes (Web con Formularios)
1. **Listar**: GET `/imagen` → Muestra todas las imágenes
2. **Ver**: GET `/imagen/{id}` → Detalle de una imagen
3. **Crear**: 
   - GET `/imagen/new` → Formulario
   - POST `/imagen/new` → Procesar creación
4. **Editar**:
   - GET `/imagen/edit/{id}` → Formulario
   - POST `/imagen/edit/{id}` → Procesar edición
5. **Eliminar**: POST `/imagen/delete/{id}`

### Imágenes (API REST)
1. **Listar**: GET `/api/imagenesapi`
2. **Ver una**: GET `/api/imagenesapi/{id}`
3. **Crear**: POST `/api/imagenesapinueva`
4. **Actualizar**: PUT `/api/imagenesapi/{id}`
5. **Eliminar**: DELETE `/api/imagenesapi/{id}`

---

## 🎯 CARACTERÍSTICAS ESPECIALES

### 1. Validación
- Symfony Validator con constraints
- Grupos de validación: `['upload']`, `['api']`
- Validación de tipos de archivo (JPEG, PNG)

### 2. Repositorios Personalizados
Los repositorios tienen métodos custom:
```php
ImagenRepository:
- findByNombrePattern(string $pattern)
- findByCategoria(int $categoriaId)
- findOrderedBy(string $field, string $order)
```

### 3. Búsqueda y Filtrado
- Por descripción
- Por rango de fechas
- Ordenación por diferentes campos

### 4. Upload de Archivos
- Almacenamiento en `public/images/`
- Diferentes carpetas según tipo (portfolio, galería, clientes)
- Validación de tipo MIME

---

## 🧩 PATRONES Y ARQUITECTURA

### 1. Separación de Capas
```
Controller → BLL → Repository → Entity
```
- **Controller**: Maneja HTTP (request/response)
- **BLL**: Lógica de negocio
- **Repository**: Consultas a BD
- **Entity**: Modelo de datos

### 2. API Base Controller
```php
BaseApiController:
- getContent(Request): array
- getResponse($data, $statusCode): JsonResponse
```
Todos los controladores API heredan de este.

### 3. Dependency Injection
- Services autowired
- Inyección en constructores y métodos

---

## 📝 COMANDOS ÚTILES

```bash
# Instalar dependencias
composer install

# Crear base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Crear entidad
php bin/console make:entity

# Crear controlador
php bin/console make:controller

# Crear formulario
php bin/console make:form

# Generar claves JWT
php bin/console lexik:jwt:generate-keypair

# Limpiar caché
php bin/console cache:clear

# Ver rutas
php bin/console debug:router

# Ver servicios
php bin/console debug:container

# Servidor de desarrollo
symfony server:start
# o
php -S localhost:8000 -t public/
```

---

## ✅ CHECKLIST PARA REPLICAR

### Setup Inicial
- [ ] Instalar PHP 8.1+
- [ ] Instalar Composer
- [ ] Instalar Symfony CLI
- [ ] Instalar Docker (para PostgreSQL)

### Configuración Proyecto
- [ ] `composer create-project symfony/website-skeleton proyecto`
- [ ] Copiar `composer.json` y hacer `composer install`
- [ ] Copiar estructura de carpetas
- [ ] Configurar `.env` con DATABASE_URL

### Base de Datos
- [ ] Iniciar Docker Compose
- [ ] Crear entidades (User, Imagen, Categoria, Asociado)
- [ ] Generar migraciones
- [ ] Ejecutar migraciones
- [ ] (Opcional) Importar cursosym.sql

### Seguridad
- [ ] Instalar lexik/jwt-authentication-bundle
- [ ] Generar claves JWT
- [ ] Configurar security.yaml (firewalls, access_control)
- [ ] Crear UserProvider

### Backend
- [ ] Crear controladores web (ProyectoController, ImagenController, etc.)
- [ ] Crear controladores API (ImagenApiController, UsuarioApiController)
- [ ] Crear BLL (BaseBLL, ImagenBLL, UsuarioBLL)
- [ ] Crear repositorios personalizados
- [ ] Crear formularios

### Frontend
- [ ] Copiar assets (bootstrap, css, js, font-awesome)
- [ ] Crear plantilla base (base.html.twig)
- [ ] Crear plantillas de páginas
- [ ] Crear componentes parciales (parts/)
- [ ] Copiar imágenes a public/images/

### Testing
- [ ] Probar rutas web
- [ ] Probar login JWT
- [ ] Probar endpoints API
- [ ] Verificar CRUD completo

---

## 🔍 NOTAS IMPORTANTES PARA OTRO AGENTE

### Orden de Creación Recomendado
1. **Primero**: Entidades y migraciones
2. **Segundo**: Repositorios
3. **Tercero**: Seguridad y autenticación
4. **Cuarto**: BLL
5. **Quinto**: Controladores
6. **Sexto**: Plantillas Twig
7. **Séptimo**: Assets y frontend

### Relaciones Clave
- User → Imagen (OneToMany)
- Categoria → Imagen (OneToMany)
- Imagen tiene usuario obligatorio
- Imagen tiene categoría obligatoria

### Puntos Críticos
1. **JWT**: Configurar bien los 3 firewalls (dev, login, api)
2. **Rutas API**: Prefijo `/api` y stateless
3. **BLL**: No poner lógica de negocio en controladores
4. **Validación**: Usar grupos para diferenciar web vs API
5. **CORS**: Si frontend separado, configurar NelmioCorsBundle

### Diferencias con Plantilla Nueva
Al cambiar de plantilla frontend:
- Mantener la misma estructura de rutas
- Mantener los mismos controladores
- Cambiar solo las plantillas Twig
- Actualizar assets (CSS, JS)
- Mantener las clases CSS importantes en el nuevo CSS

---

## 📞 ENDPOINTS COMPLETOS

### Web (HTML)
```
GET  /                     - Homepage
GET  /about                - About
GET  /blog                 - Blog
GET  /contact              - Contacto
GET  /login                - Login
POST /login                - Procesar login
GET  /logout               - Logout
GET  /register             - Registro
POST /register             - Procesar registro
GET  /verify/email         - Verificar email
GET  /imagen               - Lista imágenes
GET  /imagen/new           - Formulario nueva imagen
POST /imagen/new           - Crear imagen
GET  /imagen/edit/{id}     - Formulario editar
POST /imagen/edit/{id}     - Actualizar imagen
POST /imagen/delete/{id}   - Eliminar imagen
GET  /imagen/{id}          - Ver imagen
GET  /galeria              - Galería completa
```

### API REST (JSON)
```
POST   /api/auth/register           - Registro
POST   /api/auth/login_check        - Login (obtener JWT)
GET    /api/prueba                  - Test API
GET    /api/imagenesapi             - Listar imágenes
GET    /api/imagenesapi/ordenadas/{order} - Listar ordenadas
GET    /api/imagenesapi/{id}        - Ver imagen
POST   /api/imagenesapinueva        - Crear imagen
PUT    /api/imagenesapi/{id}        - Actualizar imagen
DELETE /api/imagenesapi/{id}        - Eliminar imagen
PATCH  /api/profile/password        - Cambiar password
```

---

## 🎨 PLANTILLA FRONTEND ACTUAL

### Características Visuales
- **Header**: Carousel con imagen hero
- **Navegación**: Bootstrap navbar sticky
- **Homepage**: Grid de imágenes por categorías con tabs
- **About**: Sección de clientes con logos
- **Blog**: Cards de artículos con imágenes
- **Galería**: Lightbox con Magnific Popup
- **Footer**: Links y copyright

### Librerías JS
- jQuery 3.x
- Bootstrap 3/4
- Magnific Popup (lightbox)
- ScrollReveal (animaciones)
- jQuery Easing (smooth scroll)

### Paleta de Colores y Estilo
- Ver `public/css/style.css` para colores específicos
- Tipografía: Ver fonts en base.html.twig
- Iconos: Font Awesome

---

**FIN DEL DOCUMENTO**

Este documento contiene toda la información necesaria para replicar este proyecto con otra plantilla frontend manteniendo la misma estructura, funcionalidad y lógica de negocio.
