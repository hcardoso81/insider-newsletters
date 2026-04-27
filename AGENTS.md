# AGENTS.md

Guia de trabajo para agentes en este plugin WordPress.

## Proyecto

- Plugin: `Insider Newsletters`.
- Entrada principal: `insider-newsletters.php`.
- Namespace PHP: `InsiderLatam\Newsletter`.
- Text domain: `insider-newsletters`.
- Version actual definida en header y constante: `1.0.4`.
- Objetivo: centralizar newsletters y banners en WordPress, renderizar newsletters como HTML responsive para email y permitir exportar el codigo HTML desde el admin.

## Arquitectura

- `src/Bootstrap`: autoload y arranque del plugin.
- `src/Domain`: modelo, helpers de dominio y renderizado.
- `src/Infrastructure`: integraciones con WordPress, ACF y logging.
- `src/UI/Admin`: columnas, assets admin, filtros ACF y accion de exportacion.
- `src/UI/Frontend`: carga del template single para newsletters.
- `templates`: HTML final del newsletter y template single.
- `assets/js`: comportamiento admin para filtros ACF.
- `logs`: logs generados por el plugin; no commitear archivos `.log`.

El plugin usa un autoloader propio PSR-4 simple. No hay Composer en este repo.

## Convenciones PHP

- Mantener `declare(strict_types=1);`.
- Mantener clases `final` salvo que haya una razon clara para abrir extension.
- Usar el namespace `InsiderLatam\Newsletter\...`.
- Preferir dependencias explicitas por constructor cuando una clase necesita servicios como `NewsletterRenderer` o `ErrorLogger`.
- Registrar hooks dentro de metodos `registerHooks()`.
- Evitar logica pesada en el archivo principal del plugin; agregarla en clases bajo `src/`.
- Usar constantes de `Domain\Model\PostType` para CPTs en vez de strings repetidos cuando aplique.
- Escapar salida con APIs WordPress: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- Sanitizar entrada: `absint`, `sanitize_text_field`, nonces y capacidades segun corresponda.
- Para textos visibles, usar funciones i18n de WordPress con text domain `insider-newsletters`.

## CPTs

Los post types actuales son:

- `newsletter`
- `banner-vertical`
- `banner-horizontal`

Se registran en `src/Infrastructure/WordPress/PostTypeRegistrar.php`.

Si agregas o cambias CPTs:

- Actualizar `src/Domain/Model/PostType.php`.
- Actualizar registros, templates, admin assets/columns y cualquier filtro ACF relacionado.
- Considerar `flush_rewrite_rules()` solo en activacion/desactivacion, no en cada request.

## ACF

Los grupos locales se registran en `src/Infrastructure/WordPress/AcfFieldGroupRegistrar.php`.

Campos relevantes:

- Newsletter:
  - `news_header_image`
  - `link_header`
  - `news_description`
  - `post_1_column`
  - `post_2_columns_posts`
  - `post_2_columns_banners`
- Overrides en posts:
  - `newslettter_title_description_active` (mantener este nombre aunque tiene triple `t`; es compatibilidad de datos)
  - `post_newsletter_title`
  - `post_newsletter_description`
- Banner vertical:
  - `banner_vertical_link`
- Banner horizontal:
  - `banner_horizonal_link` (mantener typo por compatibilidad)

Usar `InsiderLatam\Newsletter\Domain\Support\Acf::getField()` para leer campos ACF con fallback cuando ACF no este disponible.

El registrador evita duplicar campos legacy por nombre/clave. Tener cuidado al renombrar keys o names porque puede afectar contenido ya existente.

## Render de newsletters

El render principal vive en `src/Domain/Service/NewsletterRenderer.php`.

- `render(int $newsletterId)` valida que el post sea `newsletter`, arma contexto e incluye `templates/newsletter.php`.
- `buildContext()` normaliza campos ACF y listas de IDs.
- Los metodos `createNode...()` imprimen bloques HTML/table markup para email.

El HTML de email debe:

- Mantener layout basado en tablas.
- Mantener estilos criticos inline cuando afecten compatibilidad email.
- Ser conservador con CSS moderno; priorizar compatibilidad con clientes de email.
- Mantener ancho base de `650px` salvo pedido explicito.
- Cuidar que imagenes usen `display:block`, `height:auto`, `max-width`/`width` adecuados.

## Exportacion

La exportacion se maneja en `src/UI/Admin/ExportAction.php`.

- Link de fila en newsletters: `Exportar codigo`.
- Endpoint admin-post: `admin_post_insider_newsletter_export`.
- Requiere nonce `insider_newsletter_export_{postId}`.
- Requiere capacidad `edit_post`.
- Devuelve `text/html; charset=UTF-8` como descarga.

Si modificas exportacion, preservar validaciones de permiso, nonce y tipo de post.

## Frontend

`src/UI/Frontend/SingleTemplateLoader.php` reemplaza el template para single de `newsletter` por `templates/single-newsletter.php`.

El single renderiza directamente el HTML del newsletter usando `NewsletterRenderer`.

## Admin

- `AdminColumns` quita columnas no usadas de CPTs del plugin.
- `AdminAssets` carga `assets/js/acf-custom-filter.js` solo en pantallas de edicion de los CPTs del plugin y agrega CSS para relaciones ACF.
- `RelationshipFieldQuery` ordena relaciones ACF por fecha descendente.

## Logging

`src/Infrastructure/Logging/ErrorLogger.php` escribe logs en `logs/plugin-errors-YYYY-MM-DD.log`.

- No commitear archivos `.log`.
- Usar `$logger->warning()`, `$logger->error()` o `$logger->exception()` en flujos donde conviene diagnostico.
- No registrar datos sensibles innecesarios.

## Validacion

Antes de cerrar cambios PHP, correr lint sobre archivos modificados:

```powershell
php -l insider-newsletters.php
php -l src\Ruta\Archivo.php
```

Si se toca JavaScript, revisar sintaxis manualmente o con las herramientas disponibles del proyecto. Este repo no define build/test runner propio.

## Cuidado con compatibilidad

- No introducir Composer, frameworks ni dependencias nuevas sin pedir confirmacion.
- No cambiar nombres de campos ACF con typos existentes salvo que tambien haya migracion/compatibilidad.
- No cambiar slugs de CPTs o nombres de post type sin considerar URLs y datos existentes.
- No borrar logs ni archivos del usuario.
- Mantener compatibilidad con instalaciones donde ACF no este activo; el plugin debe degradar con logs/fallbacks.

## Estilo de cambios

- Cambios pequenos y enfocados.
- Seguir la estructura por capas existente.
- Preferir helpers existentes antes de crear abstracciones nuevas.
- Si se agrega una feature, conectarla desde `Plugin::boot()` instanciando la clase correspondiente y llamando `registerHooks()`.
- Si se agregan templates, mantenerlos en `templates/` y documentar variables esperadas al inicio.
