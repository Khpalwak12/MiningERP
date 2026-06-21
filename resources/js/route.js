import { route as ziggyRoute } from 'ziggy-js';

function route(name, params, absolute, config) {
    const ziggy = config ?? globalThis.Ziggy;

    return ziggyRoute(name, params, absolute, ziggy);
}

route.current = function (name, params, absolute, config) {
    const ziggy = config ?? globalThis.Ziggy;

    return ziggyRoute(undefined, undefined, undefined, ziggy).current(name, params, absolute);
};

globalThis.route = route;
