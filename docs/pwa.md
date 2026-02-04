# PWA (Progressive Web App)

## Objetivo
Implementar una experiencia instalable y confiable para SEIM utilizando un manifiesto web, un service worker y una pantalla offline.

## Componentes
- **manifest.php**: define nombre, colores, iconos y modo de visualización a partir de la configuración de empresa.
- **pwa-icon.php**: genera íconos PNG (192/512) desde el logo configurado para cumplir requisitos de instalación.
- **sw.js**: gestiona cacheo de recursos y respuesta offline.
- **offline.html**: página de respaldo cuando no hay conectividad.
- **Registro del Service Worker**: se ejecuta desde `partials/footer-scripts.php`.

## Estrategia de cache
- **Precache**: manifiesto, iconos, favicon y página offline.
- **Navegación (HTML)**: estrategia *network-first* con fallback a cache u offline.
- **Recursos estáticos (CSS/JS/imagenes/fuentes)**: *stale-while-revalidate*.

## Checklist de despliegue
1. Servir el sitio en HTTPS.
2. Verificar que `manifest.php` se entregue con `Content-Type: application/manifest+json`.
3. Confirmar que `sw.js` se entregue sin cacheo agresivo desde el servidor.
4. Ejecutar auditoría en Lighthouse para validar la instalación.

## Validaciones recomendadas
- Abrir la app en Chrome y revisar `Application > Manifest`.
- Simular modo offline en DevTools y navegar a una página.
- Probar instalación desde el menú del navegador.
