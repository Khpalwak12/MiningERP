export function buildExportQuery(params = {}) {
    const searchParams = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value !== '' && value != null && value !== undefined) {
            searchParams.set(key, String(value));
        }
    });

    return searchParams.toString();
}
