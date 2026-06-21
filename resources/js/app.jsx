import '../css/app.css';
import './route';
import './bootstrap';

import { syncDocumentDirection } from '@/utils/direction';
import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        if (props.initialPage?.props?.ziggy) {
            globalThis.Ziggy = props.initialPage.props.ziggy;
        }

        syncDocumentDirection(props.initialPage?.props?.locale);

        router.on('success', (event) => {
            if (event.detail.page.props.ziggy) {
                globalThis.Ziggy = event.detail.page.props.ziggy;
            }

            syncDocumentDirection(event.detail.page.props.locale);
        });

        router.on('navigate', (event) => {
            if (event.detail.page.props.ziggy) {
                globalThis.Ziggy = event.detail.page.props.ziggy;
            }
        });

        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
