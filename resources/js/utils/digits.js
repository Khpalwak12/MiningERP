/**
 * Convert Eastern Arabic (٠-٩) and Persian (۰-۹) digits to Western (0-9).
 */
export function normalizeDigits(value) {
    if (value == null || value === '') {
        return value ?? '';
    }

    return String(value).replace(/[\u06F0-\u06F9\u0660-\u0669]/g, (digit) => {
        const code = digit.charCodeAt(0);

        return String(code >= 0x06F0 ? code - 0x06F0 : code - 0x0660);
    });
}
