# Sistema Web PEEC-INLASA

Este sistema web está desarrollado en **Laravel 12** para la gestión de programas y procesos del **PEEC - INLASA**.  
Incluye módulos para la administración de usuarios, gestión de laboratorios, inscripción a paquetes, documentos y más.

## 📋 Requisitos previos

Antes de instalar el proyecto, asegúrate de tener instalado:

- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** >= 8.0
- **Node.js** >= 20.x y **npm** >= 10.x
- **Git**
- Extensiones de PHP habilitadas:
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `fileinfo`
  - `tokenizer`
  - `xml`
  - `ctype`

> **Nota:** Se recomienda usar [XAMPP](https://www.apachefriends.org/) o [Laragon](https://laragon.org/) para el entorno local.

---

## 🚀 Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/OmarRMC/peec-inlasa.git
cd peec-inlasa
```

2. **Instalar dependencias de PHP**

```bash
composer install
```

3. **Instalar dependencias de JavaScript**

```bash
npm install
```

4. **Configurar el archivo `.env`**

- Copia el archivo de ejemplo:

```bash
cp .env.example .env
```

- Edita `.env` y configura:
  - Conexión a base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
  - Configuración de correo (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`)

5. **Generar la clave de la aplicación**

```bash
php artisan key:generate
```

6. **Ejecutar migraciones y seeders**

```bash
php artisan migrate --seed
```

Esto creará las tablas y datos iniciales como roles, permisos y usuario administrador.

---

## ▶️ Ejecución del proyecto

Para iniciar el servidor local:

```bash
php artisan serve
```
o si se requiere en el puerto 80
```bash
php artisan serve --host=localhost --port=80
```

El sistema estará disponible en:

```
http://127.0.0.1:8000  o http://127.0.0.1:80  dependiendo el comando que se ejecuto
```

Para compilar los assets (CSS y JS):

- **Modo desarrollo**:

```bash
npm run dev
```

- **Modo producción**:

```bash
npm run build
```

---

## 🔑 Usuario inicial (Admin)

Después de instalar, puedes ingresar con:

- **Usuario:** admin  
- **Contraseña:** password

---

## 📂 Estructura principal del proyecto

```
app/            # Lógica de negocio y controladores
database/       # Migraciones, seeders y factories
resources/      # Vistas Blade, JS y CSS
routes/         # Rutas web y API
public/         # Archivos públicos
```

---

## 🛠 Tecnologías utilizadas

- **Laravel 12**
- **PHP 8.2**
- **MySQL**
- **Blade Templates**
- **Tailwind CSS**
- **JavaScript (ES6)**
- **npm / Vite**

---

# Migración a XAMPP
## Mover el archivo del proyecto al httpdocs
# Crear 2 VirtualHost 
# Editar **httpd-vhosts.conf**
1. Archivo en:
2. Abre con permisos de administrador:
```bash
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```
3. Agrega lo siguiente:
```bash
# Dominio principal que apunta a htdocs
<VirtualHost *:80>
    ServerName inlasa.bo
    DocumentRoot "C:/xampp/htdocs"
    <Directory "C:/xampp/htdocs">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Subdominio que apunta a peec-inlasa/public
<VirtualHost *:80>
    ServerName peec.inlasa.bo
    DocumentRoot "C:/xampp/htdocs/peec-inlasa/public"
    <Directory "C:/xampp/htdocs/peec-inlasa/public">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
## Editar hosts de Windows
1. Abre con permisos de administrador:
```bash
C:\Windows\System32\drivers\etc\hosts
```
2. Agrega al final estas líneas:
```bash
127.0.0.1   inlasa.bo
127.0.0.1   peec.inlasa.bo
```
# Configuraciones  php render de PDF 
## En XAMPP (Windows)
1. Ve a la carpeta donde está tu PHP, por ejemplo:
```bash
C:\xampp\php\php.ini
```
2. Abre php.ini con un editor de texto.
3. Busca la línea:
```bash
;extension=gd
```
4. Quítale el ; (punto y coma) para habilitarla
```bash
extension=gd
```
# Ejecutar el siguiente comando para los archivos
```bash
php artisan storage:link
```
- Este comando en Laravel crea un enlace simbólico (shortcut) desde la carpeta:
```bash
public/storage
```
- hacia la carpeta:
```bash
storage/app/public
```
