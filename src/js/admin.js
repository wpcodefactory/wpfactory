/**
 * WPFactory theme - Admin js.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

// Loads modules dynamically and asynchronously
__webpack_public_path__ = WPFTAJS.themeURI + "/assets/";
let modules = WPFTAJS.modulesRequired;
if (modules && modules.length) {
    modules.forEach(function (module) {
        import(
            /* webpackMode: "lazy"*/
            `./modules/${module}`)
            .then(function (component) {
                if (document.readyState !== 'loading') {
                    component.init();
                } else {
                    document.addEventListener('DOMContentLoaded', function () {
                        component.init();
                    });
                }
            });
    });
}

// Loads modules manually and synchronously
// import module from './modules/menus.js';
// module.init()