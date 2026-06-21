export function resourceData(resource) {
    if (resource == null) {
        return {};
    }

    if (resource.data != null && typeof resource.data === 'object' && !Array.isArray(resource.data)) {
        return resource.data;
    }

    return resource;
}

export function resourceItems(resource) {
    if (Array.isArray(resource)) {
        return resource;
    }

    if (Array.isArray(resource?.data)) {
        return resource.data;
    }

    return [];
}

export function asArray(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (value == null) {
        return [];
    }

    if (typeof value === 'object') {
        return Object.values(value);
    }

    return [];
}
